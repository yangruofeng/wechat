<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Change Password</h3>
        </div>
    </div>
    <div class="container" style="width: 500px;">
        <form class="form-horizontal" style="margin-top: 15px">
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
            <div class="form-group">
                <div class="col-sm-offset-4 col-col-sm-8" style="padding-left: 15px">
                    <button type="button" class="btn btn-danger"><?php echo 'Submit' ?></button>
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

        var values = $('.form-horizontal').getValues();
        yo.loadData({
            _c: 'user',
            _m: 'apiChangePassword',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
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