<?php $info = $data['data']; ?>
<?php if($info){?>
    <div>
        <form class="form-horizontal">
            <table class="table contract-table">
                <input type="hidden" id="uid" name="uid" value="<?php echo $info['uid']?>">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Contact-Sn</label></td>
                    <td><?php echo $info['contract_sn'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Product Name</label></td>
                    <td><?php echo $info['sub_product_name'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Client-Name</label></td>
                    <td><?php echo $info['client_info']['display_name'] ? : $info['client_info']['login_code']?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan Date</label></td>
                    <td><?php echo dateFormat($info['start_date']) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Currency</label></td>
                    <td><?php echo $info['currency'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan Amount</label></td>
                    <td><?php echo ncPriceFormat($info['apply_amount']) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan State</label></td>
                    <td><?php echo $lang['loan_contract_state_'.$info['state']] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">loss Principal</label></td>
                    <td class="money-style"><?php echo ncPriceFormat($info['loss_info']['loss_principal']) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loss Interest</label></td>
                    <td class="money-style"><?php echo ncPriceFormat($info['loss_info']['loss_interest']) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loss Operation Fee</label></td>
                    <td class="money-style"><?php echo ncPriceFormat($info['loss_info']['loss_operation_fee']) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Total Loss</label></td>
                    <td class="money-style"><?php echo ncPriceFormat($info['loss_info']['loss_amount'])?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Remark</label></td>
                    <td>
                        <textarea class="form-control" name="remark" style="width: 80%;height: 100px"></textarea>
                        <div class="error_msg"></div>
                    </td>
                </tr>

                <?php if ($data['hint']) { ?>
                    <tr>
                        <td><label class="control-label">Hint</label></td>
                        <td>
                            <span style="color: red"><?php echo $data['hint']?></span>
                        </td>
                    </tr>
                <?php } ?>

                <tr style="text-align: center">
                    <td colspan="2">
                        <button type="button" class="btn btn-danger" <?php echo $data['is_submit'] ? '' : 'disabled' ?> onclick="submit_written_off();">
                            <i class="fa fa-check"></i><?php echo 'Submit' ?>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
<?php }else{?>
    <div style="padding: 10px 10px">Null</div>
<?php }?>