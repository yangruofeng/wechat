<?php

class requestControl extends wap_operator_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout('empty_layout');
        Tpl::setDir('request');
    }

    public function indexOp()
    {
        $this->pageCheckToken();
        Tpl::output('html_title', 'Request');
        Tpl::output('header_title', 'Request');
        Tpl::output('nav_footer', 'request');
        Tpl::showPage('index');
    }

    public function getRequestDataOp()
    {
        $params['page_num'] = $_GET['page_num'];
        $params['page_size'] = $_GET['page_size'];
        $params['state'] = $_GET['state'];
        $params['officer_id'] = cookie('member_id');
        $ret = credit_officerClass::getAllotLoanConsultPageListResult($params);
        $data = $ret->DATA;
        $list = $data['list'];
        foreach ($list as $key => $value) {
            $str = 'New';
            switch ($value['state']) {
                case loanApplyStateEnum::LOCKED :
                    $str = 'Locked';
                    break;
                case loanApplyStateEnum::CREATE :
                    $str = 'New';
                    break;
                case loanApplyStateEnum::OPERATOR_REJECT :
                    $str = 'Reject';
                    break;
                case loanApplyStateEnum::ALLOT_CO :
                    $str = 'Allot';
                    break;
                case loanApplyStateEnum::CO_HANDING :
                    $str = 'Handing';
                    break;
                case loanApplyStateEnum::CO_CANCEL :
                    $str = 'Cancel';
                    break;
                case loanApplyStateEnum::CO_APPROVED :
                    $str = 'Approved';
                    break;
                default:
                    break;
            }
            $list[$key]['state'] = $str;
            $list[$key]['apply_time'] = timeFormat($value['apply_time']);
        }
        $data['list'] = $list;
        if ($ret->STS) {
            return new result(true, L('tip_success'), $data);
        } else {
            return new result(false, L('tip_code_' . $ret->CODE));
        }
    }

    public function detailOp()
    {
        $this->pageCheckToken();
        $data['request_id'] = $_GET['uid'];
        $url = ENTRY_API_SITE_URL . '/co.get.loan.request.detail.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if (!$rt['DATA']['request_detail']['member_id']) {
            Tpl::output('data', $rt['DATA']);
            Tpl::output('html_title', 'Bind Client');
            Tpl::output('header_title', 'Bind Client');
            Tpl::showPage('client.bind');
        } else {
            Tpl::output('data', $rt['DATA']);
            Tpl::output('html_title', 'Request Detail');
            Tpl::output('header_title', 'Request Detail');
            Tpl::showPage('detail');
        }

    }

    public function loanConsultDetailOp()
    {
        $this->pageCheckToken();
        $uid = $_GET['uid'];
        $m_loan_consult = new loan_consultModel();
        $ret = $m_loan_consult->getConsultById($uid);
        if (!$ret) {
            $this->pageErrorMsg(L('tip_code_' . errorCodesEnum::INVALID_PARAM));
        }
        Tpl::output('id', $uid);
        Tpl::output('html_title', 'Request Detail');
        Tpl::output('header_title', 'Request Detail');
        Tpl::showPage('handle.first');
    }

    public function handleFirstOp()
    {
        $this->pageCheckToken();
        Tpl::output('id', $_GET['id']);
        Tpl::output('html_title', 'Request Detail');
        Tpl::output('header_title', 'Request Detail');
        Tpl::showPage('handle.first');
    }

    public function ajaxHandleFirstOp()
    {
        $re = $this->ajaxCheckToken();
        if (!$re->STS) {
            return $re;
        }
        $params = $_POST;
        $params['officer_id'] = cookie('member_id');
        $rt = credit_officerClass::submitLoanConsultHandle($params);
        if ($rt->STS) {
            return new result(true, L('tip_success'));
        } else {
            return new result(false, $rt->MSG);
        }
    }

    public function handleSecondOp()
    {
        $url = ENTRY_API_SITE_URL . '/co.get.all.loan.product.php';
        $rt = curl_post($url, array());
        $rt = json_decode($rt, true);
        Tpl::output('request_id', $_GET['id']);
        Tpl::output('product', $rt['DATA']['list']);
        Tpl::output('html_title', 'Request Detail');
        Tpl::output('header_title', 'Request Detail');
        Tpl::showPage('handle.second');
    }

    public function ajaxHandleSecondOp()
    {
        $data = $_POST;
        $data['token'] = cookie('token');
        $url = ENTRY_API_SITE_URL . '/co.loan.request.bind.product.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            if ($rt['DATA']['request_detail']) {
                return new result(true, '11', $rt['DATA']);
            } else {
                return new result(false, 'Have Canceled');
            }
        } else {
            return new result(false, 'Have Canceled');
        }
    }

    public function handleThirdOp()
    {
        $data['request_id'] = $_GET['request_id'];
        $url = ENTRY_API_SITE_URL . '/co.get.loan.request.detail.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        Tpl::output('type', $_GET['type']);
        Tpl::output('detail', $rt['DATA']['request_detail']);
        Tpl::output('html_title', 'Request Detail');
        Tpl::output('header_title', 'Request Detail');
        Tpl::showPage('handle.third');
    }

    public function ajaxHandleApprovedOp()
    {
        $data['request_id'] = $_POST['request_id'];
        $data['token'] = cookie('token');
        $url = ENTRY_API_SITE_URL . '/co.loan.request.approved.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, '11', $rt['DATA']);
        } else {
            return new result(false, $rt['MSG']);
        }
    }

    public function ajaxBindClientOp()
    {
        $data = $_POST;
        $data['token'] = cookie('token');
        $url = ENTRY_API_SITE_URL . '/co.loan.request.bind.member.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, '11', $rt['DATA']);
        } else {
            return new result(false, $rt['MSG']);
        }
    }

    public function checkApplicationOp()
    {
        $this->pageCheckToken();
        Tpl::output('html_title', 'Check Consult');
        Tpl::output('header_title', 'Check Consult');
        Tpl::showPage('check');
    }

    public function checkOverdueOp()
    {
        $this->pageCheckToken();
        Tpl::output('html_title', 'Check Overdue');
        Tpl::output('header_title', 'Check Overdue');
        Tpl::showPage('check_overdue');
    }

    public function getOverdueDataOp()
    {
        $params['page_num'] = $_GET['page_num'];
        $params['page_size'] = $_GET['page_size'];
        $params['state'] = $_GET['state'];
        $params['officer_id'] = cookie('member_id');
        $rt = credit_officerClass::getAllotOverdueContractListResult($params);
        if ($rt->STS) {
            return new result(true, L('tip_success'), $rt->DATA);
        } else {
            return new result(false, L('tip_code_' . $rt->CODE));
        }
    }

    public function dealOverdueContractOp()
    {
        $uid = $_GET['uid'];
        $rt = credit_officerClass::getOverdueContractTaskDetail($uid);
        Tpl::output('detail', $rt->DATA['detail']);
        Tpl::output('list', $rt->DATA['list']);
        Tpl::output('html_title', 'Task Detail');
        Tpl::output('header_title', 'Task Detail');
        Tpl::showPage('overdue_handle');
    }

    public function overdueOperOp()
    {
        $this->pageCheckToken();
        $type = $_GET['type'];
        Tpl::output('html_title', 'Task Done');
        Tpl::output('header_title', 'Task Done');
        if ($type == 1) {
            Tpl::output('html_title', 'Add Log');
            Tpl::output('header_title', 'Add Log');
        }
        Tpl::showPage('overdue_submit');
    }

    public function ajaxEditOverdueOp()
    {
        $data = $_POST;
        $data['officer_id'] = cookie('member_id');
        $data['token'] = cookie('token');
        $url = ENTRY_API_SITE_URL . '/officer.submit.overdue.task.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['CODE'] == errorCodesEnum::INVALID_TOKEN || $rt['CODE'] == errorCodesEnum::NO_LOGIN) {
            setNcCookie('token', '');
            setNcCookie('member_id', '');
            setNcCookie('user_code', '');
            setNcCookie('user_name', '');
            return new result(false, L('tip_code_' . $rt['CODE']), array(), $rt['CODE']);
        }
        if ($rt['STS']) {
            return new result(true, L('tip_success'), $rt['DATA']);
        } else {
//            return new result(false, L('tip_code_' . $rt['CODE']));
            return new result(false, $rt['MSG']);
        }
    }
}
