<?php

/**
 * Created by PhpStorm.
 * User: 66343
 * Date: 2018/8/27
 * Time: 14:12
 */
abstract class topUpBase
{
    protected $scene = '';
    protected $memberObject = null;
    protected $userObject = null;

    function __construct($scene)
    {
        $this->scene = $scene;
    }

    /**
     * 检查充值手机号是否有效
     * @param $country_code
     * @param $phone
     * @return result
     */
    protected function checkPhoneIsValid($country_code, $phone)
    {
        //check规则待定
        return new result(true, '');
    }

    /**
     * 获取电信公司
     * @param $country_code
     * @param $phone
     * @return result
     */
    protected function getTelecomCodeByPhone($country_code, $phone)
    {
        //柬埔寨电话号码是否有规则可区分telecom，如不能需选择telecom步骤
        return new result(true, '', array('telecom_code' => 'smarty'));
    }

    /**
     * 获取telecom充值方式和可选充值金额
     * @param $telecom_code
     * @return result
     */
    protected function getTelecomDataByCode($telecom_code)
    {
        //获取telecom info
        $telecom_info = $this->getTelecomInfoByCode($telecom_code);
        if (!$telecom_info) {
            return new result(false, 'Invalid Telecom.');
        }

        //获取充值金额规格
        $recharge_spec = $this->getRechargeSpecByTelecomId($telecom_info['uid']);
        if (!$recharge_spec) {
            return new result(false, 'No recharge specifications.');
        }

        if ($telecom_info['recharge_type'] == topUpRechargeType::PIN_CODE) {
            $recharge_spec = $this->getPinCodeStockBySpec($recharge_spec);
        }

        //返回数据格式
        $telecom_data = array(
            'telecom_code' => $telecom_info['telecom_code'],
            'telecom_name' => $telecom_info['telecom_name'],
            'telecom_icon' => $telecom_info['telecom_icon'],
            'telecom_desc' => $telecom_info['telecom_desc'],
            'recharge_type' => $telecom_info['recharge_type'],
        );
        $telecom_data['recharge_spec'] = $recharge_spec;
        return new result(true, '', $telecom_data);
    }

    /**
     * 获取telecom info
     * @param $telecom_code
     * @param int $is_close
     * @return bool|mixed|null
     */
    protected function getTelecomInfoByCode($telecom_code, $is_close = 0)
    {
        $telecom_code = trim($telecom_code);
        $m_top_up_telecom = M('top_up_telecom');
        $telecom_info = $m_top_up_telecom->find(
            array(
                'telecom_code' => $telecom_code,
                'is_close' => $is_close,
            )
        );
        return $telecom_info;
    }

    /**
     * 获取telecom Spec
     * @param $telecom_id
     * @return bool|mixed|null
     */
    protected function getRechargeSpecByTelecomId($telecom_id)
    {
        $telecom_id = intval($telecom_id);
        $m_top_up_telecom_detail = M('top_up_telecom_detail');
        $telecom_detail = $m_top_up_telecom_detail->orderBy('recharge_amount ASC')->select(
            array(
                'telecom_id' => $telecom_id,
                'is_close' => 0
            )
        );

        if (!$telecom_detail) {
            return array();
        }

        $recharge_spec = array();
        foreach ($telecom_detail as $val) {
            $telecom_spec[] = array(
                'recharge_amount' => $val['recharge_amount'],
                'discount_rate' => $val['discount_rate'],
                'service_fee' => $val['service_fee'],
                'actual_amount' => $val['actual_amount'],
            );
        }

        return $recharge_spec;
    }

    /**
     * 获取telecom详情 根据传入参数
     * @param $country_code
     * @param $phone
     * @return result
     */
    protected function getTelecomDataByPhone($country_code, $phone)
    {
        //检查电话号码是否有效
        $check_rt = $this->checkPhoneIsValid($country_code, $phone);
        if (!$check_rt->STS) {
            return $check_rt;
        }

        //根据电话号码获取telecom code
        $rt_1 = $this->getTelecomCodeByPhone($country_code, $phone);
        if (!$rt_1->STS) {
            return $rt_1;
        }
        $telecom_code = $rt_1->DATA['telecom_code'];

        //根据telecom code获取telecom详情
        $rt_2 = $this->getTelecomDataByCode($telecom_code);
        return $rt_2;
    }

