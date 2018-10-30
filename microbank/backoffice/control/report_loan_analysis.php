<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/3
 * Time: 11:41
 */
class report_loan_analysisControl extends back_office_baseControl
{
    public $limit_branch_id = 0;

    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report Loan Analysis");
        Tpl::setDir("report_loan_analysis");

        $limit_position = array(userPositionEnum::BRANCH_MANAGER, userPositionEnum::TELLER, userPositionEnum::CHIEF_TELLER);
        if (in_array($this->user_position, $limit_position)) {
            $this->limit_branch_id = $this->branch_id;
            Tpl::output("limit_branch_id", $this->branch_id);//这些用户限制只能查看自己branch的
        }
    }

    protected function getSubMenuListOp()
    {
        // 生成二级菜单的配置
        return array(
            'group_by_overdue' => array(
                'title' => 'Overdue',
                'url' => getBackOfficeUrl('report_loan_analysis', 'getOverdueData'),
                'is_active' => 0
            ),
            'group_by_day' => array(
                'title' => 'GroupBy Date',
                'url' => getBackOfficeUrl('report_loan_analysis', 'showDayDataPage'),
                'is_active' => 0
            ),
            'group_by_days_alarm' => array(
                'title' => 'PendingRepay Alarm',
                'url' => getBackOfficeUrl('report_loan_analysis', 'showDayAlarmPage'),
                'is_active' => 0
            ),
        );
    }

    public function indexOp()
    {
        $this->getOverdueDataOp();
    }

    public function getOverdueDataOp()
    {
        $params = array_merge($_GET, $_POST);
        $menu_key = 'group_by_overdue';
        $menu_list = $this->getSubMenuListOp();
        $menu_list[$menu_key]['is_active'] = 1;
        Tpl::output('sub_menu_list', $menu_list);

        // 获得所有分行列表
        $branch_list = (new site_branchModel())->select(array(
            'uid' => array('>', 0),
        ));
        Tpl::output('branch_list', $branch_list);

        if ($this->limit_branch_id) {
            $branch_id = $this->limit_branch_id;
        } else {
            $branch_id = intval($params['branch_id']);
        }

        $day = $params['day'] ?: date('Y-m-d');
        $currency = $params['currency'] ?: currencyEnum::USD;
        $condition = array(
            'day' => $day,
            'branch_id' => $branch_id,
            'currency' => $currency
        );

        Tpl::output('search_condition', $condition);


        $data = loanReportAnalysisClass::getAllOverdueContractData($condition);

        Tpl::output('data', $data);
        Tpl::showpage('loan.overdue.data.page');

    }

    public function showDayDataPageOp()
    {
        $menu_key = 'group_by_day';
        $menu_list = $this->getSubMenuListOp();
        $menu_list[$menu_key]['is_active'] = 1;
        Tpl::output('sub_menu_list', $menu_list);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        // 获得所有分行列表
        $branch_list = (new site_branchModel())->select(array(
            'uid' => array('>', 0),
        ));
        Tpl::output('branch_list', $branch_list);


        $category = M('loan_category')->getCategoryList();
        Tpl::output("category", $category);

        Tpl::showpage('loan.day.data.page');
    }

    public function getDayDataOp($p)
    {
        $filter = array(
            'date_start' => $p['date_start'],
            'date_end' => $p['date_end'],
            'category' => $p['category'],
            'branch_id' => $p['branch_id']
        );
        $loan = loanReportAnalysisClass::getDayDataOfLoan($filter);
        $repayment = loanReportAnalysisClass::getDayDataOfRepayment($filter);
        $pending_repayment = loanReportAnalysisClass::getDayDataOfPendingRepayment($filter);
        $list = array_merge_recursive($loan, $repayment, $pending_repayment);
        ksort($list);
        return $list;
    }

    public function showDayAlarmPageOp()
    {
        $menu_key = 'group_by_days_alarm';
        $menu_list = $this->getSubMenuListOp();
        $menu_list[$menu_key]['is_active'] = 1;
        Tpl::output('sub_menu_list', $menu_list);
        $category = M('loan_category')->getCategoryList();
        Tpl::output('category', $category);

        // 获得所有分行列表
        $branch_list = (new site_branchModel())->select(array(
            'uid' => array('>', 0),
        ));
        Tpl::output('branch_list', $branch_list);

        Tpl::showpage('loan.day.alarm.page');
    }

    public function getDayAlarmOp($p)
    {
        $filter = array(
            'category' => $p['category'],
            'branch_id' => $p['branch_id']
        );
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $after_tomorrow = date('Y-m-d', strtotime('+2 day'));
        $today_repayment = loanReportAnalysisClass::getDayAlarmByDate($today, $filter);
        $tomorrow_repayment = loanReportAnalysisClass::getDayAlarmByDate($tomorrow, $filter);
        $after_tomorrow_repayment = loanReportAnalysisClass::getDayAlarmByDate($after_tomorrow, $filter);
        return Array(
            'today_repayment' => $today_repayment,
            'tomorrow_repayment' => $tomorrow_repayment,
            'after_tomorrow_repayment' => $after_tomorrow_repayment
        );
    }
}