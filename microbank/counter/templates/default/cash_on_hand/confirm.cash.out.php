<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .page .col-sm-7 {
        padding-left: 0px!important;
        margin-bottom: 60px;
    }

    .page .col-sm-5 {
        padding-right: 0px!important;
        margin-bottom: 60px;
    }

    .verify-state .btn {
        margin-left: -1px;
    }

    .verify-state .btn.active {
        color: #fff;
        background-color: #5cb85c;
        border-color: #4cae4c;
    }

    .col-sm-5 .business-condition {
        margin-top: 20px;
    }

    #notCheck, #notCheckCashier {
        width: 20px;
        position: absolute;
        top: 6px;
        right: 10px;
    }

    #checkCashierFailure, #checkFailure{
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
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="row" style="max-width: 700px">
        <div class="basic-info">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Cash Out Authorization</h5>
            </div>
            <div class="content">
                <form id="cash_out_confirm">
                    <input type="hidden" name="biz_id" value="<?php echo $output['biz']['uid']?>"/>
                    <div class="mincontent">
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-4 control-label" ><span class="required-options-xing">*</span><?php echo 'Currency'?></label>
                            <div class="col-sm-8">
                                <input class="form-control"  value="<?php echo $output['biz']['currency']?>" readonly >
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Amount'?></label>
                            <div class="col-sm-8">
                                <input class="form-control" name="amount" value="<?php echo ncPriceFormat($output['biz']['amount'])?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'My Trading Password'?></label>
                            <div class="col-sm-8">
                                <a class="form-control authorize_input btn btn-default" onclick="cashierPassword()" style="position: relative">Cashier Verify
                                    <img id="notCheckCashier" src="resource/img/member/verify-1.png">
                                    <img id="checkCashierDone" src="resource/img/member/verify-2.png">
                                    <img id="checkCashierFailure" src="resource/img/member/verify-3.png">
                                </a>
                                <input type="hidden" name="cashier_card_no"  class="form-control authorize_input" value="">
                                <input type="hidden" name="cashier_key" class="form-control authorize_input" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
<!--                        <div class="col-sm-12 form-group">-->
<!--                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>--><?php //echo 'Manager Authorize'?><!--</label>-->
<!--                            <div class="col-sm-8">-->
<!--                                <a class="form-control authorize_input btn btn-default" onclick="verifyManger()" style="position: relative">Manager Verify-->
<!--                                    <img id="notCheck" src="resource/img/member/verify-1.png">-->
<!--                                    <img id="checkDone" src="resource/img/member/verify-2.png">-->
                        <!--            <img id="checkFailure" src="resource/img/member/verify-3.png">       -->
<!--                                </a>-->
<!--                                <input type="hidden" name="chief_teller_card_no"  class="form-control authorize_input" value="">-->
<!--                                <input type="hidden" name="chief_teller_key" class="form-control authorize_input" value="">-->
<!--                                <div class="error_msg"></div>-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="col-sm-12 form-group" style="text-align: center;margin-bottom: 0px">
                            <a  class="btn btn-default" style="min-width: 80px;margin-left: 10px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
                            <a  class="btn btn-primary" onclick="confirm_cash_out()">
                                <i class="fa fa-arrow-right"></i>Submit
                            </a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


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
        }else{
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


    function confirm_cash_out() {
        if (!$("#cash_out_confirm").valid()) {
            return;
        }

        var values = $('#cash_out_confirm').getValues();
        yo.loadData({
            _c: 'cash_on_hand',
            _m: 'confirmCashOut',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href='<?php echo getUrl('cash_on_hand', 'cashOut', array(), false, ENTRY_COUNTER_SITE_URL) ?>'
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#cash_out_confirm').validate({
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
//            chief_teller_card_no : {
//                required: true
//            },
//            chief_teller_key : {
//                required: true
//            }
        },
        messages: {
            cashier_card_no : {
                required: '<?php echo 'Required'?>'
            },
            cashier_key : {
                required: '<?php echo 'Required'?>'
            },
//            chief_teller_card_no : {
//                required: '<?php //echo 'Required'?>//'
//            },
//            chief_teller_key : {
//                required: '<?php //echo 'Required'?>//'
//            }
        }
    });
</script>



