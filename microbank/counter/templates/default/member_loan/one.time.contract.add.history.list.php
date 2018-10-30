
<div>
    <table class="table">
        <thead>
        <tr style="background-color: rgb(246, 246, 246)" >
            <td>Contract Sn</td>
            <td>Product Name</td>
            <td>Currency</td>
            <td>Amount</td>
            <td>Update Time</td>
        </tr>
        </thead>
        <tbody>
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <a href="<?php echo getUrl('member_loan', 'contractIndex', array('contract_id'=>$row['contract_id']), false, ENTRY_COUNTER_SITE_URL) ?>">
                            <?php echo $row['contract_sn'] ?>
                        </a>
                    </td>
                    <td>
                        <?php echo $row['alias'] ?>
                    </td>
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['apply_amount']) ?>
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

