<?php

class balanceSheetClass {
    private static function sumAmount($arr) {
        $ret = array();
        foreach ($arr as $item) {
            foreach ($item as $k => $v) {
                if (!$ret[$k])
                    $ret[$k] = $v;
                else
                    $ret[$k] += $v;
            }
        }
        return $ret;
    }



    private static function createSubTree($amount, $children = null) {
        if (!$children) {
            return array('amount' => $amount);
        } else {
            if (!$amount) {
                $amount = self::sumAmount(array_column($children, 'amount'));
            }

            $ret = array('children' => array(), 'amount' => $amount);

            foreach ($children as $k => $v) {
                if (!empty($v['amount'])) {
                    $ret['children'][$k] = $v;
                }
            }

            return $ret;
        }
    }



    public static function getReportData() {
        $model_passbook = new passbookModel();

        $cash_on_hand_co = self::createSubTree($model_passbook->getAssetCashOnHandTotalOfCreditOfficer());
        $cash_on_hand_co['redirect'] = balanceSheetColumnRedirectTypeEnum::CASH_ON_HAND_CO;
        $cash_on_hand_teller = self::createSubTree($model_passbook->getAssetCashOnHandTotalOfTeller());
        $cash_on_hand_teller['redirect'] = balanceSheetColumnRedirectTypeEnum::CASH_ON_HAND_TELLER;
        $cash_on_hand_other = self::createSubTree($model_passbook->getAssetCashOnHandTotalOfOtherUser());
        $cash_on_hand_other['redirect'] = balanceSheetColumnRedirectTypeEnum::CASH_ON_HAND_OTHER;
        $cash_on_hand = self::createSubTree($model_passbook->getAssetCashOnHand(), array(
            'Teller' => $cash_on_hand_teller,
            'Credit Officer' => $cash_on_hand_co,
            'Other' => $cash_on_hand_other
        ));
        $cash_in_vault_hq = self::createSubTree($model_passbook->getAssetCashInVaultTotalOfHq());
        $cash_in_vault_hq['redirect'] = balanceSheetColumnRedirectTypeEnum::CASH_IN_VAULT_HEADQUARTERS;
        $cash_in_vault_branch = self::createSubTree($model_passbook->getAssetCashInVaultTotalOfBranch());
        $cash_in_vault_branch['redirect'] = balanceSheetColumnRedirectTypeEnum::CASH_IN_VAULT_BRANCHES;
        $cash_in_vault = self::createSubTree(null, array(
            'Headquarters' => $cash_in_vault_hq,
            'Branches' => $cash_in_vault_branch
        ));
        $cash = self::createSubTree(null, array(
            'Cash On Hand' => $cash_on_hand,
            'Cash In Vault' => $cash_in_vault
        ));
        $bank = self::createSubTree($model_passbook->getAssetBankTotal(), $model_passbook->getAssetEachBank());
        $receivable_long_term_principal = self::createSubTree($model_passbook->getAssetLongTermReceivablePrincipal());
        $receivable_long_term_principal['redirect'] = balanceSheetColumnRedirectTypeEnum::RECEIVABLE_LONG_TERM_PRINCIPAL;
        $receivable_short_term_principal = self::createSubTree($model_passbook->getAssetShortTermReceivablePrincipal());
        $receivable_short_term_principal['redirect'] = balanceSheetColumnRedirectTypeEnum::RECEIVABLE_SHORT_TERM_PRINCIPAL;
        $receivable_interest = $model_passbook->getAssetReceivableInterest();
        $receivable = self::createSubTree(null, array(
            'Short-term Principal' => $receivable_short_term_principal,
            'Long-term Principal' => $receivable_long_term_principal,
            'Interest' => $receivable_interest
        ));
        $other_asset_accounts = $model_passbook->getAssetOfGlAccount();
        $other_assets = self::createSubTree($model_passbook->getAssetOther());
        $current_assets = self::createSubTree($model_passbook->getAssetsTotal(), array_merge(array(
            'Cash' => $cash,
            'Bank' => $bank,
            'Receivable' => $receivable
        ), $other_asset_accounts, array(
            'Other' => $other_assets
        )));

        $deposit_savings = self::createSubTree($model_passbook->getLiabilitySavings());
        $deposit_savings['redirect'] = balanceSheetColumnRedirectTypeEnum::LIABILITY_SAVINGS;
        $deposit_short_term = self::createSubTree($model_passbook->getLiabilityShortTermDeposit());
        $deposit_long_term = self::createSubTree($model_passbook->getLiabilityLongTermDeposit());
        $deposit = self::createSubTree(null, array(
            'Savings' => $deposit_savings,
            'Short-term' => $deposit_short_term,
            'Long-term' => $deposit_long_term
        ));
        $payable_partner = self::createSubTree($model_passbook->getLiabilityPayableOfPartner());
        $payable = self::createSubTree(null, array(
            'Partner' => $payable_partner
        ));
        $other_libilities = self::createSubTree($model_passbook->getLiabilityOther());
        $liabilities = self::createSubTree($model_passbook->getLiabilitiesTotal(), array(
            'Deposit' => $deposit,
            'Payable' => $payable,
            'Other' => $other_libilities
        ));
        $other_equities = self::createSubTree($model_passbook->getEquityOther());
        $equities = self::createSubTree($model_passbook->getEquitiesTotal(), array_merge(array(

        ),$model_passbook->getEquityOfGlAccount(), array(
            'Other' => $other_equities
        )));

        return new result(true, null, array(
            'assets' => array(
                'children' => array(
                    'Current Assets' => $current_assets
                )
            ),
            'liabilities_and_equities' => array(
                'children' => array(
                    'Liabilities' => $liabilities,
                    'Equities' => $equities
                )
            )
        ));
    }



