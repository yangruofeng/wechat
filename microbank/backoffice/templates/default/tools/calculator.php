<style>
    .calculator_td {
        min-width: 450px;
        padding-right: 65px;
        padding-top: 30px;
    }

    .arrow-right {
        margin-left: -17px;
        background: #e9ecf3;
        z-index: 10;
        position: absolute;
        top: 150px;
    }

    .arrow-td {
        width: 70px;
        border-left: 2px solid #CCC;
    }

    .result-td {
        padding-top: 30px;
        vertical-align: top;
        min-width: 450px;
        font-size: 20px;
    }

    .result-td h4 {
        margin-bottom: 25px;
    }

    .result-td .form-group {
        height: 25px;
        line-height: 25px;
        display: none;
    }

    .result-td .form-group label {
        font-size: 16px;
        font-weight: 500;
        text-align: right;
    }

    .result-td .form-group div {
        font-weight: 500;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Calculator</h3>
        </div>
    </div>
    <div class="container" style="margin-top: 45px">
        <table>
            <tr>
                <td class="calculator_td">
                    <form class="form-horizontal" id="calculator_form">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Loan Product'?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="product_id">
                                    <option value="0"><?php echo $lang['common_select']?></option>
                                    <?php foreach ($output['valid_products'] as $key => $val) { ?>
                                        <option value="<?php echo $val['uid']?>"><?php echo $val['sub_product_name']?></option>
                                    <?php } ?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <!--<div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span><?php /*echo $output['mortgage_type']['name']*/?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="mortgage_type">
                                    <option value="0"><?php /*echo $lang['common_select']*/?></option>
                                    <?php /*foreach ($output['mortgage_type']['item_list'] as $key => $val) { */?>
                                        <option value="<?php /*echo $key*/?>"><?php /*echo $val*/?></option>
                                    <?php /*} */?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->

                        <!--<div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span><?php /*echo 'Guarantee Type'*/?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="guarantee_type">
                                    <option value="0"><?php /*echo $lang['common_select']*/?></option>
                                    <?php /*foreach ($output['guarantee_type']['item_list'] as $key => $val) { */?>
                                        <option value="<?php /*echo $key*/?>"><?php /*echo $val*/?></option>
                                    <?php /*} */?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        -->

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Loan Amount'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%;">
                                    <input style="display: inline-block;width: 60%;" type="number" class="form-control" required="true" name="amount" value="10000">
                                    <select style="display: inline-block;width: 40%;"  name="currency" id="" class="form-control">
                                        <?php foreach( (new currencyEnum())->toArray() as $currency ){ ?>
                                            <option value="<?php echo $currency ?>"><?php echo $currency ?></option>
                                        <?php } ?>
                                    </select>
                                    <!--<span class="input-group-addon" style="min-width: 60px;border-left: 0">$</span>
                                -->
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>


                       <!-- <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php /*echo 'Repayment Method'*/?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="repayment_type">
                                    <option value="0"><?php /*echo $lang['common_select']*/?></option>
                                    <?php /*foreach ($output['interest_payment'] as $key => $val) { */?>
                                        <option value="<?php /*echo $key*/?>" <?php /*if($key == interestPaymentEnum::ANNUITY_SCHEME) echo 'selected'; */?> ><?php /*echo $lang['enum_' . $key]*/?></option>
                                    <?php /*} */?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->


                        <!--<div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php /*echo 'Repayment Frequency'*/?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="repayment_period">
                                    <option value="0"><?php /*echo $lang['common_select']*/?></option>
                                    <?php /*foreach ($output['interest_rate_period'] as $key => $val) { */?>
                                        <option value="<?php /*echo $key*/?>" <?php /*if($key == interestRatePeriodEnum::MONTHLY) echo 'selected'; */?> ><?php /*echo $lang['enum_' . $key]*/?></option>
                                    <?php /*} */?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Loan Period'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%;">
                                    <input style="width: 60%;" type="number" class="form-control " required="true" name="loan_period" value="1">
                                    <!--<span class="input-group-addon" style="min-width: 60px;border-left: 0">Months</span>-->
                                    <select style="width: 40%;" name="loan_period_unit" class="form-control " required="true">
                                        <option value="<?php echo loanPeriodUnitEnum::YEAR; ?>" selected >Year</option>
                                        <option value="<?php echo loanPeriodUnitEnum::MONTH; ?>">Month</option>
                                        <option value="<?php echo loanPeriodUnitEnum::DAY; ?>">Day</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button type="button" class="btn btn-danger" style="min-width: 80px">
                                    <i class="fa fa-check"></i>
                                    Calculate
                                </button>
                                <button type="button" class="btn btn-default" style="min-width: 80px">
                                    <i class="fa fa-close"></i>
                                    Clear
                                </button>
                            </div>
                        </div>
                    </form>
                </td>
                <td class="arrow-td">
                    <div class="arrow-right">
                        <img src="<?php echo BACK_OFFICE_SITE_URL?>/resource/image/arrow-right.png">
                    </div>
                </td>
                <td class="result-td">
                    <div>
                        <h4>Calculate Result</h4>
                        <div class="form-group clearfix loan_amount">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Loan Amount : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group clearfix repayment_amount">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Repayment Amount : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group clearfix arrival_amount">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Arrival Amount : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group clearfix service_charge">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Service Charge : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group clearfix interest_rate">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Interest Rate : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group clearfix operation_fee">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Operation Fee : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group clearfix total_interest">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Total Interest : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group clearfix single_repayment">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Single Repayment : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group clearfix each_repayment">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Each Repayment : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group clearfix first_repayment">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'First Repayment : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group clearfix repayment_number">
                            <label for="inputEmail3" class="col-sm-6 control-label"><?php echo 'Repayment periods : '?></label>
                            <div class="col-sm-6"></div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script>
    $('select[name="repayment_type"]').change(function () {
        var _val = $(this).val();
        if (_val == 'single_repayment') {
            $('select[name="repayment_period"]').closest('.form-group').hide();
        } else {
            $('select[name="repayment_period"]').closest('.form-group').show();
        }
    })

    $('.btn-default').click(function () {
        $("#calculator_form input").val('');
        $('#calculator_form select').prop('selectedIndex', 0);
        $('.result-td .form-group').hide();
    })

    $('.btn-danger').click(function () {
        var is_check = true;
        var repayment_type = $('select[name="repayment_type"]').val();
        var repayment_period = $('select[name="repayment_period"]').val();
        if (repayment_type != 'single_repayment' && repayment_period == 0) {
            $('select[name="repayment_period"]').next().html('<label>Required!</label>');
            is_check = false;
        } else {
            $('select[name="repayment_period"]').next().html('');
        }
        if (!$("#calculator_form").valid()) {
            is_check = false;
        }

        if (!is_check) {
            return;
        }

        var values = $('#calculator_form').getValues();

        yo.loadData({
            _c: 'home',
            _m: 'loanPreview',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $('.loan_amount').show().find('div').html(data.loan_amount);
                    $('.repayment_amount').show().find('div').html(data.repayment_amount);
                    $('.arrival_amount').show().find('div').html(data.arrival_amount);
                    $('.service_charge').show().find('div').html(data.service_charge);
                    $('.total_interest').show().find('div').html(data.total_interest);
                    $('.interest_rate').show().find('div').html(data.interest_rate+'('+data.interest_rate_unit+')');
                    $('.operation_fee').show().find('div').html(data.operation_fee);
                    if (data.repayment_number > 1) {
                        $('.repayment_number').show().find('div').html(data.repayment_number);
                    }  else {
                        $('.repayment_number').hide()
                    }
                    if (data.single_repayment != 0) {
                        $('.single_repayment').show().find('div').html(data.single_repayment);
                    } else {
                        $('.single_repayment').hide()
                    }
                    if (data.each_repayment != 0) {
                        $('.each_repayment').show().find('div').html(data.each_repayment);
                    } else {
                        $('.each_repayment').hide()
                    }
                    if (data.first_repayment != 0) {
                        $('.first_repayment').show().find('div').html(data.first_repayment);
                    } else {
                        $('.first_repayment').hide();
                    }
                } else {
                    alert(_o.MSG);
                }
            }
        });
    })

    $('#calculator_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            product_id: {
                checkSelect: true
            },
//            mortgage_type: {
//                checkSelect: true
//            },
//            guarantee_type: {
//                checkSelect: true
//            },
            amount: {
                required: true
            }
           /* repayment_type: {
                checkSelect: true
            },
            loan_period: {
                required: true
            }*/
        },
        messages: {
            product_id: {
                checkSelect: '<?php echo 'Required!'?>'
            },
//            mortgage_type: {
//                checkSelect: '<?php //echo 'Required!'?>//'
//            },
//            guarantee_type: {
//                checkSelect: '<?php //echo 'Required!'?>//'
//            },
            amount: {
                required: '<?php echo 'Required!'?>'
            },
            repayment_type: {
                checkSelect: '<?php echo 'Required!'?>'
            },
            loan_period: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

    jQuery.validator.addMethod("checkSelect", function (value, element) {
        if (value == 0) {
            return false;
        } else {
            return true;
        }
    });
</script>
