<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 9/13/2018
 * Time: 10:43 AM
 */
class report_super_loanControl extends back_office_baseControl
{

    public $category=array();
    public $category_id=0;//super loan对应的cate-id
    public $limit_branch_id=0;

    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Great Loan");
        Tpl::setDir("report_super_loan");

        //获取super loan的cate-id
        $sql="SELECT * FROM loan_category WHERE is_special=1 AND special_key='".specialLoanCateKeyEnum::FIX_REPAYMENT_DATE."'";
        $r=new ormReader();
        $this->category=$r->getRow($sql);
        if( $this->category ){
            $this->category_id=$this->category['uid'];
        }

        $limit_position=array(userPositionEnum::BRANCH_MANAGER,userPositionEnum::TELLER,userPositionEnum::CHIEF_TELLER);
        if(in_array($this->user_position,$limit_position)){
            $this->limit_branch_id=$this->branch_id;
            Tpl::output("limit_branch_id",$this->branch_id);//这些用户限制只能查看自己branch的
        }

    }
    protected function getSubMenuListOp()
    {
        // 生成二级菜单的配置
        return array(
            'group_by_credit' => array(
                'title' => 'Member Credit',
                'url' => getBackOfficeUrl('report_super_loan','getCreditListPage'),
                'is_active' => 0
            ),
            'trx_loan' => array(
                'title' => 'Loan Transaction',
                'url' => getBackOfficeUrl('report_super_loan','getLoanListPage'),
                'is_active' => 0
            ),
            'trx_repay' => array(
                'title' => 'Repay Transaction',
                'url' => getBackOfficeUrl('report_super_loan','getRepayListPage'),
                'is_active' => 0
            ),
            'day_report' => array(
                'title' => 'Daily Report',
                'url' => getBackOfficeUrl('report_super_loan','dailyReportPage'),
                'is_active' => 0
            ),
        );
    }
    public function indexOp(){
        $this->getCreditListPageOp();
    }
    public function getCreditListPageOp(){
        $menu_key = 'group_by_credit';
        $menu_list = $this->getSubMenuListOp();
        $menu_list[$menu_key]['is_active'] = 1;
        Tpl::output('sub_menu_list',$menu_list);

        // 获得所有分行列表
        $branch_list = (new site_branchModel())->select(array(
            'uid' => array('>',0),
        ));
        Tpl::output('branch_list',$branch_list);

        Tpl::showPage("credit.category.page");
    }
    public function getLoanListPageOp(){
        $menu_key = 'trx_loan';
        $menu_list = $this->getSubMenuListOp();
        $menu_list[$menu_key]['is_active'] = 1;
        Tpl::output('sub_menu_list',$menu_list);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        // 获得所有分行列表
        $branch_list = (new site_branchModel())->select(array(
            'uid' => array('>',0),
        ));
        Tpl::output('branch_list',$branch_list);

        Tpl::showPage("trx.loan.page");
    }
    public function getRepayListPageOp(){
        $menu_key = 'trx_repay';
        $menu_list = $this->getSubMenuListOp();
        $menu_list[$menu_key]['is_active'] = 1;
        Tpl::output('sub_menu_list',$menu_list);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        // 获得所有分行列表
        $branch_list = (new site_branchModel())->select(array(
            'uid' => array('>',0),
        ));
        Tpl::output('branch_list',$branch_list);

        Tpl::showPage("trx.repay.page");
    }
    //Member Credit
    public function getCreditListOp($p){
        $p['category_id'] = $this->category_id;
        $list = superLoanReportClass::getCreditList($p);
        return $list;
    }
    //Loan Transaction
    public function getTrxLoanListOp($p){
        $p['category_id'] = $this->category_id;
        $list = superLoanReportClass::getTrxLoanList($p);
        return $list;
    }
    //Repay Transaction
    public function getTrxRepayListOp($p){
        $p['category_id'] = $this->category_id;
        $list = superLoanReportClass::getTrxRepayList($p);
        return $list;
    }


    public function dailyReportPageOp()
    {
        $menu_key = 'day_report';
        $menu_list = $this->getSubMenuListOp();
        $menu_list[$menu_key]['is_active'] = 1;
        Tpl::output('sub_menu_list',$menu_list);

        $condition = array(
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        // 获得所有分行列表
        $branch_list = (new site_branchModel())->select(array(
            'uid' => array('>',0),
        ));
        Tpl::output('branch_list',$branch_list);

        Tpl::showPage("daily.report.page");
    }

    public function getDailyReportDataOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $day = $p['date_end']?:date('Y-m-d');
        $search_text = trim($p['search_text']);
        $branch_id=intval($p['branch_id']);
        $currency = $p['currency'];

        $filter = array(
            'loan_category_id' => $this->category_id,
            'branch_id' => $branch_id,
            'currency' => $currency
        );

        if( $search_text ){
            $filter['search_text'] = $search_text;
        }

        // total-> credit amount,  loan amount , repayment amount ,loan arrea(逾期的金额)
        $data = superLoanReportClass::getDailyReportData($day,$pageNumber,$pageSize,$filter);
        return $data;

    }
}