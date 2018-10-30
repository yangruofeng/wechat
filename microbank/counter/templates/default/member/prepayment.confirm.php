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
        margin-top: 3px!important;
        margin-bottom: 2px;
        position: relative;
    }

    .table{
        background-color: white!important;
    }

    #notCheck, #notCheckCashier,#notCheckPassword{
        width: 20px;
        position: absolute;
        top: 6px;
        right: 6px;
    }

    #checkDone, #checkCashierDone,#checkPasswordDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 6px;
    }

    td{
        background-color: white;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }
</style>

<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="collection-div">
        <div class="basic-info container">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Prepayment Authorize</h5>
            </div>
            <div class="content">
                <div class="col-sm-6 mincontent">
                    <table class="table">
                        <tr>
                            <td>Client Name</td>
                            <td>
                                <lable><?php echo $output['client_info']['login_code']?></lable>
                            </td>
                        </tr>
                        <?php $i = 0;foreach($output['currency_amount'] as $currency => $amount){ ++$i?>
                            <tr>
                                <td>
                                    <?php if($i == 1) {?>
                                        <label class="control-label">Prepayment Amount</label>
                                    <?php }?>
                                </td>
                                <td><span class="repayment-amount money-style"><?php echo $currency . ' ' . ncPriceFormat($amount)?></span></td>
                            </tr>
                        <?php }?>
                        <tr>
                            <td>Receive Money From</td>
                            <td>
                                <lable><?php echo $lang['prepayment_way_'.$output['confirm_info']['prepayment_type']]?></lable>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="required-options-xing">*</span>Scene Photo</td>
                            <td>
                                <div class="snapshot_div" onclick="callWin_snapshot_slave_withdraw();">
                                    <img id="img_slave_prepayment" src="resource/img/member/photo.png" style="width: 140px;">
                                </div>
                            </td>
                        </tr>

                    </table>
                </div>
                <div class="col-sm-6 mincontent "  style="padding-left: 0px!important;">
                    <form id='client_prepayment'>
                        <input type="hidden" id="member_image" name="member_image" value="">
                        <input type='hidden' class="form-control" name="biz_id" value="<?php echo $output['confirm_info']['biz_id'] ?>">
                        <div class="col-sm-12 form-group"  style="padding-left: 0px!important;">
                            <table class="table">
                                <tr>
                                    <td>Contract Sn</td>
                                    <td>
                                        <lable><?php echo $output['contract_info']['contract_sn']?></lable>
                                    </td>
                                </tr>

                                <tr>
                                    <td>My Trading Password</td>
                                    <td style="background-color: white!important;position: relative">
                                        <a class="form-control authorize_input btn btn-default" onclick="cashierPassword()">Cashier Verify
                                            <img id="notCheckCashier" src="resource/img/member/verify-1.png">
                                            <img id="checkCashierDone" src="resource/img/member/verify-2.png">
                                        </a>
                                        <input type="hidden" name="cashier_card_no"  class="form-control authorize_input" value="">
                                        <input type="hidden" name="cashier_key" class="form-control authorize_input" value="">
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: white!important;">
                                        Client Trading Password
                                    </td>
                                    <td style="background-color: white!important;position: relative">
                                        <a class="form-control authorize_input btn btn-default" onclick="clientPassword()">Client Verify
                                            <img id="notCheckPassword" src="resource/img/member/verify-1.png">
                                            <img id="checkPasswordDone" src="resource/img/member/verify-2.png">
                                        </a>
                                        <input type="hidden" name="client_trade_pwd" class="form-control authorize_input">
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Manager Authorize
                                    </td>
                                    <td style="background-color: white!important;position: relative">
                                        <a class="form-control authorize_input btn btn-default" onclick="verifyManger()">Manager Verify
                                            <img id="notCheck" src="resource/img/member/verify-1.png">
                                            <img id="checkDone" src="resource/img/member/verify-2.png">
                                        </a>
                                        <input type="hidden" name="chief_teller_card_no"  class="form-control authorize_input" value="">
                                        <input type="hidden" name="chief_teller_key" class="form-control authorize_input" value="">
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                    <div class="col-sm-12 form-group" style="text-align: center;">
                        <button type="button" class="btn btn-primary btn-block" onclick="btn_submit_prepayment_onclick()"><i class="fa fa-arrow-right"></i>Submit</button>
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
        $('#notCheckCashier').hide();
        $('#checkCashierDone').show();
    }

    function clientPassword() {
        var client_password = window.external.inputPasswordWithKeyInfo('');
        $("input[name='client_trade_pwd']").val(client_password);
        $('#notCheckPassword').hide();
        $('#checkPasswordDone').show();
    }

    function verifyManger() {
        var card = window.external.swipeCard();
        var card_info=card.split("|");
        $("input[name='chief_teller_card_no']").val(card_info[0]);
        $("input[name='chief_teller_key']").val(card_info[1]);
        $('#notCheck').hide();
        $('#checkDone').show();

    }

    function btn_submit_prepayment_onclick() {
        if(!$('#client_prepayment').valid()){
            return;
        }
        var values = $('#client_prepayment').getValues();
        $('.content').waiting();
        yo.loadData({
            _c: 'member',
            _m: 'confirmPrepayment',
            param: values,
            callback: function (_o) {
                $('.content').unmask();
                if (_o.STS) {
                    alert("Prepayment Successful!");
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member', 'loan', array(), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#client_prepayment').validate({
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
            client_trade_pwd : {
                required: true
            },
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
            client_trade_pwd: {
                required: '<?php echo 'Required'?>'
            },
            chief_teller_card_no : {
                required: '<?php echo 'Required'?>'
            },
            chief_teller_key : {
                required: '<?php echo 'Required'?>'
            }
        }
    });

    function callWin_snapshot_slave_withdraw() {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $("#img_slave_prepayment").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('#member_image').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);
            }
        }
    }
</script>



