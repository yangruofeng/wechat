<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<!--<div class="page">-->
<!--    <div class="fixed-bar">-->
<!--        <div class="item-title">-->
<!--            <h3>Change Password</h3>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="container" style="width: 500px;">-->
<!--        <form class="form-horizontal" style="margin-top: 15px">-->
<!--            <div class="form-group">-->
<!--                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>--><?php //echo 'Old Password'?><!--</label>-->
<!--                <div class="col-sm-8">-->
<!--                    <input type="password" class="form-control" name="old_password" placeholder="" value="">-->
<!--                    <div class="error_msg"></div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>--><?php //echo 'New Password'?><!--</label>-->
<!--                <div class="col-sm-8">-->
<!--                    <input type="password" class="form-control" name="new_password" placeholder="" value="">-->
<!--                    <div class="error_msg"></div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>--><?php //echo 'Verify Password'?><!--</label>-->
<!--                <div class="col-sm-8">-->
<!--                    <input type="password" class="form-control" name="verify_password" placeholder="" value="">-->
<!--                    <div class="error_msg"></div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <div class="col-sm-offset-4 col-col-sm-8" style="padding-left: 15px">-->
<!--                    <button type="button" class="btn btn-danger">--><?php //echo 'Submit' ?><!--</button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </form>-->
<!--    </div>-->
<!--</div>-->

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<style>
    .mortgage_type .col-sm-4 {
        margin-top: 7px;
        padding-left: 0px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    #select_area .col-sm-6:nth-child(2n+1) {
        padding-left: 0px;
        margin-bottom: 10px;
    }

    #select_area .col-sm-6:nth-child(2n) {
        padding-right: 0px;
        margin-bottom: 10px;
    }



    .container{
        margin-top: 30px;
        width: 600px !important;
        margin-left: 60px;
    }

    .content{
        padding-top: 25px;
    }

    .button{
        margin: 30px 270px;
        border-radius:0px !important;
    }

    .form-group{
        margin-bottom: 25px;
    }
    .control-label{
        font-size: 15px;
    }



</style>

<div class="page">
    <div class="container">
        <div class="register-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h4><i class="fa fa-id-card-o"></i>Reset Password</h4>
                </div>
                <div class="content">
                    <form class="form-horizontal" method="post">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Old Password'?></label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="old_password" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'New Password'?></label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="new_password" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Verify Password'?></label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="verify_password" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div><button type="button" class="btn btn-danger button"><?php echo 'Submit' ?></button></div>



</div>

<script>
    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        var values = $('.form-horizontal').getValues();
        yo.loadData({
            _c: 'user',
            _m: 'apiChangePassword',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert('Change Success!');
                    setTimeout(relogin,2000);
                    function relogin() {
                        window.location.href="<?php echo getUrl('login','login', array(), false, ENTRY_COUNTER_SITE_URL)?>"
                    }
                } else {
                    alert(_o.MSG);
                }
            }
        });
    })

    $('.form-horizontal').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.next());
        },
        rules : {
            old_password : {
                required : true
            },
            new_password : {
                required : true,
                checkPwd : true
            },
            verify_password : {
                required : true,
                verifyPwd : true
            }
        },
        messages : {
            old_password : {
                required : '<?php echo 'Required'?>'
            },
            new_password : {
                required : '<?php echo 'Required'?>',
                checkPwd : '<?php echo 'The password must be 6-18 digits or letters!'?>'
            },
            verify_password : {
                required : '<?php echo 'Required'?>',
                verifyPwd : '<?php echo 'Verify password error!'?>'
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
        var new_password = $.trim($('input[name="new_password"]').val());
        value = $.trim(value);
        if (new_password == value) {
            return true;
        } else {
            return false;
        }
    });

</script>