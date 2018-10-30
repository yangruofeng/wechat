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

    .title{
        text-align: center;
        margin-bottom:5px
    }
</style>
<?php $memberStateLang =  enum_langClass::getMemberStateLang() ?>

<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="collection-div" style="max-width: 800px">
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
                                <span class="show pull-left base-name marginright3">State</span>:
                                <span class="marginleft10" id="khmer-name"><?php echo $memberStateLang[$output['client_info']["member_state"]] ?></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Create Time</span>:
                                <span class="marginleft10" id="english-name"><?php echo timeFormat($output['client_info']['create_time'])?></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Id Number </span>:
                                <span class="marginleft10" id="english-name"><?php echo $output['client_info']['id_sn']?></span>
                            </p>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="register-div" style="margin-top: 20px;max-width: 800px">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Change Trade Password</h5>
                </div>
                <div class="content" style="padding-left: 60px">
                    <form class="form-horizontal" style="width: 700px" id="form-change" action="<?php echo getUrl('member', 'unLock', array(), false, ENTRY_COUNTER_SITE_URL) ?>"
                          method="post">
                        <input type="hidden" id="client_id" name="member_id" value="<?php echo $output['client_info']['uid']?>">
                        <input type="hidden" name="member_image" value="">
                        <input type="hidden" id="verify_id" name="verify_id" value="">
                        <div class="col-sm-6 form-group title" style="height: 80px!important;line-height: 80px">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'Scence Photo'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;">
                            <div class="snapshot_div" onclick="callWin_snapshot_slave();">
                                <img id="img_slave" src="resource/img/member/photo.png" style="width: 150px;height: 90px">
                            </div>
                            <div class="error_msg"></div>
                        </div>
                        <div class="col-sm-6 form-group">

                        </div>
                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;margin-bottom:20px">
                            <a class="form-control authorize_input btn btn_default" onclick="send_verify_code()" ><?php echo 'Send Verify Code'?></a>
                        </div>
                        <div class="col-sm-6 form-group title">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'input Verify Code'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;margin-bottom:5px">
                            <a class="form-control authorize_input btn btn_default" onclick="verifyCode()" ><?php echo 'Client Input'?></a>
                            <img id="verify_code" src="resource/img/member/verify-1.png" style="width:25px;position: absolute;top: 4px;right: 20px">
                            <img id="verify_codeDone" src="resource/img/member/verify-2.png" style="display: none;width: 25px;position: absolute;top: 4px;right: 20px">
                            <input type="hidden" name="verify_code" value="">
                            <div class="error_msg"></div>
                        </div>

                        <div class="col-sm-6 form-group" style="text-align: center;margin-top: 10px;margin-left: 10px!important">
                            <a  class="btn btn-primary col-sm-12"><?php echo 'Submit' ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="form-group button" style="text-align: center">
            <a  class="btn btn-default" style="min-width: 80px;margin-bottom: 40px;margin-top: 20px" href="<?php echo getUrl('member', 'profile', array('client_id'=>$output['client_info']['uid']), false, ENTRY_COUNTER_SITE_URL) ?>"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
        </div>
    </div>


</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script>
    function callWin_snapshot_slave() {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $('input[name="member_image"]').val(_img_path);
                    $("#img_slave").attr("src", getUPyunImgUrl(_img_path, "180x120"));

                }
            } catch (ex) {
                alert(ex.Message);

            }
        }
    }

    function send_verify_code() {
        var client_id = $('#client_id').val();
        if (!client_id) {
            return;
        }


        yo.loadData({
            _c: "member",
            _m: "sendVerifyCodeByUid",
            param: {client_id: client_id},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $('input[name="verify_id"]').val(_o.DATA.verify_id);
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }


    $('.btn-primary').click(function () {
        if (!$("#form-change").valid()) {
            return;
        }
        $("#form-change").submit();
    })

    function verifyCode() {
        var verify_code_input = window.external.inputVerifyCode();
        $("input[name='verify_code']").val(verify_code_input);
        $('#verify_code').hide();
        $('#verify_codeDone').show();
    }




    $('#form-change').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules: {
            verify_code: {
                required: true
            }
        },
        messages: {
            verify_code: {
                required: '<?php echo 'Required'?>'
            }
        }
    });




</script>