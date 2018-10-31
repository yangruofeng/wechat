<?php
$list = $data['data'];
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr class="table-header">
        <td><?php echo 'Client Type';?></td>
        <td><?php echo 'Login Ip';?></td>
        <td><?php echo 'Login Area';?></td>
        <td><?php echo 'Login Time';?></td>
        <td><?php echo 'Logout Time';?></td>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php if ($list) { ?>
        <?php foreach($list as $row){?>
            <tr>
                <td>
                    <?php echo ucwords($row['client_type']) ?><br/>
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
    <?php } else { ?>
        <tr>
            <td colspan="5"> <?php include(template(":widget/no_record")); ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<?php if (count($list) > 0 || $data['pageNumber'] != 1) { ?>
    <?php include_once(template("widget/inc_content_pager")); ?>
<?php } ?>

