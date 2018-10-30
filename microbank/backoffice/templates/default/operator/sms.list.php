<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><input type="checkbox" id="select_all"></td>
            <td><?php echo 'Task Type';?></td>
            <td><?php echo 'Phone Number';?></td>
<!--            <td>--><?php //echo 'Content';?><!--</td>-->
            <td><?php echo 'State';?></td>
            <td><?php echo 'Handler';?></td>
            <td><?php echo 'Handle Time';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr uid="<?php echo $row['uid']?>">
                <td>
                    <?php if($row['task_state'] == smsTaskState::SEND_FAILED){?>
                        <input type="checkbox" name="uid" value="<?php echo $row['uid']?>">
                    <?php }?>
                </td>
                <td>
                    <?php echo $row['task_type'] ?>
                </td>
                <td>
                    <?php echo $row['phone_id'] ?>
                </td>
<!--                <td class="content">-->
<!--                    --><?php //echo $row['content'] ?>
<!--                </td>-->
                <td class="task_state">
                    <?php echo $lang['task_state_' . $row['task_state']] ?>
                </td>
                <td>
                    <?php echo $row['handler'] ?>
                </td>
                <td>
                    <?php echo timeFormat($row['handle_time']) ?>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']) ?>
                </td>
                <td class="function">
                    <?php if($row['task_state'] == smsTaskState::SEND_FAILED){?>
                        <button class="btn btn-default resend" style="padding: 5px 10px" onclick="sms_resend(<?php echo $row['uid']?>)">
                            <i class="fa fa-send"></i>
                            Resend
                        </button>
                        <button class="btn btn-default resending" style="padding: 5px 10px;display: none">
                            <i class="fa fa-refresh fa-spin"></i>
                            Resending
                        </button>
                        <button class="btn btn-default resend_success" style="padding: 5px 10px;display: none">
                            <i class="fa fa-check"></i>
                            Resend Success
                        </button>
                    <?php }?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

