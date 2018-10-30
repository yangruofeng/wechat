<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Global Settings</h3>
            <ul class="tab-base">
<!--                <li><a class="current"><span>Edit</span></a></li>-->
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Date format'?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 5px"><input type="radio" name="date_format" value="1" <?php echo $output['global_settings']['date_format'] != 2 ? 'checked' : ''?>><?php echo 'dd/mm/yyyy'?></label>
                    <label style="margin-top: 5px;margin-left: 10px"><input type="radio" name="date_format" value="2" <?php echo $output['global_settings']['date_format'] == 2 ? 'checked' : ''?>><?php echo 'yyyy-mm-dd'?></label>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Credit line(register)' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="credit_register" value="<?php echo $output['global_settings']['credit_register']?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">$</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Credit line(without approval)' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="credit_without_approval" value="<?php echo $output['global_settings']['credit_without_approval']?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">$</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Credit line(system limit)' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="credit_system_limit" value="<?php echo $output['global_settings']['credit_system_limit']?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">$</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <!--<div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php /*echo 'Withdrawal(single limit)' */?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="withdrawal_single_limit" value="<?php /*echo $output['global_settings']['withdrawal_single_limit']*/?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">$</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>-->

            <!--<div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php /*echo 'Withdrawal(monitor limit)' */?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="withdrawal_monitor_limit" value="<?php /*echo $output['global_settings']['withdrawal_monitor_limit']*/?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">$</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>-->

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Operator credits the maximum amount' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="operator_credit_maximum" value="<?php echo $output['global_settings']['operator_credit_maximum']?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">$</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Teller reduce penalty line' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="teller_reduce_penalty_maximum" value="<?php echo $output['global_settings']['teller_reduce_penalty_maximum']?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">$</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Member change phone number fee' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="member_change_phone_number_fee" value="<?php echo $output['global_settings']['member_change_phone_number_fee']?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">$</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Member change trading password fee' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="member_change_trading_password_fee" value="<?php echo $output['global_settings']['member_change_trading_password_fee']?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">$</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Must use trade password' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="is_trade_password" value="1" checked disabled <?php //echo $output['global_settings']['is_trade_password'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Automatically open the savings account' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" value="1" checked disabled>
                        <input type="hidden" name="is_create_savings_account" value="1">
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Counter access denied from all except client' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="counter_deny_without_client" value="1" <?php echo $output['global_settings']['counter_deny_without_client'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Back office access denied from all except client' ?></label>
                <div class="col-sm-6">
                    <label style="margin-top: 7px">
                        <input type="checkbox" name="backoffice_deny_without_client" value="1" <?php echo $output['global_settings']['backoffice_deny_without_client'] == 1 ? 'checked' : ''?>>
                    </label>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-6 col-sm-6">
                    <button type="button" class="btn btn-danger" style="margin-left: 0;min-width: 100px"><?php echo 'Submit' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            credit_register: {
                required: true,
                min:0
            },
            credit_without_approval: {
                required: true,
                min:0
            },
            credit_system_limit: {
                required: true,
                min:0
            },
            withdrawal_single_limit: {
                required: true,
                min:0
            },
            withdrawal_monitor_limit: {
                required: true,
                min:0
            },
            operator_credit_maximum: {
                required: true,
                min:0
            }
        },
        messages: {
            credit_register: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>"
            },
            credit_without_approval: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>"
            },
            credit_system_limit: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>"
            },
            withdrawal_single_limit: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>"
            },
            withdrawal_monitor_limit: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>"
            },
            operator_credit_maximum: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>"
            }
        }
    });
</script>