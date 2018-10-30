<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 15:49
 */
class common_exchange_rateModel extends tableModelBase
{

    public function __construct()
    {
        parent::__construct('common_exchange_rate');
    }

    public function getKindList()
    {
        $currency_list = (new currencyEnum())->Dictionary();
        return $currency_list;
    }

    public function defaultCCY()
    {
        if (Language::currentCode() == "zh_cn") {
            return currencyEnum::CNY;
        } else {
            return currencyEnum::USD;
        }
    }

    /**
     * @param $source_ccy
     * @param $target_ccy
     * @return float  1源货币 = x目标货币
     */
    public function getRateBetween($source_ccy, $target_ccy)
    {

        if( $source_ccy == $target_ccy ){
            return 1;
        }
        $exchange_rate = $this->find(array('first_currency' => $source_ccy, 'second_currency' => $target_ccy));
        if ($exchange_rate) {

            return ($exchange_rate['buy_rate'] > 0 && $exchange_rate['buy_rate_unit'] > 0) ? $exchange_rate['buy_rate'] / $exchange_rate['buy_rate_unit'] : 0;
        }

        $exchange_rate = $this->find(array('second_currency' => $source_ccy, 'first_currency' => $target_ccy));

        return ($exchange_rate['sell_rate'] > 0 && $exchange_rate['sell_rate_unit'] > 0) ? $exchange_rate['sell_rate'] / $exchange_rate['sell_rate_unit'] : 0;
    }

    public function getSign($lang_code)
    {
        switch ($lang_code) {
            case "en":
                return '$';
            case "zh_cn":
                return "￥";
            default:
                return "$";
        }
    }
}