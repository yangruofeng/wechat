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

    #checkCashierFailure, #checkFailure, #checkPasswordFailure{
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 6px;
    }

    #checkDone, #checkCashierDone,#checkPasswordDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 6px;
    }
    .margin40{
        margin-top: 40px;
    }
</style>

<?php
$contract_info = $output['contract_info'];
?>

<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="collection-div">


        <?php include template('member_loan/loan.contract.info.item');?>

        <div class="basic-info container"  style="width: 900px!important;">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Loan disburse Confirm</h5>
            </div>
            <div class="content">
                <div class="col-sm-6 mincontent">
                    <table class="table">
                        <tr>
                            <td>Scene Photo</td>
                            <td>
                                <div class="snapshot_div" onclick="callWin_snapshot_slave_loan();">
                                    <img id="img_add_contract" src="<?php echo $output['member_scene_image']?getImageUrl($output['member_scene_image']):'resource/img/member/photo.png'; ?>" style="width: 150px">
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>
                <div class="col-sm-6 mincontent"  style="padding-left: 0px!important;">
                    <form id='add_contract'>
                        <input type="hidden" id="add_contract_image" name="add_contract_image" value="<?php echo $output['member_scene_image']; ?>">
                        <input type='hidden' class="form-control" name="biz_id" value="<?php echo $output['biz_id']?>">
                        <div class="col-sm-12 form-group"  style="padding-left: 0px!important;">
                            <table class="table">
                                <tr>
                                    <td>My Trading Password</td>
                                    <td style="background-color: white!important;position: relative">
                                        <a class="form-control authorize_input btn btn-default" onclick="cashierPassword()">Cashier Verify
                                            <img id="notCheckCashier" src="resource/img/member/verify-1.png">
                                            <img id="checkCashierDone" src="resource/img/member/verify-2.png">
                                            <img id="checkCashierFailure" src="resource/img/member/verify-3.png">
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
                                            <img id="checkPasswordFailure" src="resource/img/member/verify-3.png">
                                        </a>
                                        <input type="hidden" name="client_trade_pwd" class="form-control authorize_input">
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>

                                <?php if( $output['is_ct_check'] ){ ?>
                                    <tr>
                                        <td>
                                            Manager Authorize
                                        </td>
                                        <td style="background-color: white!important;position: relative">
                                            <a class="form-control authorize_input btn btn-default" onclick="verifyManger()">Manager Verify
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
                </div>
                <div class="col-sm-12 form-group" id="cancel_button" style="text-align: center;">
                    <button style="width:380px; float: left;margin-left: 30px" type="button" class="btn btn-info btn-block" onclick="btn_cancel_addcontract_onclick()"><i class="fa fa-reply"></i>Cancel</button>
                    <button style="width:380px; float: right;margin-right: 30px;margin-top: 0px" type="button" class="btn btn-primary btn-block" onclick="btn_submit_addcontract_onclick()"><i class="fa fa-arrow-right"></i>Submit</button>
                </div>
            </div>
        </div>
        <div class="form-group button" style="width:1000px;text-align: center">
            <button type="button" class="btn btn-default" style="min-width: 80px;margin-top: 30px;margin-left: -100px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
        </div>
    </div>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        if('<?php echo $output['is_ct_check'] ?>'){
            $('#cancel_button').addClass('margin40')
        }
    });

    function cashierPassword() {
        var card = window.external.swipeCard();
        var card_info=card.split("|");
        $("input[name='cashier_card_no']").val(card_info[0]);
        $("input[name='cashier_key']").val(card_info[1]);
        if($("input[name='cashier_card_no']").val()){
            $('#notCheckCashier').hide();
            $('#checkCashierFailure').hide();
            $('#checkCashierDone').show();
        }else{
            $('#notCheckCashier').hide();
            $('#checkCashierFailure').show();
        }

    }

    function clientPassword() {
        var client_password = window.external.inputPasswordWithKeyInfo('');
        $("input[name='client_trade_pwd']").val(client_password);
        if($("input[name='client_trade_pwd']").val()){
            $('#notCheckPassword').hide();
            $('#checkPasswordFailure').hide();
            $('#checkPasswordDone').show();
        }else{
            $('#notCheckPassword').hide();
            $('#checkPasswordFailure').show();
        }
    }

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


    function btn_submit_addcontract_onclick() {
        if (!$("#add_contract").valid()) {
            return;
        }
        $(document).waiting();
        var values = $('#add_contract').getValues();
        yo.loadData({
            _c: 'member_loan',
            _m: 'addOneTimeContractSubmit',
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert("addContract Successful!");
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member_loan', 'loanOneTimeIndex', array('member_id'=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    <?php if( $output['is_ct_check'] ){ ?>
    $('#add_contract').validate({
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
    <?php }else{ ?>
    $('#add_contract').validate({
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
            },
            client_trade_pwd: {
                required: '<?php echo 'Required'?>'
            }
        }
    });
    <?php } ?>



    function callWin_snapshot_slave_loan() {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $("#img_add_contract").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('#add_contract_image').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);
            }
        }
    }
    
    
    function btn_cancel_addcontract_onclick() {
        $(document).waiting();
        var values = $('#add_contract').getValues();
        yo.loadData({
            _c: 'member_loan',
            _m: 'oneTimeContractCancel',
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member_loan', 'loanOneTimeIndex', array('member_id'=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }


</script>