    /**
     * 创建充值订单
     * @param $object_id
     * @param $country_code
     * @param $phone
     * @param $telecom_code
     * @param $recharge_amount
     * @param $currency
     * @param $creator_id
     * @return result
     */
    public function createTopUpTrx($object_id, $country_code, $phone, $telecom_code, $recharge_amount, $currency, $creator_id)
    {
        //检查电话号码是否有效
        $check_rt = $this->checkPhoneIsValid($country_code, $phone);
        if (!$check_rt->STS) {
            return $check_rt;
        }

        //根据电话号码获取telecom code
        $rt_1 = $this->getTelecomCodeByPhone($country_code, $phone);
        if (!$rt_1->STS) {
            return $rt_1;
        }
        if ($rt_1->DATA['telecom_code'] != $telecom_code) {
            return new result(false, 'The telephone do not match the telecom.');
        }

        $telecom_info = $this->getTelecomInfoByCode($telecom_code);
        if (!$telecom_info) {
            return new result(false, 'Invalid Telecom.');
        }
        $recharge_type = $telecom_info['recharge_type'];

        $rt_2 = $this->getRechargeAmountDetail($telecom_info['uid'], $recharge_type, $recharge_amount);
        if (!$rt_2->STS) {
            return $rt_2;
        }
        $telecom_detail = $rt_2->DATA;
        $service_fee = $telecom_detail['service_fee'];
        $actual_amount = $telecom_detail['actual_amount'];
        $discount_amount = $recharge_amount + $service_fee - $actual_amount;

        if (!$this->userObject) {
            $this->userObject = new objectUserClass($creator_id);
        }

        $m_top_up_trx = M('top_up_trx');
        $trx_row = $m_top_up_trx->newRow();
        $trx_row->object_id = $object_id;
        $trx_row->scene = $this->scene;
        $trx_row->telecom_code = $telecom_code;
        $trx_row->country_code = $country_code;
        $trx_row->phone = $phone;
        $trx_row->recharge_type = $recharge_type;
        $trx_row->currency = $currency;
        $trx_row->recharge_amount = $recharge_amount;
        $trx_row->discount_amount = $discount_amount;
        $trx_row->service_fee = $service_fee;
        $trx_row->actual_amount = $actual_amount;
        $trx_row->state = topUpTrxState::CREATE;
        $trx_row->creator_id = $creator_id;
        $trx_row->creator_name = $this->userObject->user_name;
        $trx_row->create_time = Now();
        $insert_rt = $trx_row->insert();
        if (!$insert_rt->STS) {
            return new result(false, 'Create trx failed.');
        }

        $trx_id = $insert_rt->AUTO_ID;

        if ($recharge_type == topUpRechargeType::DIRECT) {
            $rt_3 = $this->insertTopUpDirectRecharge($trx_id, $telecom_info['uid'], $currency, $actual_amount, $creator_id);
        } else {
            $rt_3 = $this->insertTopUpPincodeRecharge($trx_id, $telecom_info['uid'], $currency, $actual_amount, $creator_id);
        }
        if (!$rt_3->STS) {
            return $rt_3;
        }

        $return_data = array(
            'trx_id' => $insert_rt->AUTO_ID,
            'recharge_type' => $recharge_type,
            'recharge_amount' => $recharge_amount,
            'discount_amount' => $discount_amount,
            'service_fee' => $service_fee,
            'actual_amount' => $actual_amount,
        );

        return new result(true, 'Create trx successful.', $return_data);

    }

    /**
     * 获取trx详情
     * @param $trx_id
     * @return bool|mixed|null
     */
    protected function getTopUpTrxById($trx_id)
    {
        $m_top_up_trx = M('top_up_trx');
        $trx_info = $m_top_up_trx->find(array('uid' => $trx_id));
        return $trx_info;
    }

    /**
     * 检查充值金额是否可用
     * @param $telecom_id
     * @param $recharge_type
     * @param $amount
     * @return result
     */
    protected function getRechargeAmountDetail($telecom_id, $recharge_type, $amount)
    {
        $m_top_up_telecom_detail = M('top_up_telecom_detail');
        $telecom_detail = $m_top_up_telecom_detail->find(array(
            'telecom_id' => $telecom_id,
            'is_close' => 0,
            'recharge_amount' => $amount
        ));
        if ($telecom_detail) {
            return new result(false, 'Invalid Amount.');
        }

        if ($recharge_type == topUpRechargeType::PIN_CODE) {//todo:: pin code检查库存

        }

        return $telecom_detail;
    }

