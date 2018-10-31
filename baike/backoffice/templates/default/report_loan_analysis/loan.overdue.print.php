


    <?php foreach( $output['data'] as $group){ ?>
        <div>
            <table class="table table-bordered">
                <caption class="text-center"><?php echo $group['title']; ?></caption>
                <tr class="table-header">
                    <td>No</td>
                    <td>Name</td>
                    <td>Account</td>
                    <td>Balance</td>
                    <td>Disb Amt</td>
                    <td>Day Late</td>
                </tr>
                <?php $start = 0; $total_balance=0;$total_amt=0; ?>
                <?php foreach( $group['list'] as $item ){ ?>
                    <tr>
                        <td><?php echo $start+1; ?></td>
                        <td><?php echo $item['display_name'].' / '.$item['kh_display_name']; ?></td>
                        <td><?php echo $item['contract_sn']; ?></td>
                        <td><?php echo ncPriceFormat($item['principal_balance']); ?></td>
                        <td><?php echo ncPriceFormat($item['apply_amount']); ?></td>
                        <td><?php echo $item['overdue_day']; ?></td>
                    </tr>
                <?php $start++;$total_balance+=$item['principal_balance'];$total_amt+=$item['apply_amount']; } ?>
                <tr>
                    <td></td>
                    <td class="text-right">
                        <b>Total:</b>
                    </td>
                    <td>
                        <?php echo $start; ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($total_balance); ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($total_amt); ?>
                    </td>
                    <td></td>
                </tr>

            </table>
        </div>
    <?php } ?>