    public static function accumulatedAmount($data,$row_data)
    {

        if( !empty( $row_data['multi_currency'] ) ){

            // 累计金额到父级
            $multi_currency = $row_data['multi_currency'];

            while( $data[$row_data['parent_book_code']] ){

                foreach( $multi_currency as $c=>$a ){
                    $data[$row_data['parent_book_code']]['multi_currency'][$c] += $a;
                }

                $row_data = $data[$row_data['parent_book_code']];
            }
        }

        return $data;

    }

    protected static function mergeCurrencyAndAccumulated($list)
    {
        $total_multi_currency = array();

        // 先组装多币种
        $format_list = array();
        $book_code_arr = array();  // 通过book_code合并
        foreach( $list as $v ){


            $amount = $v['balance'];
            $v['parent_book_code'] = $v['parent_book_code']?:0;

            // 有的话不要覆盖了 ！！！！
            if( !$format_list[$v['uid']] ){
                $format_list[$v['uid']] = $v;
            }

            if( !$book_code_arr[$v['book_code']]){
                $book_code_arr[$v['book_code']] = $v;
            }

            if( $v['currency'] ){
                $total_multi_currency[$v['currency']] += $amount;
                $format_list[$v['uid']]['multi_currency'][$v['currency']] += $amount;
                $book_code_arr[$v['book_code']]['multi_currency'][$v['currency']] += $amount;

                if($v['currency'] == currencyEnum::USD){
                    $total_multi_currency['total'] += $amount;
                    $format_list[$v['uid']]['multi_currency']['total'] += $amount;
                    $book_code_arr[$v['book_code']]['multi_currency']['total'] += $amount;
                }else{
                    $total_multi_currency['total'] += global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD) * $amount;
                    $format_list[$v['uid']]['multi_currency']['total'] += global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD) * $amount;;
                    $book_code_arr[$v['book_code']]['multi_currency']['total'] += global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD) * $amount;;
                }



            }

        }

       /* echo '<hr />';
        foreach( $book_code_arr as $v ){
            echo $v['book_code'].':'.json_encode($v['multi_currency']).'<br />';
        }
        die;*/

        foreach( $format_list as $v ){
            $book_code_arr = self::accumulatedAmount($book_code_arr,$v);
        }

        // 并入合计金额
        foreach($format_list as $key=>$v ){
            $v['multi_currency'] = $book_code_arr[$v['book_code']]['multi_currency'];
            $format_list[$key] = $v;
        }

        return array(
            'total_multi_amount' => $total_multi_currency,
            'format_list' => $format_list,
        );
    }



    public static function getDevBalanceSheetDataOfAsset($date_end)
    {
        $r = new ormReader();
        // 资产部分 1

        $sql = "  select * from (
select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where  gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
    SELECT a.account_id,MAX(a.uid) uid FROM passbook_account_flow a inner join (
        SELECT account_id,MAX(update_time) last_update_time FROM passbook_account_flow 
        WHERE update_time <= ".qstr(system_toolClass::getFormatEndDate($date_end))." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)."
        GROUP BY account_id
    ) b on a.account_id = b.account_id and a.update_time = b.last_update_time
    group by a.account_id
) b ON a.uid=b.uid)
f on f.account_id = pa.uid
union all

