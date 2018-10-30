<?php $member_info = $data['data'];?>
<?php $credit_info = $data['credit_info'] ?>
<?php $credit_balance = $data['credit_balance'] ?>
<?php if($member_info){?>
    <table class="table contract-table">
        <tbody class="table-body">
        <tr>
            <td><label class="control-label">Cid</label></td>
            <td><?php echo $member_info['obj_guid'] ?></td>
            <td><label class="control-label">Login Code</label></td>
            <td class="money-style"><?php echo $member_info['login_code'] ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Client Name</label></td>
            <td><?php echo $member_info['display_name']; ?></td>
            <td><label class="control-label">Client phone</label></td>
            <td><?php echo  $member_info['phone_id']; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Client Credit</label></td>
            <td><?php echo ncAmountFormat($credit_info['credit']); ?></td>
            <td><label class="control-label">Credit Balance</label></td>
            <td><?php echo ncAmountFormat($credit_info['balance']); ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Credit State</label></td>
            <td>
            <?php if($credit_info['is_active']){
               echo 'Active';
             }else{
                echo 'Invalid';
            } ?>
            </td>
            <td><label class="control-label">Expire Time</label></td>
            <td><?php echo $credit_info['expire_time']?></td>
        </tr>
        </tbody>
    </table>
    <input type="hidden" id="credit_state" value="<?php echo $credit_info['is_active'] ?>">
    <input type="hidden" id="member_id" value="<?php echo $member_info['uid'] ?>">
    <?php foreach ($credit_balance as $key => $val) { ?>
        <input type="hidden" id="credit_balance_<?php echo $key?>" value="<?php echo $val?>">
    <?php } ?>
<?php }else{?>
    <div style="min-height: 200px;padding: 5px 20px">Null</div>
<?php }?>
