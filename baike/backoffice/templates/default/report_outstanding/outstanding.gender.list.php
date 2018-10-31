<div>
    <table class="table">
        <thead>
            <tr class="table-header t1">
                <td rowspan="3">Credit Officer's Name</td>
                <td colspan="3" class="number">Male</td>
                <td colspan="3" class="number">Female</td>
                <td colspan="3" class="number">Total</td>
            </tr>
            <tr class="table-header t1">
                <td rowspan="2" class="number">#</td>
                <td colspan="2" class="number">Amount</td>
                <td rowspan="2" class="number">#</td>
                <td colspan="2" class="number">Amount</td>
                <td rowspan="2" class="number">#</td>
                <td colspan="2" class="number">Amount</td>
            </tr>
            <tr class="table-header t1">
                <td class="number">USD</td>
                <td class="number">KHR</td>
                <td class="number">USD</td>
                <td class="number">KHR</td>
                <td class="number">USD</td>
                <td class="number">KHR</td>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php $list = $data['data'];$total_amount = $data['total_amount'];?>
            <?php foreach ($list as $k => $v) { ?>
                <tr>
                    <td><?php echo $v['officer_name'];?></td>
                    <td class="number"><?php echo $v['male']['count']?:'-';?></td>
                    <td class="currency"><?php echo $v['male']['amount']['USD']?ncPriceFormat($v['male']['amount']['USD']):'-';?></td>
                    <td class="currency"><?php echo $v['male']['amount']['KHR']?ncPriceFormat($v['male']['amount']['KHR']):'-';?></td>
                    <td class="number"><?php echo $v['female']['count']?:'-';?></td>
                    <td class="currency"><?php echo $v['female']['amount']['USD']?ncPriceFormat($v['female']['amount']['USD']):'-';?></td>
                    <td class="currency"><?php echo $v['female']['amount']['KHR']?ncPriceFormat($v['female']['amount']['KHR']):'-';?></td>
                    <td class="number"><?php echo $v['total']['count']?:'-';?></td>
                    <td class="currency"><?php echo $v['total']['amount']['USD']?ncPriceFormat($v['total']['amount']['USD']):'-';?></td>
                    <td class="currency"><?php echo $v['total']['amount']['KHR']?ncPriceFormat($v['total']['amount']['KHR']):'-';?></td>
                </tr>
            <?php }?>
            <tr class="fontweight700">
                <td>Total</td>
                <td class="number"><?php echo $total_amount['male']['count'];?></td>
                <td class="currency"><?php echo ncPriceFormat($total_amount['male']['amount']['USD']);?></td>
                <td class="currency"><?php echo ncPriceFormat($total_amount['male']['amount']['KHR']);?></td>
                <td class="number"><?php echo $total_amount['female']['count'];?></td>
                <td class="currency"><?php echo ncPriceFormat($total_amount['female']['amount']['USD']);?></td>
                <td class="currency"><?php echo ncPriceFormat($total_amount['female']['amount']['KHR']);?>0</td>
                <td class="number"><?php echo $total_amount['total']['count'];?></td>
                <td class="currency"><?php echo ncPriceFormat($total_amount['total']['amount']['USD']);?></td>
                <td class="currency"><?php echo ncPriceFormat($total_amount['total']['amount']['KHR']);?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>