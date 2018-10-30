
<div>
    <table class="table">
        <thead>
        <tr style="background-color: rgb(246, 246, 246)">
            <td>Amount</td>
            <td>Exchange Rate</td>
            <td>Exchange Amount</td>
            <td>Operator</td>
            <td>Time</td>
        </tr>
        </thead>
        <tbody>
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo ncPriceFormat($row['amount']) ?>
                    </td>
                    <td>
                        <?php echo $row['exchange_rate'] ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['exchange_amount']) ?>
                    </td>
                    <td>
                        <?php echo $row['handler_name'] ?>
                    </td>
                    <td>
                        <?php echo $row['update_time'] ?>
                    </td>
                </tr>
            <?php }?>
        <?php }else{ ?>
            <tr>
                <td colspan="4">No Record</td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

