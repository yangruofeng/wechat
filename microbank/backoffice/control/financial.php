<?php

class financialControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('financial');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "");
        Tpl::setDir("financial");
        Language::read('financial');
    }

    public function hqVaultOp()
    {
        $hq_civ = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_CIV);
        $civ_balance = $hq_civ->getAccountBalance();
        Tpl::output("civ_balance", $civ_balance);
        $m_br = new site_branchModel();
        $br_list = $m_br->select("1=1");
        Tpl::output("branch_list", $br_list);

        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        Tpl::showpage('hq.vault');
    }

    public function getHqVaultFlowOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $type = $p['type'];

        if (!is_array($type)) {
            $type = array($type);
        }
        $object_id_arr = array();
        $m_site_bank = M('site_bank');
        if (in_array('hq_bank', $type)) {
            $hq_bank_list = $m_site_bank->select(array('branch_id' => 0));
            $hq_bank_list = array_column($hq_bank_list, 'obj_guid');
            $object_id_arr = array_merge($object_id_arr, $hq_bank_list);
        }
        if (in_array('hq_2_branch', $type)) {
            $hq_br_list = (new site_branchModel())->select("1=1");
            $hq_br_list = array_column($hq_br_list, 'obj_guid');
            $object_id_arr = array_merge($object_id_arr, $hq_br_list);
        }


        if (in_array('hq_bank', $type)) {
            $branch_bank_list = $m_site_bank->select(array('branch_id' => array('neq', 0)));
            $branch_bank_list = array_column($branch_bank_list, 'obj_guid');
            $object_id_arr = array_merge($object_id_arr, $branch_bank_list);
        }

        if (in_array('hq_capital', $type)) {
            $capitalObj = new objectGlAccountClass(systemAccountCodeEnum::HQ_CAPITAL);
            $object_id_arr = array_merge($object_id_arr, array($capitalObj->object_id));
        }



        $civObj = new objectGlAccountClass(systemAccountCodeEnum::HQ_CIV);
        $m_biz_obj_transfer = new biz_obj_transferModel();
        $page = $m_biz_obj_transfer->getHqVaultFlow($civObj->object_id, $object_id_arr, $pageNumber, $pageSize);
        $page['object_id'] = $civObj->object_id;
        return $page;
    }

    public function addHqVaultOp()
    {
        $currency = trim($_POST['currency']);
        $amount = round($_POST['amount'], 2);
        $remark = trim($_POST['remark']);

        $class_biz = bizFactoryClass::getInstance(bizSceneEnum::BACK_OFFICE, bizCodeEnum::CAPITAL_TO_CIV);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $class_biz->execute($this->user_id, $amount, $currency, $remark, $this->user_id);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage("Add Successful");
        } else {
            $conn->rollback();
            showMessage('Add Failure--' . $rt->MSG);
        }
    }

    public function hqCIVTransferToBranchCIVOp()
    {
        $currency = trim($_POST['currency']);
        $amount = round($_POST['amount'], 2);
        $remark = trim($_POST['remark']);
        $branch_id = intval($_POST['branch_id']);

        $class_biz = bizFactoryClass::getInstance(bizSceneEnum::BACK_OFFICE, bizCodeEnum::HEADQUARTER_TO_BRANCH);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $class_biz->execute($branch_id, $amount, $currency, $remark, $this->user_id);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage("Successful Transfer");
        } else {
            $conn->rollback();
            showMessage('Failed To Transfer--' . $rt->MSG);
        }
    }

    public function hqCIVReceiveFromBranchCIVOp()
    {
        $currency = trim($_POST['currency']);
        $amount = round($_POST['amount'], 2);
        $remark = trim($_POST['remark']);
        $branch_id = intval($_POST['branch_id']);

        $class_biz = bizFactoryClass::getInstance(bizSceneEnum::BACK_OFFICE, bizCodeEnum::BRANCH_TO_HEADQUARTER);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $class_biz->execute($branch_id, $amount, $currency, $remark, $this->user_id);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage("Successful Transfer");
        } else {
            $conn->rollback();
            showMessage('Failed To Transfer--' . $rt->MSG);
        }
    }

    /**
     * 弃用
     * @throws Exception
     */
    public function cutHqVaultOp()
    {
        $currency = trim($_POST['currency']);
        $amount = round($_POST['amount'], 2);
        $remark = trim($_POST['remark']);

        $class_biz = bizFactoryClass::getInstance(bizSceneEnum::BACK_OFFICE, bizCodeEnum::CIV_TO_COD);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $class_biz->execute($this->user_id, $amount, $currency, $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage("Cut Successful");
        } else {
            $conn->rollback();
            showMessage('Cut Failure--' . $rt->MSG);
        }
    }

    /**
     * HQ银行账号
     */
    public function hqBankOp()
    {
        $m_site_bank = M('site_bank');
        $bank_list = branchClass::getBankList(0, true, true);
        Tpl::output("bank_list", $bank_list);
        /*
        $hq_bank = $m_site_bank->select(array('branch_id' => 0, 'account_state' => array('neq', -1)));
        foreach ($hq_bank as $key => $bank) {
            $object_sys_bank_class = new objectSysBankClass($bank['uid']);
            $balance = $object_sys_bank_class->getPassbookCurrencyBalance();
            $bank['balance'] = $balance[$bank['currency']];
            $hq_bank[$key] = $bank;
        }
        */

        Tpl::output('bank_list', $bank_list);
        Tpl::showpage('bank.hq');
    }

    /**
     * 添加bank
     */
    public function addBankOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $type = $p['type'];
        if ($p['form_submit'] == 'ok') {
            $m_bank_account = M('site_bank');
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $m_bank_account->addBank($p);
            if ($rt->STS) {
                $method = $type == 'hq' ? 'hqBank' : 'branchBank';
                showMessage($rt->MSG, getUrl('financial', $method, array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('financial', 'addBank', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $m_common_bank_lists = M('common_bank_lists');
            $bank_list = $m_common_bank_lists->select(array('uid' => array('neq', 0)));
            Tpl::output("bank_list", $bank_list);

            if ($type == 'branch') {
                $m_site_branch = M('site_branch');
                $branch_list = $m_site_branch->select(array('status' => 1));
                Tpl::output("branch_list", $branch_list);
            }

            $currency_list = currency::getKindList();
            Tpl::output("currency_list", $currency_list);
            Tpl::output("type", $type);
            Tpl::showPage("bank.add");
        }
    }

    /**
     * 修改bank
     */
    public function editBankOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_bank_account = M('site_bank');
        $uid = intval($p['uid']);
        $bank_info = $m_bank_account->find(array('uid' => $uid));
        if (!$bank_info) {
            showMessage('Invalid Id!');
        }
        if ($p['form_submit'] == 'ok') {
            $rt = $m_bank_account->editBank($p);
            if ($rt->STS) {
                $method = $bank_info['branch_id'] == 0 ? 'hqBank' : 'branchBank';
                showMessage($rt->MSG, getUrl('financial', $method, array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('financial', 'editBank', array('uid' => intval($p['uid'])), false, BACK_OFFICE_SITE_URL));
            }
        } else {
            Tpl::output('bank_info', $bank_info);
            if ($bank_info['branch_id']) {
                $m_site_branch = M('site_branch');
                $branch_info = $m_site_branch->find(array('uid' => $bank_info['branch_id']));
                Tpl::output("branch_info", $branch_info);
            }
            Tpl::showPage("bank.edit");
        }
    }

    /**
     * branch Bank
     */
    public function branchBankOp()
    {
        $m_site_branch = M('site_branch');
        $branch_list = $m_site_branch->select(array('status' => 1));
        Tpl::output("branch_list", $branch_list);

        $m_common_bank_lists = M('common_bank_lists');
        $bank_list = $m_common_bank_lists->select(array('uid' => array('neq', 0)));
        Tpl::output("bank_list", $bank_list);

        $group = $_GET['group'] ?: 'bank';
        Tpl::output("group", $group);
        Tpl::showpage('bank.branch');
    }

    /**
     * @param $p
     * @return array
     */
    public function getBankListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $search_text = trim($p['search_text']);
        $branch_id = intval($p['branch_id']);
        $bank_code = trim($p['bank_code']);
        $group = $p['group'];

        $filter = array('branch_id' => $branch_id, 'bank_code' => $bank_code, 'type' => 'branch');
        $bank_model = new site_bankModel();
        if ($group != 'branch') {
            $ret = $bank_model->searchBankListByFreeText($search_text, $pageNumber, $pageSize, $filter);
            $rows = $ret->DATA['rows'];
            foreach ($rows as $key => $bank) {
                $object_sys_bank_class = new objectSysBankClass($bank['uid']);
                $balance = $object_sys_bank_class->getPassbookCurrencyBalance();
                $bank['balance'] = $balance[$bank['currency']];
                $rows[$key] = $bank;
            }
        } else {
            $ret = $bank_model->searchBankListGroupByBranch($search_text, $pageNumber, $pageSize, $filter);
            $rows = $ret->DATA['rows'];
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $ret->DATA['total'],
            "pageNumber" => $pageNumber,
            "pageTotal" => $ret->DATA['page_total'],
            "pageSize" => $pageSize,
            "group" => $group,
        );
    }

    public function deleteBankOp()
    {
        $uid = intval($_GET['uid']);
        $m_bank_account = M('site_bank');
        $rt = $m_bank_account->deleteBank($uid);
        if ($rt->STS) {
            showMessage('Remove successful!');
        } else {
            showMessage('Remove failed!');
        }
    }

    public function bankTransactionOp()
    {
        $uid = intval($_GET['uid']);
        $m_site_bank = M('site_bank');
        $bank_info = $m_site_bank->find($uid);
        $object_sys_bank_class = new objectSysBankClass($uid);
        $balance = $object_sys_bank_class->getPassbookCurrencyBalance();
        $bank_info['balance'] = $balance[$bank_info['currency']];
        Tpl::output("bank_info", $bank_info);

        $type = $_GET['type'] ?: 'branch';
        Tpl::output("type", $type);
        Tpl::showpage('bank.transaction');
    }

    /**
     * 存款
     * @throws Exception
     */
    public function depositOp()
    {
        $bank_id = intval($_POST['bank_id']);
        $amount = round($_POST['amount'], 2);
        $remark = trim($_POST['remark']);

        $m_bank = M('site_bank');
        $bank = $m_bank->getRow($bank_id);
        if (!$bank) {
            showMessage('Invalid Id!');
        }

        $class_biz = bizFactoryClass::getInstance(bizSceneEnum::BACK_OFFICE, bizCodeEnum::HEADQUARTER_TO_BANK);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $class_biz->execute($bank_id, $this->user_id, $amount, $bank['currency'], $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage("Deposit Successful");
        } else {
            $conn->rollback();
            showMessage('Deposit Failure--' . $rt->MSG);
        }
    }

    /**
     * 取款
     * @throws Exception
     */
    public function withdrawalOp()
    {
        $bank_id = intval($_POST['bank_id']);
        $amount = round($_POST['amount'], 2);
        $remark = trim($_POST['remark']);

        $m_bank = M('site_bank');
        $bank = $m_bank->getRow($bank_id);
        if (!$bank) {
            showMessage('Invalid Id!');
        }

        $class_biz = bizFactoryClass::getInstance(bizSceneEnum::BACK_OFFICE, bizCodeEnum::BANK_TO_HEADQUARTER);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $class_biz->execute($bank_id, $this->user_id, $amount, $bank['currency'], $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage("withdrawal Successful");
        } else {
            $conn->rollback();
            showMessage('withdrawal Failure--' . $rt->MSG);
        }
    }

    /**
     * 银行交易记录
     * @param $p
     * @return array
     */
    public function getTransactionListOp($p)
    {
        $uid = intval($p['uid']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $m_site_bank = M('site_bank');
        $bank_info = $m_site_bank->getRow($uid);

        $m_biz_obj_transfer = new biz_obj_transferModel();
        $page = $m_biz_obj_transfer->getBankTransactionByGuid($bank_info['obj_guid'], $pageNumber, $pageSize);

        $m_site_branch = M('site_branch');
        $branch_list = $m_site_branch->select(array('uid' => array('neq', 0)));
        $branch_list = resetArrayKey($branch_list, 'obj_guid');
        $page['branch_list'] = $branch_list;
        $page['object_id'] = $bank_info['obj_guid'];
        return $page;
    }


    public function exchangeRateIndexOp()
    {
        $m_common_exchange_rate =  new common_exchange_rateModel();
        $exchange_rate_list = $m_common_exchange_rate->select(array('uid' => array('neq', 0)));

        Tpl::output('exchange_rate_list', $exchange_rate_list);
        Tpl::showpage('exchange_rate.list');
    }



    /**
     * 货币汇率表
     */
    public function exchangeRateOp()
    {

        return $this->exchangeRateIndexOp();

        $p = array_merge(array(), $_GET, $_POST);
        $m_common_exchange_rate = M('common_exchange_rate');
        if ($p['form_submit'] == 'ok') {
            $sell_price = round($p['sell_price'], 2);
            $buy_price = round($p['buy_price'], 2);
            if ($sell_price <= 0 || $buy_price <= 0) {
                showMessage('The price can\'t be less than 0.');
            }
            if ($sell_price > $buy_price) {
                showMessage('The sell price is greater than the buy price.');
            }

            $row = $m_common_exchange_rate->getRow(array('first_currency' => currencyEnum::USD, 'second_currency' => currencyEnum::KHR));
            if ($row) {
                $row->buy_rate = $sell_price;
                $row->buy_rate_unit = 1;
                $row->sell_rate = 1;
                $row->sell_rate_unit = $buy_price;
                $row->update_id = $this->user_id;
                $row->update_name = $this->user_name;
                $row->update_time = Now();
                $rt = $row->update();
            } else {
                $row = $m_common_exchange_rate->newRow();
                $row->first_currency = currencyEnum::USD;
                $row->second_currency = currencyEnum::KHR;
                $row->buy_rate = $sell_price;
                $row->buy_rate_unit = 1;
                $row->sell_rate = 1;
                $row->sell_rate_unit = $buy_price;
                $row->update_id = $this->user_id;
                $row->update_name = $this->user_name;
                $row->update_time = Now();
                $rt = $row->insert();
            }
            if ($rt->STS) {
                showMessage('Setting successful!', getUrl('financial', 'exchangeRate', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage('Setting failed!');
            }
        } else {
            $exchange_rate = $m_common_exchange_rate->find(array('first_currency' => currencyEnum::USD, 'second_currency' => currencyEnum::KHR));
            Tpl::output('exchange_rate', $exchange_rate);
            Tpl::showpage('exchange_rate.setting.new');
        }
    }


    public function addNewExchangeRateOp($p)
    {
        $m_common_exchange_rate = new common_exchange_rateModel();
        $row = $m_common_exchange_rate->newRow();
        $first_currency = $p['first_currency'];
        $second_currency = $p['second_currency'];
        $buy_rate = round($p['buy_rate'], 2);
        $buy_rate_unit = round($p['buy_rate_unit'], 2);
        $sell_rate = round($p['sell_rate'], 2);
        $sell_rate_unit = round($p['sell_rate_unit'], 2);
        if (($buy_rate / $buy_rate_unit) > ($sell_rate_unit / $sell_rate)) {
            showMessage('The sell price is greater than the buy price!');
        }
        $row->first_currency = $first_currency;
        $row->second_currency = $second_currency;
        $row->buy_rate = $buy_rate;
        $row->buy_rate_unit = $buy_rate_unit;
        $row->sell_rate = $sell_rate;
        $row->sell_rate_unit = $sell_rate_unit;
        $row->update_id = $this->user_id;
        $row->update_name = $this->user_name;
        $row->update_time = Now();
        $insert = $row->insert();
        return $insert;
    }

    /**
     * 设置汇率
     */
    public function setExchangeRateOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_common_exchange_rate = M('common_exchange_rate');
        if ($p['form_submit'] == 'ok') {
            $first_currency = $p['first_currency'];
            $second_currency = $p['second_currency'];
            $row = $m_common_exchange_rate->getRow(array('first_currency' => $first_currency, 'second_currency' => $second_currency));
            $currency_list = (new currencyEnum)->Dictionary();
            unset($currency_list[$first_currency]);
            unset($currency_list[$second_currency]);
            $other_currency = array_pop($currency_list);
            if ($row) {
                $buy_rate = round($p['buy_rate'], 2);
                $buy_rate_unit = round($p['buy_rate_unit'], 2);
                $sell_rate = round($p['sell_rate'], 2);
                $sell_rate_unit = round($p['sell_rate_unit'], 2);
                if (($buy_rate / $buy_rate_unit) > ($sell_rate_unit / $sell_rate)) {
                    showMessage('The sell price is greater than the buy price!');
                }

                $exchange_0 = $buy_rate / $buy_rate_unit;
                $exchange_1 = $m_common_exchange_rate->getRateBetween($second_currency, $other_currency);
                $exchange_2 = $m_common_exchange_rate->getRateBetween($other_currency, $first_currency);
                $exchange = $exchange_0 * $exchange_1 * $exchange_2;
                if ($exchange > 1) {
                    showMessage('If buying in a third currency, the principal increases!');
                }

                $row->buy_rate = $buy_rate;
                $row->buy_rate_unit = $buy_rate_unit;
                $row->sell_rate = $sell_rate;
                $row->sell_rate_unit = $sell_rate_unit;
            } else {
                $row = $m_common_exchange_rate->getRow(array('second_currency' => $first_currency, 'first_currency' => $second_currency));

                if (!$row) {
                    $re = $this->addNewExchangeRateOp($p);
                    if ($re->STS) {
                        showMessage('Setting successful!', getUrl('financial', 'exchangeRate', array(), false, BACK_OFFICE_SITE_URL));
                    } else {
                        showMessage('Setting failed!');
                    }
                }

                $buy_rate = round($p['buy_rate'], 2);
                $buy_rate_unit = round($p['buy_rate_unit'], 2);
                $sell_rate = round($p['sell_rate'], 2);
                $sell_rate_unit = round($p['sell_rate_unit'], 2);

                if (($sell_rate / $sell_rate_unit) > ($buy_rate_unit / $buy_rate)) {
                    showMessage('The sell price is greater than the buy price!');
                }

                $exchange_0 = $sell_rate / $sell_rate_unit;
                $exchange_1 = $m_common_exchange_rate->getRateBetween($first_currency, $other_currency);
                $exchange_2 = $m_common_exchange_rate->getRateBetween($other_currency, $second_currency);
                $exchange = $exchange_0 * $exchange_1 * $exchange_2;
                if ($exchange > 1) {
                    showMessage('If buying in a third currency, the principal increases!');
                }

                $row->buy_rate = $sell_rate;
                $row->buy_rate_unit = $sell_rate_unit;
                $row->sell_rate = $buy_rate;
                $row->sell_rate_unit = $buy_rate_unit;
            }
            $row->update_id = $this->user_id;
            $row->update_name = $this->user_name;
            $row->update_time = Now();
            $rt = $row->update();
            if ($rt->STS) {
                showMessage('Setting successful!', getUrl('financial', 'exchangeRate', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage('Setting failed!');
            }
        } else {
            $uid = intval($p['uid']);
            if ($uid > 0) {
                $currency = $m_common_exchange_rate->find(array('uid' => $uid));
                Tpl::output('currency', $currency);
            }
            $currency_list = (new currencyEnum)->Dictionary();
            Tpl::output('currency_list', $currency_list);
            Tpl::showpage('exchange_rate.setting');
        }
    }

    public function deleteRateByIdOp($p)
    {
        $uid = intval($p['uid']);
        $m = new common_exchange_rateModel();
        $row = $m->getRow($uid);
        if( !$row ){
            return new result(false,'Not found rate info:'.$uid,null,errorCodesEnum::NO_DATA);
        }
        $del = $row->delete();
        if( !$del->STS ){
            return new result(false,'Delete rate fail:'.$del->MSG,null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }

    /**
     * 获取汇率
     * @param $p
     * @return result
     */
    public function getRateOp($p)
    {
        $first_currency = $p['first_currency'];
        $second_currency = $p['second_currency'];

        $m_common_exchange_rate = M('common_exchange_rate');
        $currency_rate = $m_common_exchange_rate->find(array('first_currency' => $first_currency, 'second_currency' => $second_currency));
        if ($currency_rate) {
            $data = array(
                'buy_rate' => rtrim(rtrim($currency_rate['buy_rate'], '0'), '.'),
                'buy_rate_unit' => rtrim(rtrim($currency_rate['buy_rate_unit'], '0'), '.'),
                'sell_rate' => rtrim(rtrim($currency_rate['sell_rate'], '0'), '.'),
                'sell_rate_unit' => rtrim(rtrim($currency_rate['sell_rate_unit'], '0'), '.')
            );
            return new result(true, '', $data);
        }
        $currency_rate = $m_common_exchange_rate->find(array('first_currency' => $second_currency, 'second_currency' => $first_currency));
        if ($currency_rate) {
            $data = array(
                'buy_rate' => rtrim(rtrim($currency_rate['sell_rate'], '0'), '.'),
                'buy_rate_unit' => rtrim(rtrim($currency_rate['sell_rate_unit'], '0'), '.'),
                'sell_rate' => rtrim(rtrim($currency_rate['buy_rate'], '0'), '.'),
                'sell_rate_unit' => rtrim(rtrim($currency_rate['buy_rate_unit'], '0'), '.')
            );
            return new result(true, '', $data);
        } else {
            return new result(true, '', null);
        }
    }

    public function setBranchOp()
    {
        $uid = $_GET['uid'];
        $m_bank_account = M('site_bank');
        $bank_list = $m_bank_account->find(array('uid' => $uid));
        Tpl::output('bank_list', $bank_list);

        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('uid' => array('neq', 0)));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage('set.branch');
    }

    /**
     * 获取branch列表
     */
    public function getBranchListOp($p)
    {
        $bank_uid = $p['bank_uid'];
        Tpl::output('bank_uid', $bank_uid);
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT * FROM site_branch WHERE uid > 0";

        if ($search_text) {
            $sql .= " AND branch_name LIKE '%" . $search_text . "%'";
        }

        $sql .= " ORDER BY uid ASC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $m_site_bank = M('site_bank');
        foreach ($rows as $key => $row) {
            $bank_list = $m_site_bank->select(array('branch_id' => $row['uid']));
            $rows[$key]['bank_list'] = $bank_list;
        }

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
     * 分配Bank 到Branch
     */
    public function chooseBranchOp($p)
    {
        $branch_id = intval($p['branch_id']);
        $bank_uid = intval($p['bank_uid']);
        $m_site_bank = M('site_bank');
        $row = $m_site_bank->getRow(array('uid' => $bank_uid));
        if (!$row) {
            return new result(false, "Bank Doesn't Exist");
        }
        $row->branch_id = $branch_id;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Select Branch Successful');
        } else {
            return new result(false, 'Select Branch Failure');
        }
    }

    /**
     * 提前还款
     */
    public function requestToPrepaymentOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        $request_state = (new prepaymentApplyStateEnum())->Dictionary();
        Tpl::output('request_state', $request_state);
        Tpl::showPage('request.prepayment');
    }

    /**
     * 获取提前还款申请
     * @param $p
     * @return array
     */
    public function getRequestPrepaymentListOp($p)
    {
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);

        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn FROM loan_prepayment_apply lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " WHERE (lrr.apply_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if ($p['state'] >= 0) {
            $sql .= " AND lrr.state = " . intval($p['state']);
        }
        if (trim($p['search_text'])) {
            $sql .= " AND lc.contract_sn like '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY lrr.apply_time DESC";
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
            "cur_uid" => $this->user_id,
            "type" => trim($p['type']),
        );
    }

    /**
     * 提前还款审核页面
     */
    public function auditRequestPrepaymentOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $m_loan_request_repayment = M('loan_prepayment_apply');
        $row = $m_loan_request_repayment->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        //审核中
        if ($row->state == prepaymentApplyStateEnum::AUDITING) {
            // 超时放开，让别人可审 1小时
            if ((strtotime($row['update_time']) + 3600) < time()) {
                $row->state = prepaymentApplyStateEnum::AUDITING;
                $row->auditor_id = $this->user_id;
                $row->auditor_name = $this->user_name;
                $row->update_time = Now();
                $up = $row->update();
                if (!$up->STS) {
                    showMessage($up->MSG);
                }
            }
        } elseif ($row->state == prepaymentApplyStateEnum::CREATE) {
            $row->state = prepaymentApplyStateEnum::AUDITING;
            $row->auditor_id = $this->user_id;
            $row->auditor_name = $this->user_name;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                showMessage($rt->MSG);
            }
        }
        $lock = false;
        if ($this->user_id != $row['auditor_id']) {
            //审核中
            $lock = true;
        }
        Tpl::output('lock', $lock);

        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn,lc.currency contract_currency"
            . " FROM loan_prepayment_apply lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " WHERE lrr.uid =" . $uid;
        $detail = $r->getRow($sql);
        Tpl::output('detail', $detail);

        $re = loan_contractClass::getPrepaymentDetail($detail['contract_id']);
        $prepayment_detail = $re->DATA;
        Tpl::output('prepayment_detail', $prepayment_detail);

        Tpl::showpage('request.prepayment.audit');
    }

    /**
     * 审核提前还款  批准？不批准
     * @param $p
     * @return result
     */
    public function auditPrepaymentOp($p)
    {
        $uid = intval($p['uid']);
        $type = trim($p['type']);
        $remark = trim($p['remark']);

        $m_loan_request_prepayment = M('loan_prepayment_apply');
        $row = $m_loan_request_prepayment->getRow($uid);
        if ($row->auditor_id != $this->user_id) {
            return new result(false, 'Auditor Error!');
        }
        if ($type == 'approve') {
            $row->state = prepaymentApplyStateEnum::APPROVED;
        } else {
            $row->state = prepaymentApplyStateEnum::DISAPPROVE;
        }

        $row->auditor_id = $this->user_id;
        $row->auditor_name = $this->user_name;
        $row->audit_remark = $remark;
        $row->audit_time = Now();
        $row->update_time = Now();
        $rt = $row->update();

        if ($rt->STS) {
            return new result(true, 'Audit Successful!');
        } else {
            return new result(false, 'Audit Failed!');
        }
    }

    /**
     * 查看提前还款申请情况
     */
    public function viewRequestPrepaymentOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn"
            . " FROM loan_prepayment_apply lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " WHERE lrr.uid =" . $uid;
        $detail = $r->getRow($sql);
        if (!$detail) {
            showMessage('Invalid Id!');
        }
        Tpl::output('detail', $detail);
        Tpl::showpage('request.prepayment.view');
    }

    /**
     * bill pay
     */
    public function checkBillPayOp()
    {
        $this->checkBillPayIndexOp();
        Tpl::showpage('billPay.check');
    }


    public function checkBillPayIndexOp()
    {
        // 获得所有银行的列表
        $bank_list = bank_accountClass::getHQBillPayBankList();
        Tpl::output('bank_list',$bank_list);
        Tpl::showpage('billpay.check.index');
    }

    public function checkBillPayStepTwoOp()
    {
        $params = array_merge($_GET,$_POST);
        $bank_id = $params['bank_id'];
        $bank_info = (new site_bankModel())->find(array(
            'uid' => $bank_id
        ));
        if( !$bank_info ){
            showMessage('No found bank info.');
        }
        Tpl::output('bank_info',$bank_info);

        Tpl::showpage('billpay.check.step.two');
    }

    public function getContractSchemasByBillCodeOp($p)
    {
        $params = $p;
        $bank_id = $params['bank_id'];
        $bill_code = $params['bill_code'];
        $schema = bizCheckLoanBillPayByConsoleClass::getLoanSchemasDetailByBillCode($bill_code);
        $data = array(
            'bank_id' => $bank_id,
            'payment_schemas' => $schema,
            'bill_code' => $bill_code
        );

        return $data;

    }

    public function submitBillPayOp($p)
    {
        $bank_id = $p['bank_id'];
        $schema_ids_arr = $p['schema_ids'];
        $amount = $p['amount'];
        $bill_code = $p['bill_code'];
        $remark = $p['remark'];
        $user_id = $this->user_id;
        $rt = (new bizCheckLoanBillPayByConsoleClass())->execute($user_id,$bank_id,$bill_code,$schema_ids_arr,$amount,$remark);
        return $rt;

    }

    /**
     * 获取合同信息
     * @param $p
     * @return array|result
     * @throws Exception
     */
    public function getContractInfoByBillCodeOp($p)
    {
        $bill_code = trim($p['bill_code']);
        $m_loan_contract_billpay_code = M('loan_contract_billpay_code');
        $billpay_info = $m_loan_contract_billpay_code->find(array('bill_code' => $bill_code));
        if (!$billpay_info) {
            return array(
                'sts' => true,
                'msg' => 'Invalid bill code.'
            );
        }
        $contract_id = $billpay_info['contract_id'];
        $rt_1 = loan_contractClass::getLoanContractDetailInfo($contract_id);
        if (!$rt_1->STS) {
            return array(
                'sts' => true,
                'msg' => $rt_1->MSG
            );
        }
        $contract_info = $rt_1->DATA;

        $rt_2 = loan_contractClass::getContractLeftPayableInfo($contract_id);
        $left_payable_info = $rt_2->DATA;
        $bill_currency = $billpay_info['currency'];
        $contract_currency = $contract_info['currency'];
        $exchange_rate = global_settingClass::getCurrencyRateBetween($contract_currency, $bill_currency);
        if ($exchange_rate <= 0) {
            return new result(false, 'Not set currency exchange rate:' . $contract_currency . '-' . $bill_currency);
        }
        $left_payable_info['total_payable_principal'] = round($exchange_rate * $left_payable_info['total_payable_principal'], 2);
        $left_payable_info['total_payable_amount'] = round($exchange_rate * $left_payable_info['total_payable_amount'], 2);
        $left_payable_info['next_repayment_amount'] = round($exchange_rate * $left_payable_info['next_repayment_amount'], 2);

        return array(
            'sts' => true,
            'contract_info' => $contract_info,
            'left_payable_info' => $left_payable_info,
            'billpay_info' => $billpay_info,
        );
    }



    /**
     * 确认提交bill pay
     * @param $p
     * @return result
     */
    public function submitBillPayOp_old($p)
    {
        $bill_code = trim($p['bill_code']);
        $amount = round($p['amount'], 2);
        $pay_time = $p['pay_time'];
        $remark = trim($p['remark']);

        $m_loan_contract_billpay_code = M('loan_contract_billpay_code');
        $bill_info = $m_loan_contract_billpay_code->find(array('bill_code' => $bill_code));
        if (!$bill_info) {
            return new result(false, 'Invalid bill code1.');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt_1 = loanRepaymentWorkerClass::repaymentByBillPayCode($this->user_id, $bill_code, $amount, $bill_info['currency'], $remark);
            if (!$rt_1->STS) {
                $conn->rollback();
                $state = loanBillPayCheckState::API_FAILURE;
                $msg = $rt_1->MSG;
            } else {

                $state = loanBillPayCheckState::SUCCESS;
                $msg = $rt_1->MSG;
            }

            $rt_2 = (new loan_billpay_checkModel())->addBillPayCheck($bill_code, $amount, $pay_time, $remark, $state, $msg, $this->user_id);
            if (!$rt_2->STS) {
                $conn->rollback();
                return $rt_2;
            }

            $conn->submitTransaction();
            if ($state != loanBillPayCheckState::API_FAILURE) {
                $msg = $rt_2->MSG;
            }
            return new result(true, $msg);
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

    }

    /**
     * 获取check list
     * @param $p
     * @return array
     */
    public function getBillPayCheckListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $bank_id = $p['bank_id'];

        $class = new bizCheckLoanBillPayByConsoleClass();
        $list = $class->getCheckList($pageNumber, $pageSize, array(
            'operator_id' => $this->user_id,
            'bank_id' => $bank_id
        ));
        return $list;
    }
}
