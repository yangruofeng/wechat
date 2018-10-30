<div>
    <table class="table">
        <thead>
            <tr class="table-header t1">
                <td rowspan="2">Credit Officer's Name</td>
                <td conspan="2" class="number">Principal Collection</td>
                <td conspan="2" class="number">Interest Collection</td>
                <td rowspan="2" class="number">Penalty Collection</td>
                <td rowspan="2" class="number">Recovery Collection</td>
                <td rowspan="2" class="number">Fee on Loan Size</td>
                <td rowspan="2" class="number">Admin Fee</td>
                <td rowspan="2" class="number">Operation Fee</td>
                <td rowspan="2" class="number">Total Collection</td>
            </tr>
            <tr class="table-header t1">
                <td class="number">Current</td>
                <td class="number">Current</td>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php $list = $data['data'];?>
            <?php if(count($list) > 0){ ?>
                <?php foreach ($list as $k => $v) { ?>
                    <tr>
                        <td><?php echo $v['officer_name'];?></td>
                        <td class="currency"><?php echo $v['principal']?ncPriceFormat($v['principal']):'-';?></td>
                        <td class="currency"><?php echo $v['interest']?ncPriceFormat($v['interest']):'-';?></td>
                        <td class="currency"><?php echo $v['penalty']?ncPriceFormat($v['penalty']):'-';?></td>
                        <td class="currency"><?php echo $v['recovery']?ncPriceFormat($v['recovery']):'-';?></td>
                        <td class="currency"><?php echo $v['loan_fee']?ncPriceFormat($v['loan_fee']):'-';?></td>
                        <td class="currency"><?php echo $v['admin_fee']?ncPriceFormat($v['admin_fee']):'-';?></td>
                        <td class="currency"><?php echo $v['operation_fee']?ncPriceFormat($v['operation_fee']):'-';?></td>
                        <td class="currency"><?php echo $v['total']?ncPriceFormat($v['total']):'-';?></td>
                        
                    </tr>
                <?php }?>
            <?php }else{?>
                <tr><td colspan="17"><div class="no-record">No Record</div></td></tr>
            <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>