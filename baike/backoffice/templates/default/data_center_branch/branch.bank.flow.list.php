<?php
$list = $data['data'];
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr class="table-header t1">
            <td class="number">No</td>
            <td class="number">Flow ID</td>
            <td class="number">Time</td>
            <td class="number">Subject</td>
            <td class="number">Currency</td>
            <td class="number">Begin Balance</td>
            <td class="number">Credit</td>
            <td class="number">Debit</td>
            <td class="number">End Balance</td>
            <td class="number">Remark</td>
        </tr>
    </thead>
    <tbody class="table-body">
        <?php foreach( $list as $v ){ ?>
            <tr>
                <td class="number"><?php echo $v['no'];?></td>
                <td class="number"><?php echo $v['flow_id'];?></td>
                <td class="number"><?php echo timeFormat($v['update_time']);?></td>
                <td><?php echo $v['subject'];?></td>
                <td class="number"><?php echo $v['currency'];?></td>
                <td class="currency"><?php echo ncPriceFormat($v['begin_balance']);?></td>
                <td class="currency"><?php echo ncPriceFormat($v['credit']);?></td>
                <td class="currency"><?php echo ncPriceFormat($v['debit']);?></td>
                <td class="currency"><?php echo ncPriceFormat($v['end_balance']);?></td>
                <td><?php echo $v['sys_memo'];?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php if(count($list) > 0){?>
    <?php include_once(template("widget/inc_content_pager")); ?>
<?php }else{?>
    <?php if($data['pageNumber'] != 1){?>
        <?php include_once(template("widget/inc_content_pager")); ?>
    <?php }?>
<?php }?>

