<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/1/31
 * Time: 16:47
 */

class test_ace_apiControl
{
    public function testOp() {
        return asiaweiluyApi::Instance()->test();
    }

    public function verify_client_accountOp() {
        return asiaweiluyApi::Instance()->verifyClientAccount($_GET['ace_account']);
    }

    public function query_client_balanceOp() {
        return asiaweiluyApi::Instance()->queryClientBalance($_GET['ace_account']);
    }

    public function bind_finishOp() {
        return asiaweiluyApi::Instance()->bindFinish($_GET["application_id"], $_GET["verify_code"]);
    }

    public function bind_startOp() {
        return asiaweiluyApi::Instance()->bindStart($_GET['ace_account']);
    }

    public function unbind_finishOp() {
        return asiaweiluyApi::Instance()->unbindFinish($_GET["application_id"], $_GET["verify_code"]);
    }

    public function unbind_startOp() {
        return asiaweiluyApi::Instance()->unbindStart($_GET['ace_account']);
    }

    public function query_my_balanceOp() {
        return asiaweiluyApi::Instance()->queryMyBalance();
    }

    public function disburse_startOp() {
        return asiaweiluyApi::Instance()->disburseStart($_GET['ace_account'], $_GET['amount'], $_GET['currency'], $_GET['description']);
    }

    public function disburse_finishOp() {
        return asiaweiluyApi::Instance()->disburseFinish($_GET['transfer_id']);
    }

    public function collect_startOp() {
        return asiaweiluyApi::Instance()->collectStart($_GET['ace_account'], $_GET['amount'], $_GET['currency'], $_GET['description']);
    }

    public function collect_finishOp() {
        return asiaweiluyApi::Instance()->collectFinish($_GET['transfer_id']);
    }
}