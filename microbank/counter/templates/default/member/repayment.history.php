<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Periods</td>
            <td>Repayment Time</td>
            <td>Payable Principal</td>
            <td>Payable Interest</td>
            <td>Operating Charges</td>
            <td>Penalty</td>
            <td>Repayment Total</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td><?php echo $row['scheme_name']?></td>
                    <td><?php echo $row['done_time']?></td>
                    <td><?php echo ncPriceFormat($row['receivable_principal'])?></td>
                    <td><?php echo ncPriceFormat($row['receivable_interest'])?></td>
                    <td><?php echo ncPriceFormat($row['receivable_operation_fee'] + $row['receivable_admin_fee'])?></td>
                    <td><?php echo ncPriceFormat($row['penalty'])?></td>
                    <td><?php echo ncPriceFormat($row['amount'] + $row['penalty'])?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="7">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>