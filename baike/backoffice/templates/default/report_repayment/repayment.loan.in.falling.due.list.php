<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Account No.</td>
            <td>GL Code</td>
            <td>Due Date</td>
            <td class="number">CID</td>
            <td>Customer Name</td>
            <td class="currency-title">Principal Amount</td>
            <td class="currency-title">Scheduled Interest</td>
            <td class="currency-title">Charge Amount</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td><?php echo $row['virtual_contract_sn']?></td>
                    <td><?php echo $row['sub_product_code']?></td>
                    <td><?php echo dateFormat($row['receivable_date'])?></td>
                    <td><?php echo $row['obj_guid']?></td>
                    <td><?php echo $row['display_name']?></td>
                    <td class="currency"><?php echo ncPriceFormat($row['principal_amount'])?></td>
                    <td class="currency"><?php echo ncPriceFormat($row['scheduled_interest'])?></td>
                    <td class="currency"><?php echo ncPriceFormat($row['penalty'])?></td>
                </tr>
            <?php } ?>
            <tr class="total_amount border_top">
                <td colspan="3"></td>
                <td><?php echo 'Grand Total'?></td>
                <td class="number"><?php echo $data['amount_total']['count']?></td>
                <td class="currency"><?php echo ncPriceFormat($data['amount_total']['principal_amount'])?></td>
                <td class="currency"><?php echo ncPriceFormat($data['amount_total']['scheduled_interest'])?></td>
                <td class="currency"><?php echo ncPriceFormat($data['amount_total']['penalty'])?></td>
            </tr>
        <?php } else { ?>
            <tr>
                <td colspan="8">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>