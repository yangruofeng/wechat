<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Event Code';?></td>
            <td><?php echo 'Description';?></td>
            <td><?php echo 'Min-Point';?></td>
            <td><?php echo 'Max-Point';?></td>
            <td><?php echo 'Status';?></td>
            <?php if($data['is_system'] == 0){?>
            <td><?php echo 'Function';?></td>
            <?php }?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr uid="<?php echo $row['uid']?>"
                event_code="<?php echo $row['event_code'] ?>"
                description="<?php echo $row['description'] ?>"
                min_point="<?php echo $row['min_point'] ?>"
                max_point="<?php echo $row['max_point'] ?>"
                status="<?php echo $row['status'] ?>"
                >
                <td class="event_code">
                    <?php echo $row['event_code'] ?>
                </td>
                <td class="description">
                    <?php echo $row['description'] ?>
                </td>
                <td class="min_point">
                    <?php echo $row['min_point'] ?>
                </td>
                <td class="max_point">
                    <?php echo $row['max_point'] ?>
                </td>
                <td class="status" >
                    <?php echo $row['status'] == 100 ? 'Active' : 'Inactive' ?>
                </td>
                <?php if($data['is_system'] == 0){?>
                <td>
                    <button class="btn btn-default" style="padding: 5px 10px;min-width: 70px" title="<?php echo $lang['common_edit'] ;?>" href="#" onclick="edit_event(this)">
                        <i class="fa fa-edit"></i>
                        Edit
                    </button>
                    <a class="btn btn-default" style="padding: 5px 10px;min-width: 70px" title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('user', 'deleteEvent', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                        <i class="fa fa-trash-o"></i>
                        Delete
                    </a>
                </td>
                <?php }?>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

