<div>
    <table class="table">
        <thead>
        <tr style="background-color: rgb(246, 246, 246)">
            <td>Bill Code</td>
            <td>Client Name</td>
            <td>Bank</td>
            <td>Amount</td>
            <td>Currency</td>

            <td>Time</td>
        </tr>
        </thead>
        <tbody>
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['bill_code'] ?>
                    </td>
                    <td>
                        <?php echo $row['display_name']?:$row['login_code']; ?>
                    </td>
                    <td>
                        <?php echo $row['bank_name'] ?>
                    </td>

                    <td>
                        <?php echo ncPriceFormat($row['amount']) ?>
                    </td>
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>

                    <td>
                        <?php echo timeFormat($row['create_time']) ?>
                    </td>
                </tr>
            <?php }?>
        <?php }else{ ?>
            <tr>
                <td colspan="10">No Record</td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

