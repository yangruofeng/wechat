<div>
    <table class="table table-bordered table-hover">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Book Code';?></td>
            <td><?php echo 'Book Name';?></td>
            <td><?php echo 'Book Type';?></td>
            <td><?php echo 'GUID';?></td>
            <td><?php echo 'Balance';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($data['data'] as $key => $row) {?>
            <tr>
                <td>
                    <?php echo $row['book_code']; ?>
                </td>
                <td>
                    <?php echo $row['book_name']; ?>
                </td>
                <td>
                    <?php echo $row['book_type']; ?>
                </td>
                <td>
                    <?php echo $row['obj_guid']; ?>
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
