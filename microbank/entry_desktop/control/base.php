<?php

class baseControl extends control
{
    public $user_id;
    public $user_name;
    public $user_info;
    public $auth_list;
    public $user_position;

    function __construct()
    {
        parent::__construct();
        //if (!$this->checkSecurity()) die("Access Denied");
        $this->checkLogin();

        Language::read('auth');
        $user = userBase::Current();
        $user_info = $user->property->toArray();
        $this->user_info = $user_info;
        $this->user_id = $user_info['uid'];
        $this->user_name = $user_info['user_code'];
        $this->user_position = $user_info['user_position'];
        $auth_arr = $user->getAuthList();
        $this->auth_list = $auth_arr['back_office'];

        $is_system_close = userClass::chkSystemIsClose($this->user_position);
        if ($is_system_close) {
            $this->alertExit("System Closed.");
        }
    }

    protected function checkSecurity()
    {
        if (global_settingClass::getCommonSetting()['backoffice_deny_without_client']) {
            return $_COOKIE['SITE_PRIVATE_KEY'] == md5(date("Ydm"));
        } else {
            return true;
        }
    }

    /**
     * 根据权限获取menu
     * @return array
     */
    protected function getResetMenu()
    {
        if ($this->user_position == userPositionEnum::OPERATOR) {
            Language::read('certification');
            $index_menu = $this->getOperatorMenu();
            $certification_type = enum_langClass::getCertificationTypeEnumLang();
            unset($certification_type[certificationTypeEnum::GUARANTEE_RELATIONSHIP]);
            $index_menu['certification_file']['child'] = $certification_type;
            //print_r($index_menu);
            return $index_menu;
        } else if ($this->user_position == userPositionEnum::BRANCH_MANAGER) {
            $index_menu = $this->getBranchMangerMenu();
            return $index_menu;
        } else if ($this->user_position == userPositionEnum::DEVELOPER) {
            $index_menu = $this->getDeveloperMenu();
            return $index_menu;
        } else {
            $index_menu = $this->getIndexMenu();
            if ($this->user_position == userPositionEnum::ROOT) return $index_menu;
            foreach ($index_menu as $key => $menu) {
                foreach ($menu['child'] as $k => $child) {
                    $argc = explode(',', $child['args']);
                    $auth = $argc[1] . '_' . $argc[2];
                    if (!in_array($auth, $this->auth_list)) {
                        unset($index_menu[$key]['child'][$k]);
                    }
                }
                if (empty($index_menu[$key]['child'])) {
                    unset($index_menu[$key]);
                }
            }
            return $index_menu;
        }
    }

