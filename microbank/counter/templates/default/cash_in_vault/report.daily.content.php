<?php $ccy_list=(new currencyEnum())->Dictionary();?>
<table class="table table-bordered table-hover">
    <tr class="table-header">
        <td>ID</td>
        <td> Account Code</td>
        <td>Account Name</td>
        <?php foreach($ccy_list as $ccy){?>
            <td><?php echo $ccy?></td>
        <?php }?>
        <td>
            Remark
        </td>
    </tr>
    <?php foreach($data as $item){?>
        <tr>
            <td><?php echo $item['uid']?></td>
            <td><?php echo $item['book_code']?></td>
            <td><?php echo $item['book_name']?></td>
            <?php foreach($ccy_list as $ccy){?>
                <td><?php if($item['balance'][$ccy]>0) echo ncPriceFormat($item['balance'][$ccy])?></td>
            <?php }?>
            <td>
                <?php echo $item['remark']?>
            </td>
        </tr>
    <?php }?>
</table>