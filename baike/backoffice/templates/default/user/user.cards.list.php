<table class="table table-hover table-bordered">
    <thead>
    <tr class="table-header">
        <td><?php echo 'Card No';?></td>
        <td><?php echo 'Expire Time';?></td>
        <td><?php echo 'Bind Time';?></td>
        <td><?php echo 'Action';?></td>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php foreach($data['DATA'] as $row){?>
        <tr>
            <td>
                <?php echo $row['card_no'] ?><br/>
            </td>
            <td>
                <?php echo dateFormat(date("Y-m-d",$row['expire_time'])) ?><br/>
            </td>
            <td>
                <?php echo timeFormat($row['create_time']) ?><br/>
            </td>
            <td>
                <div class="custom-btn-group">
                    <a title="" class="custom-btn custom-btn-secondary" href="#" onclick="unbind_card('<?php echo $row['uid']?>')">
                        <span><i class="fa fa-trash"></i>Delete</span>
                    </a>
                </div>
            </td>
        </tr>
    <?php }?>
    </tbody>
</table>