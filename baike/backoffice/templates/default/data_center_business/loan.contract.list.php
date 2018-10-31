<?php
$list = $data['list'];
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr class="table-header t1">
        <td class="number">CID</td>
        <td class="number">Member</td>
        <td class="number">Contract SN</td>
        <td class="number">Product</td>
        <td class="number">Currency</td>
        <td class="number">Apply Amount</td>
        <td class="number">Admin Fee</td>
        <td class="number">Loan Fee</td>
        <td class="number">Time</td>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php if(count($list)>0){?>
        <?php foreach ($list as $v) { ?>
            <tr>
                <td class="number"><?php echo $v['client_obj_guid']; ?></td>
                <td class="number"><?php echo $v['login_code']; ?></td>
                <td class="number"><?php echo $v['contract_sn']; ?></td>
                <td class="number"><?php echo $v['alias']; ?></td>
                <td class="number"><?php echo $v['currency']; ?></td>
                <td class="number"><?php echo ncPriceFormat($v['apply_amount']); ?></td>
                <td class="number"><?php echo ncPriceFormat($v['admin_fee']); ?></td>
                <td class="number"><?php echo ncPriceFormat($v['loan_fee']); ?></td>
                <td class="number"><?php echo timeFormat($v['update_time']); ?></td>
            </tr>

        <?php } ?>
    <?php }else{ ?>
        <tr>
            <td colspan="9">
                <div>
                    <?php include(template(":widget/no_record")); ?>
                </div>
            </td>
        </tr>
    <?php } ?>



    </tbody>
</table>
<?php include_once(template("widget/inc_content_pager")); ?>

