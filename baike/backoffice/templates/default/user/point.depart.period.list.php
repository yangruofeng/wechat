<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Period';?></td>
            <td><?php echo 'Start Date';?></td>
            <td><?php echo 'End Date';?></td>
            <td><?php echo 'Status';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
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
                    <?php echo $row['depart_status'] == 0 ? ($row['start_date'] <= Now() ? 'Processing' : 'New') : ($row['status'] == 10 ? 'Processing' : 'Closed')  ?>
                </td>
                <td>
                    <?php if ($row['depart_status'] == 0 && $row['start_date'] <= Now()) { ?>
                        <a class="btn btn-default" style="padding: 5px 10px;min-width: 80px" title="<?php echo $lang['common_edit']; ?>" href="<?php echo getUrl('user', 'handleDepartPeriod', array('uid' => $row['uuid']), false, BACK_OFFICE_SITE_URL) ?>">
                            <i class="fa fa-edit"></i>
                            Handle
                        </a>
                    <?php } ?>
                    <?php if ($row['depart_status'] == 100) { ?>
                        <a class="btn btn-default" style="padding: 5px 10px;min-width: 80px" title="<?php echo 'Report'; ?>" href="<?php echo getUrl('user', 'handleDepartPeriodReport', array('uid' => $row['uuid']), false, BACK_OFFICE_SITE_URL) ?>">
                            <i class="fa fa-bar-chart"></i>
                            Report
                        </a>
                    <?php } ?>
                    <?php if ($row['depart_status'] == 100 && $row['status'] != 100) { ?>
                        <a class="btn btn-default" style="padding: 5px 10px;min-width: 80px" title="<?php echo 'Active'; ?>" onclick="active_click(<?php echo $row['uuid']?>)">
                            <i class="fa fa-long-arrow-up"></i>
                            Active
                        </a>
                    <?php } ?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

