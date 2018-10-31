<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Department';?></td>
            <td><?php echo 'Event Code';?></td>
            <td><?php echo 'Event Name';?></td>
            <td><?php echo 'Point';?></td>
            <td><?php echo 'Is Audit';?></td>
            <td><?php echo 'Creator';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr uid="<?php echo $row['uid']?>">
                <td class="depart">
                    <?php echo $row['branch_name'] . ' ' . $row['depart_name'] ?>
                </td>
                <td class="event">
                    <?php echo ucwords($row['event_code']) ?>
                </td>
                <td class="event_name">
                    <?php echo $row['event_name'] ?>
                </td>
                <td class="point">
                    <?php echo $row['creator_id'] ? $row['point'] : '' ?>
                </td>
                <td class="is_audit" is_audit="<?php echo $row['is_audit']?>">
                    <?php if($row['creator_id']){?>
                        <i class="fa fa-<?php echo $row['is_audit'] ? 'check' : 'remove' ?>"></i>
                        <br/>
                    <?php }?>
                </td>
                <td class="creator_name">
                    <?php echo $row['update_name'] ?>
                </td>
                <td class="create_time">
                    <?php echo timeFormat($row['update_time']) ?>
                </td>
                <td>
                    <button class="btn btn-default" style="padding: 5px 10px" title="<?php echo $lang['common_edit'] ;?>" href="#" onclick="edit_event(this)">
                        <i class="fa fa-edit"></i>
                        Edit
                    </button>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