select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
    SELECT a.account_id,MAX(a.uid) uid FROM passbook_account_flow a inner join (
        SELECT account_id,MAX(update_time) last_update_time FROM passbook_account_flow 
        WHERE update_time <= ".qstr(system_toolClass::getFormatEndDate($date_end))." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)."
        GROUP BY account_id
    ) b on a.account_id = b.account_id and a.update_time = b.last_update_time
    group by a.account_id
) b ON a.uid=b.uid)
f on f.account_id = pa.uid

) x

where category=".qstr(passbookTypeEnum::ASSET)."  order by book_code ";

        $list = $r->getRows($sql);

        $data = self::mergeCurrencyAndAccumulated($list);
        $format_list = $data['format_list'];
        $asset_total_multi_currency = $data['total_multi_amount'];

        $tree_data = phpTreeClass::makeTreeForHtml(0,$format_list,array(
            'primary_key' => 'book_code',
            'parent_key' => 'parent_book_code',
        ));


        $asset_data = array(
            'total_amount' => $asset_total_multi_currency,
            'list' => $tree_data
        );

        return $asset_data;

    }

    public static function getDevBalanceSheetDataOfAssetOld()
    {
        $r = new ormReader();
        // 资产部分 1

        $sql = "  select * from (
select gl.*,pa.currency,pa.balance,pa.outstanding
from (select * from gl_account where  gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
union all

select gl.*,pa.currency,pa.balance,pa.outstanding
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid

) x

where category=".qstr(passbookTypeEnum::ASSET)."  order by book_code ";
        $list = $r->getRows($sql);

        $data = self::mergeCurrencyAndAccumulated($list);
        $format_list = $data['format_list'];
        $asset_total_multi_currency = $data['total_multi_amount'];

        $tree_data = phpTreeClass::makeTreeForHtml(0,$format_list,array(
            'primary_key' => 'book_code',
            'parent_key' => 'parent_book_code',
        ));


        $asset_data = array(
            'total_amount' => $asset_total_multi_currency,
            'list' => $tree_data
        );

        return $asset_data;

    }

    public static function getDevBalanceSheetDataOfLiabilities($date_end)
    {
        $r = new ormReader();

        // 负债+权益  2+4
        $sql = "  select * from (
select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
    SELECT a.account_id,MAX(a.uid) uid FROM passbook_account_flow a inner join (
        SELECT account_id,MAX(update_time) last_update_time FROM passbook_account_flow 
        WHERE update_time <= ".qstr(system_toolClass::getFormatEndDate($date_end))." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)."
        GROUP BY account_id
    ) b on a.account_id = b.account_id and a.update_time = b.last_update_time
    group by a.account_id
) b ON a.uid=b.uid)
f on f.account_id = pa.uid
union all

select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
    SELECT a.account_id,MAX(a.uid) uid FROM passbook_account_flow a inner join (
        SELECT account_id,MAX(update_time) last_update_time FROM passbook_account_flow 
        WHERE update_time <= ".qstr(system_toolClass::getFormatEndDate($date_end))." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)."
        GROUP BY account_id
    ) b on a.account_id = b.account_id and a.update_time = b.last_update_time
    group by a.account_id
) b ON a.uid=b.uid)
f on f.account_id = pa.uid

) x

