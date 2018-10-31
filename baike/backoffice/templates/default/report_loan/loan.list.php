<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td>Account</td>
            <td>Customer Name</td>
            <td>Province</td>
            <td>District</td>
            <td>Commune</td>
            <td>Village</td>
            <td class="currency-title">Principal Disbursed</td>
            <td class="currency-title">Overdue Bal.</td>
            <td class="ta-right">Opened</td>
            <td class="ta-right">InstNo</td>
            <td class="currency-title">Last Trn</td>
            <td class="currency-title">Pri Paid</td>
        </tr>
        <tr class="table-header t2">
            <td>CID</td>
            <td>Prod./GL/St.</td>
            <td>Group</td>
            <td>Street</td>
            <td>House</td>
            <td></td>
            <td class="currency-title">Current Balance</td>
            <td class="currency-title">Overdue Int.</td>
            <td class="ta-right">Maturing</td>
            <td class="ta-right">Int Rate</td>
            <td class="currency-title">LastTrn Dt</td>
            <td class="currency-title">Int Paid</td>
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
                    <td class="name">
                        <?php echo $row['display_name']?>
                    </td>
                    <td>
                        <?php echo $row['id1'] ? : '-'?>
                    </td>
                    <td>
                        <?php echo $row['id2'] ? : '-'?>
                    </td>
                    <td>
                        <?php echo $row['id3'] ? : '-'?>
                    </td>
                    <td>
                        <?php echo $row['id4'] ? : '-'?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['apply_amount'])?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['overdue_balance'])?>
                    </td>
                    <td class="ta-right">
                        <?php echo dateFormat($row['start_date'])?>
                    </td>
                    <td class="ta-right">
                        <?php echo $row['loan_period_value']?>
                        <?php echo ucwords($row['loan_period_unit'])?>
                    </td>
                    <td class="currency">
                        <?php echo $row['last_transaction'] ? ncPriceFormat($row['last_transaction']) : ''?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['paid_principal'])?>
                    </td>
                </tr>
                <tr class="<?php echo $i % 2 == 0 ? 'tr_even' : 'tr_odd'?>">
                    <td>
                        <?php echo $row['obj_guid']?:generateGuid($row['uid'], objGuidTypeEnum::CLIENT_MEMBER);?>
                    </td>
                    <td>
                        <?php echo $row['sub_product_code']?>/
                        <?php echo 'AB'?>/
                        <?php echo '11'?>
                    </td>
                    <td>
                        <?php echo $row['group'] ? : 'N/A'?>
                    </td>
                    <td>
                        <?php echo $row['street'] ? : 'N/A'?>
                    </td>
                    <td>
                        <?php echo $row['house_number'] ? : 'N/A'?>
                    </td>
                    <td>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['current_balance'])?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['overdue_interest'])?>
                    </td>
                    <td class="ta-right">
                        <?php echo dateFormat($row['end_date'])?>
                    </td>
                    <td class="ta-right">
                        <?php echo $row['interest_rate']?>
                        <?php echo $row['interest_rate_type'] == 1 ? '' : '%'?>
                    </td>
                    <td class="currency">
                        <?php echo dateFormat($row['last_transaction_date'])?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['paid_interest'])?>
                    </td>
                </tr>
            <?php } ?>
            <?php $loan_total = $data['loan_total']; ?>
            <tr class="total_amount border_top" style="border-top: 1px solid #DDD">
                <td><?php echo $loan_total['loan_count']?></td>
                <td colspan="2"><?php echo 'Grant Total '?></td>
                <td class="currency"><?php echo ncPriceFormat($loan_total['principal_disbursement_total'])?></td>
                <td class="currency"><?php echo ncPriceFormat($loan_total['overdue_balance_total'])?></td>
                <td colspan="3"></td>
                <td colspan="2" class="currency"><?php echo ncPriceFormat($loan_total['paid_principal_total'])?></td>
            </tr>
            <tr class="total_amount tr_td_pd_top_0">
                <td colspan="3"></td>
                <td class="currency"><?php echo ncPriceFormat($loan_total['principal_current_total'])?></td>
                <td class="currency"><?php echo ncPriceFormat($loan_total['overdue_interest_total'])?></td>
                <td colspan="3"></td>
                <td colspan="2" class="currency"><?php echo ncPriceFormat($loan_total['paid_interest_total'])?></td>
            </tr>
        <?php } else { ?>
            <tr>
                <td colspan="9">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php if (!$is_print) { ?>
    <?php include_once(template("widget/inc_content_pager")); ?>
<?php } ?>
