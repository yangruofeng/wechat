<div>
    <table class="table">
        <thead>
            <tr class="table-header">
                <td><?php echo 'CID'; ?></td>
                <td><?php echo 'Account'; ?></td>
                <td><?php echo 'Phone'; ?></td>
                <td><?php echo 'Work Type'; ?></td>
                <td><?php echo 'Total Limits'; ?></td>
                <td><?php echo 'Total Liabilities'; ?></td>
                <td><?php echo 'Creator Name'; ?></td>
                <td><?php echo 'Check Time'; ?></td>
                <td><?php echo 'Function'; ?></td>
            </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['list']) { ?>
            <?php foreach ($data['list'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['obj_guid'] ?>
                    </td>
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
                        <?php echo ncPriceFormat($row['total_limits']); ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['total_liabilities']); ?>
                    </td>
                    <td>
                        <?php echo $row['creator_name'];?>
                    </td>
                    <td>
                        <?php echo dateFormat($row['create_time']); ?>
                    </td>
                    

                    <td>
                        <div class="custom-btn-group">
                            <a class="custom-btn custom-btn-secondary"
                                href="<?php echo getUrl('operator', 'getClientCbcHistoryDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                <span><i class="fa fa-vcard-o"></i>Detail</span>
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