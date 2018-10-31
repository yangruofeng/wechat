<?php
$currency = (new currencyEnum())->toArray();
$count = count($currency);
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr class="table-header">
            <td class="td-border-right000">&nbsp;</td>
            <td colspan="4" class="td-border-right000">Loan</td>
            <td colspan="4" class="td-border-right000">Repayment</td>
            <td colspan="4">Pending Repayment</td>
        </tr>
        <tr class="table-header">
            <td class="td-border-right000">Day</td>
            <td>Client</td>
            <td>Contract</td>
            <?php $i = 0; foreach($currency as $ccy){ $i++; ?>
                <td class="c-<?php echo strtolower($ccy);?> <?php if($i == $count){echo 'td-border-right000';}?>"><?php echo $ccy.'-Amt';?></td>
            <?php }?>
            <td>Client</td>
            <td>Contract</td>
            <?php $i = 0; foreach($currency as $ccy){ $i++; ?>
                <td class="c-<?php echo strtolower($ccy);?> <?php if($i == $count){echo 'td-border-right000';}?>"><?php echo $ccy.'-Amt';?></td>
            <?php }?>
            <td>Client</td>
            <td>Contract</td>
            <?php foreach($currency as $ccy){ ?>
                <td class="c-<?php echo strtolower($ccy);?>"><?php echo $ccy.'-Amt';?></td>
            <?php }?>
        </tr>
    </thead>
    <tbody class="table-body">
        <?php if($data){ ?>
            <?php $arr_subtotal=array();?>
            <?php foreach( $data as $k => $v ){ ?>
                <?php
                  $tmp_arr=array("loan","repayment","pending_repayment");
                foreach($tmp_arr as $tmp_i){
                    $arr_subtotal[$tmp_i]['client_count']+=$v[$tmp_i]['client_count'];
                    $arr_subtotal[$tmp_i]['contract_count']+=$v[$tmp_i]['contract_count'];
                    $arr_subtotal[$tmp_i]['usd']+=$v[$tmp_i]['amount']['USD'];
                    $arr_subtotal[$tmp_i]['khr']+=$v[$tmp_i]['amount']['KHR'];
                }



                ?>
                <tr>
                    <td class="td-border-right000"><?php echo date('m-d',strtotime($k));?></td>
                    <td><?php echo $v['loan']['client_count']?:0;?></td>
                    <td><?php echo $v['loan']['contract_count']?:0;?></td>
                    <?php $i = 0; foreach($currency as $ccy){ $i++; ?>
                        <td class="c-<?php echo strtolower($ccy);?> <?php if($i == $count){echo 'td-border-right000';}?>"><?php echo ncPriceFormat($v['loan']['amount'][$ccy]);?></td>
                    <?php }?>
                    <td><?php echo $v['repayment']['client_count']?:0;?></td>
                    <td><?php echo $v['repayment']['contract_count']?:0;?></td>
                    <?php $i = 0; foreach($currency as $ccy){ $i++; ?>
                        <td class="c-<?php echo strtolower($ccy);?> <?php if($i == $count){echo 'td-border-right000';}?>"><?php echo ncPriceFormat($v['repayment']['amount'][$ccy]);?></td>
                    <?php }?>
                    <td><?php echo $v['pending_repayment']['client_count']?:0;?></td>
                    <td><?php echo $v['pending_repayment']['contract_count']?:0;?></td>
                    <?php foreach($currency as $ccy){ ?>
                        <td class="c-<?php echo strtolower($ccy);?>"><?php echo ncPriceFormat($v['pending_repayment']['amount'][$ccy]);?></td>
                    <?php }?>
                </tr>
            <?php } ?>
            <tr style="font-weight: bold">
                <td>Total</td>
                <?php foreach($arr_subtotal as $arr_item){?>
                    <?php foreach($arr_item as $total_key=>$total_item){?>
                        <td>
                            <?php if(in_array($total_key,array("usd","khr"))){?>
                                <?php echo ncPriceFormat($total_item,2)?>
                            <?php }else{?>
                                <?php echo ncPriceFormat($total_item,0)?>
                            <?php }?>
                        </td>
                    <?php }?>

                <?php }?>

            </tr>
        <?php }else{ ?>
            <tr>
                <td colspan="13">
                    <div>
                        <?php include(template(":widget/no_record")); ?>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

