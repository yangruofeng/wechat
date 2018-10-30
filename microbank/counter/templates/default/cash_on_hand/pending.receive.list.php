
    <div>
        <table class="table">
            <thead>
            <tr class="table-header">
                <td>ID</td>
                <td>Transfer Time</td>
                <td>Currency</td>
                <td>Amount</td>
                <td>From Vault</td>
                <td>From CO</td>
                <?php if ($data['state'] == 'pending') { ?>
                    <td>Function</td>
                <?php } ?>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php if ($data['data']) { ?>
                <?php foreach ($data['data'] as $row) { ?>
                    <tr>
                        <td>
                            <?php echo $row['uid'] ?>
                        </td>
                        <td>
                            <?php echo $row['create_time'] ?>
                        </td>
                        <td>
                            <?php echo $row['currency'] ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($row['amount']) ?>
                        </td>
                        <td>
                            <?php if($row['sender_obj_type']==3){?>
                                <?php echo $row['sender_handler_name'] ?>
                            <?php }?>
                        </td>
                        <td>
                            <?php if($row['sender_obj_type']==0){?>
                                <?php echo $row['sender_handler_name'] ?>
                            <?php }?>
                        </td>
                        <?php if ($row['is_outstanding'] == '1') { ?>
                            <td>
                                <a type="button" class="btn btn-default" onclick="receive(<?php echo $row['uid'] ?>)">
                                    <i class="fa fa-check"></i>
                                    <?php echo 'Receive'; ?>
                                </a>
                                <a type="button" class="btn btn-default" onclick="reject(<?php echo $row['uid'] ?>)">
                                    <i class="fa fa-remove"></i>
                                    <?php echo 'Reject'; ?>
                                </a>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6">No Record</td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
<?php include_once(template("widget/inc_content_pager")); ?>