where ( category=".qstr(passbookTypeEnum::DEBT)." or category=".qstr(passbookTypeEnum::EQUITY)." )  order by book_code ";


        $list = $r->getRows($sql);

        $data = self::mergeCurrencyAndAccumulated($list);
        $format_list = $data['format_list'];
        $asset_total_multi_currency = $data['total_multi_amount'];

        $tree_data = phpTreeClass::makeTreeForHtml(0,$format_list,array(
            'primary_key' => 'book_code',
            'parent_key' => 'parent_book_code',
        ));


        // 合计计算profit/loss
        $sql = "  select * from (
select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
    SELECT a.account_id,MAX(a.uid) uid FROM passbook_account_flow a inner join (
        SELECT account_id,MAX(update_time) last_update_time FROM passbook_account_flow
        WHERE update_time <= ".qstr(system_toolClass::getFormatEndDate($date_end))." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)."
        GROUP BY account_id
    ) b on a.account_id = b.account_id and a.update_time = b.last_update_time
    group by a.account_id
) b ON a.uid=b.uid)
f on f.account_id = pa.uid
union all

select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
    SELECT a.account_id,MAX(a.uid) uid FROM passbook_account_flow a inner join (
        SELECT account_id,MAX(update_time) last_update_time FROM passbook_account_flow
        WHERE update_time <= ".qstr(system_toolClass::getFormatEndDate($date_end))." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)."
        GROUP BY account_id
    ) b on a.account_id = b.account_id and a.update_time = b.last_update_time
    group by a.account_id
) b ON a.uid=b.uid)
f on f.account_id = pa.uid

) x

