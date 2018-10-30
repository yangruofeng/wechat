<?php $list = $data['data']?>
<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'User Code';?></td>
            <td><?php echo 'User Name';?></td>
            <td><?php echo 'Department Name';?></td>
            <td><?php echo 'Role';?></td>
            <td><?php echo 'Position';?></td>
            <td><?php echo 'Status';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <a href="<?php echo getUrl('user', 'showUserDetail', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['user_code'] ?></a><br/>
                </td>
                <td>
                    <?php echo $row['user_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['depart_name'] ?><br/>
                </td>
                <td>
                    <?php $i = 0;foreach ($row['role_group'] as $role) { ++$i?>
                        <?php echo ($i == 1 ? '' : '/ ') . $role ?>
                    <?php } ?>
                </td>
                <td>
                    <?php echo ucwords(str_replace('_', ' ', $row['user_position'])) ?>
                </td>
                <td>
                    <?php echo $row['user_status'] == 1 ? 'Valid' : 'Invalid'; ?><br/>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

