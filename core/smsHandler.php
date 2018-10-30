<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 6/17/2016
 * Time: 6:18 PM
 */
class smsHandler
{
    public function send($phone, $type, $params)
    {
        if (!$phone) {
            return new result(false, "phone is required");
        }
        if (!$type) {
            return new result(false, "type is required");
        }

        switch ($type) {
            case smsTaskType::VERIFICATION_CODE:
                return $this->sendVerifyCode($phone, $params['code']);
            case smsTaskType::PIN_CODE:
                return $this->sendPinCode($phone, $params['pin'], $params['amount'], $params['telecom']);
            case smsTaskType::WALLET_CHANGED:
                return $this->sendWalletChange($phone, $params['text']);
            case smsTaskType::LUCKY_NOTICE:
                return $this->sendLuckyNotice($phone, $params['verify_code'], $params['order_sn']);
            default:
                return new result(false, "Not supported type - " . $type);
        }
    }

    /**
     * 重发短信
     * @param $uid
     * @return result
     */
    public function resend($uid)
    {
        $m = M("common_sms");
        $task = $m->getRow($uid);
        if (!$task) {
            return new result(false, 'Invalid Id!');
        }

        if (C("sms_api") == 'yunpian') {
            $api = new sms_api_yunpian();
            $task->task_state = smsTaskState::SENDING;
            $task->handler = "yunpian";
            $task->handle_time = Now();
            $task->update();
            $st = $api->sendSmsToYP($task->phone_id, $task->content);
            $task->handle_result = json_encode($st, true);
            if (isset($st['code']) && $st['code'] == 0) {
                $task->task_state = smsTaskState::SEND_SUCCESS;
                $task->handle_fee = $st['result']['fee'];
                $task->handler_id = $st['result']['sid'];
            } else {
                $task->task_state = smsTaskState::SEND_FAILED;
                $task->update_time = Now();
                $task->update();
                return new result(false, 'Send lucky notice fail');
            }

        }
        $task->update_time = Now();
        $update = $task->update();
        return new result($update->STS, $update->MSG, $task);

    }

    /*
     * 发送验证码模版
     * @param $phone 接收电话号码必须要跟上国际区号
     * @param $code  要发送的验证码
     * @return result
     * */
    public function sendVerifyCode($phone, $code)
    {

        //检查数据
        if (!$phone || !$code) {
            return new result(false, "Empty Data");
        }

        //构造短信模版
        //$content="【KHBUY】You verification code is ".$code;
        $content = "【Samrithisak】" . $code . " is your verification code";
        //第一步插入数据库
        $insert = $this->insertSmsTask(smsTaskType::VERIFICATION_CODE, $phone, $content);

        if (!$insert->STS) {
            return $insert;
        }

        $task = $insert->DATA;
        $task->task_state = smsTaskState::SENDING;
        $task->handler = C("sms_api");
        $task->handle_time = Now();
        $task->update();

        //发送短信
        if (C("sms_api") == 'yunpian') {
            $api = new sms_api_yunpian();
            $st = $api->sendSmsToYP($phone, $content);
            $task->handle_result = json_encode($st, true);

            // bug 没有网络等原因也认为发送成功了
            if (isset($st['code']) && $st['code'] == 0) {
                // 发送成功
                $task->task_state = smsTaskState::SEND_SUCCESS;
                $task->handle_fee = $st['result']['fee'];
                $task->hanler_id = $st['result']['sid'];
            } else {
                // 发送失败
                $task->task_state = smsTaskState::SEND_FAILED;
                $task->pending_check = 1;
                $task->update_time = Now();
                $task->update();
                return new result(false, 'Send sms fail');
            }

            $task->update_time = Now();
            $update = $task->update();
            return new result($update->STS, $update->MSG, $task);
        } else if (C("sms_api") == 'tencent') {
            $api = new sms_api_tencent();
            $arr_phone = tools::separatePhone($phone);
            $st = $api->sendVerifyCode(array(
                'mobile' => $arr_phone[1],
                'nationcode' => $arr_phone[0]
            ), $code);
            $task->handle_result = json_encode($st, true);

            // bug 没有网络等原因也认为发送成功了
            if (isset($st['result']) && $st['result'] == 0) {
                // 发送成功
                $task->task_state = smsTaskState::SEND_SUCCESS;
                $task->handle_fee = $st['fee'];
                $task->hanler_id = $st['sid'];
            } else {
                // 发送失败
                $task->task_state = smsTaskState::SEND_FAILED;
                $task->pending_check = 1;
                $task->update_time = Now();
                $task->update();
                return new result(false, 'Send sms fail');
            }

            $task->update_time = Now();
            $update = $task->update();
            return new result($update->STS, $update->MSG, $task);
        } else {
            // 没有发送短信返回失败
            $task->pending_check = 1;
            $task->update_time = Now();
            $task->update();
            return new result(false, "Did't send sms", $task);
        }

    }


