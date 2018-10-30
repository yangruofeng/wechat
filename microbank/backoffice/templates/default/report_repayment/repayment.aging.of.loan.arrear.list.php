<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Tern Bracket</td>
            <td class="number"># Accts</td>
            <td class="currency-title">Overdue Principal</td>
            <td class="currency-title">Outstanding Balance</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) {
                $min = intval($row['min']);
                $max = intval($row['max']); ?>
                <tr>
                    <td>
                        <?php if ($max == 0 && $min > 0) { ?>
                            <?php echo '> ' . $min ?>
                        <?php } else if ($min == 0 && $max == 0) { ?>
                            <?php echo '0 + 0' ?>
                        <?php } else { ?>
                            <?php echo $min . ' - ' . $max ?>
                        <?php } ?>
                    </td>
                    <td class="number">
                        <?php echo $row['statistics']['loan_count'] ?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['statistics']['due_principal']) ?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($row['statistics']['outstanding_balance']) ?>
                    </td>
                </tr>
            <?php } ?>
            <tr class="total_amount border_top">
                <td><?php echo 'Totals'?></td>
                <td class="number"><?php echo $data['amount_total']['loan_count']?></td>
                <td class="currency"><?php echo ncPriceFormat($data['amount_total']['due_principal'])?></td>
                <td class="currency"><?php echo ncPriceFormat($data['amount_total']['outstanding_balance'])?></td>
            </tr>
        <?php } else { ?>
            <tr>
                <td colspan="5">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>