where ( book_code like '3%' or  book_code like '5%'
        or book_code like '6%' )  order by book_code ";
        $list = $r->getRows($sql);

        $total_profit_lost = array();
        foreach( $list as $v ){
            $amount = $v['balance'];
            if( $v['currency'] ){
                if( startWith($v['book_code'],'5') || startWith($v['book_code'],'6-002') ){
                    $total_profit_lost[$v['currency']] -= $amount;
                    if($v['currency'] == currencyEnum::USD){
                        $total_profit_lost['total'] -= $amount;
                    }else{
                        $total_profit_lost['total'] -= global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD) * $amount;
                    }
                }else{
                    $total_profit_lost[$v['currency']] += $amount;
                    if($v['currency'] == currencyEnum::USD){
                        $total_profit_lost['total'] += $amount;
                    }else{
                        $total_profit_lost['total'] += global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD) * $amount;
                    }
                }
            }
        }


        $asset_data = array(
            'total_amount' => $asset_total_multi_currency,
            'total_profit_loss' => $total_profit_lost,
            'list' => $tree_data
        );

        return $asset_data;
    }

    public static function getDevBalanceSheetDataOfLiabilitiesOld()
    {
        $r = new ormReader();

        // 负债+权益  2+4
        $sql = "  select * from (
select gl.*,pa.currency,pa.balance,pa.outstanding
from (select * from gl_account where gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
union all

select gl.*,pa.currency,pa.balance,pa.outstanding
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid

) x

where ( category=".qstr(passbookTypeEnum::DEBT)." or category=".qstr(passbookTypeEnum::EQUITY)." )  order by book_code ";


        $list = $r->getRows($sql);

        $data = self::mergeCurrencyAndAccumulated($list);
        $format_list = $data['format_list'];
        $asset_total_multi_currency = $data['total_multi_amount'];

        $tree_data = phpTreeClass::makeTreeForHtml(0,$format_list,array(
            'primary_key' => 'book_code',
            'parent_key' => 'parent_book_code',
        ));


        // 合计计算profit/loss
        $sql = "  select * from (
select gl.*,pa.currency,pa.balance,pa.outstanding
from (select * from gl_account where gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
union all

select gl.*,pa.currency,pa.balance,pa.outstanding
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid

) x

where ( book_code like '3%' or  book_code like '5%'
        or book_code like '6%' )  order by book_code ";
        $list = $r->getRows($sql);

        $total_profit_lost = array();
        foreach( $list as $v ){
            $amount = $v['balance']; // + $v['outstanding'];
            if( $v['currency'] ){

                if( startWith($v['book_code'],'5') || startWith($v['book_code'],'6-002') ){
                    $total_profit_lost[$v['currency']] -= $amount;
                }else{
                    $total_profit_lost[$v['currency']] += $amount;
                }
            }
        }


        $asset_data = array(
            'total_amount' => $asset_total_multi_currency,
            'total_profit_loss' => $total_profit_lost,
            'list' => $tree_data
        );

        return $asset_data;
    }

    public static function getDevBalanceSheetData($date_end)
    {

        $asset = self::getDevBalanceSheetDataOfAsset($date_end);
        $liabilities = self::getDevBalanceSheetDataOfLiabilities($date_end);
        return array(
            'asset' => $asset,
            'liabilities' => $liabilities
        );



    }



    public static function getGlAccountBalanceDetailData($gl_account,$currency)
    {
        $r = new ormReader();

        // 如果是系统账户
        if( $gl_account['obj_type'] <= 0 ){

            $sql = "  select * from (
select gl.*,p.uid book_id,p.book_name passbook_book_name,pa.currency,pa.balance,pa.outstanding
from (select * from gl_account where gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
union all

select gl.*,p.uid book_id,p.book_name passbook_book_name,pa.currency,pa.balance,pa.outstanding
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid

) x

where ( book_code like '".$gl_account['book_code']."%' )  order by book_code ";
            $list = $r->getRows($sql);
            $data = self::mergeCurrencyAndAccumulated($list);
            $format_list = $data['format_list'];
            $total_multi_currency = $data['total_multi_amount'];

            $tree_data = phpTreeClass::makeTreeForHtml($gl_account['book_code']?:0,$format_list,array(
                'primary_key' => 'book_code',
                'parent_key' => 'parent_book_code',
            ));
            $data = array(
                'total_amount' => $total_multi_currency,
                'list' => $tree_data
            );
            return $data;

        }else{

            // 非系统账户

            $sql = "select gl.*,p.uid book_id,p.book_name passbook_book_name,pa.currency,pa.balance,pa.outstanding from gl_account  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid where gl.book_code like '".$gl_account['book_code']."%'".
                " and pa.currency=".qstr($currency);
            $list = $r->getRows($sql);

            // 统一格式化数据
            $total_multi_currency = array();
            foreach( $list as $k=>$v ){
                $v['is_leaf_book'] = 1;  // 标识是叶子book
                $amount = $v['balance']; // + $v['outstanding'];
                $v['multi_currency'][$v['currency']] = $amount;
                $total_multi_currency[$v['currency']] += $amount;
                $list[$k] = $v;
            }

            $data = array(
                'total_amount' => $total_multi_currency,
                'list' => $list
            );
            return $data;

        }

    }

    public static function getDevIncomeStatementData($date_start, $date_end){
        $r = new ormReader();

        // Income
        $sql = "  select * from (
select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
SELECT account_id,MAX(uid) uid FROM passbook_account_flow WHERE update_time > ".qstr(system_toolClass::getFormatStartDate($date_start))." AND update_time < ".qstr(system_toolClass::getFormatEndDate($date_end))." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)." GROUP BY account_id) b ON a.uid=b.uid)
f on f.account_id = pa.uid
union all

select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
SELECT account_id,MAX(uid) uid FROM passbook_account_flow WHERE update_time > ".qstr(system_toolClass::getFormatStartDate($date_start))." AND update_time < ".qstr(system_toolClass::getFormatEndDate($date_end))." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)." GROUP BY account_id) b ON a.uid=b.uid)
f on f.account_id = pa.uid

) x

