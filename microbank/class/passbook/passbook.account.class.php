<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/6/15
 * Time: 14:20
 */

class passbookAccountClass {
    /**
     * 通过账户的balance与outstanding计算显示的balance
     * 因为可能outstanding可能超过balance，需要进行处理
     * @param $balance
     * @param $outstanding
     * @return float  实际显示的balance
     */
    public static function getBalance($balance, $outstanding) {
        if ($balance < $outstanding)
            return 0.0;
        else
            return $balance - $outstanding;
    }

    /**
     * 通过账户的balance与outstanding计算显示的balance
     * @param $balance
     * @param $outstanding
     * @return float  实际显示的outstanding
     */
    public static function getOutstanding($balance, $outstanding) {
        if ($balance < $outstanding)
            return $balance;
        else
            return $outstanding;
    }
}