<?php

class report_printControl extends back_office_baseControl
{
    public $limit_branch_id = 0;

    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_print");

        $limit_position = array(userPositionEnum::BRANCH_MANAGER, userPositionEnum::TELLER, userPositionEnum::CHIEF_TELLER);
        if (in_array($this->user_position, $limit_position)) {
            $this->limit_branch_id = $this->branch_id;
            Tpl::output("limit_branch_id", $this->branch_id);//这些用户限制只能查看自己branch的
        }
    }

    /**
     * 打印
     */
    public function printLoanListOp()
    {
        $search_text = trim($_GET['search_text']);
        $currency = trim($_GET['currency']);
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $branch_id = $_GET['branch_id'];
        $tpl = $_GET['tpl'];

        $filters = array(
            'search_text' => $search_text,
            'currency' => $currency,
            'date_start' => $date_start,
            'date_end' => $date_end,
            "branch_id" => $branch_id
        );

        $data = loanReportClass::getLoanList(1, 1000, $filters);

        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($search_text) {
            $filter_str .= "Search Text: <span style='font-weight: 600;margin-right: 10px;'>$search_text</span>";
        }
        if ($currency) {
            $filter_str .= "Currency: <span style='font-weight: 600;margin-right: 10px;'>$currency</span>";
        }
        if ($date_start) {
            $filter_str .= "Date: <span style='font-weight: 600;margin-right: 10px;'>$date_start -- $date_end</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }

    /**
     * 打印Loan Provision
     */
    public function printLoanProvisionOp()
    {
        $p = $_GET;
        $branch_id = trim($p['branch_id']);
        $currency = trim($p['currency']);
        $tpl = $_GET['tpl'];

        $filters = array(
            'branch_id' => $branch_id,
            'currency' => $currency,
        );
        $data = loanReportClass::getLoanProvisionData($filters);

        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($currency) {
            $filter_str .= "Currency: <span style='font-weight: 600;margin-right: 10px;'>$currency</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }

    /**
     * 打印Loan Provision
     */
    public function printLoanProvisionContractOp()
    {
        $p = $_GET;
        $branch_id = trim($p['branch_id']);
        $currency = trim($p['currency']);
        $tpl = $_GET['tpl'];

        $filters = array(
            'branch_id' => $branch_id,
            'currency' => $currency,
        );
        $data = loanReportClass::getLoanProvisionContractData($filters);

        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($currency) {
            $filter_str .= "Currency: <span style='font-weight: 600;margin-right: 10px;'>$currency</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }

    /**
     * 打印Loan Status
     */
    public function printLoanStatusOp()
    {
        $tpl = $_GET['tpl'];
        $currency_list = (new currencyEnum())->Dictionary();
        $loan_contract_state = (new loanContractStateEnum())->Dictionary();
        $data = loanReportClass::getLoanSummary(array("branch_id" => $this->limit_branch_id));
        $data['currency_list'] = $currency_list;
        $data['loan_contract_state'] = $loan_contract_state;
        Tpl::output('data', $data);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::showPage('report_print');
    }

    /**
     * 打印Loan Interest Rate
     */
    public function printLoanInterestRateOp()
    {
        $p = $_GET;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $branch_id = intval($p['branch_id']);
        $tpl = $p['tpl'];

        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            "branch_id" => $branch_id
        );
        $data = loanReportClass::getLoanInterestRateList($filters);

        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($date_start) {
            $filter_str .= "Date: <span style='font-weight: 600;margin-right: 10px;'>$date_start -- $date_end</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }

    /**
     * 打印Loan Size
     */
    public function printLoanSizeOp()
    {
        $p = $_GET;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $currency = $p['currency'];
        $branch_id = intval($p['branch_id']);
        $tpl = $p['tpl'];

        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currency' => $currency,
            'branch_id' => $branch_id
        );
        $data = loanReportClass::getLoanSizeList($filters);

        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($currency) {
            $filter_str .= "Currency: <span style='font-weight: 600;margin-right: 10px;'>$currency</span>";
        }

        if ($date_start) {
            $filter_str .= "Date: <span style='font-weight: 600;margin-right: 10px;'>$date_start -- $date_end</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }

    public function printLoanInvestmentRatioOp()
    {
        $p = $_GET;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $currency = $p['currency'];
        $branch_id = intval($p['branch_id']);
        $tpl = $p['tpl'];
        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currency' => $currency,
            'branch_id' => $branch_id
        );
        $data = loanReportClass::getLoanInvestmentRatioList($filters);
        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($currency) {
            $filter_str .= "Currency: <span style='font-weight: 600;margin-right: 10px;'>$currency</span>";
        }

        if ($date_start) {
            $filter_str .= "Date: <span style='font-weight: 600;margin-right: 10px;'>$date_start -- $date_end</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }
    /**
     * 打印 Overdue
     */
    public function printOverdueOp()
    {
        $p = $_GET;
        $branch_id = trim($p['branch_id']);
        $day       = trim($p['day']);
        $currency  = trim($p['currency']);
        $tpl = $_GET['tpl'];

        $filters = array(
            'branch_id' => $branch_id,
            'day' => $day,
            'currency' => $currency,
        );

        $data = loanReportAnalysisClass::getAllOverdueContractData($filters);

        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($currency) {
            $filter_str .= "Currency: <span style='font-weight: 600;margin-right: 10px;'>$currency</span>";
        }
        if ($day) {
            $filter_str .= "Day:  <span style='font-weight: 600;margin-right: 10px;'>$day</span>   ";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }
    /**
     * 打印 GroupBy date
     */
    public function printDayDataOp()
    {
        $p = $_GET;
        $category = trim($p['category']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $branch_id = $p['branch_id'];

        $tpl = $_GET['tpl'];

        $filter = array(
            'category' => $category,
            'date_start' => $date_start,
            'date_end' => $date_end,
            "branch_id" => $branch_id
        );

        $loan = loanReportAnalysisClass::getDayDataOfLoan($filter);
        $repayment = loanReportAnalysisClass::getDayDataOfRepayment($filter);
        $pending_repayment = loanReportAnalysisClass::getDayDataOfPendingRepayment($filter);
        $data = array_merge_recursive($loan,$repayment,$pending_repayment);
        ksort($data);
        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($date_start) {
            $filter_str .= "Date: <span style='font-weight: 600;margin-right: 10px;'>$date_start -- $date_end</span>";
        }
        if ($category) {
            $filter_str .= "Category:  <span style='font-weight: 600;margin-right: 10px;'>$category</span>   ";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }
    //获取category_id 私密方法
    private function GetCategoryId(){
        //获取super loan的cate-id
        $sql="SELECT * FROM loan_category WHERE is_special=1 AND special_key='".specialLoanCateKeyEnum::FIX_REPAYMENT_DATE."'";
        $r=new ormReader();
        $result = $r->getRow($sql);
        if( $result ){
            $category_id = $result['uid'];
        }
        return $category_id;
    }
    /**
     * 打印 Member Credit
     */
    public function printMemberCreditOp()
    {
        $p = $_GET;
        $search_text = trim($p['search_text']);
        $branch_id = $p['branch_id'];
        $category_id = $this->GetCategoryId();

        $tpl = $_GET['tpl'];

        $filter = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'category_id' => $category_id
        );

        $data = superLoanReportClass::getCreditList($filter);
        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($search_text) {
            $filter_str .= "Search text: <span style='font-weight: 600;margin-right: 10px;'>$search_text</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }
    /**
     * 打印 Loan Transaction
     */
    public function printLoanTransactionOp()
    {
        $p = $_GET;
        $search_text = trim($p['search_text']);
        $branch_id = $p['branch_id'];
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $category_id = $this->GetCategoryId();

        $tpl = $_GET['tpl'];

        $filter = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'category_id' => $category_id
        );

        $data = superLoanReportClass::getTrxLoanList($filter);
        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($search_text) {
            $filter_str .= "Search text: <span style='font-weight: 600;margin-right: 10px;'>$search_text</span>";
        }
        if ($date_start) {
            $filter_str .= "Date: <span style='font-weight: 600;margin-right: 10px;'>$date_start -- $date_end</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }
    /**
     * 打印 Loan Transaction
     */
    public function printRepayTransactionOp()
    {
        $p = $_GET;
        $search_text = trim($p['search_text']);
        $branch_id = $p['branch_id'];
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $category_id = $this->GetCategoryId();

        $tpl = $_GET['tpl'];

        $filter = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'category_id' => $category_id
        );

        $data = superLoanReportClass::getTrxRepayList($filter);

        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($search_text) {
            $filter_str .= "Search text: <span style='font-weight: 600;margin-right: 10px;'>$search_text</span>";
        }
        if ($date_start) {
            $filter_str .= "Date: <span style='font-weight: 600;margin-right: 10px;'>$date_start -- $date_end</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }
    /**
     * 打印 Daily Report
     */
    public function printDailyReportOp()
    {
        $p = $_GET;
        $day = $p['date_end']?:date('Y-m-d');
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $branch_id=intval($p['branch_id']);
        $currency = $p['currency'];

        $tpl = $_GET['tpl'];

        $filters = array(
            'loan_category_id' => $this->GetCategoryId(),
            'branch_id' => $branch_id,
            'currency' => $currency,
            'search_text'=>$search_text
        );

        $res = superLoanReportClass::getDailyReportData($day,$pageNumber,$pageSize,$filters);
        $list = $res->data;
        $data['data'] = $list;
        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($search_text) {
            $filter_str .= "Search text: <span style='font-weight: 600;margin-right: 10px;'>$search_text</span>";
        }
        if ($currency) {
            $filter_str .= "Currency: <span style='font-weight: 600;margin-right: 10px;'>$currency</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }
    /**
     * 打印 Master Client
     */
    public function printMasterClientOp()
    {
        $p = $_GET;
        $search_text = trim($p['search_text']);
        $page_number = intval($p['pageNumber']) ?: 1;
        $page_size = intval($p['pageSize']) ?: 20;
        $branch_id = $p['branch_id'];
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $currency = $p['currency'];

        $tpl = $_GET['tpl'];

        $filter = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currency' => $currency,
        );

        $data = loanReportClass::getMasterClientList($page_number,$page_size,$filter);

        //筛选条件
        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: <span style='font-weight: 600;margin-right: 10px;'>$branch_name</span>";

        if ($search_text) {
            $filter_str .= "Search text: <span style='font-weight: 600;margin-right: 10px;'>$search_text</span>";
        }
        if ($currency) {
            $filter_str .= "Currency: <span style='font-weight: 600;margin-right: 10px;'>$currency</span>";
        }
        if ($date_start) {
            $filter_str .= "Date: <span style='font-weight: 600;margin-right: 10px;'>$date_start -- $date_end</span>";
        }

        Tpl::output('filter', $filter_str);
        Tpl::output('tpl', $tpl);
        Tpl::output('is_print', true);
        Tpl::output('data', $data);
        Tpl::showPage('report_print');
    }

}
