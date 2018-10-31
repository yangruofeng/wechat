<style>
    .avatar-icon {
        width: 50px;
        height: 50px;
    }
</style>
<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td>CID</td>
            <td><?php echo 'Icon'; ?></td>

            <td><?php echo 'Account'; ?></td>
            <td><?php echo 'Phone'; ?></td>
<!--            <td>--><?php //echo 'Email'; ?><!--</td>-->

            <td><?php echo 'Work Type'; ?></td>
            <td><?php echo 'Open Source'; ?></td>
            <td><?php echo 'State'; ?></td>
<!--            --><?php //if ($data['verify_state'] != newMemberCheckStateEnum::CREATE) { ?>
<!--            <td>--><?php //echo 'Operator'; ?><!--</td>-->
<!--            --><?php //}?>
            <?php if (!in_array($data['verify_state'], array(newMemberCheckStateEnum::CREATE, newMemberCheckStateEnum::LOCKED))) { ?>
                <td><?php echo 'Remark'; ?></td>
            <?php } ?>
            <?php if ($data['verify_state'] == newMemberCheckStateEnum::ALLOT) { ?>
                <td><?php echo 'Branch'; ?></td>
            <?php } ?>
            <?php if (!in_array($data['verify_state'], array(newMemberCheckStateEnum::CREATE, newMemberCheckStateEnum::LOCKED))) { ?>
                <td><?php echo 'Check Time'; ?></td>
            <?php } else { ?>
                <td><?php echo 'Create Time'; ?></td>
            <?php } ?>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['obj_guid']?>
                    </td>
                    <td>
                        <?php if ($row['member_icon']) { ?>
                            <a href="<?php echo getImageUrl($row['member_icon']) ?>">
                                <img class="avatar-icon" src="<?php echo getImageUrl($row['member_icon'], imageThumbVersion::AVATAR) ?>">
                            </a>
                        <?php } ?>
                    </td>
<!--                    <td>-->
<!--                        --><?php //echo $row['obj_guid'] ?>
<!--                    </td>-->
                    <td>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <td>
                        <?php echo $row['phone_id'] ?>
                    </td>

                    <td>
                        <?php echo $lang['work_type_' . $row['work_type']];?>
                    </td>

                    <td>
                        <?php echo $lang['source_type_' . $row['open_source']];?>
                    </td>
<!--                    <td>-->
<!--                        --><?php //echo $row['email'] ?>
<!--                    </td>-->
                    <td>
                        <?php echo $lang['operator_task_state_' . $row['operate_state']] ?>
                    </td>
<!--                    --><?php //if ($data['verify_state'] != newMemberCheckStateEnum::CREATE) { ?>
<!--                        <td>-->
<!--                            --><?php //echo $row['operator_name'] ?>
<!--                        </td>-->
<!--                    --><?php //} ?>
                    <?php if (!in_array($data['verify_state'], array(newMemberCheckStateEnum::CREATE, newMemberCheckStateEnum::LOCKED))) { ?>
                        <td>
                            <?php echo $row['operate_remark'] ?>
                        </td>
                    <?php } ?>
                    <?php if ($data['verify_state'] == newMemberCheckStateEnum::ALLOT) { ?>
                        <td>
                            <?php echo $row['branch_name'] ?>
                        </td>
                    <?php } ?>
                    <?php if (!in_array($data['verify_state'], array(newMemberCheckStateEnum::CREATE, newMemberCheckStateEnum::LOCKED))) { ?>
                        <td>
                            <?php echo timeFormat($row['operate_time']); ?>
                        </td>
                    <?php } else { ?>
                        <td>
                            <?php echo timeFormat($row['create_time']); ?>
                        </td>
                    <?php } ?>

                    <td>
                        <?php if($row['operate_state'] == newMemberCheckStateEnum::CREATE){?>
                            <div class="custom-btn-group">
                                <a class="custom-btn custom-btn-secondary"
                                   href="<?php echo getUrl('operator', 'getTaskOfNewClient', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <span><i class="fa fa-vcard-o"></i><?php echo 'Get';?></span>
                                </a>
                            </div>
                        <?php } elseif (($row['operate_state'] == newMemberCheckStateEnum::LOCKED && $row['operator_id'] == $data['current_user'])) { ?>
                            <div class="custom-btn-group">
                                <a class="custom-btn custom-btn-secondary"
                                   href="<?php echo getUrl('operator', 'checkNewClient', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <span><i class="fa fa-vcard-o"></i><?php echo 'Handle';?></span>
                                </a>
                            </div>
                        <?php } else { ?>
                            <?php echo $row['operator_name'] ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="20">
                    Null
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
