<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/30
 * Time: 17:06
 */
class member_savingsClass
{


    public static function getMemberBillFlowDetailById($id)
    {
        $m = new passbook_accountModel();

        $sql = "select f.*,t.category,t.trading_type,t.remark t_remark from passbook_account_flow f left join passbook_trading t on t.uid=f.trade_id 
        where f.uid='$id' ";

        $flow_info = $m->reader->getRow($sql);
        if (!$flow_info) {
            return new result(false, 'No bill', null, errorCodesEnum::BILL_NOT_EXIST);
        }

        $trading_type_lang = enum_langClass::getMemberTradingTypeLang();
        $flow_info['trading_type'] = ($trading_type_lang[$flow_info['trading_type']]) ?: $flow_info['trading_type'];

        $account_info = $m->getRow($flow_info['account_id']);

        return new result(true, 'success', array(
            'bill_detail' => $flow_info,
            'account_info' => $account_info
        ));
    }

    /** 获取客户转账历史（去重的）
     * @param $member_id
     * @return ormCollection
     */
    public static function getMemberDistinctRecentlyTransfer($member_id)
    {
        $r = new ormReader();
        $sql = "select t.*,m.member_icon to_member_icon,m.phone_id to_member_phone from biz_member_transfer t left join client_member m on m.uid=t.to_member_id 
        where t.member_id='$member_id' and t.state='" . bizStateEnum::DONE . "'
        group by t.to_member_id,t.receiver_bank_account_no order by t.uid desc ";
        $list = $r->getRows($sql);
        foreach ($list as $k => $v) {
            $v['to_member_icon'] = getImageUrl($v['to_member_icon']);

            if ($v['receiver_bank_account_no']) {
                $v['receiver_bank_account_no'] = maskInfo($v['receiver_bank_account_no']);
            }
            $list[$k] = $v;
        }
        return $list;
    }


    /** 最近转账记录搜索过滤
     * @param $member_id
     * @param $keyword
     * @return ormCollection
     */
    public static function getMemberRecentlyTransferKeywordSearchList($member_id, $keyword)
    {
        $r = new ormReader();
        $sql = "select t.*,m.member_icon to_member_icon,m.phone_id to_member_phone from biz_member_transfer t left join client_member m on m.uid=t.to_member_id 
        where t.member_id='$member_id' and t.state='" . bizStateEnum::DONE . "'  and (m.login_code like '%$keyword%' or m.display_name like '%$keyword%'
        or m.kh_display_name like '%$keyword%' or m.phone_id like '%$keyword%' or t.receiver_bank_account_no like '%$keyword%' 
         or t.receiver_bank_account_name like '%$keyword%' ) 
        group by t.to_member_id,t.receiver_bank_account_no order by t.uid desc ";
        $list = $r->getRows($sql);
        foreach ($list as $k => $v) {
            $v['to_member_icon'] = getImageUrl($v['to_member_icon']);
            if ($v['receiver_bank_account_no']) {
                $v['receiver_bank_account_no'] = maskInfo($v['receiver_bank_account_no']);
            }
            $list[$k] = $v;
        }
        return $list;
    }


    /** 普通流水账单（分月统计的）
     * @param $params
     * @return result
     */
    public static function getMemberBillList($params)
    {
        $member_id = $params['member_id'];
        $currency = $params['currency'];
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 100000;

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'Member not exist', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $passbook = passbookClass::getSavingsPassbookOfMemberGUID($member->obj_guid);

        $flow_model = new passbook_account_flowModel();
        $page = $flow_model->searchFlowListByBookAndCurrency($passbook, $currency, $page_num, $page_size, $params);
        $list = $page->rows;

        $f_list = array();
        $trading_type_lang = enum_langClass::getMemberTradingTypeLang();

        foreach ($list as $item) {
            $trading_type = $item['trading_type'];
            $item['trading_type'] = ($trading_type_lang[$item['trading_type']]) ?: $item['trading_type'];
            $item['trading_type_icon'] = global_settingClass::getTradingTypeIcon($trading_type);
            $month = $item['date_month'];

            if ($f_list[$month]) {
                $f_list[$month]['list'][] = $item;
            } else {
                $f_list[$month] = array(
                    'month' => $month,
                    'list' => array($item)
                );
            }
        }

        $month_summary = $flow_model->getMonthSummaryByBookAndCurrency($passbook, $currency, array_keys($f_list));
        foreach ($month_summary as $item) {
            $f_list[$item['date_month']]['summary'] = array(
                'credit' => $item['total_credit'],
                'debit' => $item['total_debit']
            );
        }

        // 去掉键值
        $f_list = array_values($f_list);

        $data = array(
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $f_list
        );

        return new result(true, 'success', $data);

    }


