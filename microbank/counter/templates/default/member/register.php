<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/ueditor/utf8-php/ueditor.config.js'?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/ueditor/utf8-php/ueditor.all.js'?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/js/common.js'?>"></script>
<style>
    .width458{
        width: 458px;
    }

    .industry-label {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
    }
</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <?php $work_type_lang=enum_langClass::getWorkTypeEnumLang();?>
    <div class="container">
        <div class="register-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Basic Information</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="basic-info">
                        <input type="hidden" name="member_image" value="">
                        <input type="hidden" name="verify_id" value="">
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Mobile Phone</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                     <span class="input-group-addon" style="padding: 0;border: 0;">
                                        <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                            <option value="855">+855</option>
                                            <option value="66">+66</option>
                                            <option value="86">+86</option>
                                        </select>
                                     </span>
                                    <input type="text" class="form-control" name="phone" value="">
                                    <span class="input-group-addon" style="padding: 0;border: 0;" >
                                        <a class="btn btn-default" id="btnSendCode" style="width: 60px;height: 30px;padding:5px 12px;border-radius: 0" onclick="send_verify_code()">Send</a>
                                    </span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Login Password</label>
                            <div class="col-sm-7">
                                <a class="form-control authorize_input btn btn_default" onclick="LoginPassword()" >Client Input</a>
                                <img id="loginPassword" src="resource/img/member/verify-1.png" style="width:26px;position: absolute;top: 4px;right: 20px">
                                <img id="loginPasswordDone" src="resource/img/member/verify-2.png" style="display: none;width: 26px;position: absolute;top: 4px;right: 20px">
                                <input type="hidden" name="login_password" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Verify Code</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="verify_code" value="" maxlength="4">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Confirm Login Password</label>
                            <div class="col-sm-7">
                                <a class="form-control authorize_input btn btn_default" onclick="confirmLoginPassword()" >Client Input</a>
                                <img id="loginPasswordConfirm" src="resource/img/member/verify-1.png" style="width:26px;position: absolute;top: 4px;right: 20px">
                                <img id="loginPasswordDoneConfirm" src="resource/img/member/verify-2.png" style="display: none;width: 26px;position: absolute;top: 4px;right: 20px">
                                <input type="hidden" name="confirm_login_password" value="" confirm_name="login_password">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Login Account</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="login_account" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Trading Password</label>
                            <div class="col-sm-7">
                                <a class="form-control authorize_input btn btn_default" onclick="tradePassword()" >Client Input</a>
                                <img id="tradePassword" src="resource/img/member/verify-1.png" style="width:26px;position: absolute;top: 4px;right: 20px">
                                <img id="tradePasswordDone" src="resource/img/member/verify-2.png" style="display: none;width: 26px;position: absolute;top: 4px;right: 20px">
                                <input type="hidden" name="trading_password" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Work Type</label>
                            <div class="col-sm-8">
                                <select name="work_type" class="form-control">
                                    <?php foreach ($output['work_type'] as $key => $type) {?>
                                        <option value="<?php echo $key?>" <?php echo $key == $client_info['work_type'] ? 'selected' : ''?>><?php echo $work_type_lang[$key]?></option>
                                    <?php } ?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Confirm Trading Password</label>
                            <div class="col-sm-7">
                                <a class="form-control authorize_input btn btn_default" onclick="confirmTradePassword()" >Client Input</a>
                                <img id="tradePasswordConfirm" src="resource/img/member/verify-1.png" style="width:26px;position: absolute;top: 4px;right: 20px">
                                <img id="tradePasswordDoneConfirm" src="resource/img/member/verify-2.png" style="display: none;width: 26px;position: absolute;top: 4px;right: 20px">
                                <input type="hidden" name="confirm_trading_password" value="" confirm_name="trading_password">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label">Own Business</label>
                            <div class="col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_with_business" value='1' id="CkOwnBusiness"> Yes
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group" style="margin-bottom: 20px">
                            <label for="inputEmail3" class="col-sm-5 control-label"><span class="required-options-xing">*</span>Marital Status</label>
                            <div class="col-sm-7">
                                <label class="radio-inline">
                                    <input type="radio" name="civil_status" value="married" checked>Married
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="civil_status" value="single">Single
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="civil_status" value="divorce">Divorce
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group" id="ownIndustry" style="display: none;">
                            <label class="col-sm-4 control-label">Industry</label>
                            <div class="col-sm-8">
                                <?php foreach ($output['industry'] as $value){?>
                                    <div class="checkbox col-sm-4" style="padding-left:0px;padding-right:3px;">
                                        <label title="<?php echo $value['industry_name']; ?>" class="industry-label">
                                            <input type="checkbox" name="member_industry" value="<?php echo $value['uid'] ?>"> <?php echo $value['industry_name']; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label id="addressTitle" for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Residence Address</label>
                            <div id="addressBox" class="col-sm-8">
                                <div class="col-sm-12" id="select_area" style="padding: 0px!important;">

                                </div>
                                <div class="col-sm-12 " style="padding: 0px!important;">
                                    <input type="text" class="form-control" name="address_detail" placeholder="Detailed Address" value="">
                                    <input type="hidden" name="address_region" value="">
                                </div>

                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="scene-photo">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Scene Photo</h5>
                </div>
                <div class="content">
                    <div class="snapshot_div" onclick="callWin_snapshot_slave();">
                        <img id="img_slave" src="resource/img/member/photo.png">
                    </div>
                    <div class="snapshot_msg error_msg" style="margin-left: 15px;float: left;background-color: #FFF"></div>
                </div>
            </div>
            <div class="operation">
                <button class="btn btn-default" onclick="reset_form()">Reset</button>
                <button class="btn btn-primary" onclick="submit_form()">Submit</button>
            </div>
        </div>
    </div>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    var InterValObj; //timer变量，控制时间
    var count = 30; //间隔函数，1秒执行
    var curCount;//当前剩余秒数

    function callWin_snapshot_slave() {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $("#img_slave").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('input[name="member_image"]').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);

            }
        }
    }

    function send_verify_code() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('input[name="phone"]').val();
        phone = $.trim(phone);
        if (!phone) {
            return;
        }

        curCount = count;
        $("#btnSendCode").attr("disabled", "true");
        $("#btnSendCode").html(curCount + "S");
        InterValObj = window.setInterval(SetRemainTime, 1000);

        yo.loadData({
            _c: "member",
            _m: "sendVerifyCodeForRegister",
            param: {country_code: country_code, phone: phone},
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


    function LoginPassword() {
        var login_password_input = window.external.inputPassword();
        var login_password=login_password_input.split("|");
        if(login_password[1] =='6'){
            $("input[name='login_password']").val(login_password[0]);
            $('#loginPassword').hide();
            $('#loginPasswordDone').show();
        }else{
            alert('The password must be 6 digits ');
        }

    }

    function confirmLoginPassword() {
         var confirm_login_password = window.external.inputPasswordAgain();
         if(confirm_login_password ==  $("input[name='login_password']").val()){
             $("input[name='confirm_login_password']").val(confirm_login_password);
             $('#loginPasswordConfirm').hide();
             $('#loginPasswordDoneConfirm').show();
         }else{
             alert('Confirm password error');
         }

    }

    function tradePassword() {
        var trading_password_input = window.external.inputPassword();
        var trading_password=trading_password_input.split("|");
        if(trading_password[1] == '6'){
            $("input[name='trading_password']").val(trading_password[0]);
            $('#tradePassword').hide();
            $('#tradePasswordDone').show();
        }else{
            alert('The password must be 6 digits ');
        }

    }

    function confirmTradePassword() {
        var confirm_trading_password = window.external.inputPasswordAgain();
        if(confirm_trading_password == $("input[name='trading_password']").val()){
            $("input[name='confirm_trading_password']").val(confirm_trading_password);
            $('#tradePasswordConfirm').hide();
            $('#tradePasswordDoneConfirm').show();
        }else{
            alert('Confirm password error');
        }

    }


    function reset_form() {
        window.location.reload()
    }

    function submit_form() {
        if (!$("#basic-info").valid()) {
            return;
        }

        var values = getFormJson('#basic-info');
        yo.loadData({
            _c: 'member',
            _m: 'registerClient',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.reload()
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#CkOwnBusiness').click(function(){
        this.checked ? $('#ownIndustry').show() : $('#ownIndustry').hide();
        this.checked ? $('#addressTitle').addClass("col-sm-5").removeClass("col-sm-4") : $('#addressTitle').removeClass("col-sm-5").addClass("col-sm-4");
        this.checked ? $('#addressBox').addClass("col-sm-7") : $('#addressBox').removeClass("col-sm-7");
    });

    $('#basic-info').validate({
        errorPlacement: function(error, element){
            if ($(element).attr('name') == 'member_image') {
                error.appendTo($('.snapshot_msg'));
            } else {
                error.appendTo(element.closest('.form-group').find('.error_msg'));
            }

        },
        rules : {
            phone : {
                required : true
            },
            verify_code : {
                required : true
            },
            login_account : {
                required : true,
                chkAccount : true
            },
            member_image : {
                required : true
            },
            login_password : {
                required : true
            },
            confirm_login_password : {
                required : true
            },
            trading_password : {
                required : true
            },
            confirm_trading_password : {
                required : true
            }
        },
        messages : {
            phone : {
                required : 'Required'
            },
            verify_code : {
                required : 'Required'
            },
            login_account : {
                required : 'Required',
                chkAccount : 'Account number one must be the letter, and the length is between 5 and 12.'
            },
            member_image : {
                required : 'Required'
            },
            login_password : {
                required : 'Required'
            },
            confirm_login_password : {
                required : 'Required'
            },
            trading_password : {
                required : 'Required'
            },
            confirm_trading_password : {
                required : 'Required'
            }
        }
    });

    jQuery.validator.addMethod("chkAccount", function (value, element) {
        value = $.trim(value);
        if (!/^[a-zA-z][A-Za-z0-9]{4,11}$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });

//    jQuery.validator.addMethod("checkPwd", function (value, element) {
//        value = $.trim(value);
//        if (!/^[A-Za-z0-9]{6,18}$/.test(value)) {
//            return false;
//        } else {
//            return true;
//        }
//    });

//    jQuery.validator.addMethod("verifyPwd", function (value, element) {
//        var confirm_name = $(element).attr('confirm_name');
//        var new_password = $.trim($('input[name="' + confirm_name + '"]').val());
//        value = $.trim(value);
//        if (new_password == value) {
//            return true;
//        } else {
//            return false;
//        }
//    });


    getArea(0);
    $('#select_area').delegate('select', 'change', function () {
        var _value = $(this).val();
        $('input[name="address_id"]').val(_value);
        $(this).closest('div').nextAll().remove();

        if (_value != 0 && $(this).find('option[value="' + _value + '"]').attr('is-leaf') != 1) {
            getArea(_value);
        }
    })

    function getArea(uid) {
        yo.dynamicTpl({
            tpl: "member/area2.list",
            control:"counter_base",
            dynamic: {
                api: "member",
                method: "getAreaList",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $("#select_area").append(_tpl);
            }
        })
    }

</script>