<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Check Time';?></td>
            <td><?php echo 'Currency';?></td>
            <td><?php echo 'Balance';?></td>
            <td><?php echo 'Check Result';?></td>
            <td><?php echo 'Operator';?></td>
            <td><?php echo 'Create Time';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo timeFormat($row['check_time']) ?><br/>
                </td>
                <td>
                    <?php echo $row['currency'] ?><br/>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['system_balance'], false, $row['currency']) ?><br/>
                </td>
                <td>
                    <?php echo ucwords($row['check_result']) ?><br/>
                </td>
                <td>
                    <?php echo $row['operator_name'] ?><br/>
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

