<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .form-group{
        text-align: left!important;
        margin-left: -45px!important;
        font-size: 15px;
    }

    .btn{
        margin-bottom: 5px!important;
    }

    .magintop20{
        margin-top: 20px;
    }

    #myModal .modal-dialog {
        margin-top: 20px!important;
    }

    .register-div {
        width: 600px;
        margin-right: 50px;
        margin-bottom: 20px;
        float: left;
    }

    .greycolor{
        color: darkgray;
    }

    .register-div .balance{
        font-size: 20px;
        color: black;
    }
</style>
<?php $branch_limit = $output['branch_limit']?>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <?php require_once template('widget/branch.balance'); ?>

    <div class="container">
        <?php foreach ($output['bank'] as $bank){ ?>
            <div class="register-div">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-credit-card"></i><?php echo $bank['bank_name']?></h5>
                    </div>
                    <div class="content" style="padding-bottom: 0px!important;">
                        <div class="col-sm-10 form-group greycolor">
                            <div class="col-sm-6">
                                <div class="col-sm-12 magintop20">
                                    <?php echo $bank['bank_account_name']?>
                                </div>
                                <div class="col-sm-12 magintop20">
                                    <?php echo $bank['bank_account_no']?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-12 magintop20">
                                    <?php echo $bank['currency']?>
                                </div>
                                <div class="col-sm-12 magintop20 balance">
                                    <?php echo $bank['balance'][$bank['currency']] ?>
                                </div>
                            </div>
                            <div class="col-sm-12" style="margin-top: 40px;margin-left:10px;padding-right: 0px!important;font-size: 14px!important;">
                                <?php if($bank['transaction']){?>
                                    <span>Recently Transaction : <?php if($bank['transaction']['biz_code'] == bizCodeEnum::BANK_TO_BRANCH ){
                                            echo 'Withdraw ';
                                        }else{
                                            echo 'Deposit';
                                        } ?>
                                         <?php echo $bank['transaction']['amount']?> at <?php echo $bank['transaction']['update_time']?></span>
                                <?php }else{
                                    echo 'No Record';
                                } ?>

                            </div>
                        </div>
                        <div class="col-sm-4 form-group" style="margin-left:-20px!important;margin-right: -50px!important;">
                            <a class="btn btn-default col-sm-10" href="<?php echo getUrl('cash_in_vault', 'showTransaction', array('bank_id'=>$bank['uid']), false, ENTRY_COUNTER_SITE_URL)?>">
                                <?php echo 'Transaction'?>
                            </a>
                            <!--
                            <button class="btn btn-default col-sm-10"  onclick="showModal(<?php echo $bank['uid'] ?>,'<?php echo $bank['bank_name'] ?>','deposit','Deposit','<?php echo $bank['currency'] ?>')">
                                <?php echo 'Deposit'?>
                            </button>
                            <button class="btn btn-default col-sm-10"  onclick="showModal(<?php echo $bank['uid'] ?>,'<?php echo $bank['bank_name'] ?>','withdraw','Withdraw','<?php echo $bank['currency'] ?>')">
                                <?php echo 'Withdraw'?>
                            </button>
                            <button class="btn btn-default col-sm-10"  onclick="showModal(<?php echo $bank['uid'] ?>,'<?php echo $bank['bank_name'] ?>','adjust','Adjust Fee/Interest','<?php echo $bank['currency'] ?>')">
                                <?php echo 'Adjust Fee/Interest'?>
                            </button>
                            -->
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body" style="margin-bottom: 20px">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="my_form">
                        <input type="hidden" id = 'bank_id' name="bank_id" value="">
                        <input type="hidden" id = 'type' name="type" value="">
                        <input type="hidden" id = 'currency' name="currency" value="">
                        <div class="col-sm-12" id="trade_type" style="display: none;margin-bottom: 15px">
                            <label  class="col-sm-3 control-label"><span class="required-options-xing">*</span>Trading Type</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="trade_type">
                                    <option value="<?php echo flagTypeEnum::INCOME?>">Cash In</option>
                                    <option value="<?php echo flagTypeEnum::PAYOUT?>">Cash Out</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label  class="col-sm-3 control-label"><span class="required-options-xing">*</span>Amount</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="amount" value=""  style="width: 400px">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12" style="margin-top: 15px">
                            <label  class="col-sm-3 control-label"><span class="required-options-xing">*</span>Trading Password</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="password" value="" style="width: 400px">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12" style="margin-top: 15px">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Remark</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="remark" value="" style="width: 400px">
                                <div class="error_msg"></div>
                            </div>
                        </div>
<!--                        <div class="col-sm-12" style="margin-top: 15px" id="branch_limit">-->
<!--                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Branch Limit</label>-->
<!--                            <div class="col-sm-8">-->
<!--                                <input id='time_limit' type="text" name="time_limit" value="" style="width: 125px;height: 30px;margin-right: 5px" readonly><span style="margin-right: 35px">Per Time</span>-->
<!--                                <input id='day_limit' type="text"  name='day_limit' value="" style="width: 125px;height: 30px;margin-right: 5px" readonly><span>Per Day</span>-->
<!--                            </div>-->
<!--                        </div>-->
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="modal_submit()"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<script>
    function showModal(bank_id,bank_name,type,title,currency) {
        $('#myModal input[name="amount"]').val('');
        $('#myModal input[name="password"]').val('');
        $('#myModal input[name="remark"]').val('');
        $('#myModal #bank_id').val(bank_id);
        $('#myModal #type').val(type);
        $('#myModal #currency').val(currency);
        $('#myModal #myModalLabel').html(bank_name+' : '+title);

        $('#myModal').modal('show');
        $('#myModal input[name="amount"]').focus();

//        if (type == 'deposit') {
//            $('#branch_limit').show();
//            $('#myModal #time_limit').val('<?php //echo $branch_limit['limit_deposit']['max_per_time']?>//');
//            $('#myModal #day_limit').val('<?php //echo $branch_limit['limit_deposit']['max_per_day']?>//');
//        }
//        if (type == 'withdraw') {
//            $('#branch_limit').show();
//            $('#myModal #time_limit').val('<?php //echo $branch_limit['limit_withdraw']['max_per_time']?>//');
//            $('#myModal #day_limit').val('<?php //echo $branch_limit['limit_withdraw']['max_per_day']?>//');
//        }
        if (type == 'adjust') {
            $('#trade_type').show();
         }

    }

    function modal_submit(){
        if (!$("#my_form").valid()) {
            return;
        }

        var values = $("#my_form").getValues();
        var type = values.type;
        if (type == 'deposit') {
            var m = 'bankDeposit';
        } else if (type == 'withdraw') {
            var m = 'bankWithdraw';
        } else {
            var m = 'bankAdjust';
        }
        yo.loadData({
            _c: 'cash_in_vault',
            _m: m,
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });

    }

    $("#my_form").validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules : {
            amount : {
                required : true,
//                checkAmount: true
            },
            password : {
                required : true
            },
            remark : {
                required : true
            }
        },
        messages : {
            amount : {
                required : '<?php echo 'Required'?>',
//                checkAmount : '<?php //echo 'Over Limit'?>//'
            },
            password : {
                required : '<?php echo 'Required'?>'
            },
            remark : {
                required : '<?php echo 'Required'?>'
            }
        }
    });

//    jQuery.validator.addMethod("checkAmount", function (value, element) {
//        var limit_amount = Number($("#time_limit").val());
//        value = Number(value);
//        if (value <= limit_amount) {
//            return true;
//        } else {
//            return false;
//        }
//    });

</script>