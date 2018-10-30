<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/8/7
 * Time: 14:31
 */
class exchangeClass
{

    public static function getExchangeRateListForShow()
    {
        $m = new common_exchange_rateModel();
        $list = $m->select(array(
            'uid' => array('gt',0)
        ));

        $return = array();
        foreach( $list as $v ){
            $temp = array();
            $temp['currency'] = $v['first_currency'].'/'.$v['second_currency'];
            $temp['buy_price'] = ncPriceFormat(round($v['buy_rate']/$v['buy_rate_unit'],6),6);
            $temp['sell_price'] = ncPriceFormat(round($v['sell_rate_unit']/$v['sell_rate'],6),6);
            $return[] = $temp;
        }
        return $return;
    }

}