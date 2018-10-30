<div>
    <?php $title = $data['title'];$column = count($title);$list = $data['data'];?>
    <table class="table">
        <thead>
            <tr class="table-header t1">
                <td rowspan="3">Credit Officer's Name</td>
                <?php foreach ($title as $k => $v) {?>
                    <td colspan="2" class="number"><?php echo $v['sub_product_name'];?></td>
                <?php }?>
            </tr>
            <tr class="table-header t1">
                <?php foreach ($title as $k => $v) {?>
                    <td colspan="2" class="number"><?php echo $v['repayment_type'];?></td>
                <?php }?>
            </tr>
            <tr class="table-header t1">
                <?php for ($i = 0;$i<$column;$i++) {?>
                    <td class="number">#</td>
                    <td class="number">Amount</td>
                <?php }?>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php foreach ($list as $k => $v) { $product = $v['product'];?>
                <tr>
                    <td><?php echo $v['officer_name'];?></td>
                    <?php foreach ($title as $tk => $tv) {?>
                        <td class="number"><?php echo $v['product'][$tv['uid']]['count']?:'-';?></td>
                        <td class="currency"><?php echo $v['product'][$tv['uid']]['amount']?ncPriceFormat($v['product'][$tv['uid']]['amount']):'-';?></td>
                    <?php }?>
                </tr>
            <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>