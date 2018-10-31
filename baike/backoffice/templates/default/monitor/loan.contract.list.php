<?php
$list = $data;
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr class="table-header">
        <td>No</td>
        <td>CID</td>
        <td>Member</td>
        <td>Contract SN</td>
        <td>Currency</td>
        <td>Apply Amount</td>
        <td>Product</td>
        <td>State</td>
        <td>Time</td>
        <td>Function</td>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php if(count($list)>0){?>
        <?php $i = 0; foreach ($list as $row) { $i++;?>
            <tr>
                <td><?php echo $i;?></td>
                <td><a href="<?php echo getUrl('common', 'showClientDetail', array('search_by' => 2, 'phone_number'=>$row['client_obj_guid']), false, BACK_OFFICE_SITE_URL); ?>"><?php echo $row['client_obj_guid']; ?></a></td>
                <td><?php echo $row['login_code']; ?></td>
                <td><?php echo $row['contract_sn']; ?></td>
                <td><?php echo $row['currency']; ?></td>
                <td><?php echo ncPriceFormat($row['apply_amount']); ?></td>
                <td><?php echo $row['alias']; ?></td>
                <td>
                    <?php echo $lang['loan_contract_state_' . $row['state']]?>
                </td>
                <td><?php echo timeFormat($row['update_time']); ?></td>
                <td>
                    <a class="btn btn-link btn-xs"
                       href="<?php echo getUrl('monitor', 'loanContractDetailPage', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL); ?>">
                        <span><i class="fa fa-vcard-o"></i>Detail</span>
                    </a>
                </td>
            </tr>

        <?php } ?>
    <?php }else{ ?>
        <tr>
            <td colspan="11">
                <div>
                    <?php include(template(":widget/no_record")); ?>
                </div>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

