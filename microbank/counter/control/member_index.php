<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/6/9
 * Time: 22:30
 */
class member_indexControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('member');
        Tpl::setDir('member_index');
        Tpl::setLayout('home_layout');
        //$this->outputSubMenu('member');
        Tpl::output("sub_menu", $this->getMemberBusinessMenu());
    }

    public function startOp()
    {

        $task_pay=counter_memberClass::getPendingRepayToday();
        $task_sign=counter_memberClass::getPendingSignCreditAgreement();
        Tpl::output("pending_sign",$task_sign);
        Tpl::output("pending_pay",$task_pay);
        $task_disburse=counter_memberClass::getPendingDisburse();
        Tpl::output("pending_disburse",$task_disburse);

        Tpl::showPage("member.start");
    }

    public function indexOp()
    {
        $member_id = $_GET['member_id'];
        $search_by = $_GET['search_by'];
        if (!$member_id) {
            if ($search_by == "1") {
                $ret = memberClass::searchMember(array(
                    "type" => '2',
                    "country_code" => $_GET['country_code'],
                    "phone_number" => $_GET['phone_number']
                ));

            } elseif ($search_by == "2") {
                $ret = memberClass::searchMember(array(
                    "type" => '1',
                    "guid" => $_GET['phone_number']
                ));

            } elseif ($search_by == "3") {
                $ret = memberClass::searchMember(array(
                    "type" => '3',
                    "login_code" => $_GET['phone_number']
                ));
            } else {
                $ret = memberClass::searchMember(array(
                    "type" => '4',
                    "display_name" => $_GET['phone_number']
                ));
            }

        } else {
            $ret = memberClass::searchMember(array(
                "type" => '0',
                "member_id" => $_GET['member_id']
            ));
        }
        $member_id = $ret['uid'];
        if ($member_id > 0) {
            $obj = new objectMemberClass($member_id);
            $save_balance = $obj->getSavingsAccountBalance();
            $ret['save_balance'] = $save_balance;
        } else {
            showMessage('<div style="font-size: 20px;font-weight: bold;color: #808080">NOT FOUND THE CLIENT</div>');
        }

        $loan_account_info = memberClass::getLoanAccountInfoByMemberId($member_id);
        $ret['loan_account_info'] = $loan_account_info;


        //get id-card-handle pic
        $sql = "SELECT b.`image_url` FROM member_verify_cert a INNER JOIN member_verify_cert_image b "
            . " ON a.uid=b.cert_id WHERE b.`image_key`='" . certImageKeyEnum::ID_HANDHELD . "' AND a.`member_id`='" . $member_id . "' order by a.uid desc";  // 必须取最后一条的
        $r = new ormReader();
        $id_handled_img = $r->getOne($sql);
        if ($id_handled_img) {
            $ret['hold_id_card'] = getImageUrl($id_handled_img);
        }
        //最近场景图片
        $ret_scene=(new biz_scene_imageModel())->getMemberNewestSceneImage($member_id);
        $ret['last_scene_image']=$ret_scene['image'];
        $ret['last_scene_time']=$ret_scene['time'];



        // 是否在柜台录入指纹
        $finger_print = memberClass::isLoggingFingerprint($member_id);
        $ret['is_logging_fingerprint'] = $finger_print;

        //获取客户的合同列表
        $ret_loan = memberClass::getLoanContractList(array(
            "type" => 1,
            "member_id" => $member_id
        ));
        $ret_loan = $ret_loan->DATA;
        $loan_list = array();
        foreach ($ret_loan['list'] as $contract) {
            if ($contract['state'] > loanContractStateEnum::CREATE) {
                $loan_list[] = $contract;
            }
        }

        if ($ret_loan) {
            Tpl::output("loan_contract_list", $loan_list);
        }

        $ret['credit_category']=loan_categoryClass::getMemberCreditCategoryList($member_id);
        //获取member的operator
        $officer_list=memberClass::getMemberCreditOfficerList($member_id,false);
        $ret['officer_list']=$officer_list;


        // 客户逾期的贷款计划
        $loan_overdue_schema = member_loan_schemaClass::getMemberOverdueContractAndPenalty($member_id);
        Tpl::output('loan_overdue_schema',$loan_overdue_schema);


        Tpl::output("client_info", $ret);
        Tpl::output("member_id", $member_id);
        Tpl::showPage("member.index");
    }


    /**
     * 修改登录密码
     */
    public function changeLoginPwdOp()
    {
        $uid = $_GET["uid"];
        $r = new ormReader();
        $sql = "select cm.*,mg.grade_code from client_member cm LEFT JOIN member_grade mg ON cm.member_grade = mg.uid WHERE cm.uid = " . $uid;
        $client_info = $r->getRow($sql);

        Tpl::output('client_info', $client_info);
        Tpl::output('show_menu', 'profile');
        Tpl::showPage("change.login.pwd");

    }


    /**
     * 登录密码
     */
    public function verifyChangeLoginPwdOp()
    {
        $member_id = intval($_POST['client_id']);
        $old_pwd = trim($_POST['old_pwd']);
        $new_pwd = trim($_POST['new_pwd']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        if ($verify_code) {
            $m_verify_code = new phone_verify_codeModel();
            $row = $m_verify_code->getRow(array(
                'uid' => $verify_id,
                'verify_code' => $verify_code,
                'state' => 0
            ));
            if (!$row) {
                $conn->rollback();
                showMessage('SMS code error!');
            }

            $row->state = 1;
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                showMessage('SMS code error!' . $rt->MSG);
            }

        } else {
            if (empty($old_pwd)) {
                $conn->rollback();
                showMessage('To change the password, must enter the verification code or the original password.');
            }
            if (md5($old_pwd) != $row->login_password) {
                $conn->rollback();
                showMessage('Old password error!');
            }
        }

        $rt = memberClass::commonUpdateMemberPassword($member_id, $new_pwd);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Change password successfully!', getUrl('member', 'profile', array('client_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage('Change password failed!');
        }
    }


    /**
     * 修改交易密码*/
    public function changeTradePwdOp()
    {
        $member_id = $_GET['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);
        $fee_setting = global_settingClass::getChangeProfileFee();
        Tpl::output('fee', $fee_setting['change_trade_password']);
        Tpl::output('show_menu', 'index');
        Tpl::showPage("change.trade.pwd");
    }

    /**
     * 交易密码
     */
    public function verifyChangeTradePwdOp()
    {
        $member_id = intval($_POST['client_id']);
        $old_pwd = trim($_POST['old_pwd']);
        $new_pwd = trim($_POST['new_pwd']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        if ($verify_code) {
            $m_verify_code = new phone_verify_codeModel();
            $row = $m_verify_code->getRow(array(
                'uid' => $verify_id,
                'verify_code' => $verify_code,
                'state' => 0
            ));
            if (!$row) {
                $conn->rollback();
                showMessage('SMS code error!');
            }
            $row->state = 1;
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                showMessage('SMS code error!' . $rt->MSG);
            }

        } else {
            if (empty($old_pwd)) {
                $conn->rollback();
                showMessage('To change the password, must enter the verification code or the original password.');
            }
            if (md5($old_pwd) != $row->trading_password) {
                $conn->rollback();
                showMessage('Old password error!');
            }
        }

        $rt = memberClass::commonUpdateMemberTradePassword($member_id, $new_pwd);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Change Trade password successfully!', getUrl('member_index', 'index', array('member_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage('Change Trade password failed!');
        }
    }


    /**
     * 修改手机号码
     */
    public function changePhoneNumOp()
    {
        $member_id = $_GET['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);

        $fee_setting = global_settingClass::getChangeProfileFee();
        Tpl::output('fee', $fee_setting['change_phone_number']);

        Tpl::output('show_menu', 'index');
        Tpl::showPage("change.phone.num");

    }

    /**
     * 修改手机号
     * @return result
     * @throws Exception
     */
    public function verifyChangePhoneNumOp()
    {
        $country_code = trim($_POST['country_code']);
        $phone_number = trim($_POST['new_num']);
        $format_phone = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $format_phone['contact_phone'];

        // 检查合理性
        if (!isPhoneNumber($contact_phone)) {
            return new result(false, 'Invalid phone', null, errorCodesEnum::INVALID_PARAM);
        }

        // 判断是否被其他member注册过
        $m_member = new memberModel();
        $row = $m_member->getRow(array(
            'phone_id' => $contact_phone,
        ));
        if ($row) {
            return new result(false, 'The phone number has been registered.');
        }
        $member_id = intval($_POST['client_id']);
        $old_pwd = trim($_POST['old_pwd']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);

        if (!$row) {
            showMessage('Invalid Id!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();

        if ($verify_code) {
            $m_verify_code = new phone_verify_codeModel();
            $row = $m_verify_code->getRow(array(
                'uid' => $verify_id,
                'verify_code' => $verify_code,
                'state' => 0
            ));
            if (!$row) {
                $conn->rollback();
                showMessage('SMS code error!');
            }
            $row->state = 1;
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                showMessage('SMS code error!' . $rt->MSG);
            }

        } else {
            if (empty($old_pwd)) {
                $conn->rollback();
                showMessage('To change the phone number, must enter the verification code or the login password.');
            }
            if (md5($old_pwd) != $row->login_password) {
                $conn->rollback();
                showMessage('login password error!');
            }
        }

        $rt = memberClass::commonUpdateMemberPhoneNumber($member_id, $country_code, $phone_number);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Change Phone Number successfully!', getUrl('member_index', 'index', array('member_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage('Change Phone Number failed!');
        }
    }

    /**
     * member 解锁
     */

    public function unlockMemberOp()
    {
        $member_id = $_GET['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);

        Tpl::output('show_menu', 'index');
        Tpl::showPage("member.unlock");
    }

    /**
     * 发送验证码
     * @param $p
     * @return result
     */
    public function sendVerifyCodeByUidOp($p)
    {
        $client_id = intval($p['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        $phone_arr = tools::separatePhone($client_info['phone_id']);

        $param = array();
        $param['country_code'] = $phone_arr[0];
        $param['phone'] = $phone_arr[1];
        $rt = $this->sendVerifyCodeOp($param);
        if ($rt->STS) {
            return new result(true, 'Send Success', $rt->DATA);
        } else {
            return new result(false, 'Send Failure', array('code' => $rt->CODE, 'msg' => $rt->MSG));
        }
    }

    public function submitUnlockOp()
    {
        $cashier_id = $this->user_id;
        $member_id = intval($_POST['member_id']);
        $member_image = trim($_POST['member_image']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $m_client_member = M('client_member');

        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        $type = handleLockTypeEnum::UNLOCK;
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $bizMember = new bizMemberLockHandleClass();
        $rt = $bizMember->execute($cashier_id, $member_id, $member_image, $verify_id, $verify_code, $type);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Unlock Member State Success!', getUrl('member_index', 'index', array('member_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage($rt->MSG);
        }
    }

    public function sendVerifyCodeOp($p)
    {
        $data = $p;
        $url = ENTRY_API_SITE_URL . '/phone.code.send.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_success'), $rt['DATA']);
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    /**
     * member 修改交易密码
     */
    public function memberChangeTradePwdOp()
    {
        $cashier_id = $this->user_id;
        $member_id = intval($_POST['member_id']);
        $member_image = trim($_POST['member_image']);
//        $verify_id = intval($_POST['verify_id']);
//        $verify_code = trim($_POST['verify_code']);
        $fee_setting = global_settingClass::getChangeProfileFee();
        $fee = $fee_setting['change_trade_password'];
        $feeMethod = trim($_POST['feeMethod']);
        $currency = currencyEnum::USD;
        $new_trade_pwd = trim($_POST['new_trade_pwd']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $bizMember = new bizMemberChangeTradingPasswordByCounterClass();
        $rt = $bizMember->execute($cashier_id, $member_id, $member_image, $new_trade_pwd, $fee, $currency, $feeMethod);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Change Trade Password Success!', getUrl('member_index', 'index', array('member_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage('Change Trade Password Failed!--' . $rt->MSG);
        }
    }


    /**
     * member 修改电话号码
     */
    public function memberChangePhoneNumOp()
    {
        $cashier_id = $this->user_id;
        $member_id = intval($_POST['member_id']);
        $member_image = trim($_POST['member_image']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $fee_setting = global_settingClass::getChangeProfileFee();
        $fee = $fee_setting['change_phone_number'];
        $currency = currencyEnum::USD;
        $feeMethod = trim($_POST['feeMethod']);
        $country_code = trim($_POST['country_code']);
        $phone_number = trim($_POST['phone']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $member_phone = memberClass::checkPhoneNumberIsRegistered($country_code, $phone_number);
        if ($member_phone) {
            return new result(false, 'The Phone Has Been Used');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $bizMember = new bizMemberChangePhoneByCounterClass();
        $rt = $bizMember->execute($cashier_id, $member_id, $member_image, $verify_id, $verify_code, $country_code, $phone_number, $fee, $currency, $feeMethod);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Change Phone Number Success!', getUrl('member_index', 'index', array('member_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage($rt->MSG);
        }
    }


    /**
     * 查询member 余额流水单
     */
    public function showMemberFlowOp()
    {
        $member_id = intval($_GET['member_id']);
        Tpl::output('member_id', $member_id);
        $member_info = memberClass::getMemberBaseInfo($member_id);
        Tpl::output('member_info', $member_info);

        $currency = trim($_GET['currency']);
        Tpl::output('currency', $currency);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        Tpl::output('show_menu', 'index');
        Tpl::showPage('member.balance.flow');
    }

    public function getMemberFlowOp($p)
    {
        $param = array(
            'member_id' => intval($p['member_id']),
            'currency' => $p['currency'],
            'page_num' => $p['pageNumber'] ?: 1,
            'page_size' => $p['pageSize'] ?: 20,
            'start_date' => $p['date_start'],
            'end_date' => $p['date_end'],
        );

        $member_flow = member_savingsClass::getMemberBillTransaction($param);
        if ($member_flow->STS) {
            $data = $member_flow->DATA;
            return array(
                "sts" => true,
                "data" => $data['data']['list'],
                "total" => $data['total_num'],
                "pageNumber" => $data['current_page'],
                "pageTotal" => $data['total_pages'],
                "pageSize" => $data['page_size'],
            );
        } else {
            return array(
                "sts" => false,
                "msg" => $member_flow->MSG
            );
        }
    }

    /**
     * 获取需要approve的任务，for chief teller
     * @param $p
     */
    public function bizPendingCtApproveOp()
    {
        $ret = $this->getChiefTellerApproveTaskOp(array());
        $ret = $ret->DATA;
        $biz = $ret['biz'];
        foreach ($biz as $k => $v) {
            foreach ($ret['items'] as $item) {
                if ($item['biz_code'] == $v['biz_code']) {
                    $biz[$k]['items'][] = $item;
                }
            }
        }

        Tpl::output("biz_list", $biz);
        Tpl::output("task_new_count", 0);
        Tpl::output("task_total_count", $ret['task_total']);
        Tpl::showPage("ct.approve");
    }

    public function getChiefTellerApproveTaskOp($p)
    {
        $last_get_time = $p['last_get_time'] ?: $_GET['last_get_time'];
        if (!$last_get_time) $last_get_time = time();
        $cnt_new = 0;
        $cnt_total = 0;
        $m_biz_setting = new common_counter_biz_settingModel();
        $biz_setting = $m_biz_setting->getFormattedSetting();
        $r = new ormReader();
        $ret = array();
        $item_keys = array();
        $items = array();
        foreach ($biz_setting as $k => $biz) {
            if ($biz['is_require_ct_approve']) {
                $tbl = $biz['biz_table'];
                $sql = "select biz.*,member.member_icon,member.display_name,member.login_code member_code,member.phone_id,cashier.user_icon,cashier.user_code cashier_code,cashier.user_name cashier_name";
                $sql .= " from " . $tbl . " biz inner join client_member member on biz.member_id=member.uid inner join um_user cashier on biz.cashier_id=cashier.uid";
                $sql .= " where biz.branch_id='" . $this->branch_id . "' and biz.state='" . bizStateEnum::PENDING_APPROVE . "'";
                $list = $r->getRows($sql);
                $list = resetArrayKey($list, "uid");
                if ($list && $biz['biz_table_detail']) {
                    $ids = array_keys($list);
                    $str_ids = implode("','", $ids);
                    $sql = "select * from " . $biz['biz_table_detail'] . " where biz_id in ('" . $str_ids . "')";
                    $details = $r->getRows($sql);
                    foreach ($details as $detail_item) {
                        if (isset($detail_item['account_type'])) {
                            if ($detail_item['account_type']) continue;
                        }
                        $list[$detail_item['biz_id']]['cash'][] = $detail_item;
                    }
                } else {
                    foreach ($list as $item_key => $item) {
                        $list[$item_key]['cash'][] = array('currency' => $item['currency'], 'amount' => $item['amount'] ?: $item['apply_amount']);
                    }
                }
                if ($list) {
                    foreach ($list as $item_key => $item) {
                        $cnt_total += 1;
                        if ($item['update_time']) {
                            if (strtotime($item['update_time']) > $last_get_time) {
                                $cnt_new += 1;
                            }
                        } else {
                            if (strtotime($item['create_time']) > $last_get_time) {
                                $cnt_new += 1;
                            }
                        }


                        $new_item = array(
                            "key" => $k . $item_key,
                            "biz_code" => $k,
                            "data" => $item,
                            "tpl" => Tpl::getTplValue("ct.approve.item", "member_index", $item)
                        );
                        $item_keys[] = $new_item['key'];
                        $items[] = $new_item;
                    }
                }
                $ret[$k] = array(
                    "biz_caption" => $biz['biz_caption'],
                    "biz_code" => $k
                );
            }
        }
        return new result(true, "", array(
            "biz" => $ret,
            "task_total" => $cnt_total,
            "task_new" => $cnt_new,
            "item_keys" => $item_keys,
            "items" => $items,
            "last_get_time" => time()
        ));
    }

    public function bizApproveDetailOp()
    {
        $biz_code = $_GET['biz_code'];
        $biz_id = $_GET['biz_id'];
        $m_biz_setting = new common_counter_biz_settingModel();
        $biz_setting = $m_biz_setting->getFormattedSetting();
        $biz = $biz_setting[$biz_code];
        $r = new ormReader();

        $tbl = $biz['biz_table'];
        $sql = "select biz.*,member.member_icon,member.display_name,member.login_code member_code,member.phone_id,cashier.user_icon,cashier.user_code cashier_code,cashier.user_name cashier_name";
        $sql .= " from " . $tbl . " biz inner join client_member member on biz.member_id=member.uid inner join um_user cashier on biz.cashier_id=cashier.uid";
        $sql .= " where biz.uid=" . qstr($biz_id) . " and biz.biz_code=" . qstr($biz_code);
        $row = $r->getRow($sql);
        if (!$row) {
            showMessage("No Object Found");
        }
        if ($row['state'] != bizStateEnum::PENDING_APPROVE) {
            showMessage("Invalid State Of Business");
        }
        $row['biz_caption'] = $biz['biz_caption'];

        if ($biz['biz_table_detail']) {
            $sql = "select * from " . $biz['biz_table_detail'] . " where biz_id=" . qstr($biz_id);
            $details = $r->getRows($sql);
            foreach ($details as $detail_item) {
                if (isset($detail_item['account_type'])) {
                    if ($detail_item['account_type']) continue;
                }
                $row['cash'][] = $detail_item;
            }
        } else {
            $row['cash'][] = array('currency' => $row['currency'], 'amount' => $row['amount'] ?: $row['apply_amount']);
        }
        $row['without_function'] = 1;
        $row['tpl'] = Tpl::getTplValue("ct.approve.item", "member_index", $row);
        Tpl::output("biz_item", $row);

        //todo 根据不同biz,输出不同的参考信息


        Tpl::showPage("ct.approve.detail");
    }

    public function bizCtApproveSubmitOp()
    {
        var_dump($_POST);

    }

    /**
     * member指纹
    */
    public function registerFingerprintOp(){
        $member_id = $_GET['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        $m_common_fingerprint_library = M('common_fingerprint_library');
        $fingerprint_info = $m_common_fingerprint_library->orderBy('uid DESC')->find(array('obj_uid' => $client_info['obj_guid'], 'obj_type' => objGuidTypeEnum::CLIENT_MEMBER));
        if ($fingerprint_info) {
            $client_info['feature_img'] = $fingerprint_info['feature_img'];
            $client_info['certification_status'] = 'Registered';
            $client_info['certification_time'] = timeFormat($fingerprint_info['create_time']);
        } else {
            //$client_info['feature_img'] = 'resource/img/member/photo.png';
            $client_info['feature_img'] = '';
            $client_info['certification_status'] = 'Unregistered';
            $client_info['certification_time'] = '';
        }
        Tpl::output("member_id", $member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output('show_menu', 'index');
        Tpl::showPage('fingerprint.collection');
    }

    /**
     * 保存指纹
     * @param $p
     * @return result
     */
    public function saveFeatureAuthenticationOp($p)
    {
        $uid = intval($p['client_id']);
        $feature_img = $p['feature_img'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        $m_common_fingerprint_library = M('common_fingerprint_library');


        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $fingerprint = $m_common_fingerprint_library->getRow(array('obj_uid' => $client_info['obj_guid'], 'obj_type' => objGuidTypeEnum::CLIENT_MEMBER));
            if ($fingerprint) {
                $rt_1 = $fingerprint->delete();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return new result(false, 'Add Failed!1');
                }
            }

            $fingerprint_row = $m_common_fingerprint_library->newRow();
            $fingerprint_row->obj_type = objGuidTypeEnum::CLIENT_MEMBER;
            $fingerprint_row->obj_uid = $client_info['obj_guid'];
            $fingerprint_row->finger_index = 1;
            $fingerprint_row->feature_img = $feature_img;
            $fingerprint_row->feature_img = $feature_img;
            $fingerprint_row->create_time = Now();
            $rt_2 = $fingerprint_row->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Add Failed!2');
            }

            $client_info->fingerprint = $feature_img;
            $client_info->update_time = Now();
            $rt_3 = $client_info->update();
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, 'Add Failed!3');
            }

            $conn->submitTransaction();
            return new result(false, 'Add Successful!');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

    }

    /**
     * 提交客户的场景照片（单独）
     */
    function  ajaxSubmitMemberScenePhotoOp($p){
        $member_id=$p['member_id'];
        if(!$member_id) return new result(false,"required to input <kbd>Member-ID</kbd>");
        $image_path=$p['image_path'];
        if(!$image_path) return new result(false,"required to input <kbd>Scene-Image</kbd>");
        $m_biz=new biz_scene_imageModel();
        $row=$m_biz->newRow();
        $row->scene_code=bizSceneEnum::COUNTER;
        $row->scene_id=$this->branch_id;
        $row->operator_id=$this->user_id;
        $row->operator_name=$this->user_name;
        $row->member_id=$member_id;
        $row->create_time=Now();
        $row->member_image=$image_path;
        $ret=$row->insert();
        if(!$ret->STS){
            return $ret;
        }else{
            return new result(true,"",array("big_image"=>getImageUrl($image_path),
                "small_image"=>getImageUrl($image_path,imageThumbVersion::SMALL_IMG),
                "last_time"=>Now()));
        }
    }
    function memberScenePhotoHistoryPageOp(){
        $member_id=$_GET['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        $m_biz=new biz_scene_imageModel();
        $items=$m_biz->limit(0,100)->orderBy("uid desc")->select(array("member_id"=>$member_id));
        Tpl::output("history",$items);

        Tpl::output("member_id", $member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output('show_menu', 'index');
        Tpl::showPage('scene.photo.history');
    }





}