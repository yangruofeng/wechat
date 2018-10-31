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
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close password reset' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_reset_password" value="1" <?php echo $output['function_switch']['close_reset_password'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close credit loan withdraw' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_credit_withdraw" value="1" <?php echo $output['function_switch']['close_credit_withdraw'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Close register to send credit'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="close_register_send_credit" value="1" <?php echo $output['function_switch']['close_register_send_credit'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Open passbook trading by bank'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="open_passbook_trading_by_bank" value="1" <?php echo $output['function_switch']['open_passbook_trading_by_bank'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Is fix member loan repayment day'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="is_fix_loan_repayment_day" value="1" <?php echo $output['function_switch']['is_fix_loan_repayment_day'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Is loan use credit grant interest'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="is_loan_use_credit_grant_interest" value="1" <?php echo $output['function_switch']['is_fix_loan_repayment_day'] == 1 ? 'checked' : ''?>>
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