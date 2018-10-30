<?php

class clientDepositByCashTradingClass extends tradingClass {
    private $client_savings_passbook;
    private $cashier_user_id;
    private $multi_currency;
    private $exchange_to_currency;

    public function __construct($clientSavingsPassbook, $cashierUserId, $amount = null, $currency = null, $multi_currency = array(), $exchange_to_currency = array())
    {
        parent::__construct();

        $this->client_savings_passbook = $clientSavingsPassbook;
        $this->cashier_user_id = $cashierUserId;

        if (!$multi_currency || empty($multi_currency)) {
            $this->multi_currency = array();
            $this->multi_currency[$currency] = $amount;
        } else {
            $this->multi_currency = $multi_currency;
        }
        if (!$exchange_to_currency || empty($exchange_to_currency)) {
            $this->exchange_to_currency = $this->multi_currency;
        } else {
            $this->exchange_to_currency = $exchange_to_currency;
        }

        $this->subject = "Client Deposit";

        $userObj = new objectUserClass($cashierUserId);

        $amount_arr = array();
        foreach( $this->multi_currency as $c=>$a ){
            $amount_arr[] = $c.':'.$a;
        }

        $this->sys_memo = $clientSavingsPassbook->getName().' deposit to savings by cash:'.
            'cashier '.$userObj->user_name.'('.$userObj->user_code.'): '
            .implode(',',$amount_arr);
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_cashier = passbookClass::getUserPassbook($this->cashier_user_id);

        foreach ($this->multi_currency as $c => $a) {
            // cashier账户 - 借
            $detail[]=$this->createTradingDetailItem(
                $passbook_cashier,
                $a,
                $c,
                accountingDirectionEnum::DEBIT,
                'Client deposit');
        }


        $remaining_to_currency = null;
        $more_currency = array();
        $less_currency = array();

        $temp_multi_currency  = $this->multi_currency;
        // 遍历的需要换汇的
        foreach( $this->exchange_to_currency as $c => $a ){

            if( $a < 0 ){
                // 换汇差额最后所在的账户货币
                $remaining_to_currency = $c;
            }

            // 有需要换汇的(金额0的就什么也不处理)
            if( $a > 0 ){
                if( $this->multi_currency[$c] <= 0 ){
                    // 没有这种货币
                    $less_currency[$c] = $a;
                }else{

                    // 有这种货币
                    if( $this->multi_currency[$c] > $a ){
                        // 多了
                        $more_currency[$c] = $this->multi_currency[$c] - $a;
                    }else if( $this->multi_currency[$c] < $a ) {
                        // 少了
                        $less_currency[$c] = $a - $this->multi_currency[$c];
                    }
                }
                // 处理了的货币clear掉
                unset($temp_multi_currency[$c]);
            }



        }
        // 处理没有遍历到的multi_currency的金额,属于多的
        foreach( $temp_multi_currency as $c=>$a ){
            if( $more_currency[$c] > 0 ){
                $more_currency[$c] += $a;
            }else{
                $more_currency[$c] = $a;
            }
        }


        // 最后多了货币的钱购汇少了的货币的钱
        foreach ($less_currency as $c => $a) {
            $remaining_amount = $a;

            while ($remaining_amount > 0) {
                $exchange_from_currency = null;
                $exchange_from_amount = 0;
                foreach ($more_currency as $c2 => $a2) {
                    if ($a2 > 0) {
                        $exchange_from_currency = $c2;
                        $exchange_from_amount = $a2;
                        break;
                    }
                }

                if ($exchange_from_amount == 0) {
                    throw new Exception("The specified amounts for multi currency are not enough to deposit.".json_encode($more_currency), errorCodesEnum::INVALID_PARAM);
                }

                $exchange_rate = global_settingClass::getCurrencyRateBetween($exchange_from_currency, $c);
                $to_amount = round($exchange_from_amount * $exchange_rate, 2);
                if ($to_amount > $remaining_amount) {
                    $exchange_from_amount = round($remaining_amount / $exchange_rate, 2);
                    $to_amount = $remaining_amount;
                }

                // From 换汇结算户 - 贷
                $detail[] = $this->createTradingDetailItem(
                    passbookClass::getSystemPassbook(systemAccountCodeEnum::EXCHANGE_SETTLEMENT),
                    $exchange_from_amount,
                    $exchange_from_currency,
                    accountingDirectionEnum::CREDIT,
                    $this->subject,
                    $exchange_rate,
                    $c);

                // To 换汇结算户 - 借
                $detail[] = $this->createTradingDetailItem(
                    passbookClass::getSystemPassbook(systemAccountCodeEnum::EXCHANGE_SETTLEMENT),
                    $to_amount,
                    $c,
                    accountingDirectionEnum::DEBIT,
                    $this->subject,
                    $exchange_rate,
                    $c);

                $remaining_amount -= $to_amount;
                $more_currency[$exchange_from_currency] -= $exchange_from_amount;
            }
        }

        // 少了的货币都换完了，剩余多的钱购买amount设为-1的那种货币，
        // 如果没有amount=-1的货币，直接用本身货币存入储蓄账户
        $exchange_to_currency = $this->exchange_to_currency;
        if ($remaining_to_currency != null) {
            $exchange_to_currency[$remaining_to_currency] = 0;
            foreach ($more_currency as $c => $a) {
                if ($c == $remaining_to_currency) {
                    $exchange_to_currency[$c] += $a;
                } else {
                    $exchange_rate = global_settingClass::getCurrencyRateBetween($c, $remaining_to_currency);
                    $to_amount = round($exchange_rate * $a, 2);
                    $exchange_to_currency[$remaining_to_currency] += $to_amount;

                    // From 换汇结算户 - 贷
                    $detail[] = $this->createTradingDetailItem(
                        passbookClass::getSystemPassbook(systemAccountCodeEnum::EXCHANGE_SETTLEMENT),
                        $a,
                        $c,
                        accountingDirectionEnum::CREDIT, $this->subject,
                        $exchange_rate,
                        $remaining_to_currency);

                    // To 换汇结算户 - 借
                    $detail[] = $this->createTradingDetailItem(
                        passbookClass::getSystemPassbook(systemAccountCodeEnum::EXCHANGE_SETTLEMENT),
                        $to_amount,
                        $remaining_to_currency,
                        accountingDirectionEnum::DEBIT, $this->subject,
                        $exchange_rate,
                        $c);
                }
            }
        } else {
            foreach ($more_currency as $c => $a) {
                $exchange_to_currency[$c] += $a;
            }
        }

        // 最后构建客人储蓄账户的flow
        foreach ($exchange_to_currency as $c => $a) {
            if ($a > 0) {
                // 构建detail
                // 客人储蓄账户 - 贷
                $detail[] = $this->createTradingDetailItem(
                    $this->client_savings_passbook,
                    $a,
                    $c,
                    accountingDirectionEnum::CREDIT,
                    'Deposit cash(Counter).');
            }
        }

        return $detail;
    }
}