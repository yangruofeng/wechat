<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/29
 * Time: 下午3:50
 */
class counter_codClass extends counter_baseClass
{

    public static function getUserDailyVoucherData($params)
    {
        $user_id = $params['user_id'];
        $userObj = new objectUserClass($user_id);
        $userPassbook = $userObj->getUserPassbook();

        $currency = $params['currency'];
        $day = $params['day'];
        $pageNumber = $params['pageNumber']?:1;
        $pageSize = $params['pageSize']?:100000;

        // 需要计算前一天的余额
        $previous_day = date('Y-m-d',strtotime('-1 day',strtotime($day)));
        $previous_balance = $userPassbook->getAccountBalanceOfEndDay($previous_day);
        $balance_brought_forward = $previous_balance[$currency];

        $page_data = (new passbook_account_flowModel())->getBookAccountFlowOfTradingByDay($userPassbook,
            $currency,$day,$pageNumber,$pageSize);

        $flow_list = $page_data->rows;

        $base_no = ($pageNumber-1)*$pageSize+1;

        $book_id = $userPassbook->getBookId();
        // 合并一个trading的
        $format_list = array();
        $total_amount = 0;
        $total_in = 0;
        $total_out = 0;
        foreach( $flow_list as $v ){

            // 可能存在其他货币的,过滤
            if( $v['currency'] != $currency ){
                continue;
            }

            // 过滤掉自己的
            if( !$format_list[$v['trade_id']]  ){
                $format_list[$v['trade_id']] = $v;
            }

            if( $v['book_id'] == $book_id ){
                $amount = $userPassbook->getBalanceDelta($v['credit'],$v['debit']);
                $format_list[$v['trade_id']]['trading_amount'] = $amount;
                $total_amount += $amount;
                if( $amount >= 0 ){
                    $total_in += $amount;
                }else{
                    $total_out += $amount*-1;
                }
            }else{
                $format_list[$v['trade_id']]['flow_list'][] = $v;
            }

        }
//print_r($format_list);die;
        foreach( $format_list as $key=>$v ){
            $v['no'] = $base_no;
            $format_list[$key] = $v;
            $base_no++;
        }


        return array(
            "sts" => true,
            "data" => $format_list,
            'book_id' => $book_id,
            'balance_before_the_day' => $balance_brought_forward,
            'total_amount' => $total_amount,
            'total_in' => $total_in,
            'total_out' => $total_out,
            "total" => $page_data->count,
            "pageTotal" => $page_data->pageCount,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );

    }

    //弃用，移到balance.sheet.class->getPassbookJournalVoucherData
    public static function getCounterVoucherData($passbook, $pageNumber, $pageSize, $filters = array()){
        $r = new ormReader();
        $where = " f.state = " . qstr(passbookAccountFlowStateEnum::DONE) ." and p.uid = ".qstr($passbook->getBookId());
        if($filters['trade_id']){
            $where .= " and f.trade_id = " . qstr($filters['trade_id']);
        }
        if($filters['trade_type']){
            $where .= " and t.trading_type = " . qstr($filters['trade_type']);
        }
        if($filters['remark']){
            $where .= " and t.sys_memo like '%" . trim($filters['remark']) . "%'";
        }
        if ($filters['start_date']) {
            $start_date = system_toolClass::getFormatStartDate($filters['start_date']);
            $where .= " AND f.update_time >= '$start_date' ";
        }
        if ($filters['end_date']) {
            $end_date = system_toolClass::getFormatEndDate($filters['end_date']);
            $where .= " AND f.update_time <= '$end_date' ";
        }

        $sql = "select DISTINCT f.trade_id FROM passbook_account_flow f
inner join passbook_trading t on t.uid = f.trade_id
left join passbook_account a on f.account_id = a.uid
join passbook p on a.book_id = p.uid
where  $where";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $rows = resetArrayKey($rows, 'trade_id');
        $trade_ids = array_column($rows, 'trade_id');
        $trade_ids_str =  count($trade_ids) > 0 ? '(' . implode(',', $trade_ids) . ')' : '(0)';

        $where1 = "t.uid in $trade_ids_str";
        $sql1 = "SELECT t.uid,t.trading_type,t.`subject`,t.remark,t.update_time,t.sys_memo,f.uid fid,p.book_name,f.credit,f.debit,a.currency from passbook_trading t
left join passbook_account_flow f on t.uid = f.trade_id
left join passbook_account a on f.account_id = a.uid
left join passbook p on p.uid = a.book_id
where  $where1";

        $ret = $r->getRows($sql1);
        $list = array();
        $num = ($pageNumber - 1) * $pageSize;
        $tempCurrency = array();
        foreach($ret as $v){
            if(!$list[$v['uid']]){
                $no = ++$num;
            }
            $list[$v['uid']]['no'] = $no;
            $list[$v['uid']]['trade_id'] = $v['uid'];
            $list[$v['uid']]['time'] = $v['update_time'];
            $list[$v['uid']]['subject'] = $v['subject'];
            $list[$v['uid']]['remark'] = $v['remark'];
            $list[$v['uid']]['sys_memo'] = $v['sys_memo'];
            if($v['debit'] + $v['credit'] != 0){
                $list[$v['uid']]['flow'][] = $v;
            }
            $tempCurrency['debit'][$v['currency']] += $v['debit'];
            $tempCurrency['credit'][$v['currency']] += $v['credit'];
        }
        $systemCurrency = (new currencyEnum())->toArray();
        $totalCurrency = array();
        foreach($systemCurrency as $k => $v){
            foreach($tempCurrency['debit'] as $ck => $cv){
                if($ck != $v){ //不是当前货币
                    $rate = global_settingClass::getCurrencyRateBetween($ck, $v);
                    $amount = $cv * $rate;
                }else{
                    $amount = $cv;
                }
                $totalCurrency['debit'][$v] += $amount;
            }
            foreach($tempCurrency['credit'] as $ck => $cv){
                if($ck != $v){ //不是当前货币
                    $rate = global_settingClass::getCurrencyRateBetween($ck, $v);
                    $amount = $cv * $rate;
                }else{
                    $amount = $cv;
                }
                $totalCurrency['credit'][$v] += $amount;
            }
            $totalCurrency['debit'][$v] = ncPriceFormat($totalCurrency['debit'][$v]);
            $totalCurrency['credit'][$v] = ncPriceFormat($totalCurrency['credit'][$v]);
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "data" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "totalCurrency" => json_encode($totalCurrency),
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }


}