where category = ".qstr(passbookTypeEnum::PROFIT_INCOME)."  order by book_code ";


        $list = $r->getRows($sql);

        $data = self::mergeCurrencyAndAccumulated($list);
        $format_list = $data['format_list'];
        $asset_total_multi_currency = $data['total_multi_amount'];

        $income_tree_data = phpTreeClass::makeTreeForHtml(6,$format_list,array(
            'primary_key' => 'book_code',
            'parent_key' => 'parent_book_code',
        ));
        $income_data = array(
            'total_amount' => $asset_total_multi_currency,
            'list' => $income_tree_data
        );

        // Common
        $sql = "  select * from (
select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
SELECT account_id,MAX(uid) uid FROM passbook_account_flow WHERE update_time > ".qstr($date_start)." AND update_time < ".qstr($date_end)." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)." GROUP BY account_id) b ON a.uid=b.uid)
f on f.account_id = pa.uid
union all

select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
SELECT account_id,MAX(uid) uid FROM passbook_account_flow WHERE update_time > ".qstr($date_start)." AND update_time < ".qstr($date_end)." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)." GROUP BY account_id) b ON a.uid=b.uid)
f on f.account_id = pa.uid

) x

where category = ".qstr(passbookTypeEnum::COMMON)."  order by book_code ";
        $list = $r->getRows($sql);
        $data = self::mergeCurrencyAndAccumulated($list);
        $format_list = $data['format_list'];
        $asset_total_multi_currency = $data['total_multi_amount'];
        $common_tree_data = phpTreeClass::makeTreeForHtml(0,$format_list,array(
            'primary_key' => 'book_code',
            'parent_key' => 'parent_book_code',
        ));
        $common_data = array(
            'total_amount' => $asset_total_multi_currency,
            'list' => $common_tree_data
        );

        // Expense
        $sql = "  select * from (
select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
SELECT account_id,MAX(uid) uid FROM passbook_account_flow WHERE update_time > ".qstr($date_start)." AND update_time < ".qstr($date_end)." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)." GROUP BY account_id) b ON a.uid=b.uid)
f on f.account_id = pa.uid
union all

select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
SELECT account_id,MAX(uid) uid FROM passbook_account_flow WHERE update_time > ".qstr($date_start)." AND update_time < ".qstr($date_end)." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)." GROUP BY account_id) b ON a.uid=b.uid)
f on f.account_id = pa.uid

) x

where category = ".qstr(passbookTypeEnum::PROFIT_EXPENSE)." order by book_code ";
    
    
        $list = $r->getRows($sql);

        $data = self::mergeCurrencyAndAccumulated($list);
        $format_list = $data['format_list'];
        $asset_total_multi_currency = $data['total_multi_amount'];

        $expense_tree_data = phpTreeClass::makeTreeForHtml(6,$format_list,array(
            'primary_key' => 'book_code',
            'parent_key' => 'parent_book_code',
        ));
        $expense_data = array(
            'total_amount' => $asset_total_multi_currency,
            'list' => $expense_tree_data
        );

         // Expense
         $sql = "  select * from (
select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where gl_account.obj_type>'0')  gl left join passbook  p on p.parent_book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
SELECT account_id,MAX(uid) uid FROM passbook_account_flow WHERE update_time > ".qstr($date_start)." AND update_time < ".qstr($date_end)." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)." GROUP BY account_id) b ON a.uid=b.uid)
f on f.account_id = pa.uid
union all

select gl.*,pa.currency,f.end_balance balance
from (select * from gl_account where (gl_account.obj_type='0' or gl_account.obj_type is null ) )  gl left join passbook  p on p.book_code=gl.book_code
        left join passbook_account pa on  pa.book_id=p.uid
        left join (
SELECT a.* FROM passbook_account_flow a INNER JOIN (
SELECT account_id,MAX(uid) uid FROM passbook_account_flow WHERE update_time > ".qstr($date_start)." AND update_time < ".qstr($date_end)." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)." GROUP BY account_id) b ON a.uid=b.uid)
f on f.account_id = pa.uid

) x

