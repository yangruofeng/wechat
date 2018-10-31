<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Member Image';?></td>
            <td><?php echo 'CID'; ?></td>
            <td><?php echo 'Login Account';?></td>
            <td>Client Name</td>
            <td><?php echo 'Phone';?></td>
            <td><?php echo 'Credit';?></td>
            <td><?php echo 'Credit Balance';?></td>
            <td><?php echo 'CO';?></td>
            <td><?php echo 'Work Type';?></td>
            <td><?php echo 'Register Time';?></td>
            <?php if ($data['member_state_cancel']) { ?>
                <td><?php echo 'Cancel Remark';?></td>
            <?php } ?>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){ ?>
            <tr>
                <td>
                    <?php if ($row['member_icon']) { ?>
                        <a target="_blank" href="<?php echo getImageUrl($row['member_icon']) ?>">
                            <img class="avatar-icon" src="<?php echo getImageUrl($row['member_icon']) ?>">
                        </a>
                    <?php } ?>
                </td>
                <td>
                    <?php echo $row['obj_guid'] ?>
                </td>
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
                    <?php echo $row['expire_time'] > Now() ? ncAmountFormat($row['credit']) : '0.00'; ?>
                </td>
                <td>
                    <?php echo $row['expire_time'] > Now() ? ncAmountFormat($row['credit_balance']) : '0.00'; ?>
                </td>
                <td>
                    <?php echo $row['co_count']; ?>
                </td>
                <td>
                    <?php echo $lang['work_type_' . $row['work_type']];?>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']); ?>
                </td>
                <?php if ($data['member_state_cancel']) { ?>
                    <td><?php echo $row['change_state_remark'];?></td>
                <?php } ?>
                <td>
                    <div class="custom-btn-group">
                        <a class="custom-btn custom-btn-secondary"
                           href="<?php echo getUrl('web_credit', 'creditClient', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                            <span><i class="fa fa-vcard-o"></i>Detail</span>
                        </a>
                    </div>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
