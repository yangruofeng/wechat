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

    #checkDone, #checkCashierDone,#checkPasswordDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 6px;
    }
</style>

<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
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
                    <td>Client Code</td>
                    <td><?php echo $output['member_info']['login_code']; ?></td>
                    <td>Client phone</td>
                    <td><?php echo  $output['member_info']['phone_id']; ?></td>
                </tr>
                <tr>
                    <td>First Repay</td>
                    <td><?php echo $output['first_repay']['receivable_date']; ?></td>
                    <td>Repay Day</td>
                    <td><?php echo 'The ' . $output['due_date'] . 'th of each month' ?></td>
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
                    <td>Actual Amount</td>
                    <td>Total Repayment</td>
                </tr>
                <?php if ($output['total_repay']) { ?>
                <tr>
                    <td>
                        <?php echo $output['total_repay']['total_loan'] ?>
                    </td>
                    <td>
                        <?php echo $output['total_repay']['total_interest'] ?>
                    </td>
                    <td>
                        <?php echo $output['total_repay']['total_admin_fee'] ?>
                    </td>
                    <td>
                        <?php echo $output['total_repay']['total_loan_fee'] ?>
                    </td>
                    <td>
                        <?php echo $output['total_repay']['total_operation_fee'] ?>
                    </td>
                    <td>
                        <?php echo $output['total_repay']['total_insurance_fee'] ?>
                    </td>
                    <td>
                        <?php echo $output['total_repay']['actual_receive_amount'] ?>
                    </td>
                    <td>
                        <?php echo $output['total_repay']['total_repayment'] ?>
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
                    <td>Payable Principal</td>
                    <td>Payable Interest</td>
                    <td>Operating Charges</td>
                    <td>Payable Total</td>
                </tr>
                <?php if ($output['loan_installment_scheme']) { ?>
                    <?php foreach ($output['loan_installment_scheme'] as $row) { ?>
                        <tr>
                            <td><?php echo $row['scheme_name']?></td>
                            <td><?php echo $row['receivable_date']?></td>
                            <td><?php echo ncPriceFormat($row['receivable_principal'])?></td>
                            <td><?php echo ncPriceFormat($row['receivable_interest'])?></td>
                            <td><?php echo ncPriceFormat($row['receivable_operation_fee'] + $row['receivable_admin_fee'])?></td>
                            <td><?php echo ncPriceFormat($row['amount'] + $row['penalty'])?></td>
                        </tr>
                    <?php } ?>
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
                <h5><i class="fa fa-id-card-o"></i>Add Loan Authorize</h5>
            </div>
            <div class="content">
                <div class="col-sm-6 mincontent">
                    <table class="table">
                        <tr>
                            <td><span class="required-options-xing">*</span>Scene Photo</td>
                            <td>
                                <div class="snapshot_div" onclick="callWin_snapshot_slave_withdraw();">
                                    <img id="img_add_contract" src="resource/img/member/photo.png">
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
                        <button type="button" class="btn btn-primary btn-block" onclick="btn_submit_addcontract_onclick()"><i class="fa fa-arrow-right"></i>Submit</button>
                    </div>
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

    function btn_submit_addcontract_onclick() {
        if (!$("#add_contract").valid()) {
            return;
        }
        var values = $('#add_contract').getValues();
        yo.loadData({
            _c: 'member',
            _m: 'addContractSubmit',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert("addContract Successful!");
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member', 'loan', array(), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

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

    function callWin_snapshot_slave_withdraw() {
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
</script>



