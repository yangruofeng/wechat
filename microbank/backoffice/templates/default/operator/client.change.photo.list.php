<style>
    .avatar-icon {
        width: 50px;
        height: 50px;
    }
</style>
<?php $approve_state_lang = enum_langClass::getCommonApproveStateLang(); ?>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Old Icon'; ?></td>
            <td><?php echo 'New Icon'; ?></td>
            <td><?php echo 'Account'; ?></td>
            <td><?php echo 'Phone'; ?></td>
            <td><?php echo 'Request Time'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php $old_icon = $row['old_image']?:$row['member_icon']; ?>
                        <?php if ( $old_icon ) { ?>
                            <a href="<?php echo getImageUrl($old_icon) ?>">
                                <img class="avatar-icon" src="<?php echo getImageUrl($old_icon,imageThumbVersion::AVATAR) ?>">
                            </a>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="<?php echo getImageUrl($row['new_image']) ?>">
                            <img class="avatar-icon" src="<?php echo getImageUrl($row['new_image'],imageThumbVersion::AVATAR) ?>">
                        </a>
                    </td>
                    <td>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <td>
                        <?php echo $row['phone_id'] ?>
                    </td>

                    <td>
                        <?php echo timeFormat($row['create_time']); ?>
                    </td>

                    <td>
                        <?php if($row['state']==commonApproveStateEnum::CREATE){?>
                            <div class="custom-btn-group">
                                <a class="custom-btn custom-btn-secondary"
                                   href="<?php echo getUrl('operator', 'getTaskOfChangeClientPhoto', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <span><i class="fa fa-vcard-o"></i><?php echo 'Detail';?></span>
                                </a>
                            </div>
                        <?php }else{?>
                            Operator : <?php echo $row['operator_name']?>
                        <?php }?>

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
