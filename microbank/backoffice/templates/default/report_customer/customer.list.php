
<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td>CID</td>
            <td colspan="2">Customer Name</td>
            <td>Address</td>
            <td>Date of Birth</td>
            <td>Last Update</td>
            <td>Occupation</td>
        </tr>
        <tr class="table-header t2">
            <td></td>
            <td>Registered date</td>
            <td colspan="2">Type</td>
            <td colspan="2">Status</td>
            <td>Net Income</td>
        </tr>
        </thead>
        <tbody class="table-body">
<?php if($data['data']){ ?>
    <?php $i = 0; foreach ($data['data'] as $row) {
        ++$i;?>
        <tr class="<?php echo $i % 2 == 0 ? 'tr_even' : 'tr_odd'?>">
            <td><?php echo $row['obj_guid']?:generateGuid($row['uid'], objGuidTypeEnum::CLIENT_MEMBER) ?></td>
            <td colspan="2"><?php echo $row['display_name']?></td>
            <td rowspan="2"><?php echo $row['full_text']?></td>
            <td><?php echo $row['birthday']?></td>
            <td><?php echo $row['update_time']?></td>
            <td><?php echo ucwords(str_replace('_', ' ', $row['work_type']))?></td>
        </tr>
        <tr class="<?php echo $i % 2 == 0 ? 'tr_even' : 'tr_odd'?>">
            <td></td>
            <td><?php echo $row['create_time']?></td>
            <td>Personal/Individu</td>
            <td colspan="2"><?php echo $lang['client_member_state_'.$row['member_state']]?></td>
            <td><?php echo $row['net_income']  ?></td>
        </tr>
        <?php }?>
    <?php }else{ ?>
        <tr>
            <td colspan="7">No Record</td>
        </tr>
    <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>