    /** 交易日记账
     * @param $params
     * @return result
     */
    public static function getMemberBillListGroupByDayAndType($params)
    {
        $member_id = $params['member_id'];
        $currency = $params['currency'];


        $memberObj = new objectMemberClass($member_id);
        $member_passbook = $memberObj->getSavingsPassbook();

        $m_flow = new passbook_account_flowModel();
        $list = $m_flow->searchAllFlow($member_passbook, $currency, $params);

        $trading_type_lang = enum_langClass::getMemberTradingTypeLang();

        $begin_balance = 0;
        $format_list = array();

        $balance_icon = global_settingClass::getTradingTypeIcon('balance');

        foreach ($list as $flow) {

            if (!$begin_balance) {
                $begin_balance = $flow['begin_balance'];
            }

            $day = date('Y-m-d', strtotime($flow['update_time'] ?: $flow['create_time']));
            $key = $day;
            $amount = $flow['end_balance'] - $flow['begin_balance'];

            $trading_type = $flow['trading_type'];
            $flow['trading_type'] = ($trading_type_lang[$flow['trading_type']]) ?: $flow['trading_type'];

            $trading_type_icon = global_settingClass::getTradingTypeIcon($trading_type);

            if ($format_list[$key]) {

                $format_list[$key]['end_balance'] = $flow['end_balance'];

                if ($format_list[$key]['list'][$trading_type]) {

                    $format_list[$key]['list'][$trading_type]['amount'] += $amount;
                    $format_list[$key]['list'][$trading_type]['end_balance'] = $flow['end_balance'];

                } else {
                    $format_list[$key]['list'][$trading_type] = array(
                        'trading_type' => $flow['trading_type'],
                        'trading_type_icon' => $trading_type_icon,
                        'begin_balance' => $flow['begin_balance'],
                        'amount' => $amount,
                        'currency' => $flow['currency'],
                        'end_balance' => $flow['end_balance']
                    );
                }

            } else {

                $format_list[$key] = array(
                    'date' => $day,
                    'list' => array(
                        $trading_type => array(
                            'trading_type' => $flow['trading_type'],
                            'trading_type_icon' => $trading_type_icon,
                            'begin_balance' => $flow['begin_balance'],
                            'amount' => $amount,
                            'currency' => $flow['currency'],
                            'end_balance' => $flow['end_balance']
                        )
                    ),
                    'begin_balance' => $flow['begin_balance'],
                    'end_balance' => $flow['end_balance'],
                );
            }

        }

        $format_list = array_values($format_list);
        foreach ($format_list as $key => $value) {
            $value['list'] = array_values($value['list']);
            $format_list[$key] = $value;
        }


        return new result(true, 'success', array(
            'begin_balance' => $begin_balance,
            'balance_icon' => $balance_icon,
            'list' => $format_list
        ));


    }


    public static function getMemberBillTransaction($params)
    {
        $member_id = $params['member_id'];
        $currency = $params['currency'];
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 100000;


        $memberObj = new objectMemberClass($member_id);
        $member_passbook = $memberObj->getSavingsPassbook();

        $m_flow = new passbook_account_flowModel();
        $page_list = $m_flow->searchFlowListByBookAndCurrency($member_passbook, $currency, $page_num, $page_size, $params);
        $bill_list = $page_list->rows;

        $trading_type_lang = enum_langClass::getMemberTradingTypeLang();


        $total_income = 0;
        $total_payout = 0;
        // 不分页的统计
        $list = $m_flow->searchAllFlow($member_passbook, $currency, $params);
        foreach ($list as $v) {
            $amount = $v['end_balance'] - $v['begin_balance'];
            if ($amount >= 0) {
                $total_income += $amount;
            } else {
                $total_payout += abs($amount);
            }
        }

        $format_list = array();
        foreach ($bill_list as $flow) {
            $trading_type = $flow['trading_type'];
            $amount = $flow['end_balance'] - $flow['begin_balance'];
            $flow['amount'] = $amount;
            $flow['trading_type'] = ($trading_type_lang[$flow['trading_type']]) ?: $flow['trading_type'];
            $flow['trading_type_icon'] = global_settingClass::getTradingTypeIcon($trading_type);
            $format_list[] = $flow;
        }


        $return = array(
            'total_num' => $page_list->count,
            'total_pages' => $page_list->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'data' => array(
                'total_income' => $total_income,
                'total_payment' => $total_payout,
                'currency' => $currency,
                'list' => $format_list
            )
        );

        return new result(true, 'success', $return);

    }

}