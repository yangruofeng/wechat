<?php

class report_exportControl extends back_office_baseControl
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

    /**
     * 导出loan
     */
    public function exportLoanToExcelOp()
    {
        $search_text = trim($_GET['search_text']);
        $currency = trim($_GET['currency']);
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $branch_id = $_GET['branch_id'];

        $filters = array(
            'search_text' => $search_text,
            'currency' => $currency,
            'date_start' => $date_start,
            'date_end' => $date_end,
            "branch_id" => $branch_id
        );

        $title = 'List of loan';
        $cellName = array(
            array('virtual_contract_sn', 'Account', 23, 'LEFT'),
            array('obj_guid', 'CID', 0, 'LEFT'),
            array('display_name', '	Customer Name', 12, 'LEFT'),
            array('sub_product_code', '	Prod./GL/St.', 10, 'LEFT'),
            array('apply_amount', '	Principal Disbursed', 10, 'LEFT'),
            array('current_balance', 'Current Balance', 10, 'LEFT'),
            array('overdue_balance', 'Overdue Bal.', 10, 'LEFT'),
            array('overdue_interest', 'Overdue Int.', 10, 'LEFT'),
            array('start_date', 'Opened', 12, 'LEFT'),
            array('end_date', 'Maturing', 12, 'LEFT'),
            array('loan_period_value', 'InstNo', 0, 'LEFT'),
            array('interest_rate', 'Int Rate', 0, 'LEFT'),
            array('last_transaction', 'Last Trn', 0, 'LEFT'),
            array('last_transaction_date', 'LastTrn Dt', 12, 'LEFT'),
            array('paid_principal', 'Pri Paid', 0, 'LEFT'),
            array('paid_interest', 'Int Paid', 0, 'LEFT'),
            array('id1', 'Province', 10, 'LEFT'),
            array('id2', 'District', 10, 'LEFT'),
            array('id3', 'Commune', 10, 'LEFT'),
            array('id4', 'Village', 10, 'LEFT'),
            array('group', 'Group', 10, 'LEFT'),
            array('street', 'Street', 10, 'LEFT'),
            array('house_number', 'House', 10, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($search_text) {
            $filter_str .= "Search text: $search_text    ";
        }
        if ($currency) {
            $filter_str .= "Currency: $currency    ";
        }
        if ($date_start) {
            $filter_str .= "Date: $date_start -- $date_end";
        }

        $data = loanReportClass::getLoanList(1, 1000, $filters);
        $list = $data['data'];
        $format_list = array();
        foreach ($list as $k => $v) {
            $v['obj_guid'] = $v['obj_guid'] ?: generateGuid($v['uid'], objGuidTypeEnum::CLIENT_MEMBER);
            $v['sub_product_code'] .= '/AB/11';
            $v['start_date'] = dateFormat($v['start_date']);
            $v['end_date'] = dateFormat($v['end_date']);
            $v['last_transaction_date'] = dateFormat($v['last_transaction_date']);
            $v['loan_period_value'] .= ucwords($v['loan_period_unit']);
            $v['interest_rate'] .= $v['interest_rate_type'] == 1 ? '' : '%';
            $v['apply_amount'] = ncPriceFormat($v['apply_amount']);
            $v['overdue_balance'] = ncPriceFormat($v['overdue_balance']);
            $v['paid_principal'] = ncPriceFormat($v['paid_principal']);
            $v['current_balance'] = ncPriceFormat($v['current_balance']);
            $v['overdue_interest'] = ncPriceFormat($v['overdue_interest']);
            $v['paid_interest'] = ncPriceFormat($v['paid_interest']);
            $v['last_transaction'] = $v['last_transaction'] ? ncPriceFormat($v['last_transaction']) : '';
            $format_list[]['data'] = $v;
        }

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }

    /**
     * 导出Loan Provision
     */
    public function exportLoanProvisionOp()
    {
        $p = $_GET;
        $branch_id = trim($p['branch_id']);
        $currency = trim($p['currency']);

        $filters = array(
            'branch_id' => $branch_id,
            'currency' => $currency,
        );

        $title = 'Loan Provision';
        $cellName = array(
            array('classification', 'Provision Classification', 20, 'LEFT'),
            array('login_code', 'Borrower\'s Name', 12, 'LEFT'),
            array('overdue_days', 'Overdue Days', 0, 'LEFT'),
            array('number', 'Number of loans', 10, 'LEFT'),
            array('loan_balance', 'Loan Balance', 10, 'LEFT'),
            array('principal', 'Overdue Balance', 10, 'LEFT'),
            array('interest', 'Accrued Interest', 10, 'LEFT'),
            array('rate', 'Provisions(Rate(%))', 12, 'LEFT'),
            array('amount', 'Provisions(Amount)', 12, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($currency) {
            $filter_str .= "Currency: $currency    ";
        }

        $data = loanReportClass::getLoanProvisionData($filters);
        $format_list = array();
        foreach ($data as $k => $v) {
            if ($k == 'total') {
                $v = $this->formatLoanProvisionData($v);
                $format_list[] = array('is_bold' => 1, 'data' => $v);
            } else {
                if ($k == 'less') {
                    $format_list[] = array(
                        'is_merge_row' => 1,
                        'is_bold' => 1,
                        'content' => 'Term Less Than 365 Days',
                    );
                } else {
                    $format_list[] = array(
                        'is_merge_row' => 1,
                        'is_bold' => 1,
                        'content' => 'Term Greater Than 365 Days',
                    );
                }
                foreach ($v as $ck => $cv) {
                    if (!$cv) continue;
                    $cv['classification'] = $ck;
                    $format_list[]['data'] = $this->formatLoanProvisionData($cv);
                }
            }
        }

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }

    /**
     * 格式化参数
     * @param $data
     * @return mixed
     */
    private function formatLoanProvisionData($data)
    {
        $data['number'] = $data['contract_id'] ?: '';
        $data['overdue_days'] = $data['days'] > 0 ? $data['days'] : 0;
        $data['loan_balance'] = $data['loan_balance'] ? ncPriceFormat($data['loan_balance']) : '-';
        $data['principal'] = $data['principal'] ? ncPriceFormat($data['principal']) : '-';
        $data['principal'] = $data['interest'] ? ncPriceFormat($data['interest']) : '-';
        $data['rate'] = $data['rate'];
        $data['amount'] = ncPriceFormat($data['amount']);
        return $data;
    }

    /**
     * 导出Loan Provision
     */
    public function exportLoanProvisionContractOp()
    {
        $p = $_GET;
        $branch_id = trim($p['branch_id']);
        $currency = trim($p['currency']);

        $filters = array(
            'branch_id' => $branch_id,
            'currency' => $currency,
        );

        $title = 'Loan Provision';
        $cellName = array(
            array('classification', 'Provision Classification', 20, 'LEFT'),
            array('login_code', 'Borrower\'s Name', 12, 'LEFT'),
            array('overdue_days', 'Overdue Days', 0, 'LEFT'),
            array('number', 'Number of loans', 10, 'LEFT'),
            array('loan_balance', 'Loan Balance', 10, 'LEFT'),
            array('principal', 'Overdue Balance', 10, 'LEFT'),
            array('interest', 'Accrued Interest', 10, 'LEFT'),
            array('rate', 'Provisions(Rate(%))', 12, 'LEFT'),
            array('amount', 'Provisions(Amount)', 12, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($currency) {
            $filter_str .= "Currency: $currency    ";
        }

        $data = loanReportClass::getLoanProvisionContractData($filters);
        $format_list = array();
        foreach ($data as $k => $v) {
            if ($k == 'less') {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 1,
                    'content' => 'Term Less Than 365 Days',
                );
            } else {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 1,
                    'content' => 'Term Greater Than 365 Days',
                );
            }
            foreach ($v as $ck => $cv) {
                $cv['total']['classification'] = $ck;
                $cv['total'] = $this->formatLoanProvisionData($cv['total']);
                $format_list[] = array('is_bold' => 1, 'data' => $cv['total']);
                foreach ($cv['contract'] as $key => $value) {
                    $value['classification'] = $value['contract_sn'];
                    $format_list[]['data'] = $this->formatLoanProvisionData($value);
                }
            }
        }

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }

    /**
     * 导出Loan Status
     */
    public function exportLoanStatusOp()
    {
        $currency_list = (new currencyEnum())->Dictionary();

        $title = 'Loan By Status';
        $cellName = array(
            array('status', 'Account Status', 22, 'LEFT'),
            array('loan_count', 'No.of Account', 10, 'LEFT'),
        );
        $total_type = array(
            'current_balance' => 'Current Balance',
            'overdue_balance' => 'Overdue Bal.',
            'principal_disbursed' => 'Principal Disabused',
        );
        foreach ($total_type as $k1 => $v1) {
            foreach ($currency_list as $k2 => $currency) {
                $cellName[] = array($k1 . '_' . $k2, "$v1($currency)", 17, 'LEFT');
            }
        }


        $loan_contract_state = (new loanContractStateEnum())->Dictionary();
        $data = loanReportClass::getLoanSummary(array("branch_id" => $this->limit_branch_id));
        $loan_summary = $data['loan_summary'];
        $format_list = array();
        foreach ($loan_contract_state as $key => $state) {
            if ($key <= loanContractStateEnum::PENDING_APPROVAL) continue;
            $val = $loan_summary[$key];
            $val['status'] = 'Status ' . $key . '-' . $state;
            foreach ($currency_list as $k2 => $currency) {
                $val['current_balance_' . $k2] = ncPriceFormat($val['current_balance'][$k2]);
                $val['overdue_balance_' . $k2] = ncPriceFormat($val['overdue_balance'][$k2]);
                $val['principal_disbursed_' . $k2] = ncPriceFormat($val['principal_disbursed'][$k2]);
            }
            $format_list[]['data'] = $val;
        }
        $total = array(
            'status' => 'Total',
            'loan_count' => $data['loan_count_total'],
        );
        foreach ($currency_list as $k2 => $currency) {
            $total['current_balance_' . $k2] = ncPriceFormat($data['current_balance_total'][$k2]);
            $total['overdue_balance_' . $k2] = ncPriceFormat($data['overdue_balance_total'][$k2]);
            $total['principal_disbursed_' . $k2] = ncPriceFormat($data['principal_disbursed_total'][$k2]);
        }
        $format_list[] = array(
            'is_bold' => 1,
            'data' => $total,
        );

        common::exportDataToExcel($title, $cellName, $format_list);
    }

    /**
     * 导出Loan Interest Rate
     */
    public function exportLoanInterestRateOp()
    {
        $p = $_GET;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $branch_id = intval($p['branch_id']);

        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            "branch_id" => $branch_id,
        );
        $currency_list = (new currencyEnum())->Dictionary();

        $title = 'Loan By Interest Rate';
        $cellName = array(
            array('term_bracket', 'Term Bracket', 15, 'LEFT'),
            array('loan_count', '# Accts', 10, 'LEFT'),
        );
        foreach ($currency_list as $key => $currency) {
            $cellName[] = array('amount_' . $key, "Amount-$currency", 17, 'LEFT');
        }

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";
        if ($date_start) {
            $filter_str .= "Date: $date_start -- $date_end";
        }


        $data = loanReportClass::getLoanInterestRateList($filters);
        $format_list = array();
        foreach ($data['data'] as $val) {
            $min = $val['min'];
            $max = $val['max'];
            $term = array();
            if ($max == 0 && $min > 0) {
                $term['term_bracket'] = '> ' . ncPriceFormat($min);
            } else if (!isset($min)) {
                $term['term_bracket'] = '0.00 + 0.00';
            } else {
                $term['term_bracket'] = ncPriceFormat($min) . ' - ' . ncPriceFormat($max);
            }
            $term['loan_count'] = $val['report']['loan_count'];
            foreach ($currency_list as $key => $currency) {
                $term['amount_' . $key] = ncPriceFormat($val['report']['loan_amount_' . $key]);
            }
            $format_list[]['data'] = $term;
        }
        $total = array(
            'term_bracket' => 'Total',
            'loan_count' => $data['amount_total']['loan_count'],
        );
        foreach ($currency_list as $key => $currency) {
            $total['amount_' . $key] = ncPriceFormat($data['amount_total']['loan_amount_' . $key]);
        }
        $format_list[] = array(
            'is_bold' => 1,
            'data' => $total,
        );

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }

    /**
     * 导出Loan Size
     */
    public function exportLoanSizeOp()
    {
        $p = $_GET;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $currency = $p['currency'];
        $branch_id = intval($p['branch_id']);
        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currency' => $currency,
            'branch_id' => $branch_id
        );

        $title = 'Loan By Size';
        $cellName = array(
            array('term_bracket', 'Term Bracket', 15, 'LEFT'),
            array('loan_count', '# Accts', 10, 'LEFT'),
            array('loan_amount', 'Amount', 15, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";
        if ($currency) {
            $filter_str .= "Currency: $currency    ";
        }
        if ($date_start) {
            $filter_str .= "Date: $date_start -- $date_end";
        }

        $data = loanReportClass::getLoanSizeList($filters);
        $format_list = array();
        foreach ($data['data'] as $val) {
            $min = $val['min'];
            $max = $val['max'];
            $term = array();
            if ($max == 0 && $min > 0) {
                $term['term_bracket'] = '> ' . ncPriceFormat($min);
            } else if (!isset($min)) {
                $term['term_bracket'] = '0.00 + 0.00';
            } else {
                $term['term_bracket'] = ncPriceFormat($min) . ' - ' . ncPriceFormat($max);
            }
            $term['loan_count'] = $val['report']['loan_count'];
            $term['loan_amount'] = ncPriceFormat($val['report']['loan_amount']);
            $format_list[]['data'] = $term;
        }
        $total = array(
            'term_bracket' => 'Total',
            'loan_count' => $data['amount_total']['loan_count'],
            'loan_amount' => ncPriceFormat($data['amount_total']['loan_amount']),
        );
        $format_list[] = array(
            'is_bold' => 1,
            'data' => $total,
        );
        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }

    public function exportLoanInvestmentRatioOp()
    {
        $p = $_GET;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $currency = $p['currency'];
        $branch_id = intval($p['branch_id']);
        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            'currency' => $currency,
            'branch_id' => $branch_id
        );

        $title = 'Loan By Size';
        $cellName = array(
            array('sub_product_code', 'Product Code', 0, 'LEFT'),
            array('sub_product_name', 'Description', 12, 'LEFT'),
            array('loan_count', 'No.of Accounts', 12, 'LEFT'),
            array('loan_amount', 'Balance', 12, 'LEFT'),
            array('investment_ratio', 'Investment Ratio(%)', 12, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";
        if ($currency) {
            $filter_str .= "Currency: $currency    ";
        }
        if ($date_start) {
            $filter_str .= "Date: $date_start -- $date_end";
        }

        $data = loanReportClass::getLoanInvestmentRatioList($filters);
        $format_list = array();
        foreach ($data['sub_product_list'] as $product) {
            $term = array();
            $row = $data['data'][$product['uid']];
            $term['sub_product_code'] = $product['sub_product_code'];
            $term['sub_product_name'] = $product['sub_product_name'];
            $term['loan_count'] = $row['loan_count'];
            $term['loan_amount'] = ncPriceFormat($row['loan_amount']);
            $term['investment_ratio'] = ncPriceFormat($row['investment_ratio']);
            $format_list[]['data'] = $term;
        }

        $total = array(
            'sub_product_name' => 'Total',
            'loan_count' => $data['amount_total']['loan_count'],
            'loan_amount' => ncPriceFormat($data['amount_total']['loan_amount']),
            'investment_ratio' => 100,
        );
        $format_list[] = array(
            'is_bold' => 1,
            'data' => $total,
        );
        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }

    /**
     * 导出Overdue 格式化参数
     * @param $data
     * @return mixed
     */
    private function formatOverdueData($data)
    {
        $data['display_name'] = $data['display_name'] ?: '';
        $data['contract_sn'] = $data['contract_sn'] ?: 0;
        $data['principal_balance'] = $data['principal_balance'] ?: '';
        $data['apply_amount'] = $data['apply_amount'] ?: '';
        $data['overdue_day'] = $data['overdue_day'] ?: '';
        return $data;
    }

    /**
     * 导出Overdue
     */
    public function exportOverdueOp()
    {
        $branch_id = $_GET['branch_id'];
        $day = $_GET['day'];
        $currency = trim($_GET['currency']);

        $filters = array(
            'day' => $day,
            "branch_id" => $branch_id,
            'currency' => $currency
        );

        $title = 'Overdue';
        $cellName = array(
            array('display_name', 'Name', 0, 'LEFT'),
            array('contract_sn', 'Account', 12, 'LEFT'),
            array('principal_balance', 'Balance', 10, 'LEFT'),
            array('apply_amount', 'Disb Amt', 10, 'LEFT'),
            array('overdue_day', 'Day Late', 10, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($currency) {
            $filter_str .= "Currency: $currency    ";
        }
        if ($day) {
            $filter_str .= "Day: $day   ";
        }

        $data = loanReportAnalysisClass::getAllOverdueContractData($filters);

        //处理数据格式 total统计
        foreach ($data as $k => $v) {
            if (empty($v['list'])) {
                $data[$k]['total']['display_name'] = "Total";
                $data[$k]['total']['contract_sn'] = "NULL";
            } else {
                foreach ($v['list'] as $key => $value) {
                    $data[$k]['total']['display_name'] = "Total";
                    $data[$k]['total']['contract_sn'] += 1;
                    $data[$k]['total']['principal_balance'] += $value['principal_balance'];
                    $data[$k]['total']['apply_amount'] += $value['apply_amount'];
                }
            }


        }

        $format_list = array();
        foreach ($data as $k => $v) {
            if ($k == 'range1_6') {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 1,
                    'content' => 'Day Late: 1-6 Days',
                );
            } elseif ($k == 'range7_14') {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 1,
                    'content' => 'Day Late: 7-14 Days',
                );
            } elseif ($k == 'range15_29') {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 1,
                    'content' => 'Day Late: 15-29 Days',
                );
            } elseif ($k == 'range30_59') {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 1,
                    'content' => 'Day Late: 30-59 Days',
                );
            } elseif ($k == 'range60_89') {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 1,
                    'content' => 'Day Late: 60_89 Days',
                );
            } elseif ($k == 'range90_179') {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 1,
                    'content' => 'Day Late: 90_179 Days',
                );
            } elseif ($k == 'range180_359') {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 1,
                    'content' => 'Day Late: 180_359 Days',
                );
            } else {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 1,
                    'content' => 'Day Late: >=360 Days',
                );
            }
            foreach ($v as $ck => $cv) {
                //统计数据
                if ($ck == 'total') {
                    $cv['total'] = $v['total'];
                    $re = $this->formatOverdueData($cv['total']);
                    $format_list[] = array('is_bold' => 1, 'data' => $re);
                }
                //列表数据
                if ($ck == 'list') {
                    $cv['list'] = $v['list'];
                    foreach ($cv['list'] as $key => $value) {
                        $format_list[]['data'] = $this->formatOverdueData($value);
                    }
                }
            }
        }
        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }

    //导出DayData     页面按钮：groupBy
    public function exportDayDataOp()
    {
        $category = trim($_GET['category']);
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $branch_id = $_GET['branch_id'];

        $filter = array(
            'category' => $category,
            'date_start' => $date_start,
            'date_end' => $date_end,
            "branch_id" => $branch_id
        );

        $title = 'GroupBy Date';

        $cellName = array(
            array('day', 'day', 0, 'LEFT'),
            array('loan_client', 'Loan client', 20, 'LEFT'),
            array('loan_contract', 'Loan contract', 20, 'LEFT'),
            array('loan_usd', 'Loan usd', 20, 'LEFT'),
            array('loan_khr', 'Loan khr', 20, 'LEFT'),

            array('repayment_client', 'Repayment client', 20, 'LEFT'),
            array('repayment_contract', 'Repayment contract', 20, 'LEFT'),
            array('repayment_usd', 'Repayment usd', 20, 'LEFT'),
            array('repayment_khr', 'Repayment khr', 20, 'LEFT'),

            array('pending_repayment_client', 'Pending repayment client', 20, 'LEFT'),
            array('pending_repayment_contract', 'Pending repayment contract', 20, 'LEFT'),
            array('pending_repayment_usd', 'Pending repayment usd', 20, 'LEFT'),
            array('pending_repayment_khr', 'Pending repayment khr', 20, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($date_start) {
            $filter_str .= "Date: $date_start -- $date_end";
        }
        //获取数据
        $loan = loanReportAnalysisClass::getDayDataOfLoan($filter);
        $repayment = loanReportAnalysisClass::getDayDataOfRepayment($filter);
        $pending_repayment = loanReportAnalysisClass::getDayDataOfPendingRepayment($filter);
        $data = array_merge_recursive($loan, $repayment, $pending_repayment);
        ksort($data);
        //处理数据格式 total
        foreach ($data as $k => $v) {
            $tmp_arr = array("loan", "repayment", "pending_repayment");
            foreach ($tmp_arr as $tmp_i) {
                $data['Total'][$tmp_i]['client_count'] += $v[$tmp_i]['client_count'] ?: 0;
                $data['Total'][$tmp_i]['contract_count'] += $v[$tmp_i]['contract_count'] ?: 0;
                $data['Total'][$tmp_i]['amount']['USD'] += $v[$tmp_i]['amount'][currencyEnum::USD] ?: 0;
                $data['Total'][$tmp_i]['amount']['KHR'] += $v[$tmp_i]['amount'][currencyEnum::KHR] ?: 0;
            }
        }

        $format_list = array();
        foreach ($data as $k => $v) {

            $temp = array();
            $temp['day'] = $k;
            $temp['loan_client'] = $v['loan']['client_count'] ?: 0;
            $temp['loan_contract'] = $v['loan']['contract_count'] ?: 0;
            $temp['loan_usd'] = $v['loan']['amount'][currencyEnum::USD] ?: 0;
            $temp['loan_khr'] = $v['loan']['amount'][currencyEnum::KHR] ?: 0;

            $temp['repayment_client'] = $v['repayment']['client_count'] ?: 0;
            $temp['repayment_contract'] = $v['repayment']['contract_count'] ?: 0;
            $temp['repayment_usd'] = $v['repayment']['amount'][currencyEnum::USD] ?: 0;
            $temp['repayment_khr'] = $v['repayment']['amount'][currencyEnum::KHR] ?: 0;

            $temp['pending_repayment_client'] = $v['pending_repayment']['client_count'] ?: 0;
            $temp['pending_repayment_contract'] = $v['pending_repayment']['contract_count'] ?: 0;
            $temp['pending_repayment_usd'] = $v['pending_repayment']['amount'][currencyEnum::USD] ?: 0;
            $temp['pending_repayment_khr'] = $v['pending_repayment']['amount'][currencyEnum::KHR] ?: 0;

            //合集
            $format_list[]['data'] = $temp;
        }

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }

    //导出DayAlarm   暂时跳过  未处理完毕
    public function exportDayAlarmOp()
    {
        $category = trim($_GET['category']);
        $branch_id = $_GET['branch_id'];

        $filter = array(
            'category' => $category,
            "branch_id" => $branch_id
        );

        $title = 'PendingRepay Alarm';
        $cellName = array(
            array('contract_sn', 'Contract Sn', 15, 'LEFT'),
            array('display_name', 'Client Name', 15, 'LEFT'),
            array('currency', 'Currency', 10, 'LEFT'),
            array('apply_amount', 'Apply Amount', 10, 'LEFT'),
            array('receivable_principal', 'Receivable Principal', 15, 'LEFT'),
            array('ref_amount', 'Receivable Total', 15, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($category) {
            $filter_str .= "Category: " . $category . "    ";
        }
        //获取数据
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $after_tomorrow = date('Y-m-d', strtotime('+2 day'));
        $today_repayment = loanReportAnalysisClass::getDayAlarmByDate($today, $filter);
        $tomorrow_repayment = loanReportAnalysisClass::getDayAlarmByDate($tomorrow, $filter);
        $after_tomorrow_repayment = loanReportAnalysisClass::getDayAlarmByDate($after_tomorrow, $filter);
        $repayment_arr = array(
            "Today ($today)" => $today_repayment,
            "Tomorrow ($tomorrow)" => $tomorrow_repayment,
            "The Day After Tomorrow ($tomorrow)" => $after_tomorrow_repayment,
        );
        $format_list = array();
        foreach ($repayment_arr as $day => $repayment) {
            $format_list[] = array(
                'is_merge_row' => 1,
                'is_bold' => 1,
                'content' => "$day",
            );
            if ($repayment) {
                foreach ($repayment as $val) {
                    $term = array();
                    $term['contract_sn'] = $val['contract_sn'];
                    $term['display_name'] = $val['display_name'];
                    $term['currency'] = $val['currency'];
                    $term['apply_amount'] = ncPriceFormat($val['apply_amount']);
                    $term['ref_amount'] = ncPriceFormat($val['ref_amount']);
                    $format_list[]['data'] = $term;
                }
            } else {
                $format_list[] = array(
                    'is_merge_row' => 1,
                    'is_bold' => 0,
                    'content' => "No data",
                );
            }
        }

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }

    /**
     * 导出Member Credit
     */
    public function exportMemberCreditOp()
    {
        $search_text = trim($_GET['search_text']);
        $branch_id = $_GET['branch_id'];
        $category_id = $this->GetCategoryId();

        $filters = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'category_id' => $category_id
        );

        $title = 'Member Credit';
        $cellName = array(
            array('num', 'No.', 10, 'LEFT'),
            array('branch_name', 'Branch Name', 12, 'LEFT'),
            array('obj_guid', 'CID', 12, 'LEFT'),
            array('display_name', '	Client Name', 10, 'LEFT'),
            array('phone_id', '	Phone', 16, 'LEFT'),
            array('credit', 'SuperLoan Credit', 14, 'LEFT'),
            array('credit_balance', 'Credit Balance', 10, 'LEFT'),
            array('loan_times', 'Loan Times', 10, 'LEFT'),
            array('loan_amount', 'Loan Amount', 12, 'LEFT'),
            array('service_fee', 'Service Fee', 12, 'LEFT'),
            array('outstanding', 'Outstanding', 12, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($search_text) {
            $filter_str .= "Search text: $search_text    ";
        }

        $data = superLoanReportClass::getCreditList($filters);

        $list = $data['data'];
        $format_list = array();
        foreach ($list as $k => $v) {
            $arr = array();
            $arr['num'] = $k+1;
            $arr['branch_name'] = $v['branch_name'];
            $arr['obj_guid'] = $v['obj_guid'];
            $arr['display_name'] = $v['display_name'];
            $arr['phone_id'] = $v['phone_id'];
            $arr['credit'] = $v['credit'];
            $arr['credit_balance'] = $v['credit_balance'];
            $arr['loan_times'] = $v['loan_times'];
            $arr['loan_amount'] = $v['loan_amount'];
            $arr['service_fee'] = $v['service_fee'];
            $arr['outstanding'] = $v['outstanding'];

            $format_list[]['data'] = $arr;
        }

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
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
     * 导出Loan Transaction
     */
    public function exportLoanTransactionOp()
    {
        $search_text = trim($_GET['search_text']);
        $branch_id = $_GET['branch_id'];
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $category_id = $this->GetCategoryId();

        $filters = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'category_id' => $category_id
        );

        $title = 'Loan Transaction';
        $cellName = array(
            array('num', 'No.', 10, 'LEFT'),
            array('branch_name', 'Branch Name', 12, 'LEFT'),
            array('client_obj_guid', 'CID', 12, 'LEFT'),
            array('display_name', '	Client Name', 10, 'LEFT'),
            array('phone_id', '	Phone', 16, 'LEFT'),
            array('contract_sn', 'Contract SN', 14, 'LEFT'),
            array('create_time', 'Time', 10, 'LEFT'),
            array('apply_amount', 'Amount', 10, 'LEFT'),
            array('receivable_service_fee', 'Service Fee', 12, 'LEFT'),
            array('outstanding', 'Outstanding', 12, 'LEFT'),
            array('end_date', 'Left Days', 12, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($search_text) {
            $filter_str .= "Search text: $search_text    ";
        }
        if ($date_start) {
            $filter_str .= "Date: $date_start -- $date_end";
        }

        $data = superLoanReportClass::getTrxLoanList($filters);

        $list = $data['data'];
        $format_list = array();
        foreach ($list as $k => $v) {
            $arr = array();
            $arr['num'] = $k+1;
            $arr['branch_name'] = $v['branch_name'];
            $arr['client_obj_guid'] = $v['client_obj_guid'];
            $arr['display_name'] = $v['display_name'];
            $arr['phone_id'] = $v['phone_id'];
            $arr['contract_sn'] = $v['contract_sn'];

            $arr['create_time'] = $v['create_time'];
            $arr['apply_amount'] = $v['apply_amount'];
            $arr['receivable_service_fee'] = $v['receivable_service_fee'];
            $arr['outstanding'] = $v['outstanding'];
            $arr['end_date'] = $v['end_date'];

            $format_list[]['data'] = $arr;
        }

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }
    /**
     * 导出RepayTransaction
     */
    public function exportRepayTransactionOp()
    {
        $search_text = trim($_GET['search_text']);
        $branch_id = $_GET['branch_id'];
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $category_id = $this->GetCategoryId();

        $filters = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'category_id' => $category_id
        );

        $title = 'Repay Transaction';
        $cellName = array(
            array('num', 'No.', 10, 'LEFT'),
            array('branch_name', 'Branch Name', 12, 'LEFT'),
            array('client_obj_guid', 'CID', 12, 'LEFT'),
            array('display_name', '	Client Name', 10, 'LEFT'),
            array('phone_id', '	Phone', 16, 'LEFT'),
            array('contract_sn', 'Contract SN', 14, 'LEFT'),
            array('create_time', 'Repay Time', 16, 'LEFT'),
            array('amount', 'Repay Amount', 16, 'LEFT'),
            array('apply_amount', 'Loan Principal', 16, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($search_text) {
            $filter_str .= "Search text: $search_text    ";
        }
        if ($date_start) {
            $filter_str .= "Date: $date_start -- $date_end";
        }

        $data = superLoanReportClass::getTrxRepayList($filters);

        $list = $data['data'];
        $format_list = array();
        foreach ($list as $k => $v) {
            $arr = array();
            $arr['num'] = $k+1;
            $arr['branch_name'] = $v['branch_name'];
            $arr['client_obj_guid'] = $v['client_obj_guid'];
            $arr['display_name'] = $v['display_name'];
            $arr['phone_id'] = $v['phone_id'];
            $arr['contract_sn'] = $v['contract_sn'];
            $arr['create_time'] = $v['create_time'];
            $arr['amount'] = $v['amount'];
            $arr['apply_amount'] = $v['apply_amount'];

            $format_list[]['data'] = $arr;
        }

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }
    /**
     * 导出Daily Report
     */
    public function exportDailyReportOp()
    {
        $day = $_GET['date_end']?:date('Y-m-d');
        $pageNumber = intval($_GET['pageNumber']) ?: 1;
        $pageSize = intval($_GET['pageSize']) ?: 20;
        $search_text = trim($_GET['search_text']);
        $branch_id=intval($_GET['branch_id']);
        $currency = $_GET['currency'];

        $filters = array(
            'loan_category_id' => $this->GetCategoryId(),
            'branch_id' => $branch_id,
            'currency' => $currency,
            'search_text'=>$search_text
        );

        $title = 'Daily Report';
        $cellName = array(
            array('num', 'No.', 10, 'LEFT'),
            array('branch_name', 'Branch Name', 12, 'LEFT'),
            array('officer_name', 'CO Name', 12, 'LEFT'),
            array('display_name', 'Customer Name', 10, 'LEFT'),
            array('client_address', '	Customer Address', 16, 'LEFT'),
            array('loan_cycle', 'Cycle', 14, 'LEFT'),
            array('obj_guid', 'Loan Account', 16, 'LEFT'),
            array('ace_account', 'ACE Account', 16, 'LEFT'),
            array('phone_id', 'Phone Number', 16, 'LEFT'),
            array('category_credit', 'Credit Amount', 12, 'LEFT'),
            array('credit_grant_time', 'Credit Date', 12, 'LEFT'),
            array('maturity_date', 'Maturity Date', 10, 'LEFT'),
            array('loan_amount', '	Loan Amount', 16, 'LEFT'),
            array('repayment_amount', 'Repayment Amount', 14, 'LEFT'),
            array('repayment_date', 'Repayment Date', 16, 'LEFT'),
            array('loan_arrea', 'Loan Arrea', 16, 'LEFT'),
            array('withdraw_number', 'Number of withdrawal', 16, 'LEFT'),
            array('day_late', 'Day Late', 16, 'LEFT'),
            array('closed_date', 'Closed Date', 16, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($search_text) {
            $filter_str .= "Search text: $search_text    ";
        }

        $data = superLoanReportClass::getDailyReportData($day,$pageNumber,$pageSize,$filters);

        $list = $data->data;
        //处理数据格式 total统计
        foreach ($list as $k => $v) {
            $list['total']['branch_name'] = "Total";
            $list['total']['category_credit'] += $v['category_credit'];
            $list['total']['loan_amount'] += $v['loan_amount'];
            $list['total']['repayment_amount'] += $v['repayment_amount'];
            $list['total']['loan_arrea'] += $v['loan_arrea'];
        }
        $format_list = array();

        foreach ($list as $k => $v) {
            $arr = array();
            if($k == "total"){
                $k = " ";
            }
            $arr['num'] = $k;
            $arr['branch_name'] = $v['branch_name'];
            $arr['officer_name'] = $v['officer_name'];
            $arr['display_name'] = $v['display_name'];
            $arr['client_address'] = $v['client_address'];
            $arr['loan_cycle'] = $v['loan_cycle'];
            $arr['obj_guid'] = $v['obj_guid'];
            $arr['ace_account'] = $v['ace_account'];
            $arr['phone_id'] = $v['phone_id'];
            $arr['category_credit'] = $v['category_credit'];
            $arr['credit_grant_time'] = $v['credit_grant_time'];
            $arr['maturity_date'] = $v['maturity_date'];
            $arr['loan_amount'] = $v['loan_amount'];
            $arr['repayment_amount'] = $v['repayment_amount'];
            $arr['repayment_date'] = $v['repayment_date'];
            $arr['loan_arrea'] = $v['loan_arrea'] ?:0;
            $arr['withdraw_number'] = $v['withdraw_number'];
            $arr['day_late'] = $v['day_late'];
            $arr['closed_date'] = $v['closed_date'];

            $format_list[]['data'] = $arr;
        }

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }
    /**
     * 导出Master Client
     */
    public function exportMasterClientOp()
    {
        $page_number = $_GET['pageNumber']?:1;
        $page_size = $_GET['pageSize']?:20;
        $search_text = trim($_GET['search_text']);
        $branch_id = $_GET['branch_id'];
        $currency = $_GET['currency'];
        $date_start = $_GET['date_start'];
        $date_end = $_GET['date_end'];
        $filters = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'currency' => $currency,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );

        $title = 'Master Client';
        $cellName = array(
            array('branch_id', 'BranchID', 8, 'LEFT'),
            array('branch_name', 'Branch Name', 8, 'LEFT'),
            array('client_obj_guid', 'CID', 12, 'LEFT'),
            array('contract_sn', 'Account Number', 20, 'LEFT'),
            array('display_name', 'Borrower Name', 16, 'LEFT'),
            array('gender', 'Gender', 14, 'LEFT'),
            array('coborrower_name', 'Coborrower Name', 8, 'LEFT'),
            array('id1_text', 'Province', 8, 'LEFT'),
            array('id2_text', 'District', 8, 'LEFT'),
            array('id3_text', 'Commune', 8, 'LEFT'),
            array('id4_text', 'Village', 8, 'LEFT'),
            array('phone_id', 'Phone Number', 18, 'LEFT'),
            array('product_type', 'Product Type', 12, 'LEFT'),
            array('start_date', 'Disbursed Date', 12, 'LEFT'),
            array('end_date', 'Maturity Date', 10, 'LEFT'),
            array('apply_amount', 'Disbursed Amt', 16, 'LEFT'),
            array('loan_balance', 'Current Balance', 14, 'LEFT'),
            array('loan_term', 'Loan Term', 10, 'LEFT'),
            array('monthly_interest_rate', 'Monthly IntRate', 10, 'LEFT'),
            array('repayment_num', 'Number Of Payment', 12, 'LEFT'),
            array('loan_actual_cycle', 'Cycle', 12, 'LEFT'),
            array('currency', 'Currency', 12, 'LEFT'),
            array('propose', 'Loan Purpose', 10, 'LEFT'),
            array('officer_name', 'Co Name', 12, 'LEFT'),
            array('repayment_type', 'Payment Type', 18, 'LEFT'),
            array('day_late', 'Day Late', 10, 'LEFT'),
            array('provision_type', 'Provision Type', 16, 'LEFT'),
            array('repayment_principal', 'Principal', 12, 'LEFT'),
            array('repayment_interest', 'Interest', 12, 'LEFT'),
            array('repayment_operation_fee', 'Operation Fee', 10, 'LEFT'),
            array('repayment_penalty', 'Penalty', 16, 'LEFT'),
            array('repayment_total', 'Total', 14, 'LEFT'),
        );

        $filter_str = '';
        if ($branch_id) {
            $m_site_branch = M('site_branch');
            $branch_info = $m_site_branch->find($branch_id);
            $branch_name = $branch_info['branch_name'];
        } else {
            $branch_name = 'All Branch';
        }
        $filter_str .= "Branch: " . $branch_name . "    ";

        if ($search_text) {
            $filter_str .= "Search text: $search_text    ";
        }
        if ($currency) {
            $filter_str .= "Currency: $currency    ";
        }
        if ($date_start) {
            $filter_str .= "Date: $date_start -- $date_end";
        }

        $data = loanReportClass::getMasterClientList($page_number,$page_size,$filters);

        $list = $data['data'];
        $format_list = array();
        foreach ($list as $k => $v) {
            $arr = array();
            $arr['branch_id'] = $v['branch_id'];
            $arr['branch_name'] = $v['branch_name'];
            $arr['client_obj_guid'] = $v['client_obj_guid'];
            $arr['contract_sn'] = $v['contract_sn'];
            $arr['display_name'] = $v['display_name'];
            $arr['gender'] = $v['gender'] == memberGenderEnum::FEMALE?'F':'M';
            $arr['coborrower_name'] = $v['coborrower_name'];
            $arr['id1_text'] = $v['id1_text'];
            $arr['id2_text'] = $v['id2_text'];
            $arr['id3_text'] = $v['id3_text'];
            $arr['id4_text'] = $v['id4_text'];
            $arr['phone_id'] = $v['phone_id'];
            $arr['product_type'] = $v['product_type'];
            $arr['start_date'] = date('Y-m-d',strtotime($v['start_date']));
            $arr['end_date'] = date('Y-m-d',strtotime($v['end_date']));
            $arr['apply_amount'] = ncPriceFormat($v['apply_amount']);
            $arr['loan_balance'] = ncPriceFormat($v['loan_balance']);
            $arr['loan_term'] = $v['loan_period_value'].' '.ucwords($v['loan_period_unit']);
            $arr['monthly_interest_rate'] = $v['monthly_interest_rate'].'%';
            $arr['repayment_num'] = $v['repayment_num'];
            $arr['loan_actual_cycle'] = $v['loan_actual_cycle'];
            $arr['currency'] = $v['currency'];
            $arr['propose'] = $v['propose'];
            $arr['officer_name'] = $v['officer_name'];
            $arr['repayment_type'] = $v['repayment_type'];
            $arr['day_late'] = $v['day_late'];
            $arr['provision_type'] = 'Regular';
            $arr['repayment_principal'] = ncPriceFormat($v['repayment_principal']);
            $arr['repayment_interest'] = ncPriceFormat($v['repayment_interest']);
            $arr['repayment_operation_fee'] = ncPriceFormat($v['repayment_operation_fee']);
            $arr['repayment_penalty'] = ncPriceFormat($v['repayment_penalty']);
            $arr['repayment_total'] = ncPriceFormat($v['repayment_principal']+$v['repayment_interest']+$v['repayment_operation_fee']+$v['repayment_penalty']);

            $format_list[]['data'] = $arr;
        }

        common::exportDataToExcel($title, $cellName, $format_list, $filter_str);
    }



}
