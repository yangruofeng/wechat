<style>
    .red{
        color:red;
    }
</style>
<?php
$list = $data['data'];
$book_id = $data['book_id'];
?>
<table class="table table-bordered table-no-background">
    <thead>
    <tr class="table-header t1">

        <td  class="number">Trx ID</td>
        <td  class="number">Subject</td>
        <td  class="number">Account</td>
        <td  class="number">Amount</td>
        <td  class="number">Memo</td>

    </tr>

    </thead>
    <tbody class="table-body">

    <?php foreach( $list as $v ){ $first_flow = $v['flow_list'][0]; $count = count($v['flow_list']);  ?>


        <tr class="ntr-<?php echo $v['no']%2 == 0 ? 'even': 'odd';?>" >

            <td rowspan="<?php echo $count;?>" >
                <?php echo $v['trade_id'];?>
            </td>

            <td rowspan="<?php echo $count;?>" >
                <?php echo $v['t_subject'];?>
            </td>
            <td >
                <?php echo $first_flow['book_code']; ?>
                <?php //echo $first_flow['account_id'].'-'. $first_flow['book_name'];?>
            </td>
            <td rowspan="<?php echo $count;?>" class="<?php echo $v['trading_amount']<0?'red':''; ?>">
                <?php echo ncPriceFormat($v['trading_amount']);?>
            </td>
            <td rowspan="<?php echo $count;?>" >
                <?php echo $v['sys_memo'];?>
            </td>

        </tr>

        <?php if( !empty($v['flow_list']) ){ foreach( $v['flow_list'] as $key=>$item ){ if( $key>0){  ?>
            <tr class="ntr-<?php echo $v['no']%2 == 0 ? 'even': 'odd';?>" >


                <td>
                    <?php echo $item['book_code']; ?>
                    <?php //echo $item['account_id'].'-'. $item['book_name'];?>
                </td>

            </tr>
        <?php } } } ?>

    <?php } ?>


    <tr class="tr-total">
        <td colspan="3" align="center">Total</td>
        <td>
            <?php echo ncPriceFormat($data['total_amount']); ?>
        </td>
        <td></td>
    </tr>

    </tbody>
</table>
<?php include_once(template("widget/inc_content_pager")); ?>
<script>

</script>
