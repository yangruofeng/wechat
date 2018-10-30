
    <div>
        <table class="table">
            <thead>
            <tr class="table-header">
                <td>ID</td>
                <td>Transfer Time</td>
                <td>Currency</td>
                <td>Amount</td>
                <td>From Teller</td>
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
                        <?php echo $row['sender_handler_name'] ?>
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
    <?php } else{ ?>
        <td colspan="5">No Record</td>
    <?php }?>
            </tbody>
        </table>
    </div>
<?php include_once(template("widget/inc_content_pager")); ?>