    /**
     * 定义menu
     * @return array
     */
    private function getIndexMenu()
    {
        $indexMenu = array(
            'home' => array(
                "title" => "Home",
                'child' => array(
                    array('args' => 'microbank/backoffice,monitor,monitor', 'title' => 'Dashboard')
                )
            ),
            'user' => array(
                "title" => 'HR',
                'child' => array(
                    array('args' => 'microbank/backoffice,user,branch', 'title' => 'Branch'),
                    array('args' => 'microbank/backoffice,user,role', 'title' => 'Role'),
                    array('args' => 'microbank/backoffice,user,user', 'title' => 'Staff'),
                    array('args' => 'microbank/backoffice,user,staff', 'title' => 'Staff Account'),
                    array('args' => 'microbank/backoffice,user,committee', 'title' => 'Committee'),
                    array('args' => 'microbank/backoffice,user,bindCard', 'title' => 'Bind IC Card'),
                    array('args' => 'microbank/backoffice,setting,companyInfo', 'title' => 'Company Info'),
                    array('args' => 'microbank/backoffice,region,list', 'title' => 'Region'),
                    array('args' => 'microbank/backoffice,user,log', 'title' => 'Staff Login Log'),
                    //array('args' => 'microbank/backoffice,user,pointEvent', 'title' => 'Point Event'),
                    //array('args' => 'microbank/backoffice,user,pointPeriod', 'title' => 'Point Period'),
                    //array('args' => 'microbank/backoffice,user,departmentPoint', 'title' => 'Department Point'),
                )
            ),
            'client' => array(
                "title" => 'Client',
                'child' => array(
                    array('args' => 'microbank/backoffice,client,client', 'title' => 'Client'),
                    array('args' => 'microbank/backoffice,client,cerification', 'title' => 'Certification File'),
                    array('args' => 'microbank/backoffice,client,blackList', 'title' => 'Black List'),
                    array('args' => 'microbank/backoffice,client,grade', 'title' => 'Grade'),
                )
            ),
            /*
            'partner' => array(
                "title" => 'Partner',
                'child' => array(
                    array('args' => 'microbank/backoffice,partner,bank', 'title' => 'Bank'),
                    array('args' => 'microbank/backoffice,partner,dealer', 'title' => 'Dealer'),
                )
            ),
            */
            'loan_committee' => array(
                "title" => 'Loan Committee',
                'child' => array(
                    array('args' => 'microbank/backoffice,loan_committee,approveCreditApplication', 'title' => 'Approve Credit Application'),
                    array('args' => 'microbank/backoffice,loan_committee,userVote', 'title' => 'My Vote'),
//                    array('args' => 'microbank/backoffice,loan_committee,fastGrantCredit', 'title' => 'Fast Grant Credit'),
//                    array('args' => 'microbank/backoffice,loan_committee,cutCredit', 'title' => 'Cut Credit'),
                    array('args' => 'microbank/backoffice,loan_committee,grantCreditHistory', 'title' => 'Pending Sign Credit-Agreement'),
                    array('args' => 'microbank/backoffice,loan_committee,signCreditAgreement', 'title' => 'Signed Credit-Agreement'),
                    array('args' => 'microbank/backoffice,loan_committee,approvePrepaymentRequest', 'title' => 'Approve Prepayment Request'),
                    array('args' => 'microbank/backoffice,loan_committee,approvePenaltyRequest', 'title' => 'Approve Penalty Request'),
                    array('args' => 'microbank/backoffice,loan_committee,approveWrittenOffRequest', 'title' => 'Approve Writtenoff Request'),
                    array('args' => 'microbank/backoffice,loan_committee,approveWithdrawMortgageRequest', 'title' => 'Approve Withdraw Mortgage'),
                )
            ),
            'loan' => array(
                "title" => 'Loan Setting',
                'child' => array(
                    array('args' => 'microbank/backoffice,loan,product', 'title' => 'Product'),
                    array('args' => 'microbank/backoffice,loan,productPackagePage', 'title' => 'Interest Package'),
                    array('args' => 'microbank/backoffice,loan,category', 'title' => 'Credit Category'),
                    array('args' => 'microbank/backoffice,loan,loanFeeSetting', 'title' => 'Loan Fee & Admin Fee'),

                    array('args' => 'microbank/backoffice,loan,prepaymentLimit', 'title' => 'Prepayment Limit'),
                    array('args' => 'microbank/backoffice,setting,creditLevel', 'title' => 'Credit Level'),
                    array('args' => 'microbank/backoffice,setting,industry', 'title' => 'Industry'),
                    array('args' => 'microbank/backoffice,setting,industryPlace', 'title' => 'Industry Place'),
                    array('args' => 'microbank/backoffice,setting,assetSurvey', 'title' => 'Asset Survey'),
                    //array('args' => 'microbank/backoffice,loan,requestToRepayment', 'title' => 'Repayment'),
                    // array('args' => 'microbank/backoffice,loan,contract', 'title' => 'Contract'),
                    //array('args' => 'microbank/backoffice,loan,writeOff', 'title' => 'Write Off'),
                    //array('args' => 'microbank/backoffice,loan,overdue', 'title' => 'Overdue'),
                    //array('args' => 'microbank/backoffice,loan,deductingPenalties', 'title' => 'Received Penalties'),
                )
            ),
            'savings' => array(
                "title" => 'Savings Setting',
                'child' => array(
                    array('args' => 'microbank/backoffice,savings,category', 'title' => 'Category'),
                    array('args' => 'microbank/backoffice,savings,product', 'title' => 'Product'),
                )
            ),
            /*
            'insurance' => array(
                "title" => 'Insurance',
                'child' => array(
                    array('args' => 'microbank/backoffice,insurance,product', 'title' => 'Insurance Product'),
                    array('args' => 'microbank/backoffice,insurance,contract', 'title' => 'Insurance Contract'),
                )
            ),
            */
            /*
            'setting' => array(
                "title" => 'Global Setting',
                'child' => array(
                   // array('args' => 'microbank/backoffice,setting,codingRule', 'title' => 'Coding Rule'),
                )
            ),
            */
            'financial' => array(
                "title" => 'Treasure',
                'child' => array(
                    array('args' => 'microbank/backoffice,financial,hqVault', 'title' => 'HQ CIV'),
                    array('args' => 'microbank/backoffice,partner,bank', 'title' => 'Bank - Partner'),
                    array('args' => 'microbank/backoffice,financial,hqBank', 'title' => 'Bank - Public'),
                    array('args' => 'microbank/backoffice,financial,branchBank', 'title' => 'Bank - Private'),
                    //array('args' => 'microbank/backoffice,treasure,branchLimit', 'title' => 'Branch Limit'),
                    array('args' => 'microbank/backoffice,financial,exchangeRate', 'title' => 'Exchange Rate'),
                    //array('args' => 'microbank/backoffice,financial,requestToPrepayment', 'title' => 'Request To Prepayment'),
                    array('args' => 'microbank/backoffice,financial,checkBillPay', 'title' => 'Check BillPay'),
                    array('args' => 'microbank/backoffice,treasure,branchList', 'title' => 'Branch CIV'),
                    array('args' => 'microbank/backoffice,treasure,settingCIVExtraType', 'title' => 'CIV Ext.Trade Type'),
                    //array('args' => 'microbank/backoffice,gl_tree,index', 'title' => 'GL Accounts'),
                    //array('args' => 'microbank/backoffice,gl_tree,voucherIndex', 'title' => 'Manual Voucher')
                )
            ),
            'data_center' => array(
                "title" => 'Data Center',
                'child' => array(
                    array('args' => 'microbank/backoffice,data_center_branch,index', 'title' => 'Branch'),
                    array('args' => 'microbank/backoffice,data_center_staff,index', 'title' => 'Staff'),
                    array('args' => 'microbank/backoffice,data_center_partner,index', 'title' => 'Partner'),
                    array('args' => 'microbank/backoffice,data_center_bank,index', 'title' => 'Bank'),
                    array('args' => 'microbank/backoffice,data_center_member,index', 'title' => 'Client-Member'),
                    array('args' => 'microbank/backoffice,data_center_certification,index', 'title' => 'Certification'),
                    array('args' => 'microbank/backoffice,data_center_business,index', 'title' => 'Business'),
                    array('args' => 'microbank/backoffice,data_center_daily,index', 'title' => 'Daily Report'),
                )
            ),
            'report' => array(
                "title" => 'Report',
                'child' => array(
                    array('args' => 'microbank/backoffice,report_loan,loan', 'title' => 'Loan-Summary'),
                    array('args' => 'microbank/backoffice,report_loan_analysis,index', 'title' => 'Loan-Analysis'),
                    array('args' => 'microbank/backoffice,report_super_loan,index', 'title' => 'Great Loan'),
                    array('args' => 'microbank/backoffice,report_repayment,repayment', 'title' => 'Repayment'),
                    array('args' => 'microbank/backoffice,report_disbursement,disbursement', 'title' => 'Disbursement'),
                    array('args' => 'microbank/backoffice,report_outstanding,outstanding', 'title' => 'Outstanding'),
                    //array('args' => 'microbank/backoffice,report_general_ledger,generalLedger', 'title' => 'General Ledger'),
                    //array('args' => 'microbank/backoffice,report_gl_transaction,glTransaction', 'title' => 'GL Transaction List'),
                    //array('args' => 'microbank/backoffice,report_financial_statement,financialStatement', 'title' => 'Financial Statement'),
                    array('args' => 'microbank/backoffice,report_customer,customer', 'title' => 'Customer'),
//                    array('args' => 'microbank/backoffice,report_cross_application,crossApplication', 'title' => 'Cross Application'),
                    array('args' => 'microbank/backoffice,report_passbook,balanceSheet', 'title' => 'Balance Sheet (Dev)'),
                    array('args' => 'microbank/backoffice,report_passbook,incomeStatement', 'title' => 'Income Statement(Dev)'),
                    array('args' => 'microbank/backoffice,report_passbook,journalVoucher', 'title' => 'Journal Voucher'),
                    array('args' => 'microbank/backoffice,report_passbook,receivableInterest', 'title' => 'Receivable Interest'),
                    array('args' => 'microbank/backoffice,report_passbook,accountBalance', 'title' => 'Account Balance'),
                    //array('args' => 'microbank/backoffice,report_accounting,balanceSheet', 'title' => 'Balance Sheet (GL)'),
                    //array('args' => 'microbank/backoffice,report_accounting,incomeStatement', 'title' => 'Income Statement(GL)'),
                )
            ),
            'monitor' => array(
                'icon_key' => 'report',//采用同样的图标
                "title" => 'Monitor',
                'child' => array(
                    array('args' => 'microbank/backoffice,monitor,showOverdueLoanPage', 'title' => 'Overdue Loan', 'task_type' => userTaskTypeEnum::MONITOR_OVERDUE_LOAN),
                    array('args' => 'microbank/backoffice,monitor,loanContractPage', 'title' => 'Loan Contract'),
                )
            ),
            'tools' => array(
                "title" => 'Tools',
                'child' => array(
                    array('args' => 'microbank/backoffice,tools,calculator', 'title' => 'Calculator'),
                    //array('args' => 'microbank/backoffice,tools,googleMap', 'title' => 'Google Map'),
                )
            )
        );
        return $indexMenu;
    }

