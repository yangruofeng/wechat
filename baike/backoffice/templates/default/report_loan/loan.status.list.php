<?php $currency_list = $data['currency_list']; ?>
<?php $loan_contract_state = $data['loan_contract_state']; ?>
<?php $loan_summary = $data['loan_summary']; ?>
<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td rowspan="2">Account Status</td>
            <td rowspan="2">No.of Account</td>
            <td colspan="<?php echo count($currency_list)?>">Current Balance</td>
            <td colspan="<?php echo count($currency_list)?>">Overdue Bal.</td>
            <td colspan="<?php echo count($currency_list)?>">Principal Disabused</td>
        </tr>
        <tr class="table-header t2">
            <?php foreach ($currency_list as $currency) { ?>
                <td><?php echo $currency?></td>
            <?php } ?>
            <?php foreach ($currency_list as $currency) { ?>
                <td><?php echo $currency?></td>
            <?php } ?>
            <?php foreach ($currency_list as $currency) { ?>
                <td><?php echo $currency?></td>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($loan_contract_state as $key => $state) {
            if ($key <= loanContractStateEnum::PENDING_APPROVAL) continue;
            $val = $loan_summary[$key];
            ?>
            <tr>
                <td>
                    <?php echo 'Status ' . $key . '-' . $state?>
                </td>
                <td>
                    <?php echo $val['loan_count']?>
                </td>

                <?php foreach ($currency_list as $key => $currency) { ?>
                    <td class="currency"><?php echo ncPriceFormat($val['current_balance'][$key])?></td>
                <?php } ?>

                <?php foreach ($currency_list as $key => $currency) { ?>
                    <td class="currency"><?php echo ncPriceFormat($val['overdue_balance'][$key])?></td>
                <?php } ?>

                <?php foreach ($currency_list as $key => $currency) { ?>
                    <td class="currency"><?php echo ncPriceFormat($val['principal_disbursed'][$key])?></td>
                <?php } ?>
            </tr>
        <?php } ?>
        <tr class="total_amount border_top">
            <td><?php echo 'Totals'?></td>
            <td><?php echo $data['loan_count_total']?></td>
            <?php foreach ($currency_list as $key => $currency) { ?>
                <td class="currency"><?php echo ncPriceFormat($data['current_balance_total'][$key])?></td>
            <?php } ?>
            <?php foreach ($currency_list as $key => $currency) { ?>
                <td class="currency"><?php echo ncPriceFormat($data['overdue_balance_total'][$key])?></td>
            <?php } ?>
            <?php foreach ($currency_list as $key => $currency) { ?>
                <td class="currency"><?php echo ncPriceFormat($data['principal_disbursed_total'][$key])?></td>
            <?php } ?>
        </tr>
        </tbody>
    </table>
</div>
