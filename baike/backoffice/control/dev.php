<?php

class devControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("dev");
    }

    protected function checkSecurity()
    {
        return true;
    }

    /**
     * app版本
     */
    public function appVersionOp()
    {
        Tpl::showPage("app_version");
    }

    /**
     * @param $p
     * @return array
     */
    public function getAppVersionOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT * FROM common_app_version";
        if ($search_text) {
            $sql .= " WHERE app_name LIKE '%" . $search_text . "%' OR version LIKE '%" . $search_text . "%'";
        }
        $sql .= " ORDER BY uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 添加新版本
     */
    public function addVersionOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $m_common_app_version = M('common_app_version');
            $rt = $m_common_app_version->addVersion($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('dev', 'appVersion', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('dev', 'addVersion', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            Tpl::showPage("app_version.add");
        }
    }

    /**
     * 功能开关
     */
    public function functionSwitchOp()
    {
        $m_core_dictionary = new core_dictionaryModel();
        if ($_POST['form_submit'] == 'ok') {
            $param = $_POST;
            unset($param['form_submit']);
            /*
            $param['close_reset_password'] = intval($param['close_reset_password']);
            $param['close_credit_withdraw'] = intval($param['close_credit_withdraw']);
            $param['close_register_send_credit'] = intval($param['close_register_send_credit']);
            $param['open_passbook_trading_by_bank'] = intval($param['open_passbook_trading_by_bank']);
            $param['is_fix_loan_repayment_day'] = intval($param['is_fix_loan_repayment_day']);
            $param['is_loan_use_credit_grant_interest'] = intval($param['is_loan_use_credit_grant_interest']);
            */
            foreach ($param as $k => $v) {
                $param[$k] = intval($v);
            }

            $rt = $m_core_dictionary->updateDictionary('function_switch', my_json_encode($param));
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('dev', 'functionSwitch', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $data = $m_core_dictionary->getDictionary('function_switch');
            if ($data) {
                tpl::output('function_switch', my_json_decode($data['dict_value']));
            }
            Tpl::showPage("function.switch");
        }
    }

    /**
     * 业务开关
     */
    public function businessSwitchOp()
    {
        $m_core_dictionary = new core_dictionaryModel();
        if ($_POST['form_submit'] == 'ok') {
            $param = $_POST;
            unset($param['form_submit']);
            foreach ($param as $k => $v) {
                $param[$k] = intval($v);
            }

            $rt = $m_core_dictionary->updateDictionary('business_switch', my_json_encode($param));
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('dev', 'businessSwitch', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {

            $data = $m_core_dictionary->getDictionary('business_switch');
            if ($data) {
                tpl::output('business_switch', my_json_decode($data['dict_value']));
            }
            Tpl::showPage("business.switch");
        }
    }


    /**
     * 重置密码
     */
    public function resetPasswordOp()
    {
        $m_core_dictionary = M('core_dictionary');
        $data = $m_core_dictionary->getDictionary('function_switch');
        $data = my_json_decode($data['dict_value']);
        if ($data['close_reset_password'] == 1) {
            showMessage('Reset password closed!');
        }
        Tpl::showpage('reset.password');
    }

    /**
     * 获取重置密码列表
     * @param $p
     * @return array
     */
    public function getResetPasswordListOp($p)
    {
        $search_text = trim($p['search_text']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $member_model = new memberModel();
        $ret = $member_model->searchMemberListByFreeText($search_text, $pageNumber, $pageSize);

        return array(
            "sts" => true,
            "data" => $ret->DATA['rows'],
            "total" => $ret->DATA['total'],
            "pageNumber" => $pageNumber,
            "pageTotal" => $ret->DATA['page_total'],
            "pageSize" => $pageSize,
        );
    }

    public function apiResetPasswordOp($p)
    {
        $m_core_dictionary = M('core_dictionary');
        $data = $m_core_dictionary->getDictionary('function_switch');
        $data = my_json_decode($data['dict_value']);
        if ($data['close_reset_password'] == 1) {
            return new result(false, 'Reset password closed!');
        }
        $uid = intval($p['uid']);
        $new_password = trim($p['new_password']);
        $verify_password = trim($p['verify_password']);
        if ($new_password != $verify_password) {
            return new result(false, 'Verify password error');
        }

        $rt = memberClass::commonUpdateMemberPassword($uid, $new_password);
        if ($rt->STS) {
            return new result(true, 'Reset Successful!');
        } else {
            return new result(false, $rt->MSG);
        }
    }

    public function issueIcCardOp()
    {
        Tpl::showpage('ic_card');
    }

    public function addIcCardOp()
    {
        if (checkSubmit()) {
            $param = array_merge(array(), $_GET, $_POST);

            $card_model = new common_ic_cardModel();
            $card_info = $card_model->newRow();
            $card_info->card_no = $param['card_no'];
            $card_info->card_key = $param['card_key'];
            $card_info->expire_time = $param['expire_time'] ? strtotime($param['expire_time']) : 2147483647;
            $card_info->create_time = date("Y-m-d H:i:s");
            $card_info->create_user_id = $this->user_id;
            $card_info->create_user_name = $this->user_name;
            $ret = $card_info->insert();
            if ($ret->STS) {
                showMessage('Success', getUrl('dev', 'issueIcCard', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage('Add failed - ' . $ret->MSG);
            }
        } else {
            Tpl::showpage('ic_card.add');
        }
    }

    public function deleteIcCardOp($p)
    {
        $card_model = new common_ic_cardModel();
        $card_info = $card_model->getRow($p['uid']);
        if (!$card_info) {
            return new result(false, 'Card not found', null, errorCodesEnum::UNEXPECTED_DATA);
        }

        $ret = $card_info->delete();
        if ($ret->STS) {
            return new result(true);
        } else {
            return new result(false, 'Delete failed - ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        }
    }

    public function getIcCardListOp($p)
    {
        $search_text = trim($p['search_text']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $card_model = new common_ic_cardModel();
        $ret = $card_model->searchCardListByFreeText($search_text, $pageNumber, $pageSize);

        return array(
            "sts" => true,
            "data" => $ret->DATA['rows'],
            "total" => $ret->DATA['total'],
            "pageNumber" => $pageNumber,
            "pageTotal" => $ret->DATA['page_total'],
            "pageSize" => $pageSize,
        );
    }

    public function passbookAccountAdjustOp()
    {
        $type = $_GET['type'];
        if (!$type) $type = 'member';

        Tpl::showPage('passbook_account.adjust.' . $type);
    }

    public function getMemberListWithPassbookAccountsOp($p)
    {
        $search_text = trim($p['search_text']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $member_model = new memberModel();
        $ret = $member_model->searchMemberListByFreeText($search_text, $pageNumber, $pageSize);
        $account_model = new passbook_accountModel();

        return array(
            "sts" => true,
            "data" => $account_model->fillSavingsPassbookAccountInfoForMembers($ret->DATA['rows']),
            "total" => $ret->DATA['total'],
            "pageNumber" => $pageNumber,
            "pageTotal" => $ret->DATA['page_total'],
            "pageSize" => $pageSize,
        );
    }

    public function adjustMemberAccountOp($p)
    {
        return passbookWorkerClass::adjustMember($p['uid'], $p['amount'], $p['currency'], $p['remark']);
    }
    public function adjustGlAccountOp($p)
    {
        return passbookWorkerClass::adjustSystem($p['uid'], $p['amount'], $p['currency'], $p['remark']);
    }
    public function adjustAccountOp($p)
    {
        return passbookWorkerClass::adjustAnyPassbook($p['guid'], $p['amount'], $p['currency'], $p['remark']);
    }

    public function getBranchListWithPassbookAccountsOp($p)
    {
        $search_text = trim($p['search_text']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $branch_model = new site_branchModel();
        $ret = $branch_model->searchBranchListByFreeText($search_text, $pageNumber, $pageSize);

        $account_model = new passbook_accountModel();
        return array(
            "sts" => true,
            "data" => $account_model->fillPassbookAccountInfoForBranches($ret->DATA['rows']),
            "total" => $ret->DATA['total'],
            "pageNumber" => $pageNumber,
            "pageTotal" => $ret->DATA['page_total'],
            "pageSize" => $pageSize,
        );
    }

    public function adjustBranchAccountOp($p)
    {
        return passbookWorkerClass::adjustBranch($p['uid'], $p['amount'], $p['currency'], $p['remark']);
    }

    public function getBankListWithPassbookAccountsOp($p)
    {
        $search_text = trim($p['search_text']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $bank_model = new site_bankModel();
        $ret = $bank_model->searchBankListByFreeText($search_text, $pageNumber, $pageSize);

        $account_model = new passbook_accountModel();
        return array(
            "sts" => true,
            "data" => $account_model->fillPassbookAccountInfoForBanks($ret->DATA['rows']),
            "total" => $ret->DATA['total'],
            "pageNumber" => $pageNumber,
            "pageTotal" => $ret->DATA['page_total'],
            "pageSize" => $pageSize,
        );
    }

    public function adjustBankAccountOp($p)
    {
        return passbookWorkerClass::adjustBank($p['uid'], $p['amount'], $p['currency'], $p['remark']);
    }

    public function creditProcessOp()
    {
        $all_dict = global_settingClass::getAllDictionary();
        Tpl::output('all_dict', $all_dict);
        Tpl::showPage('credit.process.list');
    }

    public function openCreditProcessOp($p)
    {
        $type = $p['type'];
        $m = new core_dictionaryModel();
        switch ($type) {
            case 1:
                $row = $m->getRow(array(
                    'dict_key' => 'close_credit_fingerprint_cert',
                ));
                if ($row) {
                    if ($row->dict_value == 0) {
                        return new result(false, 'Is opened!');
                    }
                    $row->dict_value = 0;
                    $up = $row->update();
                    if (!$up->STS) {
                        return new result(false, 'Open fail!');
                    }
                    return new result(true, 'Success!');
                } else {
                    $row = $m->newRow();
                    $row->dict_key = 'close_credit_fingerprint_cert';
                    $row->dict_value = 0;
                    $insert = $row->insert();
                    if (!$insert->STS) {
                        return new result(false, 'Open fail!');
                    }
                    return new result(true, 'Success!');

                }
                break;
            case 2:
                $row = $m->getRow(array(
                    'dict_key' => 'close_credit_authorized_contract',
                ));
                if ($row) {
                    if ($row->dict_value == 0) {
                        return new result(false, 'Is opened!');
                    }
                    $row->dict_value = 0;
                    $up = $row->update();
                    if (!$up->STS) {
                        return new result(false, 'Open fail!');
                    }
                    return new result(true, 'Success!');

                } else {
                    $row = $m->newRow();
                    $row->dict_key = 'close_credit_authorized_contract';
                    $row->dict_value = 0;
                    $insert = $row->insert();
                    if (!$insert->STS) {
                        return new result(false, 'Open fail!');
                    }
                    return new result(true, 'Success!');

                }
                break;
            default:
                return new result(false, 'Unknown function');
                break;
        }
    }

    public function closeCreditProcessOp($p)
    {
        $type = $p['type'];
        $m = new core_dictionaryModel();
        switch ($type) {
            case 1:
                $row = $m->getRow(array(
                    'dict_key' => 'close_credit_fingerprint_cert',
                ));
                if ($row) {
                    if ($row->dict_value == 1) {
                        return new result(false, 'Is Closed!');
                    }
                    $row->dict_value = 1;
                    $up = $row->update();
                    if (!$up->STS) {
                        return new result(false, 'Close fail!');
                    }
                    return new result(true, 'Success!');
                } else {
                    $row = $m->newRow();
                    $row->dict_key = 'close_credit_fingerprint_cert';
                    $row->dict_value = 1;
                    $insert = $row->insert();
                    if (!$insert->STS) {
                        return new result(false, 'Close fail!');
                    }
                    return new result(true, 'Success!');

                }
                break;
            case 2:
                $row = $m->getRow(array(
                    'dict_key' => 'close_credit_authorized_contract',
                ));
                if ($row) {
                    if ($row->dict_value == 1) {
                        return new result(false, 'Is Closed!');
                    }
                    $row->dict_value = 1;
                    $up = $row->update();
                    if (!$up->STS) {
                        return new result(false, 'Close fail!');
                    }
                    return new result(true, 'Success!');

                } else {
                    $row = $m->newRow();
                    $row->dict_key = 'close_credit_authorized_contract';
                    $row->dict_value = 1;
                    $insert = $row->insert();
                    if (!$insert->STS) {
                        return new result(false, 'Close fail!');
                    }
                    return new result(true, 'Success!');
                }
                break;
            default:
                return new result(false, 'Unknown function');
                break;
        }
    }

    /**
     *  授信比例设置
     */
    public function creditGrantRateOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $m_dict = new core_dictionaryModel();

        if ($params['form_submit'] == 'ok') {
            $default_credit_rate = intval($params['default_credit_rate']);
            $land_credit_rate = intval($params['land_credit_rate']);
            $house_credit_rate = intval($params['house_credit_rate']);
            $motorbike_credit_rate = intval($params['motorbike_credit_rate']);
            $car_credit_rate = intval($params['car_credit_rate']);
            $store_credit_rate = intval($params['store_credit_rate']);
            $default_terms = intval($params['default_terms']);
            $default_max_terms = intval($params['default_max_terms']);
            $default_salary_rate = intval($params['default_salary_rate']);
            $default_rental_rate = intval($params['default_rental_rate']);
            $default_attachment_rate = intval($params['default_attachment_rate']);

            $data = array(
                'default_credit_rate' => $default_credit_rate,
                'land_credit_rate' => $land_credit_rate,
                'house_credit_rate' => $house_credit_rate,
                'motorbike_credit_rate' => $motorbike_credit_rate,
                'car_credit_rate' => $car_credit_rate,
                'store_credit_rate' => $store_credit_rate,
                'default_terms' => $default_terms,
                'default_max_terms' => $default_max_terms,
                'default_salary_rate' => $default_salary_rate,
                'default_rental_rate' => $default_rental_rate,
                'default_attachment_rate' => $default_attachment_rate,
                "allow_operator_submit_to_hq" => intval($params['allow_operator_submit_to_hq']),
                'update_time' => Now()
            );

            $dict_value = json_encode($data);
            $row = $m_dict->getRow(array(
                'dict_key' => dictionaryKeyEnum::CREDIT_GRANT_RATE,
            ));
            if ($row) {
                $row->dict_value = $dict_value;
                $up = $row->update();
                if (!$up->STS) {
                    showMessage('Edit fail.' . $up->MSG);
                }
            } else {
                $new_row = $m_dict->newRow();
                $new_row->dict_key = dictionaryKeyEnum::CREDIT_GRANT_RATE;
                $new_row->dict_value = $dict_value;
                $insert = $new_row->insert();
                if (!$insert->STS) {
                    showMessage('Edit fail.' . $insert->MSG);
                }
            }
            showMessage('Edit success!');

        } else {
            $setting = $m_dict->find(array(
                'dict_key' => dictionaryKeyEnum::CREDIT_GRANT_RATE,
            ));
            if ($setting) {
                $rate = @my_json_decode($setting['dict_value']);
            } else {
                $rate = array();
            }
            Tpl::output('credit_rate', $rate);
            Tpl::showPage('credit.grant.rate');
        }
    }

    /**
     * 授权合同收费比率
     */
    public function authorizedContractFeeRateOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $m_dict = new core_dictionaryModel();

        if ($params['form_submit'] == 'ok') {
            $first_sign_contract_rate_value = round($params['first_sign_contract_rate_value'], 3);
            $first_sign_contract_rate_type = intval($params['first_sign_contract_rate_type']);
            $min_first_sign_contract_fee = intval($params['min_first_sign_contract_fee']);
            $follow_sign_contract_rate_value = round($params['follow_sign_contract_rate_value'], 3);
            $follow_sign_contract_rate_type = intval($params['follow_sign_contract_rate_type']);
            $min_follow_sign_contract_fee = intval($params['min_follow_sign_contract_fee']);
            $data = array(
                'first_sign_contract_rate_value' => $first_sign_contract_rate_value,
                'first_sign_contract_rate_type' => $first_sign_contract_rate_type,
                'min_first_sign_contract_fee' => $min_first_sign_contract_fee,
                'follow_sign_contract_rate_value' => $follow_sign_contract_rate_value,
                'follow_sign_contract_rate_type' => $follow_sign_contract_rate_type,
                'min_follow_sign_contract_fee' => $min_follow_sign_contract_fee,
                'update_time' => Now()

            );

            $dict_value = json_encode($data);
            $row = $m_dict->getRow(array(
                'dict_key' => dictionaryKeyEnum::AUTHORIZED_CONTRACT_FEE,
            ));
            if ($row) {
                $row->dict_value = $dict_value;
                $up = $row->update();
                if (!$up->STS) {
                    showMessage('Edit fail.' . $up->MSG);
                }
            } else {
                $new_row = $m_dict->newRow();
                $new_row->dict_key = dictionaryKeyEnum::AUTHORIZED_CONTRACT_FEE;
                $new_row->dict_value = $dict_value;
                $insert = $new_row->insert();
                if (!$insert->STS) {
                    showMessage('Edit fail.' . $insert->MSG);
                }
            }
            showMessage('Edit success!');

        } else {
            $setting = $m_dict->find(array(
                'dict_key' => dictionaryKeyEnum::AUTHORIZED_CONTRACT_FEE,
            ));
            if ($setting) {
                $rate = @my_json_decode($setting['dict_value']);
            } else {
                $rate = array();
            }
            Tpl::output('fee_rate', $rate);
            Tpl::showPage('authorized.contract.rate');
        }
    }


    /**
     * 配置设置
     */
    public function globalOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_core_dictionary = M('core_dictionary');
        if ($p['form_submit'] == 'ok') {
            $param = $_POST;
            unset($param['form_submit']);
            $param['credit_register'] = round($param['credit_register'], 2);
            $param['credit_without_approval'] = round($param['credit_without_approval'], 2);
            $param['credit_system_limit'] = round($param['credit_system_limit'], 2);
            $param['withdrawal_single_limit'] = round($param['withdrawal_single_limit'], 2);
            $param['withdrawal_monitor_limit'] = round($param['withdrawal_monitor_limit'], 2);
            $param['operator_credit_maximum'] = round($param['operator_credit_maximum'], 2);
            $param['teller_reduce_penalty_maximum'] = round($param['teller_reduce_penalty_maximum'], 2);
            $param['member_change_phone_number_fee'] = round($param['member_change_phone_number_fee'], 2);
            $param['member_change_trading_password_fee'] = round($param['member_change_trading_password_fee'], 2);
            $param['date_format'] = intval($param['date_format']);
            $param['is_trade_password'] = intval($param['is_trade_password']);
            $param['is_create_savings_account'] = intval($param['is_create_savings_account']);
            $param['counter_deny_without_client'] = intval($param['counter_deny_without_client']);
            $param['backoffice_deny_without_client'] = intval($param['backoffice_deny_without_client']);
            $rt = $m_core_dictionary->updateDictionary(dictionaryKeyEnum::GLOBAL_SETTINGS, my_json_encode($param));
            showMessage($rt->MSG);
        } else {
            $data = $m_core_dictionary->getDictionary(dictionaryKeyEnum::GLOBAL_SETTINGS);
            if ($data) {
                tpl::output('global_settings', my_json_decode($data['dict_value']));
            }
            Tpl::showPage("global.setting");
        }
    }

    /**
     * sms
     */
    public function smsOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('sms');
    }

    /**
     * @param $p
     * @return array
     */
    public function getSmsListOp($p)
    {
        $search_text = trim($p['search_text']);
        $need_resend = intval($p['need_resend']);
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT * FROM common_sms WHERE (create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "') AND task_state != " . smsTaskState::CANCEL;
        if ($search_text) {
            $sql .= " AND phone_id like '%" . $search_text . "%'";
        }
        if ($need_resend) {
            $sql .= " AND task_state = " . smsTaskState::SEND_FAILED;
        }
        $sql .= ' ORDER BY uid DESC';
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }

    public function resendSmsOp($p)
    {
        $uid = intval($p['uid']);
        $m_common_sms = M('common_sms');
        $sms_row = $m_common_sms->getRow($uid);
        if (!$sms_row) {
            $data = array('state' => 'Resend Failed');
            return new result(false, 'Invalid Id!', $data);
        }
        if ($sms_row->task_state != smsTaskState::SEND_FAILED) {
            $data = array('state' => 'Resend Failed');
            return new result(false, 'Sms state error!', $data);
        }

        $smsHandler = new smsHandler();
        if ($sms_row->task_type == smsTaskType::VERIFICATION_CODE) {
            // 发送短信验证码
            $verify_code = mt_rand(100001, 999999);
            $contact_phone = $sms_row->phone_id;

            $rt = $smsHandler->sendVerifyCode($contact_phone, $verify_code);
            if (!$rt->STS) {
                $data = array('state' => 'Resend Failed');
                return new result(false, 'Send code fail: ' . $rt->MSG, $data);
            }
            $data = $rt->DATA;
            $content = $data->content;
            $conn = ormYo::Conn();
            $conn->startTransaction();
            try {
                $m_phone_verify_code = M('common_verify_code');
                $new_row = $m_phone_verify_code->newRow();
                $new_row->phone_country = $sms_row->phone_country;
                $new_row->phone_id = $contact_phone;
                $new_row->verify_code = $verify_code;
                $new_row->create_time = Now();
                $new_row->sms_id = $sms_row->uid;
                $insert = $new_row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Insert verify code fail');
                }

                $sms_row->task_state = smsTaskState::CANCEL;
                $sms_row->update_time = Now();
                $update = $sms_row->update();
                if (!$update->STS) {
                    $conn->rollback();
                    $data = array('state' => 'Resend Failed');
                    return new result(false, 'Update sms fail', $data);
                }
                $conn->submitTransaction();
                $data = array('content' => $content, 'state' => L('task_state_' . smsTaskState::SEND_SUCCESS));
                return new result(true, 'Resend successful!', $data);
            } catch (Exception $ex) {
                $conn->rollback();
                return new result(false, $ex->getMessage());
            }

        } else {
            $rt = $smsHandler->resend($uid);
            if ($rt->STS) {
                $data = $rt->DATA;
                $data = array('content' => $data->content, 'state' => L('task_state_' . smsTaskState::SEND_SUCCESS));
                return new result(true, 'Resend successful!', $data);
            } else {
                $data = array('state' => 'Resend Failed');
                return new result(true, 'Resend failed!', $data);
            }
        }
    }

    public function smsSendTestOp()
    {
        Tpl::showpage('sms.send.test');
    }

    public function ajaxSmsSendTestOp($p)
    {
        $country_code = $p['country_code'];
        $phone_number = $p['phone_number'];
        if( !$country_code || !$phone_number ){
            return new result(false,'Invalid phone number.',null,errorCodesEnum::INVALID_PARAM);
        }
        $rt = smsClass::sendVerifyCode($country_code,$phone_number);
        return $rt;
    }

    /**
     * 重置系统
     */
    public function resetSystemOp()
    {
        showMessage("Not Allowed");
    }

    public function resetSystemConfirmOp()
    {
        showMessage("Not Allowed");
    }

    public function memberSettingOp()
    {
        Tpl::showPage('member.setting');
    }

    public function closeSystemOp()
    {
        $m_core_dictionary = new core_dictionaryModel();
        $items = $m_core_dictionary->getCloseSystemDictionary();
        $items_new = array();
        foreach ($items as $v) {
            $items_new[$v['dict_key']] = $v;
        }
        Tpl::output('items', $items_new);
        Tpl::showPage('close.system');
    }

    public function submitcloseSystemOp($p)
    {
        $dict_key = $p['dict_key'];
        $dict_value['state'] = 0;
        $dict_value['remark'] = $p['close_reason'];
        $dict_value['update_time'] = Now();
        $m_core_dictionary = new core_dictionaryModel();
        $rt = $m_core_dictionary->updateDictionary($dict_key, my_json_encode($dict_value));
        if ($rt->STS) {
            return new result(true, $rt->MSG);
        } else {
            return new result(false, $rt->MSG);
        }
    }

    public function submitOpenSystemOp($p)
    {
        $dict_key = $p['dict_key'];
        $dict_value['state'] = 1;
        $dict_value['update_time'] = Now();
        $m_core_dictionary = new core_dictionaryModel();
        $rt = $m_core_dictionary->updateDictionary($dict_key, my_json_encode($dict_value));
        if ($rt->STS) {
            return new result(true, $rt->MSG);
        } else {
            return new result(false, $rt->MSG);
        }
    }

    public function getClientInfoOp($p)
    {
        $country_code = trim($p['country_code']);
        $phone = trim($p['phone']);

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];
        $m_member = M('member');
        $client_info = $m_member->find(array('phone_id' => $contact_phone, 'is_verify_phone' => 1));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        $m_member_grade = M('member_grade');
        $member_grade = $m_member_grade->find(array('uid' => $client_info['member_grade']));
        $client_info['grade_code'] = $member_grade['grade_code'];
        $client_info['member_icon'] = getImageUrl($client_info['member_icon'], imageThumbVersion::AVATAR);

        return new result(true, '', $client_info);
    }

    public function getClientCreditInfoOp($p)
    {
        $member_id = intval($p['id']);
        $m_member = M('member');
        $m_core_dictionary = new core_dictionaryModel();
        $info = $m_member->getMemberSettingInfo($member_id);
        $reset_pwd = $m_core_dictionary->getDictionary('function_switch');
        return array(
            'sts' => true,
            'info' => $info,
            'reset_pwd' => json_decode($reset_pwd['dict_value'], true)
        );
    }

    public function submitAdjustStateAccountOp($p)
    {
        return memberClass::adjustMemberState($p);
    }

    public function deleteMemberOp($p)
    {
        return memberClass::deleteMember(intval($p['uid']));
    }

    public function submitResetPasswordOp($p)
    {
        return memberClass::resetMemberPassword($p);
    }


    public function memberBizLimitOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_common_limit_member = new common_limit_memberModel();
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $m_common_limit_member->setMemberLimit($p);
            if ($rt->STS) {
                showMessage($rt->MSG);
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG);
            }
        } else {
            $m_member_grade = M('member_grade');
            $member_grade = $m_member_grade->select(array('uid' => array('>=', 0)));
            Tpl::output('member_grade', $member_grade);
            TpL::showPage('member.biz.limit');
        }

    }

    public function getMemberBizLimitByGradeOp($p)
    {
        $grade_id = intval($p['member_grade']);
        $m_common_limit_member = new common_limit_memberModel();
        $limit_list = $m_common_limit_member->selectLimit($grade_id);
        $biz_code_limit = array(
            bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER,
            bizCodeEnum::MEMBER_WITHDRAW_TO_BANK,
            bizCodeEnum::MEMBER_WITHDRAW_TO_CASH,
            bizCodeEnum::MEMBER_TRANSFER_TO_MEMBER,
            bizCodeEnum::MEMBER_TRANSFER_TO_BANK,
            bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER,
            bizCodeEnum::MEMBER_DEPOSIT_BY_BANK,
            bizCodeEnum::MEMBER_DEPOSIT_BY_CASH,
        );
        $biz_code_limit_new = array();
        foreach ($biz_code_limit as $code) {
            $biz_code_limit_new[$code] = ucwords(str_replace('_', ' ', $code));
        }

        return array(
            'sts' => true,
            'limit_list' => $limit_list,
            'biz_code_limit_new' => $biz_code_limit_new
        );
    }


    public function counterBizSettingOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {

            // 组装数据
            $data = array();
            $biz_code = $p['biz_code'];
            $is_require_ct_approve = $p['is_require_ct_check'];
            $min_approve_amount = $p['min_check_amount'];
            foreach ($biz_code as $key => $v) {
                $current_code = $v;
                $is_require = intval($is_require_ct_approve[$current_code]) ? 1 : 0;
                $min_amount = intval($min_approve_amount[$current_code]);
                $data[$current_code] = array(
                    'is_require_ct_approve' => $is_require,
                    'min_approve_amount' => $min_amount
                );
            }

            // 插入数据
            $user_info = array(
                'user_id' => $this->user_id,
                'user_name' => $this->user_name
            );
            $rt = (new common_counter_biz_settingModel())->insertSetting($data, $user_info);
            if (!$rt->STS) {
                showMessage('Setting fail:' . $rt->MSG);
            }

            showMessage('Setting success.');

        } else {

            $set_list = enum_langClass::getCounterBizLang();
            $set_value = (new common_counter_biz_settingModel())->getAllSetting();
            $set_value = resetArrayKey($set_value, 'biz_code');
            Tpl::output('setting_value', $set_value);
            Tpl::output('list', $set_list);
            TpL::showPage('counter.biz.setting');

        }
    }


    //授信的参与条件设置
    public function grantVoterLimitOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $arr_min = $p['min_credit'];
            $arr_max = $p['max_credit'];
            $arr_voter = $p['voter'];
            $arr = array();
            foreach ($arr_min as $i => $v) {
                $min_credit = intval($v);
                $max_credit = intval($arr_max[$i]);
                $voter = intval($arr_voter[$i]);
                if (!$max_credit || !$voter) {
                    unset($p['form_submit']);
                    showMessage("Invalid Parameter:require to input for all item");
                }
                $arr[] = array(
                    "min_credit" => $min_credit, "max_credit" => $max_credit, "voter" => $voter
                );
            }
            if (!count($arr)) {
                showMessage("Nothing need to save!");
            }
            $m_core_dictionary = new core_dictionaryModel();
            $rt = $m_core_dictionary->updateDictionary("voter_of_granting_credit", my_json_encode($arr));
            if ($rt->STS) {
                showMessage("Save Success!");
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $arr = global_settingClass::getVoterOfGrantingCredit();
            Tpl::output("limit_list", $arr);
            TpL::showPage('credit.grant.voter');
        }
    }

    public function writtenOffVoterLimitOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $arr_min = $p['min_loss'];
            $arr_max = $p['max_loss'];
            $arr_voter = $p['voter'];
            $arr = array();
            foreach ($arr_min as $i => $v) {
                $min_loss = intval($v);
                $max_loss = intval($arr_max[$i]);
                $voter = intval($arr_voter[$i]);
                if (!$max_loss || !$voter) {
                    unset($p['form_submit']);
                    showMessage("Invalid Parameter:require to input for all item");
                }
                $arr[] = array(
                    "min_loss" => $min_loss, "max_loss" => $max_loss, "voter" => $voter
                );
            }
//            if (!count($arr)) {
//                showMessage("Nothing need to save!");
//            }
            $m_core_dictionary = new core_dictionaryModel();
            $rt = $m_core_dictionary->updateDictionary("voter_of_written_off", my_json_encode($arr));
            if ($rt->STS) {
                showMessage("Save Success!");
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $arr = global_settingClass::getVoterOfWrittenOff();
            Tpl::output("limit_list", $arr);
            TpL::showPage('written.off.voter');
        }
    }

    public function memberAceAccountOp()
    {

        Tpl::showPage('ace.account');
    }

    public function getClientAceAccountListOp($p)
    {
        $page_num = intval($p['pageNumber']);
        $page_size = intval($p['pageSize']);
        $page_list = member_handlerClass::getAllClientBindAceAccount($page_num, $page_size, $p);

        return array(
            "sts" => true,
            "data" => $page_list->rows,
            "total" => $page_list->count,
            "pageNumber" => $page_list->pageIndex,
            "pageTotal" => $page_list->pageCount,
            "pageSize" => $page_list->pageSize
        );
    }

    public function unbindClientAceAccountOp($p)
    {
        $uid = intval($p['uid']);
        $user_id = intval($this->user_id);
        $conn = ormYo::Conn();
        try {
            $conn->startTransaction();
            $rt = userClass::unbindAceAccountForClient($uid, $user_id);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        } catch (Exception $e) {
            return new result(false, $e->getMessage());
        }

    }

    public function pushNotificationOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage('system.push_notification');
    }

    public function getPushNotificationListOp($p)
    {
        $search_text = trim($p['search_text']);
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT * FROM member_message WHERE (message_time BETWEEN '" . $d1 . "' AND '" . $d2 . "') AND message_type = " . messageTypeEnum::BROADCAST;
        if ($search_text) {
            $sql .= " AND message_title like '%" . qstr2($search_text) . "%'";
        }

        $sql .= ' ORDER BY message_id DESC';
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }

    public function addPushNotificationOp()
    {
        $p = $_POST;
        if ($p['form_submit'] == 'ok') {
            $title = trim($p['message_title']);
            $body = trim($p['message_body']);
            if (!$title || !$body) {
                showMessage('Param Error.');
            }

            $rt = member_messageClass::sendBroadcastMessage($title,$body);
            if( !$rt->STS ){
                showMessage('Send fail:'.$rt->MSG);
            }
            $message = $rt->DATA;

            jpushApi::Instance()->sendUserMessage(0, $message['uid'], $body);

            showMessage('Send successful.');
        } else {
            Tpl::showPage('system.push_notification.add');
        }
    }

    public function gl_code_ruleOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_core_dictionary = new core_dictionaryModel();
        if ($p['form_submit'] == 'ok') {
            // 组装数据
            $data = array();
            $biz_code = $p['biz_code'];
            $id_width = $p['length'];
            foreach ($biz_code as $key => $v) {
                $current_code = $v;
                $prefix = array();
                foreach ((new currencyEnum())->Dictionary() as $ck => $cv) {
                    $prefix[$ck] = $p['prefix_' . $ck][$current_code];
                }
                $length = intval($id_width[$current_code]);
                $data[$current_code] = array(
                    'prefix' => $prefix,
                    'length' => $length
                );
            }

            $rt = $m_core_dictionary->updateDictionary(dictionaryKeyEnum::GL_CODE_RULE, my_json_encode($data));
            if (!$rt->STS) {
                showMessage('Setting fail:' . $rt->MSG);
            }
            showMessage('Setting success.');
        } else {
            $data = $m_core_dictionary->getDictionary(dictionaryKeyEnum::GL_CODE_RULE);

            Tpl::output('setting_value', my_json_decode($data['dict_value']));
            Tpl::output('list', array(
                'user' => 'User',
                'branch' => 'Branch',
                'bank' => 'Bank',
                'partner' => 'Partner',
                'member' => 'Member'
            ));
            Tpl::showPage('gl_code.rule');
        }
    }

    public function resumeRejectClientOp()
    {
        Tpl::showPage('reject.client');
    }

    public function getRejectClientListOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT * FROM client_member WHERE operate_state = " . newMemberCheckStateEnum::CLOSE;

        if (trim($p['search_text'])) {
            $search_text = qstr2(trim($p['search_text']));
            $sql .= " AND (display_name LIKE '%" . $search_text . "%' OR login_code LIKE '%" . $search_text . "%' OR phone_id LIKE '%" . $search_text . "%')";
        }
        $sql .= " ORDER BY operate_time DESC";

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    public function resumeClientOp($p)
    {
        $uid = intval($p['uid']);
        $class_user_task = new userTaskClass($this->user_id);
        $rt = $class_user_task->resumeClient($uid);
        return $rt;
    }

    public function sqlQueryOp()
    {

        // todo 支持多语句执行
        $params = array_merge(array(),$_GET,$_POST);
        if( $params['form_submit'] == 'ok' ){

            $conn = ormYo::Conn();
            $sql = $params['sql_desc'];
            try{

                $check = system_toolClass::isSqlValid($sql);
                if( !$check ){
                    showMessage('Invalid Sql.');
                }

                $rt = $conn->execute($sql);
                if( !$rt->STS ){
                    showMessage($rt->MSG);

                }


                $keys = array_keys($rt->FIRST_ROW);
                $result = $rt->RESULT;
                Tpl::output('keys',$keys);
                Tpl::output('list',$result);
                Tpl::showpage('sql.query.result');

            }catch( Exception $e ){
                showMessage($e->getMessage());
            }

        }
        Tpl::showpage('sql.query');
    }



    public function moduleBusinessOp()
    {

        $params = array_merge(array(),$_GET,$_POST);

        $arr_platform=array(
            bizSceneEnum::APP_MEMBER=>array("title"=>"APP-Member","default_tab"=>1,"list"=>global_settingClass::getModuleBusinessSetting(bizSceneEnum::APP_MEMBER)),
            bizSceneEnum::COUNTER=>array("title"=>"Counter","default_tab"=>0,"list"=>global_settingClass::getModuleBusinessSetting(bizSceneEnum::COUNTER)),
            bizSceneEnum::BACK_OFFICE=>array("title"=>"Console","default_tab"=>0,"list"=>global_settingClass::getModuleBusinessSetting(bizSceneEnum::BACK_OFFICE))
        );
        Tpl::output("platform",$arr_platform);

        Tpl::showpage('module.business.setting');
    }
    public function submitModuleEntranceSettingOp($p){
        $module_code=$p['module_code'];
        $platform=$p['platform'];
        if(!$module_code || !$platform){
            return new result(false,"Empty Paramter");
        }
        $m=new common_module_entranceModel();
        $row=$m->getRow(array("module_code"=>$module_code,"platform"=>$platform));
        $is_add=false;
        if(!$row){
            $is_add=true;
            $row=$m->newRow(array(
                "module_code"=>$module_code,
                "platform"=>$platform
            ));
        }
        $row->is_new=intval($p['is_new']);
        $row->is_close=intval($p['is_close']);
        $row->is_show=intval($p['is_show']);
        $row->update_time=Now();
        if($is_add){
            $ret=$row->insert();
        }else{
            $ret=$row->update();
        }
        return $ret;
    }

    public function checkDepositOp() {
        Tpl::showPage('trading_check.deposit');
    }

    public function getPartnerDepositCheckListOp($p) {
        $m = new biz_member_depositModel();
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $m->searchPendingRecordForPartner($p['search_text'], $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }

    public function partnerDepositConfirmOp($p) {
        $depositClass = new bizMemberDepositByPartnerClass(bizSceneEnum::BACK_OFFICE);
        return $depositClass->bizConfirm($p['uid']);
    }

    public function partnerDepositCancelOp($p) {
        $depositClass = new bizMemberDepositByPartnerClass(bizSceneEnum::BACK_OFFICE);
        return $depositClass->bizCancel($p['uid']);
    }

    public function checkWithdrawOp() {
        Tpl::showPage('trading_check.withdraw');
    }

    public function getPartnerWithdrawCheckListOp($p) {
        $m = new biz_member_withdrawModel();
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $m->searchPendingRecordForPartner($p['search_text'], $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }

    public function partnerWithdrawConfirmOp($p) {
        $depositClass = new bizMemberWithdrawToPartnerClass(bizSceneEnum::BACK_OFFICE);
        return $depositClass->bizConfirm($p['uid']);
    }

    public function partnerWithdrawCancelOp($p) {
        $depositClass = new bizMemberWithdrawToPartnerClass(bizSceneEnum::BACK_OFFICE);
        return $depositClass->bizCancel($p['uid']);
    }

    public function getStaffListWithPassbookAccountsOp($p){
        $search_text = trim($p['search_text']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $um=new um_userModel();
        $ret=$um->searchUserListByFreeText($search_text,$pageNumber,$pageSize);


        $account_model = new passbook_accountModel();
        return array(
            "sts" => true,
            "data" => $account_model->fillSavingsPassbookAccountInfoForUsers($ret->DATA['rows']),
            "total" => $ret->DATA['total'],
            "pageNumber" => $pageNumber,
            "pageTotal" => $ret->DATA['page_total'],
            "pageSize" => $pageSize,
        );
    }
    public function adjustUserAccountOp($p){
        return passbookWorkerClass::adjustUser($p['uid'], $p['amount'], $p['currency'], $p['remark']);
    }


    public function partnerLimitOp()
    {
        $limit = partnerClass::getPartnerLimitGroupByPartner();

        Tpl::output('limit',$limit);
        Tpl::showPage('partner.limit.list');
    }

    public function partnerLimitSettingPageOp()
    {
        $params = array_merge($_GET,$_POST);
        $uid = intval($params['uid']);
        $m = new partner_limit_settingModel();
        $setting_info = array();
        if( $uid ){
            $setting_info = $m->find(array(
                'uid' => $uid
            ));
        }

        Tpl::output('setting_info',$setting_info);
        $partner_list = (new partnerModel())->getAll();
        Tpl::output('partner_list',$partner_list);
        Tpl::showPage('partner.limit.setting');
    }

    public function savePartnerLimitSettingOp()
    {
        $params = array_merge($_GET,$_POST);
        $partner_code = $params['partner_code'];
        $biz_type = $params['biz_type'];
        if( !$partner_code ){
            showMessage('Please choose partner!');
        }
        if( !$biz_type ){
            showMessage('Please choose business type!');
        }

        $m = new partner_limit_settingModel();
        $rt = $m->editSetting($params);
        if( !$rt->STS ){
            showMessage($rt->MSG);
        }else{
            showMessage('Success!',getBackOfficeUrl('dev','partnerLimit'));
        }

    }

    public function ajaxDeleteSettingOp($p)
    {
        $uid = intval($p['uid']);
        if( !$uid ){
            return new result(false,'Invalid id:'.$uid);
        }
        $m = new partner_limit_settingModel();
        $row = $m->getRow($uid);
        if( !$row ){
            return new result(false,'No setting info:'.$uid);
        }
        $del = $row->delete();
        return $del;
    }
    public function getSystemAccountListWithPassbookAccountsOp($p){
        $search_text = trim($p['search_text']);
        $pageNumber = 1;
        $pageSize = 1000;

        $m_passbook=new passbookModel();
        $rows=$m_passbook->select(array("obj_type"=>'gl_account'));
        $rows=resetArrayKey($rows,"obj_guid");
        $guid_map=array_keys($rows);
        $str_guid=implode("','",$guid_map);


        $sql="select a.obj_guid,b.* from passbook a inner join passbook_account b on a.uid=b.book_id ";
        $sql.=" where a.obj_guid in ('".$str_guid."')";
        $account_list=$m_passbook->reader->getRows($sql);
        foreach ($account_list as $account_info) {
            $guid = $account_info['obj_guid'];
            if (!$rows[$guid]['accounts']) $rows[$guid]['accounts']=array();
            $rows[$guid]['accounts'][]=$account_info;
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => count($rows),
            "pageNumber" => $pageNumber,
            "pageTotal" => 1,
            "pageSize" => $pageSize,
        );
    }

}
