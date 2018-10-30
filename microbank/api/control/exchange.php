<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/8/7
 * Time: 14:26
 */
class exchangeControl extends bank_apiControl
{

    public function getExchangeRateListOp()
    {
        $list = exchangeClass::getExchangeRateListForShow();
        return new result(true,'success',array(
            'list' => $list
        ));
    }
}