    /**
     * 定义Operator menu
     * @return array
     */
    private function getOperatorMenu()
    {
        $cert_lang = enum_langClass::getCertificationTypeEnumLang();
        $relative_cert_child = array();
        $relative_profile = member_relativeClass::getRelativeProfileCertType();
        foreach( $relative_profile as $type=>$v ){
            /*$relative_cert_child[$type] = array(
                'args' => 'microbank/backoffice,operator,relativeCertificateFile',
                'title' => $cert_lang[$type]
            );*/
            $relative_cert_child[$type] = $cert_lang[$type];  // 特殊菜单
        }


        $indexMenu = array(
            'new_client' => array(
                "title" => "New Client",
                'args' => "microbank/backoffice,operator,newClient",
                'task_type' => userTaskTypeEnum::OPERATOR_NEW_CLIENT
            ),
            'loan_consult' => array(
                "title" => "New Consult",
                'args' => "microbank/backoffice,operator,loanConsult",
                'task_type' => userTaskTypeEnum::OPERATOR_NEW_CONSULT
            ),
            'certification_file' => array(
                "title" => "Certification File",
                'args' => "microbank/backoffice,operator,certificationFile",
                'task_type' => userTaskTypeEnum::OPERATOR_NEW_CERT
            ),
            'relative_cert_file' => array(
                "title" => "Relative Cert File",
                'args' => "microbank/backoffice,operator,relativeCertificateFile",
                'task_type' => userTaskTypeEnum::OPERATOR_RELATIVE_NEW_CERT,
                'child' => $relative_cert_child
            ),
            'pending_verify' => array(
                "title" => "Pending Verify",
                'args' => "microbank/backoffice,operator,pendingVerify"
            ),
            'my_client' => array(
                "title" => "My Client",
                'args' => "microbank/backoffice,web_credit,client"
            ),
            'my_consultation' => array(
                "title" => "My Consultation",
                'args' => "microbank/backoffice,operator,consultation",
                'task_type' => userTaskTypeEnum::OPERATOR_MY_CONSULT,
                "task_is_msg" => 1
            ),
            'client_change_photo' => array(
                "title" => "Client Change Photo",
                'args' => "microbank/backoffice,operator,clientChangePhoto",
                'task_type' => userTaskTypeEnum::CHANGE_CLIENT_ICON
            ),
            'client_change_trading_password' => array(
                "title" => "Client Change Trading Password",
                'args' => "microbank/backoffice,operator,clientChangeTradingPasswordIndex",
                'task_type' => userTaskTypeEnum::CLIENT_CHANGE_TRADING_PASSWORD
            ),
            /*
            "client_profile"=>array(
                "title"=>"Client Profile",
                "args"=>"microbank/backoffice,operator,clientProfileIndex"
            ),*/
            'request_lock' => array(
                "title" => "Request Lock",
                'args' => "microbank/backoffice,operator,requestLock"
            ),
            'device_apply' => array(
                "title" => "Device Apply",
                'args' => "microbank/backoffice,operator,deviceApply",
                'task_type' => userTaskTypeEnum::CHANGE_CLIENT_DEVICE
            ),
            'pending_committee_approve' => array(
                "title" => "Pending Committee Approve",
                'child' => array(
                    array('args' => 'microbank/backoffice,loan_committee,approveCreditApplication', 'title' => 'Approve Credit Application'),
                    array('args' => 'microbank/backoffice,loan_committee,approvePrepaymentRequest', 'title' => 'Approve Prepayment Request'),
                    array('args' => 'microbank/backoffice,loan_committee,approvePenaltyRequest', 'title' => 'Approve Penalty Request'),
                    array('args' => 'microbank/backoffice,loan_committee,approveWrittenOffRequest', 'title' => 'Approve Writtenoff Request'),
                    array('args' => 'microbank/backoffice,loan_committee,approveWithdrawMortgageRequest', 'title' => 'Approve Withdraw Mortgage'),
                )
            ),
            'data_center' => array(
                "title" => 'Data Center',
                'child' => array(
                    array('args' => 'microbank/backoffice,data_center_member,index', 'title' => 'Client-Member'),
                    array('args' => 'microbank/backoffice,data_center_business,index', 'title' => 'Business'),
                )
            ),
            'sms' => array(
                "title" => "SMS",
                'args' => "microbank/backoffice,dev,sms"
            ),
            /*
            'help' => array(
                "title" => "CMS",
                'args' => "microbank/backoffice,operator,help"
            ),
            */
            /*'check_cbc' => array(
                "title" => "Check CBC",
                'args' => "microbank/backoffice,operator,checkCbc"
            ),*/
            'complaint_advice' => array(
                "title" => "Complaint and Advice",
                'args' => "microbank/backoffice,operator,addComplaintAdvice"
            ),

            'report' => array(
                "title" => 'Report',
                'child' => array(
                    array('args' => 'microbank/backoffice,report_loan,loan', 'title' => 'Loan-Summary'),
                    array('args' => 'microbank/backoffice,report_loan_analysis,index', 'title' => 'Loan-Analysis'),
                    array('args' => 'microbank/backoffice,report_super_loan,index', 'title' => 'Great Loan')
                    //array('args' => 'microbank/backoffice,operator,warningOfExpireDate', 'title' => 'Warning Of Expire Date'),
                    //array('args' => 'microbank/backoffice,operator,warningOfOverdueLoan', 'title' => 'Warning Of Overdue Loan'),
                )
            ),
            'monitor' => array(
                'icon_key' => 'complaint_advice',//采用同样的图标
                "title" => 'Monitor',
                'child' => array(
                    array('args' => 'microbank/backoffice,monitor,showOverdueLoanPage', 'title' => 'Overdue Loan', 'task_type' => userTaskTypeEnum::MONITOR_OVERDUE_LOAN),
                )
            ),
            'tools' => array(
                "title" => "Tools",
                'child' => array(
                    array('args' => 'microbank/backoffice,tools,searchClient', 'title' => 'Search Client'),
                    array('args' => 'microbank/backoffice,tools,searchIdSn', 'title' => 'Search Id Number'),
                    array('args' => 'microbank/backoffice,tools,searchAssetSn', 'title' => 'Search Asset Id'),
                    array('args' => 'microbank/backoffice,tools,clearTradingPwdLock', 'title' => 'Clear Password Lock'),
                    array('args' => 'microbank/backoffice,tools,searchAceAccount', 'title' => 'Search ACE Account'),
                )
            )
        );
        return $indexMenu;
    }

