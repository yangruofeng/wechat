<?php

class test_tradingControl {
    public function member_deposit_by_cashOp() {
        return passbookWorkerClass::memberDepositByCash(26, 10, 10, 'USD');
    }

    public function testRowUpdateOp()
    {
        $m = new phone_verify_codeModel();
        $row = $m->getRow(2);
        //print_r($row->toArray());  // verify_code 877905
        $row->verify_code = '877905';
        $up = $row->update();  // 数据行没有变化更新方法失败（对单一字段的更正需要判断）
        print_r($up);
    }


    public function testCheckUserBalanceOp()
    {
        $param = array_merge($_GET,$_POST);
        $user_id = intval($param['user_id']);
        $userObj = new objectUserClass($user_id);
        $balance = $userObj->getPassbookBalance();
        print_r($balance);
        $balance_detail = $userObj->getAccountAllCurrencyDetail();
        //print_r($balance_detail);

        $accountObj = new objectGlAccountClass(systemAccountCodeEnum::OUT_SYSTEM_INCOME_AND_EXPENSES);
        print_r($accountObj->getPassbookCurrencyBalance());

        //$rt = (new cashierOutSystemIncomeTradingClass(10,1000,currencyEnum::USD))->execute();
        //print_r($rt);

        $rt = (new cashierOutSystemPaymentTradingClass($user_id,50,currencyEnum::USD))->execute();
        print_r($rt);
    }
}