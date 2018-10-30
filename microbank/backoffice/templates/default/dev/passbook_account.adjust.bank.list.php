<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Bank Name';?></td>
            <td><?php echo 'Account No';?></td>
            <td><?php echo 'Account Name';?></td>
            <td><?php echo 'Account Phone';?></td>
            <td><?php echo 'Currency';?></td>
            <td><?php echo 'Branch Belong';?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Balance';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($data['data'] as $key => $row) {?>
            <tr>
                <td>
                    <?php echo $row['bank_name']; ?>
                </td>
                <td>
                    <?php echo $row['bank_account_no']; ?>
                </td>
                <td>
                    <?php echo $row['bank_account_name']; ?>
                </td>
                <td>
                    <?php echo $row['bank_account_phone']; ?>
                </td>
                <td>
                    <?php echo $row['currency']; ?>
                </td>
                <td>
                    <?php echo $row['branch_name']; ?>
                </td>
                <td>
                    <?php echo $row['account_state'] == 1 ? 'Valid' : 'Invalid'; ?>
                </td>
                <td>
                    <?php echo join(",", array_map(function($v) {return $v['balance'].$v['currency'];}, array_filter($row['accounts'], function($v) {return $v['balance'] != "0";}) )) ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <a title="" class="custom-btn custom-btn-secondary" href="#" onclick="adjust_account('<?php echo $row['uid']?>', '<?php echo $row['currency']?>', <?php echo "{". join(",", array_map(function($v) {return "'" . $v['currency'] . "': " .$v['balance'];}, $row['accounts'])) . "}"; ?>)">
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