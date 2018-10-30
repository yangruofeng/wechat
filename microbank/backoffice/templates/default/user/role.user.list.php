<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'User Code';?></td>
            <td><?php echo 'User Name';?></td>
            <td><?php echo 'Branch';?></td>
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
                    <?php echo $row['branch_name'] . ' ' . $row['depart_name'] ?><br/>
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

