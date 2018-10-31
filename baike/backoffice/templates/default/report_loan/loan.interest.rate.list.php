<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td>Term Bracket</td>
            <td># Accts</td>
            <?php foreach ($data['currency_list'] as $key => $currency) { ?>
                <td>Amount-<?php echo $currency?></td>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) {
                $min = $row['min'];
                $max = $row['max']; ?>
                <tr>
                    <td>
                        <?php if ($max == 0 && $min > 0) { ?>
                            <?php echo '> ' . ncPriceFormat($min) ?>
                        <?php } else if (!isset($min)) { ?>
                            <?php echo '0.00 + 0.00' ?>
                        <?php } else { ?>
                            <?php echo ncPriceFormat($min) . ' - ' . ncPriceFormat($max) ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $row['report']['loan_count'] ?>
                    </td>
                    <?php foreach ($data['currency_list'] as $key => $currency) { ?>
                        <td class="">
                            <?php echo $row['report']['loan_amount_' . $key] ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            <tr class="total_amount border_top">
                <td><?php echo 'Totals'?></td>
                <td><?php echo $data['amount_total']['loan_count']?></td>
                <?php foreach ($data['currency_list'] as $key => $currency) { ?>
                    <td class=""><?php echo ncPriceFormat($data['amount_total']['loan_amount_' . $key])?></td>
                <?php } ?>
            </tr>
        <?php } else { ?>
            <tr>
                <td colspan="5">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>