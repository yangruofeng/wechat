<table class="table table-bordered table-striped table-hover">
    <tr class="table-header">
        <td>No.</td>
        <td>Branch Name</td>
        <td>CID</td>
        <td>Client Name</td>
        <td>Phone</td>
        <td>Contract SN</td>
        <td>Repay Time</td>
        <td>Repay Amount</td>
        <td>Loan Principal</td>

    </tr>
    <?php foreach($data['data'] as $i=>$item){?>
        <tr>
            <td>
                <?php echo $i+1;?>
            </td>
            <td><?php echo $item['branch_name']; ?></td>
            <td>
                <?php echo $item['client_obj_guid']?>
            </td>
            <td>
                <?php echo $item['display_name']?>
            </td>
            <td>
                <?php echo $item['phone_id']?>
            </td>
            <td>
                <?php echo $item['contract_sn']?>
            </td>
            <td>
                <?php echo $item['create_time']?>
            </td>
            <td>
                <kbd><?php echo ncPriceFormat($item['amount'],0)?></kbd>
            </td>
            <td>
                <?php echo ncPriceFormat($item['apply_amount'],0)?>
            </td>

        </tr>
    <?php }?>
</table>
<?php if (!$is_print) { ?>
<?php include_once(template("widget/inc_content_pager")); ?>
<?php } ?>
