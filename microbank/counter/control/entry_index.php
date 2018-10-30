<?php

/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2017/10/16
 * Time: 10:44
 */
class entry_indexControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("book_layout");
        Tpl::setDir("layout");
        Tpl::output("html_title", "Counter Office");
        Tpl::output("user_info", $this->user_info);
        Tpl::output("menu_items", $this->getResetMenu());

    }

    public function indexOp()
    {
        if ($_COOKIE['SITE_PRIVATE_KEY']) {
            $version = $_COOKIE['CLIENT_VERSION'];
            $app_name = 'samrithisak-client';
            $rt = versionClass::checkUpdate($app_name,$version);
            if($rt->DATA['is_required'] == 1){
                Tpl::output("version", $version);
                Tpl::setDir("home");
                Tpl::setLayout("empty_layout");
                Tpl::output('download_url',$rt->DATA['download_url_1']);
                Tpl::showPage("update.page");
                return;
            }
        }

        $r = new ormReader();
        $sql = "SELECT sd.depart_name,sb.branch_name FROM site_depart sd INNER JOIN site_branch sb ON sd.branch_id = sb.uid WHERE sd.uid = " . intval($this->user_info['depart_id']);
        $department_info = $r->getRow($sql);
        Tpl::output('department_info', $department_info);
        $obj_user = new objectUserClass($this->user_id);
        $trading_password = $obj_user->trading_password;
        Tpl::output("trading_password", $trading_password);
        Tpl::output("default_page", getUrl('member', 'homeIndex', array(), false, ENTRY_COUNTER_SITE_URL));

        $currency = (new currencyEnum())->Dictionary();
        Tpl::output('currency', $currency);

        $exchange_rate = M('common_exchange_rate')->find(array('first_currency' => currencyEnum::USD, 'second_currency' => currencyEnum::KHR));
        if (!$exchange_rate) {
            $exchange_rate_1 = M('common_exchange_rate')->find(array('second_currency' => currencyEnum::USD, 'first_currency' => currencyEnum::KHR));
            Tpl::output('exchange_rate_1', $exchange_rate_1);
        } else {
            Tpl::output('exchange_rate', $exchange_rate);
        }

        Tpl::showPage("null_layout");
    }

    public function getTellerBalanceOp()
    {
        $uid = $this->user_id;
        if($this->user_position==userPositionEnum::TELLER){

        }elseif($this->user_position==userPositionEnum::CHIEF_TELLER){

        }

        $rt1 = userClass::getPassbookBalanceOfUser($uid);
        $rt2 = userClass::getPassbookAccountAllCurrencyDetailOfUser($uid);
        $arr = array_merge(array(), $rt1, $rt2);

        $currency_list = (new currencyEnum())->Dictionary();
        $data = array();
        foreach ($currency_list as $key => $currency) {
            $data['cash_' . $key] = ncPriceFormat(passbookAccountClass::getBalance($arr[$key]['balance'], $arr[$key]['outstanding']));
            $data['out_' . $key] = ncPriceFormat(passbookAccountClass::getOutstanding($arr[$key]['balance'], $arr[$key]['outstanding']));
        }
        return new result(true, '', $data);
    }
    public function getBranchBalanceOp()
    {
        $branch_id = $this->user_info['branch_id'];
        $m_branch = M('site_branch');
        $branch_info = $m_branch->find(array('uid' => $branch_id));
        $branch_name = $branch_info['branch_name'];
        $branch = new objectBranchClass($branch_id);
        $rt1 = $branch->getPassbookCurrencyBalance();
        $rt2 = $branch->getPassbookCurrencyAccountDetail();
        $arr = array_merge(array(), $rt1, $rt2);

        $currency_list = (new currencyEnum())->Dictionary();
        $data = array();
        foreach ($currency_list as $key => $currency) {
            $data['cash_' . $key] = ncPriceFormat(passbookAccountClass::getBalance($arr[$key]['balance'], $arr[$key]['outstanding']));
            $data['out_' . $key] = ncPriceFormat(passbookAccountClass::getOutstanding($arr[$key]['balance'], $arr[$key]['outstanding']));
        }
        $data['branch_name'] = $branch_name;
        return new result(true, '', $data);

    }

}