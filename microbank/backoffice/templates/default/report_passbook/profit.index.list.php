<?php
$nbsp = '&nbsp;';
$nbsp_len = strlen($nbsp);
$income_data = $data['income'];
$common_data = $data['common'];
$expense_data = $data['expense'];
$cost_data = $data['cost'];
$currency = (new currencyEnum())->toArray();
?>
<table class="table table-hover table-bordered">
    <thead>
    <tr>
        <th>Item</th>
        <?php foreach($currency as $ccy){ ?>
            <th class="c-<?php echo strtolower($ccy);?>"><?php echo $ccy;?></th>
        <?php }?>
        <th>Total(<?php echo currencyEnum::USD;?>)</th>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php foreach( $data as $cv ){ ?>
        <?php foreach ($cv['list'] as $key => $v) { ?>
            <tr>
                <td>
                    <?php
                    echo str_pad('',($v['level']-1)*4*$nbsp_len,$nbsp,STR_PAD_LEFT).
                        $v['book_name'].'('.$v['book_code'].')';
                    ; ?>
                </td>
                <?php foreach($currency as $ccy){ ?>
                    <td>
                        <?php if($v['multi_currency'][$ccy]) {?>
                            <a href="<?php echo getUrl('report_passbook','balanceSheetDetail',array(
                                'book_code' => $v['book_code'],
                                'currency' => $ccy,
                                'type' => 'income'
                            ),false,BACK_OFFICE_SITE_URL); ?>" class="<?php if($v['multi_currency'][$ccy] < 0 ){echo 'negative-number';}?>">
                                <?php echo ncPriceFormat($v['multi_currency'][$ccy]); ?>
                            </a>
                        <?php }else{echo '-';}?>
                    </td>
                <?php }?>
                <td class="<?php if($v['multi_currency']['total'] < 0 ){echo 'negative-number';}?>"><?php echo ncPriceFormat($v['multi_currency']['total']);?></td>

            </tr>
        <?php }?>
    <?php } ?>
    <tr class="total">
        <td align="right">Total</td>
        <?php foreach($currency as $ccy){ ?>
            <td><?php echo ncPriceFormat($income_data['total_amount'][$ccy] + $common_data['total_amount'][$ccy] - $expense_data['total_amount'][$ccy] - $cost_data['total_amount'][$ccy]); ?></td>
        <?php }?>
        <td><?php echo ncPriceFormat($income_data['total_amount']['total'] + $common_data['total_amount']['total'] - $expense_data['total_amount']['total'] - $cost_data['total_amount']['total']); ?></td>
    </tr>

    </tbody>
</table>