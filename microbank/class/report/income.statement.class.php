<?php

class incomeStatementClass {
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

    public static function getDevincomeStatementData(){
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

where ( category=".qstr(passbookTypeEnum::PROFIT_INCOME)." or category=".qstr(passbookTypeEnum::PROFIT_EXPENSE)." )  order by book_code ";


        $list = $r->getRows($sql);

        $data = self::mergeCurrencyAndAccumulated($list);
        $format_list = $data['format_list'];
        $asset_total_multi_currency = $data['total_multi_amount'];

        $tree_data = phpTreeClass::makeTreeForHtml(0,$format_list,array(
            'primary_key' => 'book_code',
            'parent_key' => 'parent_book_code',
        ));



        print_r($tree_data);
    }



    public static function getReportData() {
        $model_gl_account = new gl_accountModel();
        $firsts = $model_gl_account->getIncomingStatementTotal();
        $data = array();
        foreach ($firsts as $v) {
            //$rt = $model_gl_account->getIncomentStatementChild($v['uid'], $v['category']);
            $data[$v['account_name']]['children'] = $v;
        }
        die;
        // Revenues
        $revenues['amount'] = $incomes['profit']['amount'];
        unset($incomes['profit']['amount']);
        $revenues['children'] = $incomes['profit'];
        // Admin Expense
        $admin_expense = array();
        if($incomes['cost']['admin_expense']){
            $admin_expense = $incomes['cost']['admin_expense'];
        }
        // Admin Expense
        $finance_expense = array();
        if($incomes['cost']['finance_expense']){
            $finance_expense = $incomes['cost']['finance_expense'];
        }
        // Net Income
        $net_income['amount'] = array(
            'USD' => $revenues['amount']['USD'] +  $admin_expense['amount']['USD'] + $finance_expense['amount']['USD'],
            'KHR' => $revenues['amount']['KHR'] +  $admin_expense['amount']['KHR'] + $finance_expense['amount']['KHR']
        );
        return new result(true, null, array(
            'Revenues' => $revenues,
            'Admin Expense' => $admin_expense,
            'Finance Expense' => $finance_expense,
            'Net Income' => $net_income,
        ));
    }
}