<style>
    .avatar-icon {
        width: 50px;
        height: 50px;
    }
</style>
<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Icon'; ?></td>
            <td><?php echo 'CID'; ?></td>
            <td><?php echo 'Account'; ?></td>
            <?php foreach ($data['verify_type'] as $key => $type) { ?>
                <td><?php echo $type; ?></td>
            <?php } ?>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { $credit_info = memberClass::getCreditBalance($row['member_id']);?>
                <tr>
                    <td>
                        <?php if ($row['member_icon']) { ?>
                            <a target="_blank" href="<?php echo getImageUrl($row['member_icon']) ?>">
                                <img class="avatar-icon" src="<?php echo getImageUrl($row['member_icon'],imageThumbVersion::AVATAR) ?>">
                            </a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $row['obj_guid'] ?>
                    </td>
                    <td>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <?php foreach ($data['verify_type'] as $key => $type) { ?>
                        <td>
                            <?php if ($row['cert_list'][$key]) { ?>
                                <span style="color: #008000;padding-left: 10px"><i class="fa fa-check"></i></span>
                            <?php } else { ?>
                                <span style="color: red;padding-left: 10px"><i class="fa fa-close"></i></span>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td>
                        <div class="custom-btn-group">
                            <a class="custom-btn custom-btn-secondary"
                                href="<?php echo getUrl('operator', 'pendingVerifyDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                <span><i class="fa fa-vcard-o"></i><?php echo 'Detail';?></span>
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
