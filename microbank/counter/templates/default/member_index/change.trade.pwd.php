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

<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="collection-div">
        <div class="basic-info">
            <?php include(template("widget/item.member.summary.v2"))?>
        </div>
        <div class="register-div" style="margin-top: 20px;">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Change Trade Password</h5>
                </div>
                <div class="content" style="padding-left: 60px">
                    <form class="form-horizontal" style="width: 700px" id="form-change" action="<?php echo getUrl('member_index', 'memberChangeTradePwd', array(), false, ENTRY_COUNTER_SITE_URL) ?>"
                          method="post">
                        <input type="hidden" id="client_id" name="member_id" value="<?php echo $output['member_id']?>">
                        <input type="hidden" name="member_image" value="<?php echo $client_info['member_scene_image']?>">
                        <input type="hidden" id="verify_id" name="verify_id" value="">
                        <div class="col-sm-6 form-group title" style="height: 80px!important;line-height: 80px">
                            <label class="control-label"><?php echo 'Scene Photo'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;">
                            <div class="snapshot_div">
                                <img id="img_slave" src="<?php echo getImageUrl($client_info['member_scene_image'],imageThumbVersion::MAX_240)?>" style="width: 150px;height: 90px">
                            </div>
                        </div>
<!--                        <div class="col-sm-6 form-group">-->
<!---->
<!--                        </div>-->
<!--                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;margin-bottom:20px">-->
<!--                            <a class="form-control authorize_input btn btn_default" onclick="send_verify_code()" >--><?php //echo 'Send Verify Code'?><!--</a>-->
<!--                        </div>-->
<!--                        <div class="col-sm-6 form-group title">-->
<!--                            <label class="control-label"><span class="required-options-xing">*</span>--><?php //echo 'input Verify Code'?><!--</label>-->
<!--                        </div>-->
<!--                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;margin-bottom:5px">-->
<!--                            <a class="form-control authorize_input btn btn_default" onclick="verifyCode()" >--><?php //echo 'Client Input'?><!--</a>-->
<!--                            <img id="verify_code" src="resource/img/member/verify-1.png" style="width:25px;position: absolute;top: 4px;right: 20px">-->
<!--                            <img id="verify_codeDone" src="resource/img/member/verify-2.png" style="display: none;width: 25px;position: absolute;top: 4px;right: 20px">-->
<!--                            <input type="hidden" name="verify_code" value="">-->
<!--                            <div class="error_msg"></div>-->
<!--                        </div>-->
                        <div class="col-sm-6 form-group title" style="margin-bottom: 5px">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'Fingermark Compare'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;">
                            <a class="form-control authorize_input btn btn_default" onclick="compareFinger('<?php echo $output['client_info']['obj_guid']?>');"><?php echo 'Client Fingermark'?></a>
                            <img id="fingerCheck" src="resource/img/member/verify-1.png" style="width:25px;position: absolute;top: 4px;right: 20px">
                            <img id="fingerCheckDone" src="resource/img/member/verify-2.png" style="display: none;width: 25px;position: absolute;top: 4px;right: 20px">
                            <img id="checkFingerFailure" src="resource/img/member/verify-3.png" style="display: none;width: 25px;position: absolute;top: 4px;right: 20px">
                            <input type="hidden" name="finger" value="">
                            <div class="error_msg"></div>
                        </div>
                        <div class="col-sm-6 form-group title" style="margin-bottom: 5px">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'Fee'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="text-align: left;margin-left: 12px!important;margin-bottom: 5px">
                            <label class="control-label"><?php echo ncPriceFormat($output['fee']) ?></label>

                        </div>
                        <div class="col-sm-6 form-group title">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'Receive Fee From'?></label>
                        </div>
                        <div class="col-sm-6 form-group " style="text-align: left;margin-left: 12px!important;font-size: 14px;margin-bottom:5px">
                           <input type="radio" name="feeMethod" value="<?php echo repaymentWayEnum::PASSBOOK ?>" checked> <label class="control-label"><?php echo 'Balance'?></label><br/>
                           <input type="radio" name="feeMethod" value="<?php echo repaymentWayEnum::CASH ?>"><label class="control-label"><?php echo 'Cash'?></label>
                        </div>

                        <div class="col-sm-6 form-group title">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'New Trading Password'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;">
                            <a class="form-control authorize_input btn btn_default" onclick="tradePassword()" ><?php echo 'Client Input'?></a>
                            <img id="tradePassword" src="resource/img/member/verify-1.png" style="width:25px;position: absolute;top: 4px;right: 20px">
                            <img id="tradePasswordDone" src="resource/img/member/verify-2.png" style="display: none;width: 25px;position: absolute;top: 4px;right: 20px">
                            <input type="hidden" name="new_trade_pwd" value="">
                            <div class="error_msg"></div>
                        </div>

                        <div class="col-sm-6 form-group title">
                            <label class="control-label"><span class="required-options-xing">*</span><?php echo 'Confirm Trading Password'?></label>
                        </div>
                        <div class="col-sm-6 form-group" style="margin-left: 10px!important;">
                            <a class="form-control authorize_input btn btn_default" onclick="confirmTradePassword()" ><?php echo 'Client Input'?></a>
                            <img id="tradePasswordConfirm" src="resource/img/member/verify-1.png" style="width:25px;position: absolute;top: 4px;right: 20px">
                            <img id="tradePasswordDoneConfirm" src="resource/img/member/verify-2.png" style="display: none;width: 25px;position: absolute;top: 4px;right: 20px">
                            <input type="hidden" name="confirm_trade_password" value="">
                            <div class="error_msg"></div>
                        </div>
                        <div class="col-sm-6 form-group">

                        </div>
                        <div class="col-sm-6 form-group" style="text-align: center;margin-top: 10px;margin-left: 10px!important">
                            <a  class="btn btn-primary col-sm-12"><?php echo 'Submit' ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="form-group button" style="text-align: center">
            <a  class="btn btn-default" style="min-width: 80px;margin-bottom: 40px;margin-top: 20px" href="<?php echo getUrl('member_index', 'index', array('member_id'=>$output['client_info']['uid']), false, ENTRY_COUNTER_SITE_URL) ?>"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
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

    function compareFinger(obj_guid) {
        if (window.external) {
            try {
                var result = window.external.verifyFingerPrint(obj_guid);
                if (result == "1") {
                    $("input[name='finger']").val(result);
                    $('#fingerCheck').hide();
                    $('#checkFingerFailure').hide();
                    $('#fingerCheckDone').show();
                } else if (result == "0") {
                    $("input[name='finger']").val(result);
                    $('#fingerCheck').hide();
                    $('#fingerCheckDone').hide();
                    $('#checkFingerFailure').show();
                } else {
                    alert(result);
                }
            } catch (ex) {
                alert(ex.toString());
            }
        }
    }


    function send_verify_code() {
        var client_id = '<?php echo $output['member_id']?>';
        if (!client_id) {
            return;
        }


        yo.loadData({
            _c: "member_index",
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
        var finger = $("input[name='finger']").val();
        if (!$("#form-change").valid()) {
            return;
        }
        if(finger != 1 ){
            alert('Finger Verify Failure');
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

    function tradePassword() {
        var trading_password_input = window.external.inputPassword();
        var trading_password=trading_password_input.split("|");
        if(trading_password[1] == '6'){
            $("input[name='new_trade_pwd']").val(trading_password[0]);
            $('#tradePassword').hide();
            $('#tradePasswordDone').show();
        }else{
            alert('The password must be 6 digits ');
        }

    }

    function confirmTradePassword() {
        var confirm_trading_password = window.external.inputPasswordAgain();
        if(confirm_trading_password == $("input[name='new_trade_pwd']").val()){
            $("input[name='confirm_trade_password']").val(confirm_trading_password);
            $('#tradePasswordConfirm').hide();
            $('#tradePasswordDoneConfirm').show();
        }else{
            alert('Confirm password error');
        }

    }



    $('#form-change').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules: {
            finger: {
                required: true
            },
            new_trade_pwd: {
                required: true
            },
            confirm_trade_password: {
                required: true
            }
        },
        messages: {

            finger: {
                required: '<?php echo 'Required'?>'
            },
            new_trade_pwd: {
                required: '<?php echo 'Required'?>'
            },
            confirm_trade_password: {
                required: '<?php echo 'Required'?>'
            }
        }
    });




</script>