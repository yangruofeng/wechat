<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/19/2018
 * Time: 1:26 PM
 */
class commonControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator,certification');
        Tpl::setLayout("empty_layout");
        Tpl::setDir("common");
        Tpl::output("html_title", "BACK-OFFICE");
    }

    public function passbookAccountFlowPageByBookIdOp()
    {
        $book_id = $_GET['book_id'];
        $currency = $_GET['currency'];
        $m = new passbook_accountModel();
        $account = $m->find(array("book_id" => $book_id, "currency" => $currency));
        $this->passbookAccountFlowPageOp(array("account_id" => $account['uid']));
    }

    public function passbookAccountFlowPageOp($p)
    {
        $p = $p ?: $_REQUEST;
        $account_id = $p['account_id'];
        $obj_uid = $p['obj_uid'];
        $currency = $p['currency'];
        $obj_type = $p['obj_type'];
        $obj_key = $p['obj_key'];//针对system account
        if ($account_id) {
            $m = new passbook_accountModel();
            $account = $m->find(array("uid" => $account_id));
            if (!$account) {
                showMessage("Invalid account");
            }
            $book_info = (new passbookModel())->find(array("uid" => $account['book_id']));
            Tpl::output("book_info", $book_info);
            Tpl::output("title", $book_info['book_name']);
            Tpl::output("currency", $account['currency']);
            Tpl::output("book_id", $account['book_id']);
            Tpl::output("account_id", $account_id);
        } else {
            switch ($obj_type) {
                case objGuidTypeEnum::SITE_BRANCH:
                    $obj = new objectBranchClass($obj_uid);
                    $passbook = $obj->getPassbook();
                    break;
                case objGuidTypeEnum::BANK_ACCOUNT:
                    $obj = new objectSysBankClass($obj_uid);
                    $passbook = $obj->getPassbook();
                    break;
                case objGuidTypeEnum::UM_USER:
                    $obj = new objectUserClass($obj_uid);
                    $passbook = $obj->getUserPassbook();
                    break;
                case objGuidTypeEnum::CLIENT_MEMBER:
                    $obj = new objectMemberClass($obj_uid);
                    $passbook = $obj->getSavingsPassbook();
                    break;
                case objGuidTypeEnum::PARTNER:
                    $obj = new objectPartnerClass($obj_uid);
                    $passbook = $obj->getPassbook();
                    break;
                case objGuidTypeEnum::GL_ACCOUNT:
                    $obj = new objectGlAccountClass($obj_key);
                    $passbook = $obj->getPassbook();
                    break;
                default:
                    return array('sts' => false, 'data' => array());
            }
            $book_info = $passbook->getPassbookInfo();
            Tpl::output("book_info", $book_info);
            Tpl::output("title", $book_info['book_name']);
            Tpl::output("currency", $currency);
            Tpl::output("book_id", $book_info['uid']);
        }
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        $is_ajax = $p['is_ajax'];
        Tpl::output("is_ajax", $is_ajax);
        Tpl::showPage("passbook.account.flow.index");
    }

    public function getPassbookAccountFlowListOp($p)
    {
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $page_num = $p['pageNumber'];
        $page_size = $p['pageSize'];
        $filters = array(
            'start_date' => $date_start,
            'end_date' => $date_end
        );
        $book_id = $p['book_id'];
        $account_id = $p['account_id'];
        $currency = $p['currency'];
        $is_ajax = $p['is_ajax'];

        if ($book_id > 0) {
            $book_info = (new passbookModel())->getRow(array("uid" => $book_id));
            $passbook = new passbookClass($book_info);

        } elseif ($account_id > 0) {
            $account = (new passbook_accountModel())->find(array("uid" => $account_id));
            if ($account) {
                $book_info = (new passbookModel())->getRow(array("uid" => $account['book_id']));
                $passbook = new passbookClass($book_info);
            } else {
                return array('sts' => false, 'data' => array());
            }
        }
        if (!$passbook) {
            return array('sts' => false, 'data' => array());
        }
        $m = new passbook_account_flowModel();
        $page_data = $m->searchFlowListByBookAndCurrency($passbook, $currency, $page_num, $page_size, $filters);
        return array(
            'sts' => true,
            'data' => $page_data->rows,
            'pageNumber' => $page_data->pageIndex,
            'pageSize' => $page_data->pageSize,
            'total' => $page_data->count,
            'pageTotal' => $page_data->pageCount,
            'pageType' => $p['type'],
            'is_ajax' => $is_ajax
        );

    }

    public function passbookAccountVoucherFlowPageOp($p)
    {
        $p = $p ?: $_REQUEST;
        $is_ajax = $p['is_ajax'];
        $trade_id = $p['trade_id'];
        $m = new passbook_tradingModel();
        $info = $m->find(array("uid" => $trade_id));
        $data = $m->getTradingFlows($trade_id);
        Tpl::output("list", $data);
        Tpl::output("info", $info);
        Tpl::output("is_ajax", $is_ajax);
        Tpl::showpage('passbook.voucher.item');
    }

    public function passbookJournalVoucherPageOp($p)
    {
        $p = $p ?: $_REQUEST;
        $obj_uid = $p['obj_uid'];
        $obj_type = $p['obj_type'];
        $obj_key = $p['obj_key'];//针对system account
        switch ($obj_type) {
            case objGuidTypeEnum::SITE_BRANCH:
                $obj = new objectBranchClass($obj_uid);
                $passbook = $obj->getPassbook();
                break;
            case objGuidTypeEnum::BANK_ACCOUNT:
                $obj = new objectSysBankClass($obj_uid);
                $passbook = $obj->getPassbook();
                break;
            case objGuidTypeEnum::UM_USER:
                $obj = new objectUserClass($obj_uid);
                $passbook = $obj->getUserPassbook();
                break;
            case objGuidTypeEnum::CLIENT_MEMBER:
                $obj = new objectMemberClass($obj_uid);
                $passbook = $obj->getSavingsPassbook();
                break;
            case objGuidTypeEnum::PARTNER:
                $obj = new objectPartnerClass($obj_uid);
                $passbook = $obj->getPassbook();
                break;
            case objGuidTypeEnum::GL_ACCOUNT:
                $obj = new objectGlAccountClass($obj_key);
                $passbook = $obj->getPassbook();
                break;
            default:
                return array('sts' => false, 'data' => array());
        }
        $book_info = $passbook->getPassbookInfo();
        Tpl::output("book_info", $book_info);
        Tpl::output("title", $book_info['book_name']);
        Tpl::output("book_id", $book_info['uid']);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $trade_type = global_settingClass::getAllTradingType();
        Tpl::output("trade_type", $trade_type);
        $is_ajax = $p['is_ajax'];
        Tpl::output("is_ajax", $is_ajax);
        Tpl::showpage('passbook.voucher.index');
    }

    public function getPassbookJournalVoucherListOp($p)
    {
        $trade_id = $p['trade_id'];
        $trade_type = $p['trade_type'];
        $remark = $p['remark'];
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $filters = array(
            'trade_id' => $trade_id,
            'trade_type' => $trade_type,
            'remark' => $remark,
            'start_date' => $date_start,
            'end_date' => $date_end
        );

        $pageNumber = $p['pageNumber'];
        $pageSize = $p['pageSize'];
        $book_id = $p['book_id'];
        $book_info = (new passbookModel())->getRow(array("uid" => $book_id));
        $passbook = new passbookClass($book_info);
        if (!$passbook) {
            return array('sts' => false, 'data' => array());
        }
        return balanceSheetClass::getPassbookJournalVoucherData($passbook, $pageNumber, $pageSize, $filters);
    }

    /**
     * 会员详情
     * @param $p
     */
    function showClientDetailOp($p)
    {
        $p = $p ?: $_REQUEST;
        $data = memberDataClass::getMemberDetail($p);
        Tpl::output("detail", $data['detail']);
        Tpl::output("contract_info", $data['contract_info']);
        Tpl::output("loan_summary", $data['loan_summary']);
        Tpl::output('credit_info', $data['credit_info']);
        Tpl::output("contracts", $data['contracts']);
        Tpl::output("savings_balance", $data['savings_balance']);

        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        Tpl::showpage('client.detail.index');
    }

    /**
     * member注册信息
     * @param $p
     */
    public function showClientRegisterPageOp($p)
    {
        $member_id = intval($p['uid']);
        $data = memberDataClass::getMemberRegisterBy($member_id);
        Tpl::output("detail", $data);

    }

    /**
     * 贷款列表页
     * @param $p
     */
    public function showClientLoanPageOp($p)
    {
        $uid = $p['uid'];
        Tpl::output('uid', $uid);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        $contract_state = (new loanContractStateEnum())->Dictionary();
        Tpl::output("contract_state", $contract_state);
    }

    /**
     * 获取贷款列表数据
     * @param $p
     * @return array
     */
    public function getClientLoanListOp($p)
    {
        $member_id = intval($p['uid']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $state = intval($p['state']);

        $filters = array(
            'member_id' => $member_id,
            'state' => $state,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = memberDataClass::getLoanList($pageNumber, $pageSize, $filters);
        $contract_state = (new loanContractStateEnum())->Dictionary();
        $data['state'] = $contract_state;
        return $data;
    }

    /**
     * member商业页面
     * @param $p
     */
    public function showClientBusinessPageOp($p)
    {
        $member_id = intval($p['uid']);
        $data = memberDataClass::getMemberBusiness($member_id);
        Tpl::output('business', $data);
    }

    /**
     * member收入页面
     * @param $p
     */
    public function showClientSalaryPageOp($p)
    {
        $member_id = intval($p['uid']);
        $data = memberDataClass::getMemberSalary($member_id);
        Tpl::output('salary_income', $data);
    }

    /**
     * member额外收入页面
     * @param $p
     */
    public function showClientAttachmentPageOp($p)
    {
        $member_id = intval($p['uid']);
        $data = memberDataClass::getMemberAttachment($member_id);
        Tpl::output('attachment', $data);
    }

    /**
     * member证件页面
     * @param $p
     */
    public function showClientIdentityPageOp($p)
    {
        $member_id = intval($p['uid']);
        $data = memberDataClass::getMemberIdentity($member_id);
        Tpl::output('identity', $data);
    }

    /**
     * member资产页面
     * @param $p
     */
    public function showClientAssetsPageOp($p)
    {
        $member_id = intval($p['uid']);
        $data = memberDataClass::getMemberAssets($member_id);
        Tpl::output('assets', $data);
    }

    /**
     * member授信记录页面
     * @param $p
     */
    public function showClientCreditHistoryPageOp($p)
    {
        $uid = $p['uid'];
        Tpl::output('uid', $uid);
    }

    /**
     * 获取member授信记录数据
     * @param $p
     * @return array
     */
    public function getClientCreditHistoryListOp($p)
    {
        $member_id = intval($p['uid']);
        $filter = array(
            'member_id' => $member_id,
            'state' => 100,
        );
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $m_member_credit_grant = new member_credit_grantModel();
        $rt = $m_member_credit_grant->getCreditGrantList($pageNumber, $pageSize, $filter);
        $data = $rt->DATA;
        $rows = $data['rows'];
        $total = $data['total'];
        $pageTotal = $data['page_total'];

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
     * member信用变化日志页面
     * @param $p
     */
    public function showClientCreditLogPageOp($p)
    {
        $uid = $p['uid'];
        Tpl::output('uid', $uid);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
    }

    /**
     * 获取member信用变化日志数据
     * @param $p
     * @return array
     */
    public function getClientCreditLogListOp($p)
    {
        $member_id = intval($p['uid']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 50;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];

        $filters = array(
            'member_id' => $member_id,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = memberDataClass::getMemberCreditLogList($pageNumber, $pageSize, $filters);
        return $data;
    }

    /**
     * member授权记录页面
     * @param $p
     */
    public function showClientCreditAgreementPageOp($p)
    {
        $uid = $p['uid'];
        Tpl::output('uid', $uid);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
    }

    /**
     * member授权记录数据
     * @param $p
     * @return array
     */
    public function getClientCreditAgreementListOp($p)
    {
        $member_id = intval($p['uid']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];

        $filters = array(
            'member_id' => $member_id,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = memberDataClass::getMemberCreditAgreementList($pageNumber, $pageSize, $filters);
        return $data;
    }

    /**
     * member抵押物
     * @param $p
     */
    public function showClientMortgagePageOp($p)
    {
        $uid = $p['uid'];
        Tpl::output('uid', $uid);
    }

    /**
     * member抵押物数据
     * @param $p
     * @return mixed
     */
    public function getClientCreditMortgageListOp($p)
    {
        $member_id = intval($p['uid']);
        $filters = array(
            'member_id' => $member_id,
            'state' => 100,
        );
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = memberDataClass::getClientCreditMortgageList($pageNumber, $pageSize, $filters);
        return $data;
    }

    /**
     * cbc页面
     * @param $p
     */
    public function showClientCbcPageOp($p)
    {
        $member_id = intval($p['uid']);
        $data = memberDataClass::getClientCbc($member_id);
        Tpl::output('cbc', $data);
    }

    /**
     * markdown上传图片
     */
    public function markdownUploadToUpYunOp()
    {
        $default_dir = fileDirsEnum::MARKDOWN;
        $upload = new UploadFile();
        $upload->set('save_path', null);
        $upload->set('default_dir', $default_dir);
        $re = $upload->server2upun('editormd-image-file');
        if ($re == false) {
            return array('success' => 0, 'message' => 'Upload photo fail', 'url' => '');
        }
        $img_path = $upload->img_url;
        $img_path = getImageUrl($img_path);
        return array('success' => 1, 'message' => 'success', 'url' => $img_path);
    }

    public function showChangeStateLogPageOp($p)
    {
        $uid = $p['uid'];
        Tpl::output('uid', $uid);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
    }

    public function getClientChangeStateLogListOp($p)
    {
        $member_id = intval($p['uid']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 50;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];

        $filters = array(
            'member_id' => $member_id,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = memberDataClass::getClientChangeStateLogList($pageNumber, $pageSize, $filters);
        return $data;
    }

    public function showCreditCategoryOp($p){
        $member_id = intval($p['uid']);
        $data = loan_categoryClass::getAllCreditCategoryListOfMember($member_id);
        return $data;
    }

    public function showSceneImageOp($p){
        $member_id = intval($p['uid']);
        $data = M('biz_scene_image')->getRows(array('member_id'=>$member_id));
        return $data;
    }
}