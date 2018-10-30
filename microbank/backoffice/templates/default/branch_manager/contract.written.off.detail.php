<?php $info = $output['data']; ?>
<style>
    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: red;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Request Written Off</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getBackOfficeUrl('branch_manager','requestWrittenOff'); ?>"><span>Request</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <?php if($info){?>
            <div class="col-sm-8">
                <form action="?" class="form-horizontal" id="submit_form">
                    <input type="hidden" name="form_submit" value="ok">
                    <input type="hidden" id="uid" name="uid" value="<?php echo $info['uid']; ?>">

                    <input type="hidden" name="act" value="branch_manager">
                    <input type="hidden" name="op" value="contractWrittenOffDetail">

                    <table class="table contract-table">

                        <tbody class="table-body">
                        <tr>
                            <td><label class="control-label">Contact No.</label></td>
                            <td><?php echo $info['contract_sn'] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Product Name</label></td>
                            <td><?php echo $info['sub_product_name'] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Client-Name</label></td>
                            <td><?php echo $info['client_info']['display_name'] ?$info['client_info']['display_name'].'/'.$info['client_info']['kh_display_name'] : $info['client_info']['login_code']?></td>
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


                        <?php if( $info['request_record'] ){ $apply = $info['request_record']; ?>
                            <tr>
                                <td><label class="control-label">Apply State</label></td>
                                <td ><?php echo $lang['write_off_state_'.$apply['state']]; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Apply Time</label></td>
                                <td ><?php echo timeFormat($apply['create_time']); ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Remark</label></td>
                                <td ><?php echo $apply['close_remark']; ?></td>
                            </tr>
                        <?php }else{ ?>
                            <tr>
                                <td><label class="control-label">Remark</label></td>
                                <td>
                                    <textarea class="form-control" name="remark" style="width: 80%;height: 100px"></textarea>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>


                            <tr style="text-align: center">
                                <td colspan="2">
                                    <button type="button" class="btn btn-danger"  onclick="submit_written_off();">
                                        <i class="fa fa-check"></i><?php echo 'Submit' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                </form>
            </div>
        <?php }else{?>
            <div style="padding: 10px 10px">Null</div>
        <?php }?>

    </div>



</div>


<script>
    function submit_written_off()
    {
        $('#submit_form').submit();
    }
</script>
