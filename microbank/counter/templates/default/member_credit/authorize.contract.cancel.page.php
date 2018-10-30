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

    .table td{
        background-color: white !important;
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

<?php $contract_info = $output['contract_info']; ?>
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
                <h5><i class="fa fa-id-card-o"></i>Cancel Authorize</h5>
            </div>
            <div class="content">

                <div class="alert alert-danger">
                    <h4>Warning: cancel contract will clear up client's credit.If client have paid for the fee by balance,we will return
                    the fee to  client's balance!</h4>
                </div>

                <div class="col-sm-8 mincontent" style="padding-left: 0px!important;">
                    <form id='client_deposit'>
                        <input type="hidden" name="form_submit" value="ok">
                        <input type="hidden" name="uid" value="<?php echo $contract_info['uid']; ?>">
                        <div class="col-sm-12 form-group" style="padding-left: 0 !important;">
                            <table class="table">

                                <tr>
                                    <td>Contract No.</td>
                                    <td><?php echo $contract_info['contract_no']; ?></td>
                                </tr>
                                <tr>
                                    <td>Total fee</td>
                                    <td>
                                        <?php echo ncPriceFormat($contract_info['fee']).currencyEnum::USD; ?>
                                        (<?php echo $contract_info['is_paid']==1?'Paid':'Un-paid'; ?>)

                                    </td>
                                </tr>
                                <?php if( $contract_info['is_paid'] == 1 ){ ?>
                                    <tr>
                                        <td>Paid Way</td>
                                        <td>
                                            <?php echo $contract_info['payment_way'] == repaymentWayEnum::CASH?'Cash':'Balance'; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if( $contract_info['is_paid']==1 && $contract_info['payment_way'] == repaymentWayEnum::CASH ){ ?>
                                    <tr>
                                        <td>Return Fee</td>
                                        <td>
                                            <input type="radio" name="return_fee_way" value="1" checked > Return Cash
                                            <input type="radio" name="return_fee_way" value="0"> Return to balance
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td >My Trading Password</td>
                                    <td style="position: relative">
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

                                <tr>
                                    <td >
                                        Manager Authorize
                                    </td>
                                    <td style="position: relative">
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

                                <tr>
                                    <td>
                                        Remark
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="remark" id="" cols="30" rows="3"></textarea>

                                        <div class="error_msg"></div>
                                    </td>
                                </tr>


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
            <button type="button" class="btn btn-default" style="min-width: 80px;margin-top: 30px" onclick="javascript:window.history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
        </div>
    </div>

</div>

<?php require_once template('widget/app.config.js'); ?>
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
            _c: 'member_credit',
            _m: 'ajaxCancelCreditContractConfirm',
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert("Success!");
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member_credit', 'showAuthorizeContractDetail', array('uid'=>$contract_info['uid']), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#client_deposit').validate({
        errorPlacement: function(error, element){
            element.next('.error_msg').html(error);
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
            },
            remark:{
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
            chief_teller_card_no : {
                required: '<?php echo 'Required'?>'
            },
            chief_teller_key : {
                required: '<?php echo 'Required'?>'
            },
            remark:{
                required: 'Please input remark'
            }
        }
    });



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



