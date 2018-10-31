<style>
    .avatar-icon {
        width: 50px;
        height: 50px;
    }
</style>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Icon'; ?></td>
            <td><?php echo 'Account'; ?></td>
            <td><?php echo 'Phone'; ?></td>
            <td><?php echo 'Work Type'; ?></td>
<!--            <td>--><?php //echo 'State'; ?><!--</td>-->
            <td><?php echo 'Operator'; ?></td>
            <td><?php echo 'Remark'; ?></td>
            <td><?php echo 'Check Time'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php if ($row['member_icon']) { ?>
                            <a href="<?php echo getImageUrl($row['member_icon']) ?>">
                                <img class="avatar-icon" src="<?php echo getImageUrl($row['member_icon']) ?>">
                            </a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <td>
                        <?php echo $row['phone_id'] ?>
                    </td>
<!--                    <td>-->
<!--                        --><?php //echo $lang['work_type_' . $row['work_type']];?>
<!--                    </td>-->
                    <td>
                        <?php echo $lang['operator_task_state_' . $row['operate_state']] ?>
                    </td>
                    <td>
                        <?php echo $row['operator_name'] ?>
                    </td>
                    <td>
                        <?php echo $row['operate_remark'] ?>
                    </td>

                    <td>
                        <?php echo timeFormat($row['operate_time']); ?>
                    </td>
                    <td>
                        <div class="custom-btn-group">
                            <a class="custom-btn custom-btn-secondary" href="#" onclick="resumeClient(<?php echo $row['uid']?>)">
                                <span><i class="fa fa-reply"></i>Resume</span>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="20">
                    No Record
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
