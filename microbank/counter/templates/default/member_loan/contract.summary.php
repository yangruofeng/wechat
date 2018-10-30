<?php $info = $output['contract']; ?>
<?php if($info){?>
    <div>
        <table class="table contract-table">
            <input type="hidden" id="uid" value="<?php echo $info['uid']?>">
            <tbody class="table-body">
            <tr>
                <td><label class="control-label">Client Code</label></td>
                <td><?php echo $output['client_info']['login_code'] ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Product</label></td>
                <td><?php echo $info['product_category_name'] ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Loan Date</label></td>
                <td><?php echo dateFormat($info['start_date']) ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Terms</label></td>
                <td><?php echo $info['loan_period_value'] . " " . ucwords($info['loan_period_unit']) ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Due Date</label></td>
                <td><?php echo 'The ' . $info['due_date'] . 'th of each month' ?></td>
            </tr>
            <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo $lang['loan_contract_state_' . $info['state']]?><input type="hidden" id="state" value="<?php echo $info['state']?>"/></td>
            </tr>
            <tr>
                <td><label class="control-label">Currency</label></td>
                <td><?php echo $info['currency'] ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Loan Limit</label></td>
                <td><?php echo ncPriceFormat($info['receivable_principal']) ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Remains Principal</label></td>
                <td class="money-style"><?php echo ncPriceFormat($info['total_payable_principal']) ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Remains Balance</label></td>
                <td class="money-style"><?php echo ncPriceFormat($info['total_payable_amount']) ?></td>
            </tr>
            </tbody>
        </table>
    </div>

    <script>
        $('.contract-function .btn-default').attr('disabled',false);
    </script>
<?php }else{?>
    <div style="padding: 10px 10px">Null</div>
    <script>
        $('.contract-function .btn-default').attr('disabled',true);
    </script>
<?php }?>

