<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/13
 * Time: 15:51
 */
class system_toolClass
{

    public static function convertMultiCurrencyAmount($multi_currency_amount,$destination_currency)
    {
        $total_amount = 0;
        if( !$multi_currency_amount || !$destination_currency ){
            return $total_amount;
        }
        foreach( $multi_currency_amount as $c=>$a ){
            $rate = global_settingClass::getCurrencyRateBetween($c,$destination_currency);
            $amt = round($a*$rate,2);
            $total_amount += $amt;
        }
        return $total_amount;

    }

    /** 多币种金额买入单一币种金额的明细
     * @param $amount -单币种金额
     * @param $currency -币种
     * @param $multi_currency_amount  -多币种的金额
     *   array(
     *      USD => 100,
     *      KHR => 5000
     * )
     * @return  ->STS 为true 足额  ->false 不足额
     */
    public static function calMultiCurrencyDeductForSingleCurrencyAmount($amount,$currency,$multi_currency_amount)
    {
        $amount = round($amount,2);

        // 首先检查当前币种是否足额
        if( $multi_currency_amount[$currency] >= $amount ){

            return new result(true,'Ok',array(
                'multi_currency' => array(
                    $currency=>$amount
                ),
                'left_amount' => 0
            ));
        }

        // 单一币种也不足，多货币共同处理
        $left_amount = $amount;
        $return_array = array();
        foreach( $multi_currency_amount as $c=>$a ){
            if( $a > 0 ){
                if( $left_amount > 0 ){
                    $exchange_rate = global_settingClass::getCurrencyRateBetween($c,$currency);
                    // 全部买入
                    $duct_amount = round($a*$exchange_rate,2);
                    if ($duct_amount <= $left_amount) {
                        $return_array[$c] = $a;
                        $left_amount -= $duct_amount;
                    } else {
                        $return_array[$c] = round($left_amount / $exchange_rate, 2);
                        $left_amount = 0;
                        break;
                    }
                }
            }
        }

        if( $left_amount > 0 ){
            // 不足
            return new result(false,'Not enough.',array(
                'multi_currency' => $return_array,
                'left_amount' => $left_amount
            ));
        }

        return new result(true,'Ok',array(
            'multi_currency' => $return_array,
            'left_amount' => 0
        ));


    }


    /** 多币种分配多币种
     *  返回data
     *  array(
     * 'allot_detail'
     *  is_enough  是否足额
     * )
     * @param $destination_multi_currency_amount
     * @param $actual_multi_currency_amount
     */
    public static function allotMultiCurrencyForMultiCurrencyAmount($destination_multi_currency_amount,$actual_multi_currency_amount)
    {
        //封装基础方法的数据
        $destination_amounts = array();
        foreach( $destination_multi_currency_amount as $c=>$a ){
            if( $a > 0 ){
                $destination_amounts[$c] = array(
                    'amount' => $a,
                    'currency' => $c
                );
            }
        }
        $rt = self::calMultiCurrencyDeductForMultiCurrencyAmount($destination_amounts,$actual_multi_currency_amount);

        if( !$rt->STS ){
            return $rt;
        }
        $result_data = $rt->DATA;
        $is_enough = true;
        foreach( $result_data as $v ){
            if( $v['left_amount'] > 0 ){
                $is_enough = false;
                break;
            }
        }
        return new result(true,'success',array(
            'allot_detail' => $result_data,
            'is_enough' => $is_enough
        ));
    }


