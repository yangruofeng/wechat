<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td>Time</td>
            <td>Target-Type</td>
            <td>Currency</td>
            <td>Amount</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) {
            $object_id = $data['object_id']; ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['update_time'] ?>
                    </td>
                    <td>
                        <?php echo $lang['civ_target_type_' . $row['biz_code']] ?>
                    </td>
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>
                    <td>
                        <?php echo $row['sender_obj_guid'] == $object_id ? '-' : '+'?><?php echo ncPriceFormat($row['amount']) ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="4">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>