    /**
     * 定义BranchManger menu
     * @return array
     */
    private function getBranchMangerMenu()
    {
        $indexMenu = array(
            'client' => array(
                "title" => "Client",
                'args' => "microbank/backoffice,branch_manager,client",
                "task_type" => userTaskTypeEnum::BM_NEW_CLIENT,
                "task_is_msg" => 1
            ),
            'co_submit_client' => array(
                "title" => "CO Finished Research",
                'args' => "microbank/backoffice,branch_manager,coSubmitTaskList",
                "task_type" => userTaskTypeEnum::CO_SUBMIT_BM,
                "task_is_msg" => 1
            ),
            'loan_consult' => array(
                "title" => "Loan Consult",
                'args' => "microbank/backoffice,branch_manager,loanConsult",
                "task_type" => userTaskTypeEnum::BM_NEW_CONSULT,
                "task_is_msg" => 1
            ),
            'request_credit' => array(
                "title" => "Request For Credit",
                'args' => "microbank/backoffice,branch_manager,requestCredit",
                "task_type" => userTaskTypeEnum::BM_REQUEST_FOR_CREDIT,
                "task_is_msg" => 1
            ),
            'request_written_off' => array(
                "title" => "Request For WrittenOff",
                'args' => "microbank/backoffice,branch_manager,requestWrittenOff"
            ),
            'credit_officer' => array(
                "title" => "Credit Officer",
                'args' => "microbank/backoffice,branch_manager,creditOfficer"
            ),
            /*
            'overdue_contract' => array(
                "title" => "Overdue Contract",
                'args' => "microbank/backoffice,branch_manager,overdueContract"
            ),
            */

            'monitor' => array(
                'icon_key' => 'complaint_advice',//采用同样的图标
                "title" => 'Monitor',
                'child' => array(
                    array('args' => 'microbank/backoffice,monitor,showOverdueLoanPage', 'title' => 'Overdue Loan', 'task_type' => userTaskTypeEnum::MONITOR_OVERDUE_LOAN),
                )
            ),
            'overdue' => array(
                "title" => "Overdue Task By CO",
                'args' => "microbank/backoffice,branch_manager,overdue"
            ),

            /*
            'point' => array(
                "title" => "Point",
                'args' => "microbank/backoffice,branch_manager,point"
            ),*/
            'counter_business' => array(
                "title" => "Counter Business",
                'child' => array(
                    array('args' => 'microbank/backoffice,branch_manager,cashInVault', 'title' => 'Cash In Vault'),
                    array('args' => 'microbank/backoffice,branch_manager,cashOnHand', 'title' => 'Cash On Hand'),
                    array('args' => 'microbank/backoffice,branch_manager,mortgagePage', 'title' => 'Mortgage'),
                    array('args' => 'microbank/backoffice,branch_manager,journalVoucher', 'title' => 'Journal Voucher'),
                )
            ),
            'data_center' => array(
                "title" => 'Data Center',
                'child' => array(
                    array('args' => 'microbank/backoffice,data_center_member,index', 'title' => 'Client-Member'),
                    array('args' => 'microbank/backoffice,data_center_business,index', 'title' => 'Business'),
                )
            ),
            'report' => array(
                "title" => 'Report',
                'child' => array(
                    array('args' => 'microbank/backoffice,report_loan,loan', 'title' => 'Loan-Summary'),
                    array('args' => 'microbank/backoffice,report_loan_analysis,index', 'title' => 'Loan-Analysis'),
                    array('args' => 'microbank/backoffice,report_super_loan,index', 'title' => 'Great Loan'),
                )
            )
        );
        return $indexMenu;
    }

