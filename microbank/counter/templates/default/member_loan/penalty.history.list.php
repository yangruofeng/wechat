<div>
    <table class="table">
        <thead>
        <tr style="background-color: rgb(246, 246, 246)">
            <td>Member Code</td>
            <td>USD-Amount</td>
            <td>KHR-Amount</td>
            <td>Operate Time</td>
        </tr>
        </thead>
        <tbody>
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['USD']['amount']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['KHR']['amount']) ?>
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