    public function sendPinCode($phone, $pin, $amount, $telecom)
    {
        //检查数据
        if (!$phone || !$pin || !$amount || !$telecom) {
            return new result(false, "Empty Parameter");
        }
        $content = "【KHBUY】PIN:" . $pin . "\nAMOUNT:" . $amount . "\nTELECOM:" . $telecom;
        //第一步插入数据库
        $insert = $this->insertSmsTask(smsTaskType::PIN_CODE, $phone, $content);
        if (!$insert->STS) {
            return $insert;
        }
        $task = $insert->DATA;
        //发送短信
        if (C("sms_api") == 'yunpian') {
            $api = new sms_api_yunpian();
            $task->task_state = smsTaskState::SENDING;
            $task->handler = "yunpian";
            $task->handle_time = Now();
            $task->update();
            $st = $api->sendSmsToYP($phone, $content);
            $task->handle_result = json_encode($st, true);
            $task->update_time = Now();
            if (isset($st['code']) && $st['code'] == 0) {
                $task->task_state = smsTaskState::SEND_SUCCESS;
                $task->handle_fee = $st['result']['fee'];
                $task->hanler_id = $st['result']['sid'];
            } else {
                $task->task_state = smsTaskState::SEND_FAILED;
                $task->pending_check = 1;
                $task->update();
                return new result(false, 'Send pin code fail');
            }

        }
        $task->update_time = Now();
        $update = $task->update();
        return new result($update->STS, $update->MSG, $task);
    }

    public function sendWalletChange($phone, $text)
    {

        //检查数据
        if (!$phone || $text) {
            return new result(false, "Empty Parameter");
        }
        $content = "【KHBUY】wallet changed " . $text;
        //第一步插入数据库
        $insert = $this->insertSmsTask(smsTaskType::WALLET_CHANGED, $phone, $content);
        if (!$insert->STS) {
            return $insert;
        }
        $task = $insert->DATA;
        //发送短信
        if (C("sms_api") == 'yunpian') {
            $api = new sms_api_yunpian();
            $task->task_state = smsTaskState::SENDING;
            $task->handler = "yunpian";
            $task->handle_time = Now();
            $task->update();
            $st = $api->sendSmsToYP($phone, $content);
            $task->handle_result = json_encode($st, true);
            if (isset($st['code']) && $st['code'] == 0) {
                $task->task_state = smsTaskState::SEND_SUCCESS;
                $task->handle_fee = $st['result']['fee'];
                $task->handler_id = $st['result']['sid'];
            } else {
                $task->task_state = smsTaskState::SEND_FAILED;
                // $task->pending_check=1; 账户变动的短信重要度不高，不用再次介入
                $task->update_time = Now();
                $task->update();
                return new result(false, 'Send wallet change fail');
            }

        }
        $task->update_time = Now();
        $update = $task->update();
        return new result($update->STS, $update->MSG, $task);

    }

