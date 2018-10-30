<table class="table">
    <thead>
        <tr>
            <th>Currency</th>
            <th>Begin Balance</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>End Balance</th>
            <th>Subject</th>
            <th>Update Time</th>
            <th>Remark</th>
        </tr>
    </thead>
    <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $item) { ?>
                <tr>
                    <td><?php echo $item['currency'];?></td>
                    <td><?php echo ncPriceFormat($item['begin_balance']);?></td>
                    <td><?php echo ncPriceFormat($item['cedit']);?></td>
                    <td><?php echo ncPriceFormat($item['debit']);?></td>
                    <td><?php echo ncPriceFormat($item['end_balance']);?></td>
                    <td><?php echo $item['subject'];?></td>
                    <td><?php echo timeFormat($item['update_time']);?></td>
                    <td><?php echo $item['remark'];?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr><td colspan="7">Null</td></tr>
        <?php } ?>
    </tbody>
</table>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
