<div>
    <table class="table">
        <thead>
            <tr class="table-header t1">
                <td rowspan="2" class="number">No.</td>
                <td rowspan="2" class="number">Account No.</td>
                <td rowspan="2">Client Name</td>
                <td rowspan="2">Bussiness</td>
                <td rowspan="2" class="number">Phone</td>
                <td rowspan="2">Occupation</td>
                <td rowspan="2" class="number">Disburse</td>
                <td rowspan="2" class="number">Maturity</td>
                <td rowspan="2" class="number">Circle</td>
                <td rowspan="2" class="number">Disburse Amount</td>
                <td rowspan="2" class="number">Period</td>
                <td rowspan="2" class="number">Month Period</td>
                <td colspan="3" class="number">Amount Late</td>
                <td rowspan="2" class="number">Principal Owe</td>
                <td rowspan="2" class="number">Day of late</td>
            </tr>
            <tr class="table-header t1">
                <td class="number">Principal</td>
                <td class="number">Interest</td>
                <td class="number">Total</td>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php if(count($data) > 0){ ?>
                <?php foreach ($data as $k => $v) { ?>
                    <tr class="fontweight600">
                        <td colspan="17">Credit officer's name: <?php echo $v['officer_name'];?></td>
                    </tr>
                    <?php $i = 1; foreach ($v['contract'] as $ck => $cv) {?>
                        <tr>
                            <td><?php echo $i;?></td>
                            <td class="number"><?php echo $cv['contract_sn'];?></td>
                            <td><?php echo $cv['client_name'];?></td>
                            <td><?php echo $cv['bussiness']?:'-';?></td>
                            <td class="currency"><?php echo $cv['phone'];?></td>
                            <td><?php echo $cv['occupation']?:'-';?></td>
                            <td class="number"><?php echo dateFormat($cv['disburse']);?></td>
                            <td class="number"><?php echo dateFormat($cv['maturity']);?></td>
                            <td class="number"><?php echo $cv['circle'];?></td>
                            <td class="currency"><?php echo ncPriceFormat($cv['disburse_amount']);?></td>
                            <td class="number"><?php echo $cv['period'];?></td>
                            <td class="number"><?php echo $cv['month_period']?:'-';?></td>
                            <td class="currency"><?php echo ncPriceFormat($cv['principal']);?></td>
                            <td class="currency"><?php echo ncPriceFormat($cv['interest']);?></td>
                            <td class="currency"><?php echo ncPriceFormat($cv['total']);?></td>
                            <td class="currency"><?php echo ncPriceFormat($cv['late_amount']);?></td>
                            <td class="number"><?php echo $cv['days'];?></td>
                        </tr>
                    <?php $i++; }?>
                    <tr class="fontweight700">
                        <td colspan="7">Total By <?php echo $v['officer_name'];?></td>
                        <td class="number"><?php echo $v['total']['contract_number'];?></td>
                        <td></td>
                        <td class="currency"><?php echo ncPriceFormat($v['total']['disburse_amount']);?></td>
                        <td></td>
                        <td></td>
                        <td class="currency"><?php echo ncPriceFormat($v['total']['principal']);?></td>
                        <td class="currency"><?php echo ncPriceFormat($v['total']['interest']);?></td>
                        <td class="currency"><?php echo ncPriceFormat($v['total']['total']);?></td>
                        <td class="currency"><?php echo ncPriceFormat($v['total']['late_amount']);?></td>
                        <td></td>
                    </tr>
                <?php }?>
            <?php }else{?>
                <tr><td colspan="17"><div class="no-record">No Record</div></td></tr>
            <?php }?>
        </tbody>
    </table>
</div>