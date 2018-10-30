<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/17/2018
 * Time: 11:26 AM
 */
class data_center_branchControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("data_center_branch");
    }

    function indexOp()
    {
        $m_branch = M('site_branch');
        $branch_list = $m_branch->getAll();
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage("index");
    }

    /**
     * 单个branch详情
     * @param $p
     * @return array
     */
    function getBranchInfoOp($p)
    {
        if (!$p['branch_id']) return array();
        $m_branch = M('site_branch');
        return $m_branch->find(array('uid' => $p['branch_id']));
    }

    /**
     * 更改branch状态
     * @param $p
     * @return array
     */
    function editBranchStatusOp($p)
    {
        $branch_id = $p['branch_id'];
        $status = $p['status'];
        return branchClass::editBranchStatus($branch_id, $status);
    }

    /**
     * Staff List 页面
     * @param $p
     */
    function showBranchStaffPageOp($p)
    {
        $branch_id = $p['branch_id'];
        Tpl::output('branch_id', $branch_id);
    }

    /**
     * Staff List数据
     * @param $p
     * @return array
     */
    function getBranchStaffListOp($p)
    {
        $branch_id = $p['branch_id'];
        $pageNumber = $p['pageNumber'];
        $pageSize = $p['pageSize'];
        $filters = array(
            'staff' => trim($p['staff']),
            'country_code' => trim($p['country_code']),
            'phone_number' => trim($p['phone_number']),
            'user_status' => $p['user_status']
        );
        return branchDataClass::getBranchStaffData($branch_id, $pageNumber, $pageSize, $filters);
    }

    /**
     * Bank List 页面
     * @param $p
     */
    function showBranchBankPageOp($p)
    {
        $branch_id = $p['branch_id'];
        Tpl::output('branch_id', $branch_id);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $m_bank = M('site_bank');
        $bank_list = $m_bank->select(array('branch_id' => $branch_id));
        Tpl::output('bank_list', $bank_list);
    }

    /**
     * Bank List数据
     * @param $p
     * @return array
     */
    function getBranchBankListOp($p)
    {
        $branch_id = $p['branch_id'];
        $pageNumber = $p['pageNumber'];
        $pageSize = $p['pageSize'];
        $filters = array(
            'bank_id' => trim($p['bank']),
            'account_state' => trim($p['status']),
            'start_date' => $p['date_start'],
            'end_date' => $p['date_end']
        );
        return branchDataClass::getBranchBankData($branch_id, $pageNumber, $pageSize, $filters);
    }

    /**
     * Bank List --> Flow 页面
     * @param $p
     */
    function showBranchBankFlowPageOp($p)
    {
        $account_id = $p['account_id'];
        Tpl::output('account_id', $account_id);
        //时间范围与bank一致
        $condition = array(
            "date_start" => $p['date_start'],
            "date_end" => $p['date_end']
        );
        Tpl::output("condition", $condition);
    }

    /**
     * Bank List --> Flow 数据
     * @param $p
     */
    function getBranchBankFlowListOp($p)
    {
        $account_id = $p['account_id'];
        $pageNumber = $p['pageNumber'];
        $pageSize = $p['pageSize'];
        $filters = array(
            'start_date' => $p['date_start'],
            'end_date' => $p['date_end']
        );
        return branchDataClass::getBranchBankFlowData($account_id, $pageNumber, $pageSize, $filters);
    }

    public function dailyReportOp()
    {
        Tpl::showPage("report.daily");
    }

    public function getDailyReportByDayOp($p)
    {
        $branch_id = $p['branch_id'];
        $end_date = $p['year'] . '-' . str_pad($p['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($p['day'], 2, '0', STR_PAD_LEFT);
        $end_date = date('Y-m-d 23:59:59', strtotime($end_date));
        $ret = array();

        $br_book = passbookClass::getBranchPassbook($branch_id);
        $br_book_info = $br_book->getPassbookInfo();
        $ret[] = array(
            'uid' => $br_book_info['uid'],
            'book_code' => $br_book_info['book_code'],
            'book_name' => $br_book_info['book_name'],
            'balance' => $br_book->getAccountBalanceOfEndDay($end_date),
            'remark' => 'Cash In Vault'
        );

        //获取branch下所有的acct
        $r = new ormReader();
        $sql = "SELECT uu.uid,uu.user_name FROM um_user uu"
            . " LEFT JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " LEFT JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE  sb.uid = " . $branch_id;
        $cashier_list = $r->getRows($sql);
        $teller_acct = array();
        foreach ($cashier_list as $item) {
            $teller_book = passbookClass::getUserPassbook($item['uid']);
            $teller_book_info = $teller_book->getPassbookInfo();
            $ret[] = array(
                'uid' => $teller_book_info['uid'],
                'book_code' => $teller_book_info['book_code'],
                'book_name' => $teller_book_info['book_name'],
                'balance' => $teller_book->getAccountBalanceOfEndDay($end_date),
                'remark' => '' . $item['user_name']
            );
        }

        //获取bank的acct
        $mb = new site_bankModel();
        $bank_acct = $mb->select(array("branch_id" => $branch_id));
        foreach ($bank_acct as $item) {
            $bank_book = passbookClass::getBankAccountPassbook($item['uid']);
            $bank_book_info = $bank_book->getPassbookInfo();
            $ret[] = array(
                'uid' => $bank_book_info['uid'],
                'book_code' => $bank_book_info['book_code'],
                'book_name' => $bank_book_info['book_name'],
                'balance' => $bank_book->getAccountBalanceOfEndDay($end_date),
                'remark' => 'BANK : ' . $item['bank_account_name']
            );
        }
        return $ret;
    }

    public function showBranchLocationOp($p)
    {
        $branch_id = $p['branch_id'];
        $info = (new branchClass())->getBranchInfo($branch_id);
        return array(
            "point" => array(
                'x' => $info['coord_x'],
                'y' => $info['coord_y']
            )
        );
    }
}