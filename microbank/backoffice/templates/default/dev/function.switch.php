<!--<script src="--><?php //echo GLOBAL_RESOURCE_SITE_URL; ?><!--/js/jquery.validation.min.js"></script>-->
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Function Switch</h3>
            <ul class="tab-base">
<!--                <li><a class="current"><span>Edit</span></a></li>-->
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close Loan' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_loan" value="1" <?php echo $output['function_switch']['close_loan'] == 1 ? 'checked' : ''?>>
                        禁止贷款(close_loan)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close Return Loan' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_return_loan" value="1" <?php echo $output['function_switch']['close_return_loan'] == 1 ? 'checked' : ''?>>
                        禁止还款(close_return_loan)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close Withdraw' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_withdraw" value="1" <?php echo $output['function_switch']['close_withdraw'] == 1 ? 'checked' : ''?>>
                        禁止取款(close_withdraw)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close Deposit' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_deposit" value="1" <?php echo $output['function_switch']['close_deposit'] == 1 ? 'checked' : ''?>>
                        禁止存款(close_deposit)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close Transfer' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_transfer" value="1" <?php echo $output['function_switch']['close_transfer'] == 1 ? 'checked' : ''?>>
                        禁止转账(close_transfer)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close Collect' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_collect" value="1" <?php echo $output['function_switch']['close_collect'] == 1 ? 'checked' : ''?>>
                        禁止收款(close_collect)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close Pay' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_pay" value="1" <?php echo $output['function_switch']['close_pay'] == 1 ? 'checked' : ''?>>
                        禁止付款(close_pay)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close password reset' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_reset_password" value="1" <?php echo $output['function_switch']['close_reset_password'] == 1 ? 'checked' : ''?>>
                        禁止重设密码(close_reset_password)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close ACE business' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_ace_business" value="1" <?php echo $output['function_switch']['close_ace_business'] == 1 ? 'checked' : ''?>>
                        关闭ACE业务(close_ace_business)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>



            <div class="form-group">
                <div style="text-align: center">
                    <button type="button" class="btn btn-danger" style="margin-left: 0;min-width: 100px">
                        <i class="fa fa-check"></i>
                        <?php echo 'Submit' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('.btn-danger').click(function () {
        $('.form-horizontal').submit();
    })

</script>