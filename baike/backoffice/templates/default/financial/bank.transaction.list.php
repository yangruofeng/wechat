<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Trading-Type</td>
            <td>Subject</td>
            <td>Amount</td>
            <td>Handler</td>
            <td>Time</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo in_array($row['biz_code'], array(bizCodeEnum::HEADQUARTER_TO_BANK,bizCodeEnum::BRANCH_TO_BANK)) ? 'Deposit' : 'Withdrawal'; ?>
                    </td>
                    <td>
                        <?php echo $lang['subject_' . $row['biz_code']] ?>
                        <?php if ($row['biz_code'] == bizCodeEnum::BRANCH_TO_BANK) {
                            echo $data['branch_list'][$row['sender_obj_guid']]['branch_name'];
                        } elseif ($row['biz_code'] == bizCodeEnum::BANK_TO_BRANCH) {
                            echo $data['branch_list'][$row['receiver_obj_guid']]['branch_name'];
                        } ?>
                    </td>
                    <td>
                        <?php echo $row['sender_obj_guid'] == $object_id ? '-' : '+'?><?php echo ncPriceFormat($row['amount']) ?>
                    </td>
                    <td>
                        <?php echo in_array($row['biz_code'], array(bizCodeEnum::HEADQUARTER_TO_BANK,bizCodeEnum::BRANCH_TO_BANK)) ? $row['sender_handler_name'] : $row['receiver_handler_name']; ?>
                    </td>
                    <td>
                        <?php echo $row['update_time'] ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>