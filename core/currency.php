<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/14
 * Time: 11:23
 */
class currency extends Enum{

    const USD="USD";
    const KHR="KHR";
    const CNY="CNY";

    static function getKindList(){
        return (new currency())->Dictionary();
    }

    static function defaultCCY(){
        if (Language::currentCode() == "zh_cn")
            return self::CNY;
        else
            return "USD";
    }

    static function getRateOf($ccy){
        $lst=self::getRateList();
        return $lst[$ccy];
    }

    static function getIdOf($ccy){
        $lst=self::getIdList();
        return $lst[$ccy];

    }
    static function checkCurrency($ccy){
        return in_array($ccy,array('USD','KHR','CNY'));
    }

    static function getIdList(){ //编号
        return array(
            "USD"=>1,
            "KHR"=>2,
            "CNY"=>3
        );
    }
    static function getRateList(){//todo,要拆网，获取即时的汇率信息
        return array(
            "USD"=>1,
            "KHR"=>4100,
            "CNY"=>6.5
        );
    }

    static function getRateListTo($ccy) {
        $r = self::getRateList();
        return array(
            'USD' => $r['USD'] / $r[$ccy],
            'KHR' => $r['KHR'] / $r[$ccy],
            'CNY' => $r['CNY'] / $r[$ccy]
        );
    }

    static function getRateBetween($source_ccy,$target_ccy){
        $rate1=self::getRateOf($source_ccy);
        $rate2=self::getRateOf($target_ccy);
        if($rate1>0){
            return $rate2/$rate1;
        }else{
            return $rate2;
        }
    }

    static function getSign($lang_code){
        switch($lang_code){
            case "en": return '$';
            case "zh_cn": return "￥";
            default:
                return "$";
        }
    }
}