    public function sendLuckyNotice($phone, $verify_code, $order_sn)
    {

        if (!$phone || !$verify_code || !$order_sn) {
            return new result(false, "Empty Parameter");
        }

        // 构造短信模板
        $content = "【KHBUY】Your $1 bid win order verify code is $verify_code,order sn: " . $order_sn;

        //第一步插入数据库
        $insert = $this->insertSmsTask(smsTaskType::LUCKY_NOTICE, $phone, $content);
        if (!$insert->STS) {
            return $insert;
        }
        $task = $insert->DATA;
        //发送短信
        if (C("sms_api") == 'yunpian') {
            $api = new sms_api_yunpian();
            $task->task_state = smsTaskState::SENDING;
            $task->handler = "yunpian";
            $task->handle_time = Now();
            $task->update();
            $st = $api->sendSmsToYP($phone, $content);
            $task->handle_result = json_encode($st, true);
            if (isset($st['code']) && $st['code'] == 0) {
                $task->task_state = smsTaskState::SEND_SUCCESS;
                $task->handle_fee = $st['result']['fee'];
                $task->handler_id = $st['result']['sid'];
            } else {
                $task->task_state = smsTaskState::SEND_FAILED;
                $task->update_time = Now();
                $task->update();
                return new result(false, 'Send lucky notice fail');
            }

        }
        $task->update_time = Now();
        $update = $task->update();
        return new result($update->STS, $update->MSG, $task);
    }

    private function insertSmsTask($type, $phone, $content)
    {
        $m = M("common_sms");
        $row = $m->newRow();
        $arr_phone = tools::separatePhone($phone);
        $region_code = $arr_phone[0];
        $phone_code = $arr_phone[1];
        $isp_code = $this->get_isp_name($phone_code);
        $row->task_type = $type;
        $row->phone_country = $region_code;
        $row->phone_id = $phone;
        $row->isp_code = $isp_code;
        $row->content = $content;
        $row->create_time = Now();
        $row->task_state = smsTaskState::NONE;
        $insert = $row->insert();
        return new result($insert->STS, $insert->MSG, $row);
    }

    private function get_isp_name($phone_number)
    {
        if (strlen($phone_number) > 2) {
            $isp_code = str_cut($phone_number, 2);
            $isp_code = "0" . $isp_code;//因为懒得改下面的，就多此一举
            $smart = array('016', '10', '098', '070', '069', '096', '093', '081', '086');
            $cellcard = array('012', '017', '011', '085', '092', '077', '078', '099', '089');
            $metfone = array('097', '088', '071');
            if (in_array($isp_code, $metfone)) return 'metfone';
            if (in_array($isp_code, $cellcard)) return 'cellcard';
            if (in_array($isp_code, $smart)) return 'smart';
            return 'unknown';
        }
        return 'unknown';
    }

    /*
     * 发送充值成功
     * @param $phone 接收电话号码必须要跟上国际区号
     * @return result
     * */
    public function sendTopupSuccess($phone, $text)
    {
        //检查数据
        if (!$phone || !$text) {
            return new result(false, "Empty Data");
        }

        //构造短信模版
        $content = $text;

        //第一步插入数据库
        $insert = $this->insertSmsTask(smsTaskType::TOPUP_NOTICE, $phone, $content);
        if (!$insert->STS) {
            return $insert;
        }

        $task = $insert->DATA;
        //发送短信
        if (C("sms_api") == 'yunpian') {
            $api = new sms_api_yunpian();
            $task->task_state = smsTaskState::SENDING;
            $task->handler = "yunpian";
            $task->handle_time = Now();
            $task->update();
            $st = $api->sendSmsToYP($phone, $content);
            $task->handle_result = json_encode($st, true);
            if (isset($st['code']) && $st['code'] == 0) {
                $task->task_state = smsTaskState::SEND_SUCCESS;
                $task->handle_fee = $st['result']['fee'];
                $task->hanler_id = $st['result']['sid'];
            } else {
                $task->task_state = smsTaskState::SEND_FAILED;
                $task->pending_check = 1;
                $task->update_time = Now();
                $task->update();
                return new result(false, 'Send topup fail');
            }

        }
        $task->update_time = Now();
        $update = $task->update();
        $task_state = $task->task_state == smsTaskState::SEND_SUCCESS ? 1 : 0;
        return new result($update->STS, $update->MSG, $task_state);
    }

}