    /**
     * 保存直冲详情
     * @param $trx_id
     * @param $telecom_id
     * @param $currency
     * @param $amount
     * @param $creator_id
     * @return result
     */
    private function insertTopUpDirectRecharge($trx_id, $telecom_id, $currency, $amount, $creator_id)
    {
        $partner_info = $this->getPartnerInfoByTelecomId($telecom_id);
        if (!$partner_info) {
            return new result(false, 'No direct support.');
        }

        $partner_id = $partner_info['partner_id'];
        $m_top_up_direct_recharge = M('top_up_direct_recharge');
        $row = $m_top_up_direct_recharge->newRow();
        $row->trx_id = $trx_id;
        $row->partner_id = $partner_id;
        $row->currency = $currency;
        $row->amount = $amount;
        $row->state = topUpDirectRechargeState::CREATE;

        if (!$this->userObject) {
            $this->userObject = new objectUserClass($creator_id);
        }

        $row->creator_id = $creator_id;
        $row->creator_name = $this->userObject->user_name;
        $row->create_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true);
        } else {
            return new result(false, 'Insert direct recharge failed.');
        }
    }

    /**
     * 获取直冲partner
     * @param $telecom_id
     * @return bool|mixed|null
     * @return result
     */
    private function getPartnerInfoByTelecomId($telecom_id)
    {
        $m_top_up_telecom_partner = M('top_up_telecom_partner');
        $partner_info = $m_top_up_telecom_partner->find(array('telecom_id' => $telecom_id));
        return $partner_info;
    }

    /**
     * 保存pincode充值详情
     * todo:: 保存详情  锁定pin code卡
     * @param $trx_id
     * @param $telecom_id
     * @param $currency
     * @param $amount
     * @param $creator_id
     */
    private function insertTopUpPincodeRecharge($trx_id, $telecom_id, $currency, $amount, $creator_id)
    {

    }

    /**
     * 充值订单支付
     * @param $passbook_id
     * @param $amount
     * @param $currency
     * @return result
     */
    protected function paymentTopUpTrx($passbook_id, $amount, $currency = currencyEnum::USD)
    {
        //扣款
        $trading = new incomeFromBalanceTradingClass($passbook_id, $amount, $currency, incomingTypeEnum::TOP_UP_INCOMING, businessTypeEnum::OTHER);
        $trading->subject = "Member Top Up";
        $trading->remark = "";
        $trading->sys_memo = "";
        $rt = $trading->execute();
        return $rt;
    }

    /**
     * 充值操作
     * @param $trx_id
     * @param $operator_id
     * @return result
     */
    protected function directRecharge($trx_id, $operator_id)
    {
        $m_top_up_direct_recharge = M('top_up_direct_recharge');
        $row = $m_top_up_direct_recharge->getRow(array('trx_id' => $trx_id, 'api_state' => topUpDirectRechargeState::CREATE));
        if (!$row) {
            return new result(false, 'Invalid trx id.');
        }

        //调用充值接口
        $rt_1 = $this->apiDirectRecharge($row['partner_id'], $row['amount'], $row['currency']);
        if (!$rt_1->STS) {
            return $rt_1;
        }
        $data = $rt_1->DATA;

        $row->state = topUpDirectRechargeState::SUCCESS;
        $row->api_trx_id = $data['api_trx_id'];
        $row->api_result = $data['api_result'];

        if (!$this->userObject) {
            $this->userObject = new objectUserClass($operator_id);
        }

        $row->updater_id = $operator_id;
        $row->updater_name = $this->userObject->user_name;
        $row->update_time = Now();
        $rt_2 = $row->update();
        if (!$rt_2->STS) {
            return new result(false, 'Update direct recharge failed.');
        }
        return new result(true);
    }

    /**
     * 充值接口调用
     * todo:: api确定
     * @param $partner_id
     * @param $amount
     * @param $currency
     * @return result
     */
    private function apiDirectRecharge($partner_id, $amount, $currency)
    {
        return new result(true);
    }

    /**
     * 修改订单
     * @param $trx_id
     * @param $update_arr
     * @return result
     */
    protected function updateTopUpTrx($trx_id, $update_arr)
    {
        $m_top_up_trx = M('top_up_trx');
        $row = $m_top_up_trx->getRow($trx_id);
        $row->state = $update_arr['state'];
        $row->passbook_trading_id = $update_arr['passbook_trading_id'];
        if (!$this->userObject) {
            $this->userObject = new objectUserClass($update_arr['operator_id']);
        }
        $row->remark = $update_arr['remark'];
        $row->updater_id = $update_arr['operator_id'];
        $row->updater_name = $this->userObject->user_name;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true);
        } else {
            return new result(false, 'Update trx failed.');
        }
    }

    /**
     * 获取可充值信息
     * @param $params
     * @return result
     */
    abstract public function getTelecomData($params);

    /**
     * 创建订单
     * @param $params
     * @return result
     */
    abstract public function createTopUpTrxByScene($params);

}

