<?php $list=$output['pending_sign'];?>
<table class="table table-bordered table-hover">
    <tr class="table-header">
        <td></td>
        <td>CID</td>
        <td>Icon</td>
        <td>Member</td>
        <td>Grant Time</td>
        <td>Credit</td>
        <td>Terms</td>
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
                <?php echo date('Y-m-d',strtotime($item['grant_time']));?>
            </td>
            <td><?php echo $item['credit']?></td>
            <td><?php echo $item['credit_terms']?></td>
            <td><?php echo $item['branch_name']?></td>
        </tr>
    <?php }?>

</table>