<?php
$client = $data['client'];
$loan = $data['loan'];
$disbursement = $data['disbursement'];
$deposit = $data['deposit'];
$withdraw = $data['withdraw'];
$currency = (new currencyEnum())->toArray();
?>
<div class="col-sm-4">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Loan</h3>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th class="number">Item</th>
                <th class="number">Contract Count</th>
                <th class="number">Client Count</th>
                <th class="number">Amount</th>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php $total = 0;$client_total = 0;$amount = 0;
            foreach($currency as $k => $v){
                $total += intval($loan[$k]['count']);
                $client_total += intval($loan[$k]['client_count']);
                if($k == currencyEnum::USD){
                    $amount += $loan[$k]['amount'];
                }else{
                    $amount += global_settingClass::getCurrencyRateBetween($k, currencyEnum::USD) * $loan[$k]['amount'];
                }
                ?>
                <tr>
                    <td class="number"><?php echo $v;?></td>
                    <td class="number"><?php echo $loan[$k]['count']?:0;?></td>
                    <td class="number"><?php echo $loan[$k]['client_count']?:0;?></td>
                    <td class="currency"><span><?php echo ncPriceFormat($loan[$k]['amount']);?></span></td>
                </tr>
            <?php }?>
            <tr style="font-weight: bold;border-top: solid 2px #000">
                <td class="number">Total To USD</td>
                <td class="number"><?php echo $total;?></td>
                <td class="number"><?php echo $client_total;?></td>
                <td class="currency"><span><?php echo ncPriceFormat($amount);?></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="col-sm-4">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Disbursement</h3>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th class="number">Item</th>
                <th class="number">Contract</th>
                <th class="number">Amount</th>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php $total = 0;$amount = 0;
            foreach($currency as $k => $v){
                $total += intval($disbursement[$k]['count']);
                if($k == currencyEnum::USD){
                    $amount += $disbursement[$k]['amount'];
                }else{
                    $amount += global_settingClass::getCurrencyRateBetween($k, currencyEnum::USD) * $disbursement[$k]['amount'];
                }
                ?>
                <tr>
                    <td class="number"><?php echo $v;?></td>
                    <td class="number"><?php echo $disbursement[$k]['count']?:0;?></td>
                    <td class="currency"><span><?php echo ncPriceFormat($disbursement[$k]['amount']);?></span></td>
                </tr>
            <?php }?>
            <tr style="font-weight: bold;border-top: solid 2px #000">
                <td class="number">Total To USD</td>
                <td class="number"><?php echo $total;?></td>
                <td class="currency"><span><?php echo ncPriceFormat($amount);?></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="col-sm-4">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">New Client</h3>
        </div>
        <ul class="list-group">
            <li class="list-group-item">
                <a href="<?php echo getBackOfficeUrl("data_center_business","creditOverview")?>"><?php echo $client?:0;?></a>
            </li>
        </ul>
    </div>
</div>
<div class="clearfix"></div>

<div class="col-sm-4">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Deposit</h3>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th class="number">Item</th>
                <th class="number">Client</th>
                <th class="number">Amount</th>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php $total = 0;$amount = 0;
            foreach($currency as $k => $v){
                $total += intval($deposit[$k]['count']);
                if($k == currencyEnum::USD){
                    $amount += $deposit[$k]['amount'];
                }else{
                    $amount += global_settingClass::getCurrencyRateBetween($k, currencyEnum::USD) * $deposit[$k]['amount'];
                }
                ?>
                <tr>
                    <td class="number"><?php echo $v;?></td>
                    <td class="number"><?php echo $deposit[$k]['count']?:0;?></td>
                    <td class="currency"><span><?php echo ncPriceFormat($deposit[$k]['amount']);?></span></td>
                </tr>
            <?php }?>
            <tr style="font-weight: bold;border-top: solid 2px #000">
                <td class="number">Total To USD</td>
                <td class="number"><?php echo $total;?></td>
                <td class="currency"><span><?php echo ncPriceFormat($amount);?></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="col-sm-4">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Withdraw</h3>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th class="number">Item</th>
                <th class="number">Client</th>
                <th class="number">Amount</th>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php $total = 0;$amount = 0;
            foreach($currency as $k => $v){
                $total += intval($withdraw[$k]['count']);
                if($k == currencyEnum::USD){
                    $amount += $withdraw[$k]['amount'];
                }else{
                    $amount += global_settingClass::getCurrencyRateBetween($k, currencyEnum::USD) * $withdraw[$k]['amount'];
                }
                ?>
                <tr>
                    <td class="number"><?php echo $v;?></td>
                    <td class="number"><?php echo $withdraw[$k]['count']?:0;?></td>
                    <td class="currency"><span><?php echo ncPriceFormat($withdraw[$k]['amount']);?></span></td>
                </tr>
            <?php }?>
            <tr style="font-weight: bold;border-top: solid 2px #000">
                <td class="number">Total To USD</td>
                <td class="number"><?php echo $total;?></td>
                <td class="currency"><span><?php echo ncPriceFormat($amount);?></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>