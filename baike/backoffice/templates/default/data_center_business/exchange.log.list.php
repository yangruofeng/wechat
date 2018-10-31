<?php
$list = $data['list'];
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr class="table-header t1">
        <td class="number">Type</td>
        <td class="number">From Currency</td>
        <td class="number">Amount</td>
        <td class="number">To Currency</td>
        <td class="number">Exchange Amount</td>
        <td class="number">Exchange Rate</td>
        <td class="number">Time</td>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php if(count($list)>0){?>
        <?php foreach ($list as $v) { ?>
            <tr>
                <td class="number"><?php echo $v['item']; ?></td>
                <td class="number"><?php echo $v['from_currency']; ?></td>
                <td class="number"><?php echo ncPriceFormat($v['amount']); ?></td>
                <td class="number"><?php echo $v['to_currency']; ?></td>
                <td class="number"><?php echo ncPriceFormat($v['exchange_amount']); ?></td>
                <td class="number"><?php echo ncPriceFormat($v['exchange_rate']); ?></td>
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

