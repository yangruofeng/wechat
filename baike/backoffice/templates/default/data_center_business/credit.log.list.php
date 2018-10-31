<?php
$list = $data['list'];
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr class="table-header t1">
        <td class="number">CID</td>
        <td class="number">Member</td>
        <td class="number">Event Type</td>
        <td class="number">Amount</td>
        <td class="number">Begin Credit</td>
        <td class="number">After Credit</td>
        <td class="number">Time</td>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php if(count($list)>0){?>
        <?php foreach ($list as $v) { ?>
            <tr>
                <td class="number"><?php echo $v['obj_guid']; ?></td>
                <td class="number"><?php echo $v['login_code']; ?></td>
                <td class="number"><?php echo $v['event_type']; ?></td>
                <td class="number"><?php echo ncPriceFormat($v['amount']); ?></td>
                <td class="number"><?php echo ncPriceFormat($v['begin_credit']); ?></td>
                <td class="number"><?php echo ncPriceFormat($v['after_credit']); ?></td>
                <td class="number"><?php echo timeFormat($v['create_time']); ?></td>
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

