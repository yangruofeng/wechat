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
$decimal=2;
if($contract_info['currency']==currencyEnum::KHR){
    $decimal=0;
}
?>

<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="collection-div">
        <div class="basic-info" style="width: 900px">
            <table class="table contract-table">
                <thead>
                <tr class="table-header">
                    <td colspan="4"><label class="control-label">Basic Information</label></td>
                </tr>
                </thead>
                <tbody class="table-body">
                <tr>
                    <td>Contract SN</td>
                    <td><?php echo $output['contract_info']['contract_sn']; ?></td>
                    <td>Loan Terms</td>
                    <td><?php echo $contract_info['loan_period_value'].$contract_info['loan_period_unit']; ?></td>

                </tr>

                <tr>
                    <td>Operation Fee</td>
                    <td><?php echo  $contract_info['operation_fee'].'% '.ucwords($contract_info['operation_fee_unit']); ?></td>
                    <td>Interest Rate</td>
                    <td><?php echo  $output['contract_info']['interest_rate'].'% '.ucwords($output['contract_info']['interest_rate_unit']); ?></td>
                </tr>


                <tr>
                    <td>Client Code</td>
                    <td><?php echo $output['member_info']['login_code']; ?></td>
                    <td>Client phone</td>
                    <td><?php echo  $output['member_info']['phone_id']; ?></td>
                </tr>
                <tr>
                    <td>First Repay</td>
                    <td><?php echo $output['first_repay']['receivable_date']; ?></td>
                    <td>Repay Day</td>
                    <td><?php
                        switch ($output['due_date_type']){
                            case dueDateTypeEnum::FIXED_DATE:
                                echo $output['first_repay']['receivable_date'];
                                break;
                            case dueDateTypeEnum::PER_WEEK:
                                echo 'The ' . $output['due_date'] .'th of each week';
                                break;
                            case dueDateTypeEnum::PER_MONTH:
                                echo 'The ' . $output['due_date'] . 'th of each month';
                                break;
                            case dueDateTypeEnum::PER_YEAR:
                                echo 'The ' . $output['due_date'] . ' of each year';
                                break;
                            case dueDateTypeEnum::PER_DAY:
                                echo 'every day';
                                break;
                        }
                        ?></td>
                </tr>
                </tbody>
            </table>
            <table class="table">
                <thead>
                <tr class="table-header">
                    <td colspan="8"><label class="control-label">Total Amount</label></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Total Loan</td>
                    <td>Total Interest</td>
                    <td>Admin Fee</td>
                    <td>Loan Fee</td>
                    <td>Operation Fee</td>
                    <td>Insurance Fee</td>
                    <td>Received Amount</td>
                    <td>Total Repayment</td>
                </tr>
                <?php if ($output['total_repay']) { ?>
                    <tr>
                        <td>
                            <?php echo ncPriceFormat($output['total_repay']['total_loan'],$decimal) ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($output['total_repay']['total_interest'],$decimal) ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($output['total_repay']['total_admin_fee'],$decimal) ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($output['total_repay']['total_loan_fee'],$decimal) ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($output['total_repay']['total_operation_fee'],$decimal) ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($output['total_repay']['total_insurance_fee'],$decimal) ?>
                        </td>

                        <td>
                            <kbd>
                                <?php echo ncPriceFormat($output['total_repay']['actual_receive_amount'],$decimal) ?>
                            </kbd>
                        </td>
                        <td>
                            <strong>
                                <?php echo ncPriceFormat($output['total_repay']['total_repayment'],$decimal) ?>
                            </strong>
                        </td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td colspan="8">No Record</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <table class="table">
                <thead>
                <tr class="table-header">
                    <td colspan="8"><label class="control-label">Installment Scheme</label></td>
                </tr>
                </thead>
                <tbody class="table-body">
                <tr>
                    <td>Periods</td>
                    <td>Repayment Time</td>
                    <td>Days</td>
                    <td>Payable Principal</td>
                    <td>Payable Interest</td>
                    <td>Operating Charges</td>
                    <td>Remaining Principal</td>
                    <td>Payable Total</td>
                </tr>
                <?php if ($output['loan_installment_scheme']) { ?>
                    <?php foreach ($output['loan_installment_scheme'] as $row) { ?>
                        <tr>
                            <td><?php echo $row['scheme_name']?></td>
                            <td><?php echo $row['receivable_date']?></td>
                            <td><?php echo system_toolClass::diffBetweenTwoDays($row['receivable_date'],$row['interest_date']); ?></td>
                            <td><?php echo ncPriceFormat($row['receivable_principal'],$decimal);?></td>
                            <td><?php echo ncPriceFormat($row['receivable_interest'],$decimal);?></td>
                            <td><?php echo ncPriceFormat($row['receivable_operation_fee'],$decimal);?></td>
                            <td><?php echo ncPriceFormat($row['initial_principal']-$row['receivable_principal'],$decimal);?></td>
                            <td><?php echo ncPriceFormat($row['amount'],$decimal);?></td>
                        </tr>
                    <?php } ?>
                    <tr style="text-align: center"><td colspan="10">
                            <input type="hidden" id="contract_id" value="<?php echo $output['contract_id'] ?>"/>
                            <button type="button" class="btn btn-default" style="width: 200px" onclick="print_installment_scheme()"><i class="fa fa-print"></i>  Print Scheme</button>
                        </td></tr>
                <?php } else { ?>
                    <tr>
                        <td colspan="8">No Record</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>


        </div>
        <div class="basic-info container"  style="width: 900px!important;">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Add Loan Confrim</h5>
            </div>
            <div class="content">
                <div class="col-sm-6 mincontent">
                    <table class="table">
                        <tr>
                            <td>Scene Photo</td>
                            <td>
                                <div class="snapshot_div" onclick="callWin_snapshot_slave_loan();">
                                    <img id="img_add_contract" src="resource/img/member/photo.png" style="width: 150px">
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>
                <div class="col-sm-6 mincontent"  style="padding-left: 0px!important;">
                    <form id='add_contract'>
                        <input type="hidden" id="add_contract_image" name="add_contract_image" value="">
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
            _m: 'addContractSubmit',
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert("addContract Successful!");
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member_index', 'index', array('member_id'=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL) ?>";
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
            },
            client_trade_pwd : {
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
            _m: 'addContractCancel',
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member_loan', 'loanIndex', array('member_id'=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function print_installment_scheme() {
        var _uid = $('#contract_id').val();
        var _show_scheme = 1;
//        window.location.href = "<?php //echo getUrl('print_form', 'printInstallmentScheme', array(), false, ENTRY_COUNTER_SITE_URL)?>//&contract_id="+_uid+"&_show_scheme="+_show_scheme
        window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printInstallmentScheme', array(), false, ENTRY_COUNTER_SITE_URL)?>&contract_id="+_uid+"&_show_scheme="+_show_scheme);
    }
</script>



