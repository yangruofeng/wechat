<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>

<?php $credit_rate = $output['fee_rate']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Authorized Contract Fee Rate</h3>
            <ul class="tab-base">
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'First sign contract rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="first_sign_contract_rate_value" value="<?php echo $credit_rate['first_sign_contract_rate_value'];?>" style="width: 200px;">
                        <select name="first_sign_contract_rate_type" id="" class="form-control valid" style="width: 60px;">
                            <option value="0" <?php if( $credit_rate['first_sign_contract_rate_type'] == 0 ){ echo 'selected';} ?> >%</option>
                            <option value="1" <?php if( $credit_rate['first_sign_contract_rate_type'] == 1 ){ echo 'selected';} ?> >$</option>
                        </select>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Min first sign contract fee' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="min_first_sign_contract_fee" value="<?php echo $credit_rate['min_first_sign_contract_fee'];?>" style="width: 260px;">

                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Follow sign contract rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="follow_sign_contract_rate_value" value="<?php echo $credit_rate['follow_sign_contract_rate_value'];?>" style="width: 200px;">
                        <select name="follow_sign_contract_rate_type" id="" class="form-control valid" style="width: 60px;">
                            <option value="0" <?php if( $credit_rate['follow_sign_contract_rate_type'] == 0 ){ echo 'selected';} ?> >%</option>
                            <option value="1" <?php if( $credit_rate['follow_sign_contract_rate_type'] == 1 ){ echo 'selected';} ?> >$</option>
                        </select>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Min follow sign contract fee' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="min_follow_sign_contract_fee" value="<?php echo $credit_rate['min_follow_sign_contract_fee'];?>" style="width: 260px;">

                    </div>
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
    });

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            first_sign_contract_rate_value: {
                required: true
            },
            follow_sign_contract_rate_value: {
                required: true
            }
        },
        messages: {
            first_sign_contract_rate_value: {
                required: '<?php echo 'Required!'?>'
            },
            follow_sign_contract_rate_value: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>