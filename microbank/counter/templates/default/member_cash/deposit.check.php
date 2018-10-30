<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .container {
        width: 800px !important;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    .collection-div {
        margin-bottom: 70px;
    }

    .authorize_input {
        margin-top: -8px!important;
        margin-bottom: 10px;
    }

    .table{
        background-color: white!important;
    }

    #notCheck, #notCheckCashier {
        width: 20px;
        position: absolute;
        top: 6px;
        right: 10px;
    }

    #checkFailure, #checkCashierFailure {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 10px;
    }

    #checkDone, #checkCashierDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 10px;
    }
</style>

<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="collection-div">
        <div class="basic-info">
            <?php $client_info=$output['client_info'];?>
            <?php require_once template("widget/item.member.summary.v2");?>
        </div>
        <?php $biz = $output['biz']?>
        <div class="basic-info container">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Deposit Authorize</h5>
            </div>
            <div class="content">
                <div class="col-sm-6 mincontent">
                    <table class="table">
                        <tr>
                            <td>Currency</td>
                            <td>
                                <lable style="font-size: 15px"><?php echo $biz['currency']?></lable>
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: white!important;">
                                Amount
                            </td>
                            <td style="background-color: white!important;">
                                <label style="font-size: 20px"><?php echo ncPriceFormat($biz['amount'])?></label>
                            </td>
                        </tr>
                        <tr>
                            <td>Scene Photo</td>
                            <td>
                                <div class="snapshot_div" onclick="callWin_snapshot_slave_deposit();">
                                    <img id="img_slave_deposit" src="<?php echo $output['member_scene_image']?getImageUrl($output['member_scene_image']):'resource/img/member/photo.png'; ?>" style="width: 150px">
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>
                <div class="col-sm-6 mincontent" style="padding-left: 0px!important;">
                    <form id='client_deposit'>
                        <input type="hidden" id="deposit_member_image" name="deposit_member_image" value="<?php echo $output['member_scene_image']; ?>">
                        <input type='hidden' class="form-control" name="biz_id" value="<?php echo $biz['uid']?>">
                        <div class="col-sm-12 form-group" style="padding-left: 0px!important;">
                            <table class="table">
                                <tr>
                                    <td class="col-sm-5">My Trading Password</td>
                                    <td style="background-color: white!important;position: relative">
                                        <a class="form-control authorize_input btn btn-default" onclick="cashierPassword()" style="position: relative">Cashier Verify
                                            <img id="notCheckCashier" src="resource/img/member/verify-1.png">
                                            <img id="checkCashierDone" src="resource/img/member/verify-2.png">
                                            <img id="checkCashierFailure" src="resource/img/member/verify-3.png">
                                        </a>
                                        <input type="hidden" name="cashier_card_no"  class="form-control authorize_input" value="">
                                        <input type="hidden" name="cashier_key" class="form-control authorize_input" value="">
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
<!--                                <tr>-->
<!--                                    <td style="background-color: white!important;">-->
<!--                                        Client Trading Password-->
<!--                                    </td>-->
<!--                                    <td style="background-color: white!important;position: relative">-->
<!--                                        <a class="form-control authorize_input btn btn_default" onclick="clientPassword()" >Client Password</a>-->
<!--                                        <img id="notCheckPassword" src="resource/img/member/verify-1.png" style="width:26px;position: absolute;top: 4px;right: 10px">-->
<!--                                        <img id="checkPasswordDone" src="resource/img/member/verify-2.png" style="display: none;width: 26px;position: absolute;top: 4px;right: 10px">-->
<!--                                        <input type="hidden" name="client_trade_pwd" class="form-control authorize_input">-->
<!--                                        <div class="error_msg"></div>-->
<!--                                    </td>-->
<!--                                </tr>-->

                                <?php if( $output['is_ct_check'] ){ ?>
                                    <tr>
                                        <td style="background-color: white!important;">
                                            Manager Authorize
                                        </td>
                                        <td style="background-color: white!important;position: relative">
                                            <a class="form-control authorize_input btn btn-default" onclick="verifyManger()" style="position: relative">Manager Verify
                                                <img id="notCheck" src="resource/img/member/verify-1.png">
                                                <img id="checkDone" src="resource/img/member/verify-2.png">
                                                <img id="checkFailure" src="resource/img/member/verify-3.png">
                                            </a>
                                            <input type="hidden" name="chief_teller_card_no"  class="form-control authorize_input" value="">
                                            <input type="hidden" name="chief_teller_key" class="form-control authorize_input" value="">
                                            <div class="error_msg"></div>
                                        </td>
                                    </tr>
                                <?php } ?>

                            </table>
                        </div>
                    </form>
                    <div class="col-sm-12 form-group" style="text-align: center;">
                        <button type="button" class="btn btn-primary btn-block" onclick="btn_submit_deposit_onclick()"><i class="fa fa-arrow-right"></i>Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group button" style="text-align: center">
            <button type="button" class="btn btn-default" style="min-width: 80px;margin-top: 30px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
        </div>
    </div>

