<?php $credit = memberClass::getCreditBalance($client_info['uid']);?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title"><i class="fa fa-id-card-o"></i> Client Info</h5>
    </div>
    <table class="table">
        <tbody class="table-body">
        <tr>
            <td><label class="control-label">Icon</label></td>
            <td><label class="control-label">CID</label></td>
            <td><label class="control-label">Login Account</label></td>
            <td><label class="control-label">Name</label></td>
            <td><label class="control-label">Phone</label></td>
            <td><label class="control-label">Member Credit</label></td>
            <td><label class="control-label">Credit Balance</label></td>
            <td><label class="control-label">Loan Balance</label></td>
            <td><label class="control-label">Status</label></td>
        </tr>
        <tr>
            <td>
                <a target="_blank" href="<?php echo getImageUrl($client_info['member_icon']); ?>">
                    <img src="<?php echo getImageUrl($client_info['member_icon'], imageThumbVersion::SMALL_ICON); ?>" style="max-width: 50px;max-height: 50px">
                </a>
            </td>
            <td><?php echo $client_info['obj_guid']; ?></td>
            <td><?php echo $client_info['login_code']; ?></td>
            <td><?php echo $client_info['display_name']; ?></td>
            <td><?php echo $client_info['phone_id']; ?></td>
            <td><?php echo ncPriceFormat($credit['credit']); ?></td>
            <td><?php echo ncPriceFormat($credit['balance']); ?></td>
            <td><?php echo ncPriceFormat(memberClass::getLoanBalance($credit_info['uid'])->DATA); ?></td>
            <td><?php echo $lang['client_member_state_' . $client_info['member_state']]; ?></td>
        </tr>
        </tbody>
    </table>
</div>
