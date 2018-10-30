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

<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="collection-div">
        <div class="basic-info">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Basic Information</h5>
            </div>
            <div class="content">
                <div class="col-sm-7">
                    <dl class="account-basic clearfix">
                        <dt class="pull-left">
                            <p class="account-head">
                                <img id="member-icon" src="resource/img/member/bg-member.png" class="avatar-lg">
                            </p>
                        </dt>
                        <dd class="pull-left margin-large-left">
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Login Account</span>:
                                <span class="marginleft10" id="login-account"><?php echo $output['client_info']["login_code"]?></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Khmer Name</span>:
                                <span class="marginleft10" id="khmer-name"><?php echo $output['client_info']["kh_display_name"]?></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">English Name</span>:
                                <span class="marginleft10" id="english-name"><?php echo $output['client_info']['display_name']?></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Member Grade</span>:
                                <span class="marginleft10" id="member-grade"><?php echo $output['client_info']['grade_code']?:''?></span>
                            </p>
                        </dd>
                    </dl>
                </div>
                <div class="col-sm-5">
                    <dl class="account-basic clearfix">
                        <dt class="pull-left">
                        </dt>
                        <dd class="pull-left margin-large-left">
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Phone</span>:
                                <span class="marginleft10" id="login-account"><?php echo $output['client_info']["phone_id"]?></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Email</span>:
                                <span class="marginleft10" id="khmer-name"><?php echo $output['client_info']["email"]?></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Create Time</span>:
                                <span class="marginleft10" id="english-name"><?php echo timeFormat($output['client_info']['create_time'])?></span>
                            </p>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="register-div" style="margin-top: 20px">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Change Login Password</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="form-change" action="<?php echo getUrl('member', 'verifyChangeLoginPwd', array(), false, ENTRY_COUNTER_SITE_URL) ?>"
                          method="post">
                        <input type="hidden" id="client_id" name="client_id" value="<?php echo $output['client_info']['uid']?>">
                        <input type="hidden" id="verify_id" name="verify_id" value="">
                        <div class="col-sm-6 form-group">
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
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-5 control-label"><span class="required-options-xing">*</span><?php echo 'Old Login Password'?></label>
                            <div class="col-sm-7">
                                <input type="password" class="form-control" name="old_pwd" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-5 control-label"><span class="required-options-xing">*</span><?php echo 'New Login Password'?></label>
                            <div class="col-sm-7">
                                <input type="password" class="form-control" id="new_pwd" name="new_pwd" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-5 control-label"><span class="required-options-xing">*</span><?php echo 'Verify New Password'?></label>
                            <div class="col-sm-7">
                                <input type="password" class="form-control" name="verify_pwd" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-5 control-label"><span class="required-options-xing">*</span><?php echo 'Hint'?></label>
                            <div class="col-sm-7">
                                <div class="hint">To change the password, must enter the verification code or the original password.</div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group" style="text-align: center;margin-top: 20px">
                            <a class="btn btn-default" href="<?php echo getUrl('member', 'profile', array('client_id' => $output['client_info']['uid']), false, ENTRY_COUNTER_SITE_URL); ?>">Back</a>
                            <button type="button" class="btn btn-danger"><?php echo 'Submit' ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    var InterValObj; //timer变量，控制时间
    var count = 30; //间隔函数，1秒执行
    var curCount;//当前剩余秒数

    function send_verify_code() {
        var client_id = $('#client_id').val();
        if (!client_id) {
            return;
        }

        curCount = count;
        $("#btnSendCode").attr("disabled", "true");
        $("#btnSendCode").html(curCount + "S");
        InterValObj = window.setInterval(SetRemainTime, 1000);

        yo.loadData({
            _c: "member",
            _m: "sendVerifyCodeByUid",
            param: {client_id: client_id},
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
    })

    $('#form-change').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
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
            new_pwd: {
                required: '<?php echo 'Required'?>',
                checkPwd: '<?php echo 'The password must be 6-18 digits or letters!'?>'
            },
            verify_pwd: {
                required: '<?php echo 'Required'?>',
                verifyPwd: '<?php echo 'Verify password error!'?>'
            }
        }
    });

    jQuery.validator.addMethod("checkPwd", function (value, element) {
        value = $.trim(value);
        if (!/^[A-Za-z0-9]{6,18}$/.test(value)) {
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