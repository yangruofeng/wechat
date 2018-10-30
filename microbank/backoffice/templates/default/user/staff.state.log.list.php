<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'No.'; ?></td>
            <td><?php echo 'Original State'; ?></td>
            <td><?php echo 'Current State'; ?></td>
            <td><?php echo 'Remark'; ?></td>
            <td><?php echo 'Operator'; ?></td>
            <td><?php echo 'Time'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php $i = 0;
        foreach ($data['data'] as $row) {
            ++$i ?>
            <tr>
                <td>
                    <?php echo $i; ?><br/>
                </td>
                <td>
                    <?php echo $lang['staff_status_' . $row['original_state']]; ?><br/>
                </td>
                <td>
                    <?php echo $lang['staff_status_' . $row['current_state']]; ?><br/>
                </td>
                <td>
                    <?php echo $row['remark']; ?><br/>
                </td>
                <td>
                    <?php echo $row['creator_name']; ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']); ?><br/>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

