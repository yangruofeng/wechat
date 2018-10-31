<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td>Account</td>
            <td>Currency</td>
            <td class="ta-right">Opened</td>
            <td class="ta-right">InstNo</td>
            <td class="currency-title">Pri Total</td>
            <td class="currency-title">Int Total</td>
<!--            <td class="currency-title">Admin-Fee Total</td>-->
            <td class="currency-title">Operation-Fee Total</td>
            <td class="currency-title">Loan Fee</td>
            <td class="currency-title">Penalty</td>
        </tr>
        <tr class="table-header t2">
            <td>Prod.</td>
            <td>Status</td>
            <td class="ta-right">Maturing</td>
            <td class="ta-right">Int Rate</td>
            <td class="currency-title">Pri Paid</td>
            <td class="currency-title">Int Paid</td>
<!--            <td class="currency-title">Admin-Fee Paid</td>-->
            <td class="currency-title">Operation-Fee Paid</td>
            <td class="currency-title">Admin Fee</td>
            <td class="currency-title"></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php $i = 0;
            foreach ($data['data'] as $row) {
                ++$i;?>
                <tr class="<?php echo $i % 2 == 0 ? 'tr_even' : 'tr_odd'?>">
                    <td>
                        <?php echo $row['virtual_contract_sn']?>
                    </td>
                    <td>
                        <?php echo $row['currency']?>
                    </td>
                    <td class="ta-right">
                        <?php echo dateFormat($row['start_date'])?>
                    </td>
                    <td class="ta-right">
                        <?php echo $row['loan_period_value']?>
                        <?php echo ucwords($row['loan_period_unit'])?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['apply_amount'])?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['receivable_interest'])?>
                    </td>
<!--                    <td class="currency">-->
<!--                        --><?php //echo ncPriceFormat($row['receivable_admin_fee'])?>
<!--                    </td>-->
                    <td class="currency">
                        <?php echo ncPriceFormat($row['receivable_operation_fee'])?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['receivable_loan_fee'])?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['receivable_penalty'])?>
                    </td>
                </tr>
                <tr class="<?php echo $i % 2 == 0 ? 'tr_even' : 'tr_odd'?>">
                    <td>
                        <?php echo $row['sub_product_name']?>
                    </td>
                    <td>
                        <?php echo $data['state'][$row['state']]?>
                    </td>
                    <td class="ta-right">
                        <?php echo dateFormat($row['end_date'])?>
                    </td>
                    <td class="ta-right">
                        <?php echo $row['interest_rate']?>
                        <?php echo $row['interest_rate_type'] == 1 ? '' : '%'?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['paid_principal'])?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['paid_interest'])?>
                    </td>
<!--                    <td class="currency">-->
<!--                        --><?php //echo ncPriceFormat($row['apply_amount'])?>
<!--                    </td>-->
                    <td class="currency">
                        <?php echo ncPriceFormat($row['paid_operation_fee'])?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['receivable_admin_fee'])?>
                    </td>
                    <td></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="9">
                    <?php include(template(":widget/no_record")); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
