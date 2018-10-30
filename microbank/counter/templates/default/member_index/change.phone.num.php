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

    .numinput{
        width: 140px!important;
    }


    .codebox{
        width: 220px!important;
    }
</style>

<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="collection-div">
        <div class="basic-info">
            <?php include(template("widget/item.member.summary.v2"))?>
        </div>

        <div class="register-div" style="margin-top: 20px;">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Change Phone Number</h5>
                </div>
                <div class="content" style="padding-left: 60px">
                    <form class="form-horizontal" id="form-change" action="<?php echo getUrl('member_index', 'memberChangePhoneNum', array(), false, ENTRY_COUNTER_SITE_URL) ?>"
                          method="post">
                        <input type="hidden" id="client_id" name="member_id" value="<?php echo $output['client_info']['uid']?>">
                        <input type="hidden" name="member_image" value="<?php echo $client_info['member_scene_image']?>">
                        <input type="hidden" id="verify_id" name="verify_id" value="">
                        <div class="col-sm-6 form-group" style="text-align: center;height: 80px!important;line-height: 80px">
                            <label class="control-label"><?php echo 'Scene Photo'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;">
                            <div class="snapshot_div">
                                <img id="img_slave" src="<?php echo getImageUrl($client_info['member_scene_image'],imageThumbVersion::MAX_240)?>" style="width: 150px;height: 90px">
                            </div>
                            <div class="error_msg"></div>
                        </div>
                        <div class="col-sm-6 form-group" style="text-align: center;">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'New Phone Number'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="text-align: left;margin-left: 10px!important;">
                            <div class="input-group">
                                     <span class="input-group-addon" style="padding: 0;border: 0;">
                                        <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                            <option value="855">+855</option>
                                            <option value="66">+66</option>
                                            <option value="86">+86</option>
                                        </select>
                                     </span>
                                <input type="text" class="form-control" name="phone" value="">
                            </div>
                            <div class="error_msg"></div>

                        </div>
                        <div class="col-sm-6 form-group">

                        </div>
                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;margin-bottom:20px">
                            <a class="form-control authorize_input btn btn_default" onclick="send_verify_code()" ><?php echo 'Send Verify Code'?></a>
                        </div>
                        <div class="col-sm-6 form-group" style="text-align:center;margin-bottom:5px">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'input Verify Code'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;margin-bottom:5px">
                            <a class="form-control authorize_input btn btn_default" onclick="verifyCode()" ><?php echo 'Client Input'?></a>
                            <img id="verify_code" src="resource/img/member/verify-1.png" style="width:26px;position: absolute;top: 4px;right: 20px">
                            <img id="verify_codeDone" src="resource/img/member/verify-2.png" style="display: none;width: 26px;position: absolute;top: 4px;right: 20px">
                            <input type="hidden" name="verify_code" value="">
                            <div class="error_msg"></div>
                        </div>
                        <div class="col-sm-6 form-group" style="text-align: center;margin-bottom: 0px">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'Fee'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="text-align: left;margin-left: 12px!important;margin-bottom: 0px">
                            <label class="control-label"><?php echo ncPriceFormat($output['fee']) ?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="text-align: center;margin-bottom:5px">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'Receive Fee From'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="text-align: left;margin-left: 12px!important;font-size: 14px;margin-bottom:5px">
                            <input type="radio" name="feeMethod" value="<?php echo repaymentWayEnum::PASSBOOK ?>" checked> <label class="control-label"><?php echo 'Balance'?></label><br/>
                            <input type="radio" name="feeMethod" value="<?php echo repaymentWayEnum::CASH ?>"><label class="control-label"><?php echo 'Cash'?></label>
                        </div>
                        <div class="col-sm-6 form-group">

                        </div>
                        <div class="col-sm-6 form-group" style="text-align: center;margin-top: 10px;margin-left: 10px!important">
                            <a  class="btn btn-primary col-sm-12"><?php echo 'Submit' ?></a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="form-group button" style="text-align: center;">
                <a class="btn btn-default" style="min-width: 80px;margin-bottom: 40px;margin-top: 20px" href="<?php echo getUrl('member_index', 'index', array('member_id'=>$output['client_info']['uid']), false, ENTRY_COUNTER_SITE_URL) ?>"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
            </div>
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

    function verifyCode() {
        var verify_code_input = window.external.inputVerifyCode();
        $("input[name='verify_code']").val(verify_code_input);
        $('#verify_code').hide();
        $('#verify_codeDone').show();
    }

    function send_verify_code() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('input[name="phone"]').val();
        phone = $.trim(phone);
        if (!phone) {
            alert('Please input new phone number')
            return;
        }

        yo.loadData({
            _c: "member",
            _m: "sendVerifyCodeForChangePhone",
            param: {country_code: country_code, phone: phone},
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

    $('#form-change').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules: {
            verify_code: {
                required: true,
            }
        },
        messages: {
            verify_code: {
                required: '<?php echo 'Required'?>',
            }
        }
    });




</script>