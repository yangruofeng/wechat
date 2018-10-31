<?php
$currency = (new currencyEnum())->toArray();
$count = count($currency);
$today_repayment = $data['today_repayment'];
$tomorrow_repayment = $data['tomorrow_repayment'];
$after_tomorrow_repayment = $data['after_tomorrow_repayment'];
?>

<div class="col-sm-4">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h5 class="panel-title">Today (<?php echo dateFormat(date('Y-m-d'));?>)</h5>
        </div>
        <table class="table table-hover">
            <tr class="table-header">
                <td>Contract Sn</td>
                <td>Currency</td>
                <td>Apply Amount</td>
                <td>
                    Receivable
                    <p>Principal/Total</p>
                </td>
            </tr>
            <?php if($today_repayment){?>
                <?php foreach($today_repayment as $item){?>
                    <tr>
                        <td>
                            <p><?php echo $item['contract_sn']?></p>
                            <p><kbd><?php echo $item['display_name']?></kbd></p>
                        </td>
                        <td><?php echo $item['currency']?></td>
                        <td><?php echo ncPriceFormat($item['apply_amount'])?></td>
                        <td>
                            <p><?php echo ncPriceFormat($item['receivable_principal'])?></p>
                            <p><?php echo ncPriceFormat($item['ref_amount'])?></p>
                        </td>
                    </tr>
                <?php }?>
            <?php }else{?>
                <tr>
                    <td colspan="4">
                        <div>
                            <?php include(template(":widget/no_record")); ?>
                        </div>
                    </td>
                </tr>
            <?php }?>
        </table>
    </div>
</div>
<div class="col-sm-4">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h5 class="panel-title">Tomorrow (<?php echo dateFormat(date('Y-m-d',strtotime('+1 day')));?>)</h5>
        </div>
        <table class="table table-hover">
            <tr class="table-header">
                <td>Contract Sn</td>
                <td>Currency</td>
                <td>Apply Amount</td>
                <td>
                    Receivable
                    <p>Principal/Total</p>
                </td>
            </tr>
            <?php if($tomorrow_repayment){?>
                <?php foreach($tomorrow_repayment as $item){?>
                    <tr>
                        <td>
                            <p><?php echo $item['contract_sn']?></p>
                            <p><kbd><?php echo $item['display_name']?></kbd></p>
                        </td>
                        <td><?php echo $item['currency']?></td>
                        <td><?php echo ncPriceFormat($item['apply_amount'])?></td>
                        <td>
                            <p><?php echo ncPriceFormat($item['receivable_principal'])?></p>
                            <p><?php echo ncPriceFormat($item['ref_amount'])?></p>
                        </td>
                    </tr>
                <?php }?>
            <?php }else{?>
                <tr>
                    <td colspan="4">
                        <div>
                            <?php include(template(":widget/no_record")); ?>
                        </div>
                    </td>
                </tr>
            <?php }?>
        </table>
    </div>
</div>
<div class="col-sm-4">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h5 class="panel-title">The Day After Tomorrow (<?php echo dateFormat(date('Y-m-d',strtotime('+2 day')));?>)</h5>
        </div>
        <table class="table table-hover">
            <tr class="table-header">
                <td>Contract Sn</td>
                <td>Currency</td>
                <td>Apply Amount</td>
                <td>
                    Receivable
                    <p>Principal/Total</p>
                </td>
            </tr>
            <?php if($after_tomorrow_repayment){?>
                <?php foreach($after_tomorrow_repayment as $item){?>
                    <tr>
                        <td>
                            <p><?php echo $item['contract_sn']?></p>
                            <p><kbd><?php echo $item['display_name']?></kbd></p>
                        </td>
                        <td><?php echo $item['currency']?></td>
                        <td><?php echo ncPriceFormat($item['apply_amount'])?></td>
                        <td>
                            <p><?php echo ncPriceFormat($item['receivable_principal'])?></p>
                            <p><?php echo ncPriceFormat($item['ref_amount'])?></p>
                        </td>
                    </tr>
                <?php }?>
            <?php }else{?>
                <tr>
                    <td colspan="4">
                        <div>
                            <?php include(template(":widget/no_record")); ?>
                        </div>
                    </td>
                </tr>
            <?php }?>
        </table>
    </div>
</div>



