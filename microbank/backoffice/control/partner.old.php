<?php

class partnerControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('common');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Partner");
        Tpl::setDir("partner");
    }

    /**
     * 银行
     */
    public function bankOp()
    {
        $class_partner = new partnerClass();
        $partner_list = $class_partner->getPartnerList();
        Tpl::output("partner_list", $partner_list);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        Tpl::showpage('bank');
    }

    /**
     * 添加partner
     */
    public function addPartnerOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            //保存partner
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $class_partner = new partnerClass();
            $rt = $class_partner->addPartner($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('partner', 'bank', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('partner', ' addPartner', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $currency_list = (new currencyEnum())->Dictionary();
            Tpl::output("currency_list", $currency_list);
            Tpl::showpage('partner.add');
        }
    }

    /**
     * 添加partner
     */
    public function editPartnerOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $class_partner = new partnerClass();
        if ($p['form_submit'] == 'ok') {
            //修改
            $rt = $class_partner->editPartner($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('partner', 'bank', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('partner', ' editPartner', array('uid' => $uid), false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $partner_info = $class_partner->getPartnerInfo($uid);
            Tpl::output("partner_info", $partner_info);
            $currency_list = (new currencyEnum())->Dictionary();
            Tpl::output("currency_list", $currency_list);
            Tpl::showpage('partner.edit');
        }
    }

    /**
     * 删除partner
     */
    public function deletePartnerOp()
    {
        $uid = intval($_GET['uid']);
        $class_partner = new partnerClass();
        $rt = $class_partner->deletePartner($uid);
        showMessage($rt->MSG);
    }

    /**
     * 银行
     */
    public function checkTraceOp()
    {
        $m_partner = M('partner');
        $uid = intval($_GET['uid']);
        $currency = $_GET['currency'] ?: currencyEnum::USD;
        $partner = $m_partner->find(array('uid' => $uid));
        if (empty($partner)) {
            showMessage('Invalid Id!');
        }
        Tpl::output("partner", $partner);

        $m_partner_trace_check = M('partner_trace_check');
        $last_check = $m_partner_trace_check->orderBy('uid desc')->find(array('partner_id' => $uid, 'currency' => $currency));


        $startline = $_GET['startline'] ?: date("Y-m-d H:i", strtotime($last_check['check_time'] ?: 'last month'));
        $deadline = $_GET['deadline'] ?: date("Y-m-d H:i");

        Tpl::output("last_check_time", $last_check['check_time']);
        Tpl::output("currency", $currency);
        Tpl::output("startline", $startline);
        Tpl::output("deadline", $deadline);

        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);

        $r = new ormReader();
        $sql = "SELECT SUM(trx_flag*trx_amount) begin_balance FROM partner_trx_api WHERE partner_id = $uid AND currency = '" . $currency . "' AND api_state > 10 AND is_check = 1";
        $begin_balance = $r->getOne($sql);
        $sql = "SELECT SUM(CASE trx_flag WHEN 1 THEN trx_amount ELSE 0 END) income_total,SUM(CASE trx_flag WHEN -1 THEN trx_amount ELSE 0 END) outcome_total FROM partner_trx_api WHERE partner_id = $uid AND currency = '" . $currency . "' AND api_state > 10 AND is_check = 0";
        $in_out = $r->getRow($sql);
        $income = $in_out['income_total'];
        $outcome = $in_out['outcome_total'];
        $system_balance = $begin_balance + $income - $outcome;
        $balance_detail = array(
            'system_balance' => $system_balance,
            'begin_balance' => $begin_balance,
            'income' => $income,
            'outcome' => $outcome,
        );
        Tpl::output("balance_detail", $balance_detail);
        Tpl::showpage('trace.check');
    }

    /**
     * 放款记录
     * @param $p
     * @return array
     */
    public function getTraceListOp($p)
    {
        $r = new ormReader();
        $uid = intval($p['uid']);
        $currency = $p['currency'];
        $d2 = $p['startline'] > Now() ? Now() : $p['startline'];
        $d3 = $p['deadline'] > Now() ? Now() : $p['deadline'];

        $where = "WHERE pta.partner_id = $uid AND pta.currency = '" . $currency . "' AND pta.api_state > 10 AND (pta.trx_time between '" . $d2 . "' AND '" . $d3 . "')";
        $sql = "SELECT pta.*,cm.display_name FROM partner_trx_api pta LEFT JOIN client_member cm ON pta.obj_guid = cm.obj_guid $where  ORDER BY pta.uid DESC";
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
            "currency" => $currency
        );
    }

    /**
     * 增加手工账
     * @param $p
     * @return result
     */
    public function addManualOp($p)
    {
        $class_partner = new partnerClass();
        $p['creator_id'] = $this->user_id;
        $p['creator_name'] = $this->user_name;
        $rt = $class_partner->addManual($p);
        return $rt;
    }

    /**
     * 修改手工账
     * @param $p
     * @return result
     */
    public function editManualOp($p)
    {
        $class_partner = new partnerClass();
        $rt = $class_partner->editManual($p);
        return $rt;
    }

    /**
     * 调账
     * @param $p
     * @return result
     */
    public function addAdjustOp($p)
    {
        $class_partner = new partnerClass();
        $p['creator_id'] = $this->user_id;
        $p['creator_name'] = $this->user_name;
        $rt = $class_partner->addManual($p);
        return $rt;
    }

    /**
     * 调账
     * @param $p
     * @return result
     */
    public function editAdjustOp($p)
    {
        $class_partner = new partnerClass();
        $rt = $class_partner->editManual($p);
        return $rt;
    }


    /**
     * 改变交易状态
     * @param $p
     * @return result
     */
    public function changeTraceStateOp($p)
    {
        $class_partner = new partnerClass();
        $rt = $class_partner->changeTraceState($p);
        return $rt;
    }

    /**
     * 对账记录
     * @param $p
     * @return result
     */
    public function addCheckTraceOp($p)
    {
        $class_partner = new partnerClass();
        $p['operator_id'] = $this->user_id;
        $p['operator_name'] = $this->user_name;
        $rt = $class_partner->addCheckTrace($p);
        return $rt;
    }

    /**
     * 查账记录
     */
    public function checkHistoryOp()
    {
        $m_partner = M('partner');
        $partner = $m_partner->find(array('uid' => intval($_GET['uid'])));
        if (empty($partner)) {
            showMessage('Invalid Id!');
        }
        Tpl::output("partner", $partner);

        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        Tpl::showpage('check.history');
    }

    /**
     * 获取对账列表
     * @param $p
     * @return array
     */
    public function getCheckHistoryListOp($p)
    {
        $partner_id = intval($p['uid']);
        $r = new ormReader();
        $sql = "SELECT * FROM partner_trace_check WHERE partner_id = $partner_id ORDER BY uid DESC";

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

    public function getCheckDataOp($p)
    {
        $uid = intval($p['uid']);
        $currency = $p['currency'];
        $r = new ormReader();
        $sql = "SELECT SUM(trx_flag*trx_amount) system_balance FROM partner_trx_api WHERE partner_id = $uid AND currency = '" . $currency . "' AND api_state > 10";
        $system_balance = $r->getOne($sql);
        $api_balance = $system_balance;//接口查询
        return new result(true, '', array('system_balance' => ncAmountFormat($system_balance, false, $currency), 'api_balance' => ncAmountFormat($api_balance, false, $currency), 'difference' => ncAmountFormat($system_balance - $api_balance, false, $currency), 'is_check' => $system_balance == $api_balance));
    }

    /**
     * 经销商
     */
    public function dealerOp()
    {
        Tpl::showpage('dealer');
    }
}
