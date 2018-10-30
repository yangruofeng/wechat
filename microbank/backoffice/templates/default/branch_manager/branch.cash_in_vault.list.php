<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>No.</td>
            <td>Transfer Time</td>
            <td>Trade Type</td>
            <td>Currency</td>
            <td>Amount</td>
            <td>From/To</td>
            <td>State</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php $i = 0;foreach ($data['data'] as $row) { ++$i?>
                <tr>
                    <td>
                        <?php echo $i ?>
                    </td>
                    <td>
                        <?php echo $row['create_time'] ?>
                    </td>
                    <td>
                        <?php echo ucwords(str_replace('_', ' ', $row['biz_code'])) ?>
                    </td>
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>
                    <td>
                        <?php echo ($data['branch_guid'] == $row['receiver_obj_guid'] ? '+' : '-') . ncPriceFormat($row['amount']) ?>
                    </td>
                    <td>
                        <?php echo $data['branch_guid'] == $row['receiver_obj_guid'] ? $row['sender_handler_name'] : $row['receiver_handler_name']; ?>
                    </td>
                    <td>
                        <?php if ($row['state'] == 100) { ?>
                            <span>Complete</span>
                        <?php } else { ?>
                            <span>Outstanding</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <td colspan="5">No Record</td>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>