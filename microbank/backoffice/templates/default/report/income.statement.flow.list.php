<table class="table table-hover table-bordered">
    <thead>
        <tr>
            <th>Time</th>
            <th>Account Name</th>
            <th>Currency</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Amount</th>
            <th>End Balance</th>
            <th>Subject</th>
        </tr>
    </thead>
    <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $item) { ?>
                <tr>
                    <td><?php echo $item['create_time'];?></td>
                    <td><?php echo $item['account_name'];?></td>
                    <td><?php echo $item['currency'];?></td>
                    <td><?php echo $item['credit'];?></td>
                    <td><?php echo $item['debit'];?></td>
                    <td><?php echo $item['amount'];?></td>
                    <td><?php echo ncPriceFormat($item['end_balance']);?></td>
                    <td><?php echo $item['subject'];?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr><td colspan="8">Null</td></tr>
        <?php } ?>
    </tbody>
</table>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