where category = ".qstr(passbookTypeEnum::COST)." order by book_code ";
            
        $list = $r->getRows($sql);
        $data = self::mergeCurrencyAndAccumulated($list);
        $format_list = $data['format_list'];
        $asset_total_multi_currency = $data['total_multi_amount'];

        $cost_tree_data = phpTreeClass::makeTreeForHtml(0,$format_list,array(
            'primary_key' => 'book_code',
            'parent_key' => 'parent_book_code',
        ));
        $cost_data = array(
            'total_amount' => $asset_total_multi_currency,
            'list' => $cost_tree_data
        );

        
        return array(
            'income' => $income_data,
            'common' => $common_data,
            'expense' => $expense_data,
            'cost' => $cost_data
        );
    }


    public static function getJournalVoucherData($pageNumber, $pageSize, $filters = array()){
        $r = new ormReader();
        $where = "state = " . qstr(passbookTradingStateEnum::DONE);
        if($filters['trade_id']){
            $where .= " and uid = " . qstr($filters['trade_id']);
        }
        if($filters['trade_type']){
            $where .= " and trading_type = " . qstr($filters['trade_type']);
        }
        if($filters['remark']){
            $where .= " and sys_memo like '%" . trim($filters['remark']) . "%'";
        }
        if ($filters['start_date']) {
            $start_date = system_toolClass::getFormatStartDate($filters['start_date']);
            $where .= " AND update_time >= '$start_date' ";
        }

        if ($filters['end_date']) {
            $end_date = system_toolClass::getFormatEndDate($filters['end_date']);
            $where .= " AND update_time <= '$end_date' ";
        }
        $sql = "select * from passbook_trading where $where";
        $data = $r->getPage($sql, $pageNumber, $pageSize);

        $start = ($pageNumber - 1) * $pageSize;
        $limit = "$start,$pageSize";

        $sql1 = "select t.*,f.uid fid,p.uid book_id,p.book_name,a.currency,f.debit,f.credit from passbook_account_flow f
inner join passbook_account a on f.account_id = a.uid
left join passbook p on a.book_id = p.uid
right join ($sql limit $limit) t
on t.uid = f.trade_id
where f.state = " . qstr(passbookAccountFlowStateEnum::DONE);


        $ret = $r->getRows($sql1);
        $list = array();
        $num = ($pageNumber - 1) * $pageSize;
        $tempCurrency = array();
        foreach($ret as $v) {
            if (!$list[$v['uid']]) {
                $no = ++$num;
            }
            $list[$v['uid']]['no'] = $no;
            $list[$v['uid']]['trade_id'] = $v['uid'];
            $list[$v['uid']]['time'] = $v['update_time'];
            $list[$v['uid']]['subject'] = $v['subject'];
            $list[$v['uid']]['remark'] = $v['remark'];
            if ($v['debit'] + $v['credit'] != 0) {
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

    public static function getReceivableInterestData($date_end){
        $r = new ormReader();
        $where = "c.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " AND c.state < " . qstr(loanContractStateEnum::COMPLETE) . " AND s.state != " . qstr(schemaStateTypeEnum::COMPLETE) . " AND s.state != " . qstr(schemaStateTypeEnum::CANCEL) . " AND s.interest_date < " . qstr($date_end);

        $sql = "select c.uid,c.loan_term_day,c.currency,c.interest_rate,c.interest_rate_type,c.interest_rate_unit,s.receivable_date,s.interest_date,s.initial_principal,s.receivable_principal,s.receivable_interest,s.paid_interest
from loan_contract c left join loan_installment_scheme s on c.uid = s.contract_id where $where";

        $ret = $r->getRows($sql);
        $data['less'] = array();
        $data['less']['normal'] = array();
        $data['less']['standard'] = array();
        $data['less']['substandard'] = array();
        $data['less']['doubtful'] = array();
        $data['less']['loss'] = array();
        $data['greater'] = array();
        $data['greater']['normal'] = array();
        $data['greater']['standard'] = array();
        $data['greater']['substandard'] = array();
        $data['greater']['doubtful'] = array();
        $data['greater']['loss'] = array();
        foreach($ret as $v){
            $due_day = $diff = system_toolClass::diffBetweenTwoDays($date_end ,$v['receivable_date']);
            $interest_day = system_toolClass::diffBetweenTwoDays($date_end ,$v['interest_date']);
            switch ($due_day) {
                case $due_day <= 0:
                    if($due_day == 0){
                        $interest = $v['receivable_interest'] - $v['paid_interest'];
                    }else{
                        $rt = loan_baseClass::interestRateConversion($v['interest_rate'],$v['interest_rate_unit'],interestRatePeriodEnum::DAILY);
                        if(!$rt->STS){
                            throw new Exception($rt->MSG,$rt->CODE);
                        }
                        if($v['interest_rate_type'] == 1){
                            $i_day = $rt->DATA;
                        }else{
                            $i_day = ($rt->DATA)/100;
                        }
                        $interest = round($v['initial_principal'] * $i_day * $interest_day,2);
                    }
                    if($v['loan_term_day'] <= 365){
                        $data['less']['normal'][$v['currency']] += $interest;
                    }else{
                        $data['greater']['normal'][$v['currency']] += $interest;
                    }
                    break;
                case 0 < $due_day && $due_day <= 30:
                    $interest = $v['receivable_interest'] - $v['paid_interest'];
                    if($v['loan_term_day'] <= 365){
                        $data['less']['standard'][$v['currency']] += $interest;
                    }else{
                        $data['greater']['standard'][$v['currency']] += $interest;
                    }
                    break;
                case 30 < $due_day && $due_day <= 60:
                    $interest = $v['receivable_interest'] - $v['paid_interest'];
                    if($v['loan_term_day'] <= 365){
                        $data['less']['substandard'][$v['currency']] += $interest;
                    }else{
                        $data['greater']['substandard'][$v['currency']] += $interest;
                    }
                    break;
                case 60 < $due_day && $due_day <= 90:
                    $interest = $v['receivable_interest'] - $v['paid_interest'];
                    if($v['loan_term_day'] <= 365){
                        $data['less']['doubtful'][$v['currency']] += $interest;
                    }else{
                        $data['greater']['doubtful'][$v['currency']] += $interest;
                    }
                    break;
                case 90 < $due_day:
                    $interest = $v['receivable_interest'] - $v['paid_interest'];
                    if($v['loan_term_day'] <= 365){
                        $data['less']['loss'][$v['currency']] += $interest;
                    }else{
                        $data['greater']['loss'][$v['currency']] += $interest;
                    }
                    break;
                default:
                    # code...
                    break;
            }
            $data['total'][$v['currency']] += $interest;
        }

        foreach($data['less'] as $k => $v){
            foreach($v as $ck => $cv){
                if($ck != currencyEnum::USD){
                    $rate = global_settingClass::getCurrencyRateBetween($ck, currencyEnum::USD);
                    $amount = round($cv * $rate,2);
                }else{
                    $amount = $cv;
                }
                $data['less'][$k]['total_to_usd'] += $amount;
            }
        }
        foreach($data['greater'] as $k => $v){
            foreach($v as $ck => $cv){
                if($ck != currencyEnum::USD){
                    $rate = global_settingClass::getCurrencyRateBetween($ck, currencyEnum::USD);
                    $amount = round($cv * $rate,2);
                }else{
                    $amount = $cv;
                }
                $data['greater'][$k]['total_to_usd'] += $amount;
            }
        }
        foreach($data['total'] as $k => $v){
            if($k != currencyEnum::USD){
                $rate = global_settingClass::getCurrencyRateBetween($k, currencyEnum::USD);
                $amount = round($v * $rate,2);
            }else{
                $amount = $v;
            }
            $data['total']['total_to_usd'] += $amount;
        }
        return $data;
    }

    public static function getPassbookJournalVoucherData($passbook, $pageNumber, $pageSize, $filters = array()){
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
left join passbook_trading t on t.uid = f.trade_id
left join passbook_account a on f.account_id = a.uid
left join passbook p on a.book_id = p.uid
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