<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .form-group{
        margin-bottom: 20px;
    }

    .collection-div, .register-div {
        width: 900px;
    }

    #form-change {
        margin-top: 20px;
    }

    .btn {
        border-radius: 0!important;
        min-width: 80px!important;
    }

</style>

<?php
$is_first_setting = $output['user']['trading_password']?0:1;

?>

<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="collection-div">
        <div class="register-div" style="margin-top: 20px">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Setting Trading Password</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="form-change" action="<?php echo getUrl('user', 'verifyChangeTradePwd', array(), false, ENTRY_COUNTER_SITE_URL) ?>"
                          method="post">
                        <input type="hidden" id="verify_id" name="verify_id" value="">
                        <?php if( !$is_first_setting ){ ?>
                            <div class="col-sm-6 form-group">
                                <label class="col-sm-5 control-label"><?php echo 'Last Setting Time:'?></label>
                                <div class="col-sm-7">
                                    <input  type="text" id="trading_time" class="form-control" placeholder="" value="<?php echo $output['user']['trading_pwd_update_time'] ?>" readonly>
                                </div>
                            </div>
                        <?php } else{ ?>
                            <div class="col-sm-6 form-group">
                                <label class="col-sm-5 control-label"><?php echo 'Previous Password'?></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" placeholder="" value="Not set yet" readonly>
                                </div>
                            </div>
                        <?php }?>

                        <input type="hidden" name="is_first_set" value="<?php echo $is_first_setting; ?>">

                       <!-- <div class="col-sm-6 form-group" style="margin-bottom: 25px!important;">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Setting Method</label>
                            <div class="col-sm-7">
                                <div class="input-group">
                                    <label class="radio-inline"><input type="radio" name="set_method" value="phone" checked onclick="setMethod()">Phone</label>
                                    <label class="radio-inline"><input type="radio" name="set_method" value="password" onclick="setMethod()">Old Password</label>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->
                        <input type="hidden" name="set_method" value="password">

                       <!-- <div class="col-sm-6 form-group" id="by_phone">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Verify Code</label>
                            <div class="col-sm-7">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="verify_code" value="" maxlength="4">
                                    <span class="input-group-addon" style="padding: 0;border: 0;" >
                                    <a class="btn btn-default" id="btnSendCode" style="width: 60px;height: 30px;padding:5px 12px;border-radius: 0" onclick="send_verify_code()">Send</a>
                                </span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->


                        <?php if( $is_first_setting ){ ?>
                            <div class="col-sm-6 form-group" id="by_password" style="display: block">
                                <label class="col-sm-5 control-label" style="padding-left: 0!important;"><span class="required-options-xing">*</span><?php echo 'Login Password'?></label>
                                <div class="col-sm-7">
                                    <input type="password" class="form-control" name="old_pwd" placeholder="" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        <?php }else{ ?>
                            <div class="col-sm-6 form-group" id="by_password" style="display: block">
                                <label class="col-sm-5 control-label" style="padding-left: 0!important;"><span class="required-options-xing">*</span><?php echo 'Old Trading Password'?></label>
                                <div class="col-sm-7">
                                    <input type="password" class="form-control" name="old_pwd" placeholder="" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        <?php } ?>




                        <div class="col-sm-6 form-group">
                            <label class="col-sm-5 control-label" style="padding-left: 0!important;"><span class="required-options-xing">*</span><?php echo 'New Tradding Password'?></label>
                            <div class="col-sm-7">
                                <input type="password" class="form-control" id="new_pwd" name="new_pwd" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-5 control-label"><span class="required-options-xing">*</span><?php echo 'Confirm New Password'?></label>
                            <div class="col-sm-7">
                                <input type="password" class="form-control" name="verify_pwd" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label class="col-sm-5 control-label"><span class="required-options-xing">*</span><?php echo 'Hint'?></label>
                            <div class="col-sm-7">
                                <div class="hint">
                                    Trading password should be a six digit number.
                                    To change the trading password, you need to input the original password.
                                </div>
                                <?php if( !$is_first_setting ){  ?>
                                    <div>
                                        <a  class="btn btn-info" href="<?php echo getUrl('user', 'forgotTradingPassword', array(), false, ENTRY_COUNTER_SITE_URL) ?>">Forgot old Password</a>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                        <div class="col-sm-12 form-group" style="text-align: center;margin-top: 20px">
                            <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 10px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                            <button type="button" class="btn btn-danger"><?php echo 'Submit' ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>

    $(function () {
        var _old_password = '<?php echo $output['user']['trading_password']?>';
        if(!_old_password){
            $('input[value="password"]').attr('disabled',true)
        }
    });

    var InterValObj; //timer变量，控制时间
    var count = 30; //间隔函数，1秒执行
    var curCount;//当前剩余秒数

    function send_verify_code() {

        curCount = count;
        $("#btnSendCode").attr("disabled", "true");
        $("#btnSendCode").html(curCount + "S");
        InterValObj = window.setInterval(SetRemainTime, 1000);

        yo.loadData({
            _c: "user",
            _m: "sendVerifyCodeByUid",
            param: { },
            callback: function (_o) {
                if (_o.STS) {
                    $('input[name="verify_id"]').val(_o.DATA.verify_id);
                } else {
                    alert(_o.MSG);
                    window.clearInterval(InterValObj);//停止计时器
                    $("#btnSendCode").attr("disabled", false);//启用按钮
                    $("#btnSendCode").html("Send");
                }
            }
        });
    }

    function setMethod() {
        $('#by_phone').hide();
        $('#by_password').hide();
        var method = $('input[name="set_method"]:checked').val();
        if(method =='phone'){
            $('#by_phone').show();
            $('#by_password').hide();
        }
        if(method =='password'){
            $('#by_password').show();
            $('#by_phone').hide();
        }

    }

    function SetRemainTime() {
        if (curCount == 0) {
            window.clearInterval(InterValObj);//停止计时器
            $("#btnSendCode").attr("disabled", false);//启用按钮
            $("#btnSendCode").html("Send");
        } else {
            curCount--;
            $("#btnSendCode").html(curCount + "s");
        }
    }


    $('.btn-danger').click(function () {
        if (!$("#form-change").valid()) {
            return;
        }
        $("#form-change").submit();
    });

    $('#form-change').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            verify_code: {
                chkVerifyCode: true
            },
            old_pwd: {
                chkOldPwd: true
            },
            new_pwd: {
                required: true,
                checkPwd: true
            },
            verify_pwd: {
                required: true,
                verifyPwd: true
            }
        },
        messages: {
            verify_code: {
                chkVerifyCode: '<?php echo 'Required'?>'
            },
            old_pwd: {
                chkOldPwd: '<?php echo 'Required'?>'
            },
            new_pwd: {
                required: '<?php echo 'Required'?>',
                checkPwd: '<?php echo 'The password must be 6 digits not 123456!'?>'
            },
            verify_pwd: {
                required: '<?php echo 'Required'?>',
                verifyPwd: '<?php echo 'Verify password error!'?>'
            }
        }
    });

    jQuery.validator.addMethod("chkVerifyCode", function (value, element) {
        var method = $('input[name="set_method"]:checked').val();
        if (method == 'phone') {
            value = $.trim(value);
            if(value){
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("chkOldPwd", function (value, element) {
        var method = $('input[name="set_method"]:checked').val();
        if (method == 'password') {
            value = $.trim(value);
            if(value){
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("checkPwd", function (value, element) {
        value = $.trim(value);
        if (value.length != 6) {
            return false;
        }

        if (value == '123456') {
            return false;
        }

        if (!/^[0-9]{6}$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("verifyPwd", function (value, element) {
        var new_password = $.trim($("#new_pwd").val());
        value = $.trim(value);
        if (new_password == value) {
            return true;
        } else {
            return false;
        }
    });
</script>