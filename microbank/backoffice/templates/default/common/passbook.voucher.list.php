<?php
$nbsp = '&nbsp;';
$nbsp_len = strlen($nbsp);
$list = $data['data'];
$currency = (new currencyEnum())->toArray();
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr class="table-header t1">
        <td rowspan="2" class="number">No</td>
        <td rowspan="2" class="number">ID</td>
        <td rowspan="2" class="number">Time</td>
        <td rowspan="2" class="number">Subject</td>
        <td rowspan="2" class="number">Account</td>
        <td colspan="2" class="number">Debit</td>
        <td colspan="2" class="number">Credit</td>
        <td rowspan="2" class="number">Remark</td>
    </tr>
    <tr class="table-header t1">
        <td class="number">Currency</td>
        <td class="number">Amount</td>
        <td class="number">Currency</td>
        <td class="number">Amount</td>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php if ($list) { ?>
        <?php foreach( $list as $v ){ $count = count($v['flow']); $first_flow = $v['flow'][0];?>
            <tr class="ntr-<?php echo $v['no']%2 == 0 ? 'even': 'odd';?>">
                <td rowspan="<?php echo $count?:'';?>" class="number"><?php echo $v['no'];?></td>
                <td rowspan="<?php echo $count?:'';?>" class="number"><?php echo $v['trade_id'];?></td>
                <td rowspan="<?php echo $count?:'';?>" class="number"><?php echo timeFormat($v['time']);?></td>
                <td rowspan="<?php echo $count?:'';?>" class="number"><?php echo $v['subject'];?></td>
                <td><?php echo $first_flow['fid'];?>&nbsp;&nbsp;<?php echo $first_flow['book_name'];?></td>
                <td class="number"><?php if($first_flow['debit'] != 0){ echo $first_flow['currency'];}?></td>
                <td class="currency"><?php if($first_flow['debit'] != 0){ echo ncPriceFormat($first_flow['debit']);}?></td>
                <td class="number"><?php if($first_flow['credit'] != 0){ echo $first_flow['currency'];}?></td>
                <td class="currency"><?php if($first_flow['credit'] != 0){ echo ncPriceFormat($first_flow['credit']);}?></td>
                <td rowspan="<?php echo $count?:'';?>" class="number"><?php echo $v['sys_memo'];?></td>
            </tr>
            <?php foreach($v['flow'] as $fk => $fv){ if($fk > 0){ ?>
                <tr class="ntr-<?php echo $v['no']%2 == 0 ? 'even': 'odd';?>">
                    <td><?php echo $fv['fid'];?>&nbsp;&nbsp;<?php echo $fv['book_name'];?></td>
                    <td class="number"><?php if($fv['debit'] != 0){ echo $fv['currency'];}?></td>
                    <td class="currency"><?php if($fv['debit'] != 0){ echo ncPriceFormat($fv['debit']);}?></td>
                    <td class="number"><?php if($fv['credit'] != 0){ echo $fv['currency'];}?></td>
                    <td class="currency"><?php if($fv['credit'] != 0){ echo ncPriceFormat($fv['credit']);}?></td>
                </tr>
            <?php }}?>
        <?php } ?>
        <tr class="tr-total">
            <td colspan="5" align="center">Total</td>
            <td>
                <select name="currency" id="currency" onclick="changeCurrency();">
                    <?php foreach ($currency as $k => $v) { ?>
                        <option value="<?php echo $k;?>"><?php echo $v;?></option>
                    <?php } ?>
                </select>
            </td>
            <td id="debit"></td>
            <td>&nbsp;</td>
            <td id="credit"></td>
            <td>&nbsp;</td>
        </tr>
    <?php } else { ?>
        <tr>
            <td colspan="10"> <?php include(template(":widget/no_record")); ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<?php if (count($list) > 0 || $data['pageNumber'] != 1) { ?>
    <?php include_once(template("widget/inc_content_pager")); ?>
<?php } ?>
<script>
    var total = '<?php echo $data['totalCurrency'];?>';
    total = eval('('+total+')');
    changeCurrency();
    function changeCurrency(){
        var cur = $('#currency').val(), debit = total['debit'][cur], credit = total['credit'][cur];
        $('#debit').text(debit);
        $('#credit').text(credit);
    }
</script>