    /**
     * 定义BranchManger menu
     * @return array
     */
    private function getDeveloperMenu()
    {
        $indexMenu = array(
            'app_version' => array(
                "title" => "App Version",
                'args' => "microbank/backoffice,dev,appVersion"
            ),
            'function_switch' => array(
                "title" => "Function Switch",
                'args' => "microbank/backoffice,dev,functionSwitch"
            ),
            'business_switch' => array(
                "title" => "Business Switch",
                'child' => array(
                    array('args' => 'microbank/backoffice,dev,businessSwitch', 'title' => 'Basic'),
                    array('args' => 'microbank/backoffice,dev,memberBizLimit', 'title' => 'Member Biz Limit'),
                    array('args' => 'microbank/backoffice,dev,counterBizSetting', 'title' => 'Counter Biz'),
                    array('args' => 'microbank/backoffice,setting,shortCode', 'title' => 'Short Code'),
                    array('args' => 'microbank/backoffice,dev,moduleBusiness', 'title' => 'Module Business'),
                )
            ),
            'member_setting' => array(
                "title" => "Member Setting",
                'child' => array(
                    array('args' => "microbank/backoffice,dev,memberSetting", 'title' => 'Member Setting'),
                    array('args' => "microbank/backoffice,dev,resumeRejectClient", 'title' => 'Resume Reject-Client')
                )
            ),
            'credit_loan' => array(
                "title" => 'Loan Setting',
                'child' => array(
                    array('args' => 'microbank/backoffice,dev,creditProcess', 'title' => 'Credit Process'),
                    array('args' => 'microbank/backoffice,dev,creditGrantRate', 'title' => 'Credit Grant'),
                    //array('args' => 'microbank/backoffice,dev,authorizedContractFeeRate', 'title' => 'Authorized Contract Fee'),
                    array('args' => 'microbank/backoffice,dev,grantVoterLimit', 'title' => 'Limit Of Credit Voter'),
                    array('args' => 'microbank/backoffice,dev,writtenOffVoterLimit', 'title' => 'Limit Of Written Off'),
                )
            ),

            'partner_setting' => array(
                "title" => "Partner limit setting",
                'args' => "microbank/backoffice,dev,partnerLimit"
            ),

            'issue_ic_card' => array(
                "title" => "Issue IC Card",
                'args' => "microbank/backoffice,dev,issueIcCard"
            ),
            'adjust_passbook_account' => array(
                "title" => "Adjust Passbook Account",
                'args' => "microbank/backoffice,dev,passbookAccountAdjust"
            ),
            'check_trading' => array(
                "title" => "Check Trading",
                "child" => array(
                    array('args' => "microbank/backoffice,dev,checkDeposit", 'title' => 'Deposit'),
                    array('args' => "microbank/backoffice,dev,checkWithdraw", 'title' => 'Withdraw')
                )
            ),
            'global' => array(
                "title" => "Global",
                'args' => "microbank/backoffice,dev,global"
            ),
            'sms' => array(
                "title" => "SMS",
                //'args' => "microbank/backoffice,dev,sms",
                'child' => array(
                    array('args' => "microbank/backoffice,dev,sms", 'title' => 'SMS List'),
                    array('args' => "microbank/backoffice,dev,smsSendTest", 'title' => 'Send SMS')

                ),
            ),
            'push_notification' => array(
                "title" => "Push Notification",
                'args' => "microbank/backoffice,dev,pushNotification"
            ),
            'close_system' => array(
                "title" => "Close System",
                'args' => "microbank/backoffice,dev,closeSystem"
            ),
            'ace_account' => array(
                "title" => "ACE Account",
                'args' => "microbank/backoffice,dev,memberAceAccount"
            ),
            'sql_query' => array(
                "title" => "SQL Query",
                'args' => "microbank/backoffice,dev,sqlQuery"
            ),

        );
        return $indexMenu;
    }


}
