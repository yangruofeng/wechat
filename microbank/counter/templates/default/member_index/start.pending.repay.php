<?php $list=$output['pending_pay'];?>
<table class="table table-bordered table-hover">
    <tr class="table-header">
        <td></td>
        <td>CID</td>
        <td>Icon</td>
        <td>Member</td>

        <td>Scheme Name</td>
        <td>Receivable Date</td>
        <td>Currency</td>
        <td>Remain Principal</td>
        <td>Receivable amount</td>
        <td>Branch</td>
    </tr>
    <?php foreach($list as $i=>$item){?>
        <tr>
            <td><?php echo $i+1;?></td>
            <td><?php echo $item['obj_guid']?></td>
            <td>
                <img src="<?php echo getImageUrl($item['member_icon'],imageThumbVersion::AVATAR)?>" style="width: 100px;height: 100px">
            </td>
            <td>
                <?php echo $item['display_name']?>
            </td>

            <td>
                <?php echo $item['scheme_name']?>
            </td>
            <td>
                <?php echo date('Y-m-d',strtotime($item['receivable_date']));?>
            </td>
            <td>
                <?php echo $item['currency']?>
            </td>
            <td><?php echo $item['receivable_principal']?></td>
            <td><?php echo $item['ref_amount']?></td>
            <td><?php echo $item['branch_name']?></td>
        </tr>
    <?php }?>

</table>