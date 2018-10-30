<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/10
 * Time: 13:10
 */
class scan_payControl extends bank_apiControl
{

    public function memberScanPayToMemberOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $from_member_id = $params['member_id'];
        $to_member_id = $params['to_member_id'];
        $amount = $params['amount'];
        $currency = $params['currency'];
        $remark = $params['remark'];
        $biz_class = new bizMemberScanPayMemberClass(bizSceneEnum::APP_MEMBER);
        $rt = $biz_class->bizStart($from_member_id,$to_member_id,$amount,$currency,$remark);
        return $rt;
    }


    public function memberScanPayToMemberConfirmOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $biz_id = $params['biz_id'];
        $member_id = $params['member_id'];
        $request_time = $params['request_time'];
        $sign = trim($params['sign']);
        if( !$biz_id || !$member_id || !$sign ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }


        $biz_class = new bizMemberScanPayMemberClass(bizSceneEnum::APP_MEMBER);

        // 验证
        $remark = 'Scan pay to member';
        $chk = $biz_class->checkMemberTradingPasswordSign($biz_id,$member_id,$request_time,$sign,$remark);
        if( !$chk->STS ){
            return $chk;
        }

        // 提交
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = $biz_class->bizSubmit($biz_id);
            if( !$rt->STS ){
                $conn->rollback();
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }


}