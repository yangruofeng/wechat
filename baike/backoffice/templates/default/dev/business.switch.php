<!--<script src="--><?php //echo GLOBAL_RESOURCE_SITE_URL; ?><!--/js/jquery.validation.min.js"></script>-->
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Basic</h3>
<!--            <ul class="tab-base">-->
<!--                <li><a class="current"><span>basic</span></a></li>-->
<!--            </ul>-->
        </div>
    </div>
    <div class="container" style="width: 600px">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Free Credit For Register'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="free_credit_for_register" value="1" <?php echo $output['business_switch']['free_credit_for_register'] == 1 ? 'checked' : ''?>>
                        注册送信用(free_credit_for_register）
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'fix repayment day for one member'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="is_fix_loan_repayment_day" value="1" <?php echo $output['business_switch']['is_fix_loan_repayment_day'] == 1 ? 'checked' : ''?>>
                        固定还款日期给同一个客户(is_fix_loan_repayment_day)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Use grant interest for credit-loan'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="is_loan_use_credit_grant_interest" value="1" <?php echo $output['business_switch']['is_loan_use_credit_grant_interest'] == 1 ? 'checked' : ''?>>
                        启用使用授信约定的利率(is_loan_use_credit_grant_interest)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Require Approve Prepayment Request'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="require_approve_prepayment" value="1" <?php echo $output['business_switch']['require_approve_prepayment'] == 1 ? 'checked' : ''?>>
                        提前还款需要审批(require_approve_prepayment)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Require Approve Files uploaded by CO'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="approve_co_upload_assets" value="1" <?php echo $output['business_switch']['approve_co_upload_assets'] == 1 ? 'checked' : ''?>>
                        CO提交的资产资料需要Operator审批(approve_co_upload_assets)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Is Allow Auto Deduct From ACE'; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="is_auto_deduct_from_ace" value="1" <?php echo $output['business_switch']['is_auto_deduct_from_ace'] == 1 ? 'checked' : ''?>>
                        是否允许自动从ACE扣款(is_auto_deduct_from_ace)
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Is Need Lock Client Balance '; ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="is_need_lock_client_balance" value="1" <?php echo $output['business_switch']['is_need_lock_client_balance'] == 1 ? 'checked' : ''?>>
                        是否需要锁定账户金额(is_need_lock_client_balance)
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