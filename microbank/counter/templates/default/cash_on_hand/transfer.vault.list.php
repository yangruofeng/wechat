<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Transfer Time</td>
            <td>Currency</td>
            <td>Amount</td>
            <td>Receiver Name</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
        <?php foreach ($data['data'] as $row) { ?>
            <tr>
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
                    <?php echo $row['receiver_handler_name'] ?>
                </td>
            </tr>
        <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="4">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

