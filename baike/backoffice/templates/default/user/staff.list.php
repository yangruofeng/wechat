<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'CID';?></td>
            <td><?php echo 'Display Name';?></td>
            <td><?php echo 'Branch';?></td>
            <td><?php echo 'Position';?></td>
            <td><?php echo 'Mobile Phone';?></td>
            <td><?php echo 'Entry Time';?></td>
            <td><?php echo 'Um Account';?></td>
            <td><?php echo 'Status';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['obj_guid'] ?><br/>
                </td>
                <td>
                    <?php echo $row['display_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['branch_name'] . ' / ' . $row['depart_name'] ?><br/>
                </td>
                <td>
                    <?php echo ucwords(str_replace('_', ' ', $row['staff_position'])) ?>
                </td>
                <td>
                    <?php echo $row['mobile_phone']; ?><br/>
                </td>
                <td>
                    <?php echo dateFormat($row['entry_time']); ?><br/>
                </td>
                <td>
                    <?php echo $row['um_account']; ?><br/>
                </td>
                <td>
                    <?php echo $lang['staff_status_' . $row['staff_status']]; ?><br/>
                </td>
                <td>
                    <a title="<?php echo $lang['common_view'] ;?>" href="<?php echo getUrl('user', 'showStaffInfo', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-address-card-o"></i>
                        Detail
                    </a>
                    <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('user', 'editStaff', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-edit"></i>
                        Edit
                    </a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

