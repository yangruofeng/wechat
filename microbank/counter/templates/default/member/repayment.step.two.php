<div>
<?php if(!$data['sts']){?>
    <span class="error-msg"><?php echo $data['msg']?></span>
<?php } else {?>
    <form class="form-horizontal" id="repayment-form-two">
        <input type="hidden" name="biz_id" value="<?php echo $data['biz_id']?>">
        <input type="hidden" id="member_image" name="member_image" value="">
        <table class="table">
            <tbody class="table-body">
            <?php $i = 0;foreach($data['currency_amount'] as $currency => $amount){ ++$i?>
                <tr>
                    <td>
                        <?php if($i == 1) {?>
                            <label class="control-label">Repayment Amount</label>
                        <?php }?>
                    </td>
                    <td><span class="repayment-amount money-style"><?php echo $currency . ' ' . ncPriceFormat($amount)?></span></td>
                </tr>
            <?php }?>
            <tr>
                <td><label class="control-label">Scene Photo</label></td>
                <td>
                    <div class="snapshot_div" onclick="callWin_snapshot_slave_repayment();">
                        <img id="img_slave_repayment" src="resource/img/member/photo.png" style="width: 140px;">
                    </div>
                </td>
            </tr>
            <tr>
                <td><label class="control-label"><?php echo 'My Trading Password' ?></label></td>
                <td>
                    <a class="form-control authorize_a btn btn-default" onclick="cashierPassword()">
                        Cashier Verify
                        <img id="notCheckCashier" src="resource/img/member/verify-1.png">
                        <img id="checkCashierDone" src="resource/img/member/verify-2.png">
                    </a>
                    <input type="hidden" name="cashier_card_no"  class="form-control authorize_input" value="">
                    <input type="hidden" name="cashier_key" class="form-control authorize_input" value="">
                    <div class="error_msg"></div>
                </td>
            </tr>
            <tr>
                <td><label class="control-label"><?php echo 'Manager Password' ?></label></td>
                <td>
                    <a class="form-control authorize_a btn btn-default" onclick="verifyManger()">
                        Manager Verify
                        <img id="notCheckManager" src="resource/img/member/verify-1.png">
                        <img id="checkDoneManager" src="resource/img/member/verify-2.png">
                    </a>
                    <input type="hidden" name="chief_teller_card_no"  class="form-control authorize_input" value="">
                    <input type="hidden" name="chief_teller_key" class="form-control authorize_input" value="">
                    <div class="error_msg"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;padding-top: 15px">
                    <a type="button" class="btn btn-default" onclick="showRepaymentOne()"><i class="fa fa-angle-double-left"></i>Back</a>
                    <a type="button" class="btn btn-primary" onclick="submit_repayment()"><i class="fa fa-angle-double-right"></i>Submit</a>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
<?php }?>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script>
    function callWin_snapshot_slave_repayment() {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $("#img_slave_repayment").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('#member_image').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);
            }
        }
    }
</script>

