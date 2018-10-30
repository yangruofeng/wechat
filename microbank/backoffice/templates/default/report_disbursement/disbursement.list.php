<div>
    <?php
    $rows = $data['data'];
    $total_amount = $data['total_amount'];
    $currency_list = $data['currency_list'];
    ?>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td rowspan="2">Credit Officer's Name</td>
            <td colspan="<?php echo count($currency_list) + 1 ?>">New Client</td>
            <td colspan="<?php echo count($currency_list) + 1 ?>">Old Client</td>
            <td colspan="<?php echo count($currency_list) + 1 ?>">Total</td>
        </tr>
        <tr class="table-header t1">
            <td>Count</td>
            <?php foreach ($currency_list as $c_k => $c_v) { ?>
                <td><?php echo $c_v ?></td>
            <?php } ?>
            <td>Count</td>
            <?php foreach ($currency_list as $c_k => $c_v) { ?>
                <td><?php echo $c_v ?></td>
            <?php } ?>
            <td>Count</td>
            <?php foreach ($currency_list as $c_k => $c_v) { ?>
                <td><?php echo $c_v ?></td>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($rows) { ?>
            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td>
                        <a href="<?php echo getUrl('report_disbursement', 'disbursementClientLoan', array(array('co_id' => $row['uid'])), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['user_name']?></a>
                    </td>
                    <td class="number"><?php echo $row['new_member']['loan_count'] ? : '-'?></td>
                    <?php foreach ($currency_list as $c_k => $c_v) { ?>
                        <td class="currency"><?php echo $row['new_member']['loan_amount_' . $c_k] > 0 ? ncPriceFormat($row['new_member']['loan_amount_' .$c_k]) : '-'?></td>
                    <?php } ?>
                    <td class="number"><?php echo $row['repeat_member']['loan_count'] ? : '-'?></td>
                    <?php foreach ($currency_list as $c_k => $c_v) { ?>
                        <td class="currency"><?php echo $row['repeat_member']['loan_amount_' .$c_k] > 0 ? ncPriceFormat($row['repeat_member']['loan_amount_' .$c_k]) : '-'?></td>
                    <?php } ?>
                    <td class="number"><?php echo $row['total_amount']['loan_count'] ? : '-'?></td>
                    <?php foreach ($currency_list as $c_k => $c_v) { ?>
                        <td class="currency"><?php echo $row['total_amount']['loan_amount_' .$c_k] > 0 ? ncPriceFormat($row['total_amount']['loan_amount_' .$c_k]) : '-'?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
            <tr class="total_amount border_top">
                <td><?php echo 'Totals'?></td>
                <td class="number"><?php echo $total_amount['new_member']['loan_count'] ? : '-'?></td>
                <?php foreach ($currency_list as $c_k => $c_v) { ?>
                    <td class="currency"><?php echo $total_amount['new_member']['loan_amount_' .$c_k] > 0 ? ncPriceFormat($total_amount['new_member']['loan_amount_' .$c_k]) : '-'?></td>
                <?php } ?>
                <td class="number"><?php echo $total_amount['loan_count'] ? : '-'?></td>
                <?php foreach ($currency_list as $c_k => $c_v) { ?>
                    <td class="currency"><?php echo $total_amount['repeat_member']['loan_amount_' .$c_k] > 0 ? ncPriceFormat($total_amount['repeat_member']['loan_amount_' .$c_k]) : '-'?></td>
                <?php } ?>
                <td class="number"><?php echo $total_amount['total_amount']['loan_count'] ? : '-'?></td>
                <?php foreach ($currency_list as $c_k => $c_v) { ?>
                    <td class="currency"><?php echo $total_amount['total_amount']['loan_amount_' .$c_k] > 0 ? ncPriceFormat($total_amount['total_amount']['loan_amount_' .$c_k]) : '-'?></td>
                <?php } ?>
            </tr>
        <?php } else { ?>
            <tr>
                <td colspan="20">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