//member app手机充值
class topUpMember extends topUpBase
{

    /**
     * 获取可充值信息
     * @param $params
     * @return result
     */
    public function getTelecomData($params)
    {
        $country_code = trim($params['country_code']);
        $phone = trim($params['phone']);
        return $this->getTelecomDataByPhone($country_code, $phone);
    }

    /**
     * 创建订单
     * @param $params
     * @return result
     */
    public function createTopUpTrxByScene($params)
    {
        $member_id = intval($params['member_id']);
        $memberObj = new objectMemberClass($member_id);
        $check_rt = $memberObj->checkValid();
        if (!$check_rt->STS) {
            return $check_rt;
        }
        $object_id = $memberObj->object_id;
        $country_code = trim($params['country_code']);
        $phone = trim($params['phone']);
        $telecom_code = trim($params['telecom_code']);
        $recharge_amount = round($params['recharge_amount'], 2);
        $creator_id = intval($params['creator_id']);
        return $this->createTopUpTrx($object_id, $country_code, $phone, $telecom_code, $recharge_amount, currencyEnum::USD, $creator_id);
    }

    /**
     * 确认订单
     * @param $params
     * @return result
     */
    public function submitTopUpTrxByScene($params)
    {
        $trx_id = intval($params['biz_id']);
        $trading_pwd = trim($params['trading_pwd']);
        $operator_id = intval($params['operator_id']);

        $trx_info = $this->getTopUpTrxById($trx_id);
        if ($trx_info) {
            return new result(false, 'Invalid biz id.');
        }

        //验证交易密码
        $check_pwd = $this->checkMemberTradingPwd($trx_info['object_id'], $trading_pwd);
        if (!$check_pwd->STS) {
            return $check_pwd;
        }

        //获取储蓄账号
        $passbook_id = $this->getPassbookIdByObjectId($trx_info['object_id']);

        $actual_amount = $trx_info['actual_amount'];
        $conn = ormYo::Conn();
        $conn->submitTransaction();
        try {
            //扣款
            $rt_1 = $this->paymentTopUpTrx($passbook_id, $actual_amount);
            if (!$rt_1->STS) {
                $conn->rollback();

                //失败更新trx表
                $update_arr = array(
                    'state' => topUpTrxState::PAYMENT_FAIL,
                    'operator_id' => $operator_id,
                    'remark' => $rt_1->MSG,
                );
                $this->updateTopUpTrx($trx_id, $update_arr);
                return $rt_1;
            }
            $passbook_trading_id = $rt_1->DATA;

            if ($trx_info['recharge_type'] == topUpRechargeType::DIRECT) {
                //api直冲
                $rt_2 = $this->directRecharge($trx_id, $operator_id);
                if (!$rt_2->STS) {
                    $conn->rollback();

                    //失败更新trx表
                    $update_arr = array(
                        'state' => topUpTrxState::DIRECT_RECHARGE_FAIL,
                        'operator_id' => $operator_id,
                        'remark' => $rt_1->MSG,
                    );
                    $this->updateTopUpTrx($trx_id, $update_arr);
                    return $rt_2;
                } else {
                    $conn->submitTransaction();
                }

                $update_arr = array(
                    'state' => topUpTrxState::SUCCESS,
                    'passbook_trading_id' => $passbook_trading_id,
                    'operator_id' => $operator_id,
                    'remark' => 'Top up successful.',
                );

                //修改订单状态
                $rt_3 = $this->updateTopUpTrx($trx_id, $update_arr);
                if (!$rt_3->STS) {
                    return $rt_3;
                }
                return new result(true, 'Top up successful.');
            } else {
                //获取pin code卡

            }


        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 验证交易密码
     * @param $object_id
     * @param $sign
     * @return result
     */
    private function checkMemberTradingPwd($object_id, $sign)
    {
        $sign = trim($sign);
        if (!$this->memberObject) {
            $m_member = new memberModel();
            $member = $m_member->find(array('obj_guid' => intval($object_id)));
            if (!$member) {
                return new result(false, 'Invalid Id.');
            }

            $member_id = $member['uid'];
            $this->memberObject = new objectMemberClass($member_id);
        }

        $self_sign = md5($member_id . $this->memberObject->trading_password);
        $chk = $this->memberObject->checkTradingPasswordSign($sign, $self_sign, 'Loan repayment by member app');
        return $chk;
    }

    /**
     * 获取passbook id
     * @param $object_id
     * @return result
     */
    private function getPassbookIdByObjectId($object_id)
    {
        if (!$this->memberObject) {
            $m_member = new memberModel();
            $member = $m_member->find(array('obj_guid' => intval($object_id)));
            if (!$member) {
                return new result(false, 'Invalid Id.');
            }

            $member_id = $member['uid'];
            $this->memberObject = new objectMemberClass($member_id);
        }
        return $this->memberObject->getSavingsPassbook();
    }
}


//Counter手机充值
class topUpCounter extends topUpBase
{
    /**
     * 获取可充值信息
     * @param $params
     * @return result
     */
    public function getTelecomData($params)
    {
        $country_code = trim($params['country_code']);
        $phone = trim($params['phone']);
        return $this->getTelecomDataByPhone($country_code, $phone);
    }

    /**
     * 创建订单
     * @param $params
     * @return result
     */
    public function createTopUpTrxByScene($params)
    {
        $member_id = intval($params['member_id']);
        $memberObj = new objectMemberClass($member_id);
        $check_rt = $memberObj->checkValid();
        if (!$check_rt->STS) {
            return $check_rt;
        }
        $object_id = $memberObj->object_id;
        $country_code = trim($params['country_code']);
        $phone = trim($params['phone']);
        $telecom_code = trim($params['telecom_code']);
        $recharge_amount = round($params['recharge_amount'], 2);
        $creator_id = intval($params['creator_id']);
        return $this->createTopUpTrx($object_id, $country_code, $phone, $telecom_code, $recharge_amount, currencyEnum::USD, $creator_id);
    }

    /**
     * 确认订单
     * @param $params
     * @return result
     */
    public function submitTopUpTrxByScene($params)
    {
        $trx_id = intval($params['biz_id']);
        $trading_pwd = trim($params['trading_pwd']);
        $cashier_card_no = trim($params['cashier_card_no']);
        $cashier_key = trim($params['cashier_key']);

        $trx_info = $this->getTopUpTrxById($trx_id);
        if ($trx_info) {
            return new result(false, 'Invalid biz id.');
        }

        //验证member交易密码
        $check_member_pwd = $this->checkMemberTradingPwd($trx_info['object_id'], $trading_pwd);
        if (!$check_member_pwd->STS) {
            return $check_member_pwd;
        }

        $check_teller_pwd = $this->checkTellerPassword($trx_info['creator_id'], $cashier_card_no, $cashier_key);
        if (!$check_teller_pwd->STS) {
            return $check_teller_pwd;
        }


        $passbook_id = '';

        $rt = $this->submitTopUpTrx($trx_id, $passbook_id);
        return $rt;
    }

    /**
     * 验证交易密码
     * @param $object_id
     * @param $trading_pwd
     * @return result
     */
    private function checkMemberTradingPwd($object_id, $trading_pwd)
    {
        $m_member = new memberModel();
        $member = $m_member->find(array('obj_guid' => $object_id));
        if (!$member) {
            return new result(false, 'Invalid Id.');
        }

        if (!$member['trading_password']) {
            return new result(false, 'No set the trading password.');
        }

        if ($member['trading_password'] != trim($trading_pwd)) {
            return new result(false, 'Trading password error.');
        }
        return new result(true);
    }

    /**
     * 验证teller密码
     * @param $cashier_card_no
     * @param $cashier_key
     * @return result
     */
    private function checkTellerPassword($cashier_id, $cashier_card_no, $cashier_key)
    {
        $userObj = new objectUserClass($cashier_id);
        $branch_id = $userObj->branch_id;
        $chk = $this->checkTellerAuth($cashier_id, $branch_id, $cashier_card_no, $cashier_key);
        if (!$chk->STS) {
            return $chk;
        }
        return new result(true);
    }

    /**
     * 验证teller卡
     * @param $user_id
     * @param $branch_id
     * @param $card_no
     * @param $auth_key
     * @return result
     */
    private function checkTellerAuth($user_id, $branch_id, $card_no, $auth_key)
    {
        // 先检查卡是否合法
        $rt = icCardClass::confirm($card_no, $auth_key, null);
        if (!$rt->STS) {
            return new result(false, 'Invalid card. ' . $rt->MSG, null, errorCodesEnum::INVALID_AUTH_CARD, $rt);
        }

        // 检查卡是否授权给用户
        $card = (new um_user_cardModel())->getRow(array(
            'card_no' => $card_no,
            'state' => 1,
        ));
        if (!$card) {
            return new result(false, 'This card is not authorized.', null, errorCodesEnum::INVALID_AUTH_CARD);
        }

        // 检查卡与用户是否匹配
        if ($card->user_id != $user_id) {
            return new result(false, 'Invalid user of this card!' . $card_no . '->' . $user_id, null, errorCodesEnum::INVALID_AUTH_CARD);
        }

        $userObj = new objectUserClass($card->user_id);

        // 是否当前分行的
        if ($branch_id != $userObj->branch_id) {
            return new result(false, 'Invalid branch.', null, errorCodesEnum::INVALID_AUTH_CARD);
        }

        // 检查职位信息
        if ($userObj->position != userPositionEnum::TELLER) {
            return new result(false, 'Position not match: not teller.', null, errorCodesEnum::NOT_CHIEF_TELLER);
        }

        return new result(true, 'success');
    }
}

//Partner手机充值调用
class topUpPartner extends topUpBase
{
    /**
     * 验证partner
     * @param $partner_guid
     * @param $key
     * @return result
     */
    protected function checkPartner($partner_guid, $key)
    {
        return new result(true);
    }

    /**
     * 获取可充值信息
     * @param $params
     * @return result
     */
    public function getTelecomData($params)
    {
        $partner_guid = trim($params['partner_guid']);
        $key = trim($params['key']);
        $check_result = $this->checkPartner($partner_guid, $key);
        if (!$check_result->STS) {
            return $check_result;
        }

        $country_code = trim($params['country_code']);
        $phone = trim($params['phone']);
        return $this->getTelecomDataByPhone($country_code, $phone);
    }

    /**
     * 创建订单
     * @param $params
     * @return result
     */
    public function createTopUpTrxByScene($params)
    {
        $partner_guid = trim($params['partner_guid']);
        $key = trim($params['key']);
        $check_result = $this->checkPartner($partner_guid, $key);
        if (!$check_result->STS) {
            return $check_result;
        }

        $object_id = intval($params['object_id']);
        $country_code = trim($params['country_code']);
        $phone = trim($params['phone']);
        $telecom_code = trim($params['telecom_code']);
        $recharge_amount = round($params['recharge_amount'], 2);
        $creator_id = intval($params['creator_id']);
        return $this->createTopUpTrx($object_id, $country_code, $phone, $telecom_code, $recharge_amount, currencyEnum::USD, $creator_id);
    }

    /**
     * 确认订单
     * @param $params
     * @return result
     */
    public function submitTopUpTrxByScene($params)
    {
        $trx_id = intval($params['biz_id']);
        $key = trim($params['key']);

        $trx_info = $this->getTopUpTrxById($trx_id);
        if ($trx_info) {
            return new result(false, 'Invalid biz id.');
        }

        //验证partner
        $check_result = $this->checkPartner($trx_info['object_id'], $key);
        if (!$check_result->STS) {
            return $check_result;
        }

        $passbook_id = '';

        $rt = $this->submitTopUpTrx($trx_id, $passbook_id);
        return $rt;
    }
}