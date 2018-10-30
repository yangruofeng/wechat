<?php

class icCardClass {
    public static function parseCardData($cardData) {
        $binary = base64_decode($cardData);

        $ret = array();
        for ($i=0,$l=strlen($binary);$i<$l;$i+=48) {
            // 每48个字节是一个block，每个block中
            // 0-31字节是data
            // 32-47字节是block_sign
            $arr = unpack("@$i/H64d/H32s", $binary);
            // 在data中
            // 0-1字节是版本号
            // 2-5字节是设备SN的哈希值
            // 6-9字节是刷卡时间
            // 10-25字节是关键信息的md5签名
            $arr_detail = unpack("S1v/I1s/i1t/H32k", hex2bin($arr["d"]));

            $ret[]=array(
                'data_ver' => $arr_detail['v'],
                'device_sn' => $arr_detail["s"],
                'swipe_time' => $arr_detail["t"],
                'key_info_sign' => $arr_detail['k'],
                'data' => $arr["d"],
                'block_sign' => $arr["s"]
            );
        }

        return $ret;
    }

    public static function confirm($cardNo, $cardData, $keyInfo = null) {
        $card_model = new common_ic_cardModel();
        $chain_model = new common_ic_card_block_chainModel();

        if ($keyInfo)
            $key_info_sign = md5($keyInfo);
        else
            $key_info_sign = "00000000000000000000000000000000";

        $card_info = $card_model->getRow(array('card_no' => $cardNo));
        if (!$card_info) return new result(false, 'Card [' . $cardNo . '] not found', null, errorCodesEnum::UNEXPECTED_DATA);

        $block_chain_in_card = self::parseCardData($cardData);

        // 验证卡数据中每个新的block正确性
        if (!$card_info->last_block_sign) {
            $from_inx = 0;
        } else {
            $from_inx = -1;
        }
        $last_block_sign = null;
        $last_block_time = 0;
        foreach ($block_chain_in_card as $i=>$item) {
            if ($from_inx === -1) {
                if ($item['block_sign'] == $card_info->last_block_sign) {
                    $from_inx = $i+1;
                } else {
                    $block_sign = md5($card_info->last_block_sign . base64_encode(hex2bin($item['data'])) . $card_info->card_no);
                    if ($block_sign == $item['block_sign']) {
                        $from_inx = $i;
                        $last_block_sign = $card_info->last_block_sign;
                    }
                }
            }

            if ($from_inx !== -1 && $i>=$from_inx) {
                // 检查时间
                if ($item['swipe_time'] < $last_block_time) {
                    $from_inx = -1;
                    break;
                }

                // 检查签名
                $block_sign = md5($last_block_sign . base64_encode(hex2bin($item['data'])) . $card_info->card_no);
                if ($block_sign != $item['block_sign']) {
                    $from_inx = -1;
                    break;
                }
            }

            $last_block_sign = $item['block_sign'];
            $last_block_time = $item['swipe_time'];
        }

        if ($from_inx == -1){
            // 验证失败，非法卡
            return new result(false, 'Verify failed', null, errorCodesEnum::INVALID_TOKEN);
        }

        // 写入卡中新的区块到数据库
        if ($from_inx === 0)
            $last_block_sign = $card_info->last_block_sign;
        else
            $last_block_sign = $block_chain_in_card[$from_inx-1]['block_sign'];

        for($i=$from_inx,$c=count($block_chain_in_card);$i<$c;$i++) {
            $item = $block_chain_in_card[$i];
            $block_info = $chain_model->newRow();
            $block_info->card_id = $card_info->uid;
            $block_info->block_sign = $item['block_sign'];
            $block_info->pre_block_sign = $last_block_sign;
            $block_info->data = base64_encode(hex2bin($item['data']));
            $block_info->data_version = $item['data_ver'];
            $block_info->device_sn_hash = $item['device_sn'];
            $block_info->swipe_time = $item['swipe_time'];
            $block_info->key_info_sign = $item['key_info_sign'];
            $rt = $block_info->insert();
            if (!$rt->STS) {
                return new result(false, 'Insert block chain failed - ' . $rt->MSG, null, errorCodesEnum::DB_ERROR);
            }

            $last_block_sign = $item['block_sign'];
        }

//        // 检查是否存在本次操作的新刷卡记录
//        $key_info_matched = false;
//        for ($i=count($block_chain_in_card)-1;$i>=$from_inx;$i--) {
//            $item = $block_chain_in_card[$i];
//            if ($item['key_info_sign'] == $key_info_sign) {
//                $key_info_matched = true;
//                break;
//            }
//        }
//
//        // 没有与本次操作相关的记录
//        if (!$key_info_matched) {
//            return new result(false, 'Please re-swipe card', null, errorCodesEnum::INVALID_TOKEN);
//        }

        // 更新卡信息
        $card_info->last_block_sign = $last_block_sign;
        $card_info->lmt = date("Y-m-d H:i:s");
        if( $card_info->getRowState() == ormDataRow::ROWSTATE_MODIFIED ){
            $rt = $card_info->update();
            if (!$rt->STS) {

                logger::record('ic_card_update',my_json_encode(array(
                    'old_row_info' => $card_info->getOldRow(),
                    'card_info' => $card_info,
                    'card_state' => $card_info->getRowState(),
                    'row_state' => ormDataRow::ROWSTATE_MODIFIED
                )),'ic_card_verify');

                return new result(false, 'Update card failed - '. $rt->MSG, null, errorCodesEnum::DB_ERROR);
            }
        }


        // 当前刷卡时间
        $swipe_time = end($block_chain_in_card)['swipe_time'];

        // 检查卡是否过期
        if ($card_info->expire_time < $swipe_time) {
            return new result(false, 'Card expired', null, errorCodesEnum::TOKEN_EXPIRED);
        }

        return new result(true);
    }

    public static function initializeCard($cardNo, $initialInfo, $operator) {
        $card_model = new common_ic_cardModel();
        $card_info = $card_model->getRow(array('card_no' => $cardNo));

        if (!$card_info) {
            $card_info = $card_model->newRow();
            $card_info->card_no = $cardNo;
            $card_info->card_key = $initialInfo;
            $card_info->expire_time = 2147483647;
            $card_info->last_block_sign = md5($initialInfo);
            $card_info->create_time = date("Y-m-d H:i:s");
            $card_info->create_user_id = $operator['uid'];
            $card_info->create_user_name = $operator['user_name'];
            $ret = $card_info->insert();
            if (!$ret->STS) {
                return new result(false, $ret->MSG, null, errorCodesEnum::DB_ERROR);
            }
        } else {
            $card_info->card_key = $initialInfo;
            $card_info->expire_time = 2147483647;
            $card_info->last_block_sign = md5($initialInfo);
            $card_info->lmt = date("Y-m-d H:i:s");
            $ret = $card_info->update();
            if (!$ret->STS) {
                return new result(false, $ret->MSG, null, errorCodesEnum::DB_ERROR);
            }
        }

        $chain_model = new common_ic_card_block_chainModel();
        $block_info = $chain_model->newRow();
        $block_info->card_id = $card_info->uid;
        $block_info->block_sign = $card_info->last_block_sign;
        $block_info->pre_block_sign = null;
        $block_info->data = $initialInfo;
        $block_info->data_version = 0;
        $rt = $block_info->insert();
        if (!$rt->STS) {
            return new result(false, 'Insert block chain failed - ' . $rt->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true);
    }
}