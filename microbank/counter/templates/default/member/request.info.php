<?php $info = $data['data'];?>
<?php if($info){?>
    <table class="table contract-table">
        <tbody class="table-body">
        <tr>
            <td><label class="control-label">Client Id</label></td>
            <td><?php echo $info['member_id'] ?: '' ?></td>
            <td><label class="control-label">Amount</label></td>
            <td class="money-style"><?php echo ncAmountFormat($info['apply_amount'], false, $info['currency']) ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Client Name</label></td>
            <td><?php echo $info['applicant_name']?></td>
            <td><label class="control-label">Request Time</label></td>
            <td><?php echo timeFormat($info['apply_time'])?></td>
        </tr>
        <tr>
            <td><label class="control-label">Client Phone</label></td>
            <td><?php echo $info['contact_phone']?></td>
            <td><label class="control-label">Request Status</label></td>
            <td><?php echo $lang['apply_state_' . $info['state']]?></td>
        </tr>
        <?php if ($info['state'] == loanApplyStateEnum::ALL_APPROVED) { ?>
            <input type="hidden" id="uid" value="<?php echo $info['uid'];?>">
        <tr>
            <td><label class="control-label">Product Name</label></td>
            <td><?php echo $info['product_name']?></td>
            <td><label class="control-label">Repayment Type</label></td>
            <td><?php echo ucwords(str_replace('_', ' ', $info['repayment_type'])); ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Period</label></td>
            <td><?php echo $info['repayment_period']?></td>
            <td><label class="control-label">Interest Rate</label></td>
            <td><?php echo ($info['interest_rate_type'] == 1 ? "$" . $info['interest_rate'] : $info['interest_rate'] . '%') . ' Per ' . $info['interest_rate_unit']?></td>
        </tr>
        <tr>
            <td><label class="control-label">Operate Fee</label></td>
            <td><?php echo ($info['operation_fee_type'] == 1 ? "$" . $info['operation_fee'] : $info['operation_fee'] . '%') . ' Per ' . $info['operation_fee_unit']  ?></td>
            <td><label class="control-label">Admin Fee</label></td>
            <td><?php echo $info['admin_fee_type'] == 1 ? "$" . $info['admin_fee'] : $info['admin_fee'] . '%' ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Loan Fee</label></td>
            <td colspan="3"><?php echo $info['loan_fee_type'] == 1 ? "$" . $info['loan_fee'] : $info['loan_fee'] . '%' ?></td>
        </tr>
        <?php }?>
        </tbody>
    </table>
<?php }else{?>
    <div style="min-height: 200px;padding: 5px 20px">Null</div>
<?php }?>
<script>
    <?php if($info && $info['state'] == loanApplyStateEnum::ALL_APPROVED){?>
        $('.operation').show();
    <?php } else { ?>
        $('.operation').hide();
    <?php }?>
</script>