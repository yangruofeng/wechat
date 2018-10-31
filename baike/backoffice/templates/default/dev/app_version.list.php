<div>
    <table class="table table-striped table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'App Name';?></td>
            <td><?php echo 'Version';?></td>
            <td><?php echo 'Remark';?></td>
            <td><?php echo 'Must Update';?></td>
            <td><?php echo 'Create Time';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['app_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['version'] ?><br/>
                </td>
                <td>
                    <?php echo $row['remark'] ?><br/>
                </td>
                <td>
                    <?php echo $row['is_required'] == 1 ? 'Yes' : 'No'; ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']) ?><br/>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