</div>

<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>

    function cashierPassword() {
        var card = window.external.swipeCard();
        var card_info=card.split("|");
        $("input[name='cashier_card_no']").val(card_info[0]);
        $("input[name='cashier_key']").val(card_info[1]);
        if($("input[name='cashier_card_no']").val()){
            $('#notCheckCashier').hide();
            $('#checkCashierFailure').hide();
            $('#checkCashierDone').show();
        }else {
            $('#notCheckCashier').hide();
            $('#checkCashierFailure').show();
        }
    }

//    function clientPassword() {
//        var client_password = window.external.inputPasswordWithKeyInfo('');
//        $("input[name='client_trade_pwd']").val(client_password);
//        $('#notCheckPassword').hide();
//        $('#checkPasswordDone').show();
//    }

    function verifyManger() {
       var card = window.external.swipeCard();
       var card_info=card.split("|");
        $("input[name='chief_teller_card_no']").val(card_info[0]);
        $("input[name='chief_teller_key']").val(card_info[1]);
        if($("input[name='chief_teller_card_no']").val()){
            $('#notCheck').hide();
            $('#checkFailure').hide();
            $('#checkDone').show();
        }else{
            $('#notCheck').hide();
            $('#checkFailure').show();
        }
    }

    function btn_submit_deposit_onclick() {
        if (!$("#client_deposit").valid()) {
            return false;
        }
        $(document).waiting();
        var values = $('#client_deposit').getValues();
        yo.loadData({
            _c: 'member_cash',
            _m: 'checkClientDeposit',
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert("Deposit Successful!");
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member_cash', 'depositIndex', array('member_id'=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    <?php if( $output['is_ct_check']){ ?>
            $('#client_deposit').validate({
                errorPlacement: function(error, element){
                    element.next().html(error);
                },
                rules: {
                    cashier_card_no : {
                        required: true
                    },
                    cashier_key : {
                        required: true
                    },
        //            client_trade_pwd : {
        //                required: true
        //            },
                    chief_teller_card_no : {
                        required: true
                    },
                    chief_teller_key : {
                        required: true
                    }
                },
                messages: {
                    cashier_card_no : {
                        required: '<?php echo 'Required'?>'
                    },
                    cashier_key : {
                        required: '<?php echo 'Required'?>'
                    },
        //            client_trade_pwd: {
        //                required: '<?php //echo 'Required'?>//'
        //            },
                    chief_teller_card_no : {
                        required: '<?php echo 'Required'?>'
                    },
                    chief_teller_key : {
                        required: '<?php echo 'Required'?>'
                    }
                }
            });
    <?php }else{ ?>
            $('#client_deposit').validate({
                errorPlacement: function(error, element){
                    element.next().html(error);
                },
                rules: {
                    cashier_card_no : {
                        required: true
                    },
                    cashier_key : {
                        required: true
                    }
                },
                messages: {
                    cashier_card_no : {
                        required: '<?php echo 'Required'?>'
                    },
                    cashier_key : {
                        required: '<?php echo 'Required'?>'
                    }
                }
            });
    <?php } ?>



    function callWin_snapshot_slave_deposit() {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $("#img_slave_deposit").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('#deposit_member_image').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);
            }
        }
    }
</script>



