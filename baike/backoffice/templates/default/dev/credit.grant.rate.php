<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>

<?php $credit_rate = $output['credit_rate']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Set Credit Grant Rate</h3>
            <ul class="tab-base">
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Default credit rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="default_credit_rate" value="<?php echo $credit_rate['default_credit_rate'];?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Land mortgage credit rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="land_credit_rate" value="<?php echo $credit_rate['land_credit_rate'];?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Housing & store mortgage credit rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="house_credit_rate" value="<?php echo $credit_rate['house_credit_rate'];?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Motorbike mortgage credit rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="motorbike_credit_rate" value="<?php echo $credit_rate['motorbike_credit_rate'];?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Car mortgage credit rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="car_credit_rate" value="<?php echo $credit_rate['car_credit_rate'];?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Store mortgage credit rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="store_credit_rate" value="<?php echo $credit_rate['store_credit_rate'];?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Default Credit Months' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="default_terms" value="<?php echo $credit_rate['default_terms'];?>">
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Max Credit Months' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="default_max_terms" value="<?php echo $credit_rate['default_max_terms'];?>">
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Credit and salary income rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="default_salary_rate" value="<?php echo $credit_rate['default_salary_rate'];?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Credit and rental income rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="default_rental_rate" value="<?php echo $credit_rate['default_rental_rate'];?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Credit and attachment income rate' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="number" class="form-control" name="default_attachment_rate" value="<?php echo $credit_rate['default_attachment_rate'];?>">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span><?php echo 'Allow Operator Submit Credit-Request' ?></label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="checkbox" class="form-control" name="allow_operator_submit_to_hq" <?php if($credit_rate['allow_operator_submit_to_hq']) echo 'checked'?> value="1">
                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">允许operator提交信用申请给hq</span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
<!--            <div class="form-group">-->
<!--                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span>--><?php //echo 'Interest with mortgage(monthly)' ?><!--</label>-->
<!--                <div class="col-sm-6">-->
<!--                    <div class="input-group">-->
<!--                        <input type="number" class="form-control" name="interest_with_mortgage" value="--><?php //echo $credit_rate['interest_with_mortgage'];?><!--">-->
<!--                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>-->
<!--                    </div>-->
<!--                    <div class="error_msg"></div>-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--            <div class="form-group">-->
<!--                <label for="inputEmail3" class="col-sm-6 control-label"><span class="required-options-xing">*</span>--><?php //echo 'Interest without mortgage(monthly)' ?><!--</label>-->
<!--                <div class="col-sm-6">-->
<!--                    <div class="input-group">-->
<!--                        <input type="number" class="form-control" name="interest_without_mortgage" value="--><?php //echo $credit_rate['interest_without_mortgage'];?><!--">-->
<!--                        <span class="input-group-addon" style="min-width: 60px;border-left: 0;font-weight: 700">%</span>-->
<!--                    </div>-->
<!--                    <div class="error_msg"></div>-->
<!--                </div>-->
<!--            </div>-->

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
            default_credit_rate: {
                required: true,
                min:0,
                max:100
            },
            land_credit_rate: {
                required: true,
                min:0,
                max:100
            },
            house_credit_rate: {
                required: true,
                min:0,
                max:100
            },
            motorbike_credit_rate: {
                required: true,
                min:0,
                max:100
            },
            car_credit_rate: {
                required: true,
                min:0,
                max:100
            },
            interest_with_mortgage: {
                required: true,
                min:0,
                max:100
            },
            interest_without_mortgage: {
                required: true,
                min:0,
                max:100
            }
        },
        messages: {
            default_credit_rate: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>",
                max: "<?php echo 'It can\'t be more than 100!'?>"
            },
            land_credit_rate: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>",
                max: "<?php echo 'It can\'t be more than 100!'?>"
            },
            house_credit_rate: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>",
                max: "<?php echo 'It can\'t be more than 100!'?>"
            },
            motorbike_credit_rate: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>",
                max: "<?php echo 'It can\'t be more than 100!'?>"
            },
            car_credit_rate: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>",
                max: "<?php echo 'It can\'t be more than 100!'?>"
            },
            interest_with_mortgage: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>",
                max: "<?php echo 'It can\'t be more than 100!'?>"
            },
            interest_without_mortgage: {
                required: '<?php echo 'Required!'?>',
                min: "<?php echo 'It can\'t be less than 0!'?>",
                max: "<?php echo 'It can\'t be more than 100!'?>"
            }
        }
    });
</script>