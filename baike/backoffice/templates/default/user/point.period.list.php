<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Period';?></td>
            <td><?php echo 'Start Date';?></td>
            <td><?php echo 'End Date';?></td>
            <td><?php echo 'Close Detail';?></td>
            <td><?php echo 'Status';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr uid="<?php echo $row['uid']?>"
                period="<?php echo $row['period'] ?>"
                start_date="<?php echo $row['start_date'] ?>"
                end_date="<?php echo $row['end_date'] ?>"
                status="<?php echo $row['status'] ?>"
                is_new="<?php echo $row['is_new']?>"
                >
                <td>
                    <?php echo $row['period'] ?>
                </td>
                <td>
                    <?php echo $row['start_date'] ?>
                </td>
                <td>
                    <?php echo $row['end_date'] ?>
                </td>
                <td>
                    <span title="<?php echo implode(', ',$row['closed'])?>"><span>Closed: </span><span style="font-weight: 500"><?php echo count($row['closed'])?></span></span>
                    <span title="<?php echo implode(', ',$row['not_close'])?>"><span style="margin-left: 10px">Not Close: </span><span style="font-weight: 500"><?php echo count($row['not_close'])?></span></span>
                </td>
                <td>
                    <?php echo $row['status'] == 0 ? ($row['start_date'] <= Now() ? 'Processing' : 'New') : 'Closed'  ?>
                </td>
                <td>
                    <?php if ($row['status'] == 0 && $row['start_date'] > Now()) { ?>
                        <button class="btn btn-default" style="padding: 5px 10px;min-width: 80px"
                                title="<?php echo $lang['common_edit']; ?>" href="#" onclick="edit_period(this)">
                            <i class="fa fa-edit"></i>
                            Edit
                        </button>
                    <?php } ?>
                    <?php if ($row['is_new'] && $row['start_date'] > Now()) { ?>
                        <a class="btn btn-default" style="padding: 5px 10px;min-width: 80px"
                           title="<?php echo $lang['common_edit']; ?>"
                           href="<?php echo getUrl('user', 'deletePeriod', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                            <i class="fa fa-trash-o"></i>
                            Delete
                        </a>
                    <?php } ?>
                    <?php if ($row['is_close']) { ?>
                        <a class="btn btn-default" style="padding: 5px 10px;min-width: 80px"
                           title="<?php echo 'Close'; ?>"
                           href="<?php echo getUrl('user', 'closePeriod', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                            <i class="fa fa-remove"></i>
                            Close
                        </a>
                    <?php } ?>
                    <?php if ($row['status'] == 100) { ?>
                        <a class="btn btn-default" style="padding: 5px 10px;min-width: 80px"
                           title="<?php echo 'Report'; ?>"
                           href="<?php echo getUrl('user', 'periodReport', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                            <i class="fa fa-bar-chart"></i>
                            Report
                        </a>
                    <?php } ?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

