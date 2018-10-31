<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Staff Code';?></td>
            <td><?php echo 'Staff Name';?></td>
            <td><?php echo 'Branch';?></td>
            <td><?php echo 'Role';?></td>
            <td><?php echo 'Position';?></td>
            <td><?php echo 'Mobile Phone';?></td>
            <td><?php echo 'Status';?></td>
            <td>Card</td>
            <td><?php echo 'Function';?></td>
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
                    <?php echo $row['branch_name'] . ' / ' . $row['depart_name'] ?><br/>
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
                    <?php echo $row['mobile_phone']; ?><br/>
                </td>
                <td>
                    <?php echo $row['user_status'] == 1 ? 'Valid' : 'Invalid'; ?><br/>
                </td>
                <td>
                    <?php if($row['ic_card']){?>
                        <span><i class="fa fa-check"></i></span>
                    <?php }else{?>
                    <?php }?>
                </td>
                <td>
                    <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('user', 'editUser', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-edit"></i>
                        Edit
                    </a>
                    <!--
                    <a title="<?php echo $lang['common_ic_card'] ;?>" href="<?php echo getUrl('user', 'editUserCards', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-id-card"></i>
                        IC Cards
                    </a>
                    -->
                    <a title="<?php echo $lang['common_delete'];?>" onclick="delUser(<?php echo $row['uid'];?>)">
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

