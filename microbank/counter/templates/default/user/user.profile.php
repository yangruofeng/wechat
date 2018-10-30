<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Profile</h3>
        </div>
    </div>
    <div class="container" style="width: 500px;">
        <form class="form-horizontal" style="margin-top: 15px">
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'User Code'?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" value="<?php echo $output['user_info']['user_code']?>" disabled>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'User Name'?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="user_name" value="<?php echo $output['user_info']['user_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Department'?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" value="<?php echo $output['user_info']['branch_name'] . ($output['user_info']['depart_name'] ? '(' . $output['user_info']['depart_name'] . ')' : '')?>" disabled>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Mobile Phone'?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="mobile_phone" value="<?php echo $output['user_info']['mobile_phone']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Email'?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="email" value="<?php echo $output['user_info']['email']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Avatar'?></label>
                <div class="col-sm-8">
                    <a href="<?php echo getUrl('user','userIcon', array(), false, ENTRY_COUNTER_SITE_URL)?>" title="Setting Avatar">
                        <img src="<?php echo getUserIcon($output['user_info']['user_icon'])?>" style="max-height: 70px;cursor: pointer">
                    </a>
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
            _m: 'updateProfile',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $('#profile-messages .user_name', window.parent.document).html($.trim(values.user_name));
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
            user_name : {
                required : true
            },
            email : {
                email : true
            }
        },
        messages : {
            user_name : {
                required : '<?php echo 'Required'?>'
            },
            email : {
                email : '<?php echo 'Please enter a correct email address!'?>'
            }
        }
    });

</script>