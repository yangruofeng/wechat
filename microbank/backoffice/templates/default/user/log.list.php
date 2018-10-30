<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'User Code';?></td>
            <td><?php echo 'Client';?></td>
            <td><?php echo 'Login Ip';?></td>
            <td><?php echo 'Login Area';?></td>
            <td><?php echo 'Login Time';?></td>
            <td><?php echo 'Logout Time';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['user_code'] ?><br/>
                </td>
                <td>
                    <?php echo $row['client_type'] ?><br/>
                </td>
                <td>
                    <?php echo $row['login_ip'] ?><br/>
                </td>
                <td>
                    <?php echo $row['login_area'] ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['login_time']) ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['logout_time']) ?><br/>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

