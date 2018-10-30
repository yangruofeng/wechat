<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Bank</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <div class="business-list">
                <?php if(!$output['bank_list']){ ?>
                    <?php include(template(":widget/no_record"))?>
                <?php }?>
                <?php foreach ($output['bank_list'] as $bank) {?>
                    <div class="col-sm-6">
                        <?php include(template("partner/partner.item"))?>
                    </div>
                <?php }?>
            </div>
        </div>
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
                        <input type="hidden" id = 'partner_id' name="partner_id" value="">
                        <input type="hidden" id = 'type' name="type" value="">
                        <div class="col-sm-12" id="div_currency" style="margin-bottom: 15px">
                            <label  class="col-sm-3 control-label"><span class="required-options-xing">*</span>Currency</label>
                            <div class="col-sm-8">
                                <?php $ccy_list=(new currencyEnum())->Dictionary();?>
                                <select class="form-control" name="currency">
                                    <?php foreach($ccy_list as $ccy_k=>$ccy){?>
                                        <option value="<?php echo $ccy_k?>"><?php echo $ccy;?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
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
    function showModal(partner_id,partner_name,type,title) {
        $('#myModal input[name="amount"]').val('');
        $('#myModal input[name="password"]').val('');
        $('#myModal input[name="remark"]').val('');

        $('#myModal #partner_id').val(partner_id);
        $('#myModal #type').val(type);
        $('#myModal #myModalLabel').html(partner_name+" : "+title);

        $('#myModal').modal('show');
        $('#myModal input[name="amount"]').focus();

        if (type == 'adjust') {
            $('#trade_type').show();
        }else{
            $('#trade_type').hide();
        }

    }

    function modal_submit(){
        if (!$("#my_form").valid()) {
            return;
        }
        $(document).waiting();
        var values = $("#my_form").getValues();
        var type = values.type;
        if (type == 'deposit_hq') {
            var m = 'partnerDepositByHQ';
        } else if (type == 'withdraw_hq') {
            var m = 'partnerWithdrawByHQ';
        } else {
            var m = 'partnerAdjust';
        }
        yo.loadData({
            _c: 'partner',
            _m: m,
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    $('#myModal').modal('hide');
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.reload();
                    }, 500)
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


</script>