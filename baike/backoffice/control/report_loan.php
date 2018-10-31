<?php

class report_loanControl extends back_office_baseControl
{
    public $limit_branch_id = 0;

    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_loan");

        $limit_position = array(userPositionEnum::BRANCH_MANAGER, userPositionEnum::TELLER, userPositionEnum::CHIEF_TELLER);
        if (in_array($this->user_position, $limit_position)) {
            $this->limit_branch_id = $this->branch_id;
            Tpl::output("limit_branch_id", $this->branch_id);//这些用户限制只能查看自己branch的
        }
    }


    public function getSubMenuList($active_menu=null)
    {
        $menu_list = array(
            'list_of_loan' => array(
                'title' => 'List of loan',
                'url' => getUrl('report_loan', 'loan', array(), false, BACK_OFFICE_SITE_URL),
                'is_active' => 0
            ),
            'loan_provision' => array(
                'title' => 'Loan Provision',
                'url' => getUrl('report_loan', 'loanProvision', array(), false, BACK_OFFICE_SITE_URL),
                'is_active' => 0
            ),
            'loan_status' => array(
                'title' => 'Loan by Status',
                'url' => getUrl('report_loan', 'loanStatus', array(), false, BACK_OFFICE_SITE_URL),
                'is_active' => 0
            ),
            'interest_rate' => array(
                'title' => 'Loan by Interest Rate',
                'url' => getUrl('report_loan', 'interestRate', array(), false, BACK_OFFICE_SITE_URL),
                'is_active' => 0
            ),
            'loan_size' => array(
                'title' => 'Loan by Size',
                'url' => getUrl('report_loan', 'loanSize', array(), false, BACK_OFFICE_SITE_URL),
                'is_active' => 0
            ),
            'investment_ratio' => array(
                'title' => 'Loan Investment Ratio',
                'url' => getUrl('report_loan', 'investmentRatio', array(), false, BACK_OFFICE_SITE_URL),
                'is_active' => 0
            ),
            'master_client' => array(
                'title' => 'Master Client',
                'url' => getUrl('report_loan', 'masterClient', array(), false, BACK_OFFICE_SITE_URL),
                'is_active' => 0
            ),

        );
        if( $menu_list[$active_menu] ){
            $menu_list[$active_menu]['is_active'] = 1;
        }

        return $menu_list;
    }

    /**
     * Loan list
     */
    public function loanOp()
    {
        $menu_list = $this->getSubMenuList('list_of_loan');
        Tpl::output('sub_menu_list',$menu_list);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);

        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);


        Tpl::showPage('loan');
    }

    /**
     * 获取贷款列表
     * @param $p
     * @return array
     */
    public function getLoanListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $currency = trim($p['currency']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $branch_id = $p['branch_id'];

        $filters = array(
            'search_text' => $search_text,
            'currency' => $currency,
            'date_start' => $date_start,
            'date_end' => $date_end,
            "branch_id" => $branch_id
        );

        $data = loanReportClass::getLoanList($pageNumber, $pageSize, $filters);
        return $data;
    }

    public function loanProvisionOp()
    {
        $menu_list = $this->getSubMenuList('loan_provision');
        Tpl::output('sub_menu_list',$menu_list);

        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage('loan.provision');
    }

    public function getLoanProvisionOp($p)
    {
        $branch_id = trim($p['branch_id']);
        $currency = trim($p['currency']);

        $filters = array(
            'branch_id' => $branch_id,
            'currency' => $currency,
        );
        $data = loanReportClass::getLoanProvisionData($filters);
        return $data;
    }

    public function loanProvisionContractOp()
    {
        $menu_list = $this->getSubMenuList('loan_provision');
        Tpl::output('sub_menu_list',$menu_list);

        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage('loan.provision.contract');
    }

    public function getLoanProvisionContractOp($p)
    {
        $branch_id = trim($p['branch_id']);
        $currency = trim($p['currency']);

        $filters = array(
            'branch_id' => $branch_id,
            'currency' => $currency,
        );
        $data = loanReportClass::getLoanProvisionContractData($filters);
        return $data;
    }

    /**
     * 贷款情况统计
     */
    public function loanStatusOp()
    {
        $menu_list = $this->getSubMenuList('loan_status');
        Tpl::output('sub_menu_list',$menu_list);

        $currency_list = (new currencyEnum())->Dictionary();
        $loan_contract_state = (new loanContractStateEnum())->Dictionary();
        $data = loanReportClass::getLoanSummary(array("branch_id" => $this->limit_branch_id));
        $data['currency_list'] = $currency_list;
        $data['loan_contract_state'] = $loan_contract_state;
        Tpl::output('data', $data);
        Tpl::showPage('loan.status');
    }

    public function interestRateOp()
    {
        $menu_list = $this->getSubMenuList('interest_rate');
        Tpl::output('sub_menu_list',$menu_list);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -365))),
            "date_end" => date('Y-m-d')
        );

        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);

        Tpl::output("condition", $condition);
        Tpl::showPage('loan.interest.rate');
    }

    public function getLoanInterestRateListOp($p)
    {
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            "branch_id" => $p['branch_id']
        );
        $data = loanReportClass::getLoanInterestRateList($filters);
        return $data;
    }

    public function loanSizeOp()
    {
        $menu_list = $this->getSubMenuList('loan_size');
        Tpl::output('sub_menu_list',$menu_list);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -365))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);

        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output('currency_list', $currency_list);

        Tpl::showPage('loan.size');
    }

    public function getLoanSizeListOp($p)
    {
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $currency = $p['currency'];
        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currency' => $currency,
            'branch_id' => $p['branch_id']
        );
        $data = loanReportClass::getLoanSizeList($filters);
        return $data;
    }

    public function investmentRatioOp()
    {
        $menu_list = $this->getSubMenuList('investment_ratio');
        Tpl::output('sub_menu_list',$menu_list);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -365))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output('currency_list', $currency_list);

        Tpl::showPage('loan.investment.ratio');
    }

    public function getLoanInvestmentRatioListOp($p)
    {
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $currency = $p['currency'];
        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currency' => $currency,
            'branch_id' => $p['branch_id']
        );
        $data = loanReportClass::getLoanInvestmentRatioList($filters);
        return $data;
    }

    public function masterClientOp()
    {
        $menu_list = $this->getSubMenuList('master_client');
        Tpl::output('sub_menu_list',$menu_list);

        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);

        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output('currency_list', $currency_list);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime('-1 month')),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        Tpl::showPage('master.client');
    }

    public function getMasterClientListOp($p)
    {
        $page_number = $p['pageNumber']?:1;
        $page_size = $p['pageSize']?:20;
        $data = loanReportClass::getMasterClientList($page_number,$page_size,$p);
        return $data;
    }

}
