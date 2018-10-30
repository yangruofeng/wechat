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
            <td>Payable Total</td>
            <td>State</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php $i = count($data['data']); $k=0; foreach ($data['data'] as $row) { $k++ ;?>
                <tr>
                    <td><?php echo $row['scheme_name']?></td>
                    <td><?php echo $row['receivable_date']?></td>
                    <td><?php echo ncPriceFormat($row['receivable_principal'])?></td>
                    <td><?php echo ncPriceFormat($row['receivable_interest'])?></td>
                    <td><?php echo ncPriceFormat($row['receivable_operation_fee'] + $row['receivable_admin_fee'])?></td>
                    <td><?php echo ncPriceFormat($row['penalty'])?></td>
                    <td><?php echo ncPriceFormat($row['amount'] + $row['penalty'])?></td>
                    <td>
                        <?php if($row['state'] == 100 && $k == $i) {
                            echo 'Paid off';
                        } else if ($row['state'] == 100) {
                            echo 'Paid';
                        } else if ($row['receivable_date'] < strtotime('Y-m-d 23:59:59',time())) {
                            echo 'Overdue';
                        } else {
                            echo 'Pending Repayment';
                        } ?>
                    </td>
                </tr>
            <?php } ?>
                <tr>
                    <td colspan="10" style="text-align: center">
                        <button type="button" class="btn btn-primary" style="width: 200px" onclick="print_installment_scheme()"><i class="fa fa-print"></i>  Print Scheme</button>
                    </td>
                </tr>
        <?php } else { ?>
            <tr>
                <td colspan="8">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>