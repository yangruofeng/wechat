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
            <td><?php echo 'Device Name'; ?></td>
            <?php if ($data['verify_state'] != commonApproveStateEnum::CREATE) { ?>
                <td><?php echo 'Operator Name'; ?></td>
            <?php } ?>
            <?php if (!in_array($data['verify_state'], array(commonApproveStateEnum::CREATE))) { ?>
                <td><?php echo 'Operater Time'; ?></td>
            <?php } else { ?>
                <td><?php echo 'Create Time'; ?></td>
            <?php } ?>
            <?php if (!in_array($data['verify_state'], array(commonApproveStateEnum::CREATE))) { ?>
                <td><?php echo 'Remark'; ?></td>
            <?php } ?>
            <?php if (in_array($data['verify_state'], array(commonApproveStateEnum::CREATE))) { ?>
                <td><?php echo 'Function'; ?></td>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php if ($row['member_image']) { ?>
                            <a href="<?php echo getImageUrl($row['member_image']) ?>">
                                <img class="avatar-icon" src="<?php echo getImageUrl($row['member_image'],imageThumbVersion::AVATAR) ?>">
                            </a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <td>
                        <?php echo $row['contact_phone'] ?>
                    </td>
                    <td>
                        <?php echo $row['device_name'];?>
                    </td>
                    <?php if ($row['state'] != commonApproveStateEnum::CREATE) { ?>
                        <td>
                            <?php echo $row['operator_name']; ?>
                        </td>
                    <?php } ?>
                    
                    <?php if (!in_array($data['verify_state'], array(commonApproveStateEnum::CREATE))) { ?>
                        <td>
                            <?php echo timeFormat($row['update_time']); ?>
                        </td>
                    <?php } else { ?>
                        <td>
                            <?php echo timeFormat($row['create_time']); ?>
                        </td>
                    <?php } ?>
                    <?php if (!in_array($data['verify_state'], array(commonApproveStateEnum::CREATE))) { ?>
                        <td>
                            <?php echo $row['remark'] ?>
                        </td>
                    <?php } ?>
                    <?php if (in_array($data['verify_state'], array(commonApproveStateEnum::CREATE))) { ?>
                        <td>
                        <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('operator', 'getTaskOfDeviceApply', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                            <span><i class="fa fa-vcard-o"></i><?php echo $row['state'] == commonApproveStateEnum::CREATE ? 'Get' : 'Handle';?></span>
                        </a>
                        </td>
                    <?php } ?>
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
