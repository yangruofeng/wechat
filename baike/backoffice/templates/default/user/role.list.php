<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Role Name';?></td>
            <td><?php echo 'Auth Group(Back Office/Counter)';?></td>
            <td><?php echo 'Status';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <a href="<?php echo getUrl('user', 'showRoleDetail', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                    <?php echo $row['role_name'] ?>
                    </a>
                    <br/>
                </td>
                <td>
                    <?php $i = 0;foreach ($row['auth_group_back_office'] as $group) { ++$i?>
                        <?php echo ($i == 1 ? '' : '/ ') . L('auth_' . strtolower($group)) ?>
                    <?php } ?>
                    <?php $i = 0;foreach ($row['auth_group_counter'] as $group) { ++$i?>
                        <?php echo ($i == 1 ? '' : '/ ') . L('auth_counter_' . strtolower($group)) ?>
                    <?php } ?>
                </td>
                <td>
                    <?php echo $row['role_status'] == 1 ? 'Valid' : 'Invalid'; ?><br/>
                </td>
                <td>
                    <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('user', 'editRole', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-edit"></i>
                        Edit
                    </a>
                    <a title="<?php echo $lang['common_delete'];?>" href="<?php echo getUrl('user', 'deleteRole', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" >
                        <i class="fa fa-trash"></i>
                        Delete
                    </a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

