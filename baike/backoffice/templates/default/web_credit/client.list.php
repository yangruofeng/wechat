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
            <td><?php echo 'Client Image'; ?></td>
            <td>CID</td>
            <td><?php echo 'Login Account'; ?></td>
            <td>Client Name</td>
            <td><?php echo 'Phone'; ?></td>
            <td><?php echo 'Credit';?></td>
            <td><?php echo 'Branch';?></td>
            <td><?php echo 'Work Type'; ?></td>
            <td><?php echo 'Register Time'; ?></td>
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
                    <td><?php echo $row['obj_guid']; ?></td>
                    <td>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <td>
                        <?php echo $row['display_name'].' / '.$row['kh_display_name']; ?>
                    </td>
                    <td>
                        <?php echo $row['phone_id'] ?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($row['credit']); ?>
                    </td>
                    <td>
                        <?php echo $row['branch_name']; ?>
                    </td>
                    <td>
                        <?php echo $lang['work_type_' . $row['work_type']];?>
                    </td>
                    <td>
                        <?php echo timeFormat($row['create_time']); ?>
                    </td>
                    <td>
                        <div class="custom-btn-group">
                            <a class="custom-btn custom-btn-secondary"
                                href="<?php echo getUrl('web_credit', 'creditClient', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                <span><i class="fa fa-vcard-o"></i><?php echo 'Detail';?></span>
                            </a>
                        </div>
                        <div class="custom-btn-group" style="margin-left: 5px">
                            <a class="custom-btn custom-btn-secondary"
                               href="<?php echo getUrl('web_credit', 'pushMessageToUser', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                <span><i class="fa fa-envelope-o"></i><?php echo 'Push';?></span>
                            </a>
                        </div>
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
