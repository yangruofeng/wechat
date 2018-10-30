<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'User Code';?></td>
            <td><?php echo 'User Name';?></td>
            <td><?php echo 'Branch';?></td>
            <td><?php echo 'Position';?></td>
            <td><?php echo 'Status';?></td>
            <td><?php echo 'Balance';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['user_code'] ?><br/>
                </td>
                <td>
                    <?php echo $row['user_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['branch_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['user_position'] ?><br/>
                </td>

                <td>
                    <?php echo $row['status'] == 1 ? 'Valid' : 'Invalid'; ?><br/>
                </td>
                <td>
                    <?php echo join(",", array_map(function($v) {return $v['balance'].$v['currency'];}, array_filter($row['accounts'], function($v) {return $v['balance'] != "0";}) )) ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <a title="" class="custom-btn custom-btn-secondary" href="#" onclick="adjust_account('<?php echo $row['uid']?>', <?php echo "{". join(",", array_map(function($v) {return "'" . $v['currency'] . "': " .$v['balance'];}, array_filter($row['accounts'], function($v) {return $v['balance'] != "0";}))) . "}"; ?>)">
                            <span><i class="fa fa-edit"></i>Adjust</span>
                        </a>
                    </div>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