    public static function calMultiCurrencyDeductForMultiCurrencyAmount($destination_amounts,$multi_currency_amount)
    {
        // 处理后$destination_amounts中数据结构：
        // array(
        //   key =>array(
        //     'currency' => 'USD'          目标货币  （输入数据）
        //     'amount' => 120,             目标货币需要的金额  （输入数据）
        //     'multi_currency' => array(   换汇表
        //       'USD'=>100.00              源货币 => 金额
        //       'KHR'=>40000
        //     ),
        //     'left_amount' => 10          剩余不能分配的金额
        //   )
        // )

        // 首先优先不用换汇的进行分配
        foreach ($destination_amounts as $k=>$item) {
            $c = $item['currency'];
            $a = $item['amount'];
            $destination_amounts[$k]['multi_currency'] = array();
            $destination_amounts[$k]['left_amount'] = $item['amount'];

            if ($multi_currency_amount[$c]>0) {
                if ($multi_currency_amount[$c]>$a) {
                    $destination_amounts[$k]['multi_currency'][$c] = $a;
                    $destination_amounts[$k]['left_amount'] = 0;
                    $multi_currency_amount[$c] -= $a;
                } else {
                    $destination_amounts[$k]['multi_currency'][$c] = $multi_currency_amount[$c];
                    $destination_amounts[$k]['left_amount'] -= $multi_currency_amount[$c];
                    $multi_currency_amount[$c] = 0;
                }
            }
        }

        // 还有剩余的按顺序换汇分配
        // 源货币的顺序按照Enum中货币定义的顺序
        $currencies = (new currencyEnum())->Dictionary();
        foreach ($destination_amounts as $k=>$item) {
            if ($item['left_amount'] > 0) {
                $c = $item['currency'];
                foreach ($currencies as $sc => $label) {
                    if ($multi_currency_amount[$sc] > 0) {
                        $exchange_rate = global_settingClass::getCurrencyRateBetween($sc,$c);
                        $need_amount = round($item['left_amount'] / $exchange_rate, 2);

                        // 精度产生的误差处理
                        if (round($need_amount * $exchange_rate,2) < $item['left_amount'])
                            $need_amount=round($need_amount+0.01,2);

                        if ($multi_currency_amount[$sc] > $need_amount) {
                            $destination_amounts[$k]['multi_currency'][$sc] = $need_amount;
                            $destination_amounts[$k]['left_amount'] = 0;
                            $multi_currency_amount[$sc] -= $need_amount;
                        } else {
                            // 全部转换过来
                            $need_amount = $multi_currency_amount[$sc];
                            $to_amount = round($need_amount * $exchange_rate, 2);
                            $destination_amounts[$k]['multi_currency'][$sc] = $multi_currency_amount[$sc];
                            $destination_amounts[$k]['left_amount'] -= $to_amount;
                            $multi_currency_amount[$sc] = 0;
                        }
                    }
                }
            }
        }

        return new result(true, null, $destination_amounts);
    }


    public static function isSqlValid($sql)
    {
        $sql = strtoupper($sql);
        $invalid_key = array(
            'UPDATE',
            'DELETE',
            'INSERT',
            'CREATE',
            'DROP',
            'ALTER',
            'GRANT ',
            'SHUTDOWN',
        );
        foreach( $invalid_key as $v ){
            if( strpos($sql,$v) !== false ){
                return false;
            }
        }
        return true;
    }

    /**
     * 求两个日期之间相差的天数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $day1
     * @param string $day2
     * @return number
     */
    public static function diffBetweenTwoDays($day1, $day2)
    {
        $day1 = date('Y-m-d',strtotime($day1));
        $day2 = date('Y-m-d',strtotime($day2));
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);
        return ($second1 - $second2) / 86400;
    }


    public static function getCurrencyOption($select_currency=null)
    {
        $str = '';
        foreach( (new currencyEnum())->toArray() as $currency ){
            if( $select_currency == $currency ){
                $str .= "<option value='".$currency."' selected >".$currency."</option>";

            }else{
                $str .= "<option value='".$currency."'>".$currency."</option>";

            }
        }
        return $str;
    }


    public static function getFormatStartDate($start_date = null)
    {
        if($start_date){
            $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
        }
        return $start_date;
    }

    public static function getFormatEndDate($end_date = null)
    {
        if($end_date){
            $end_date = date('Y-m-d 23:59:59', strtotime($end_date));
        }
        return $end_date;
    }


    public static function formatWapHtmlContent($content)
    {
        $str = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title></title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
                <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-status-bar-style" content="black">
                <meta name="format-detection" content="telephone=no">


            </head>
            <body>
            <div>'.$content.'

            </div>
            </body>
            </html>
        ';
        return $str;
    }



}