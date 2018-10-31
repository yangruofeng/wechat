<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<?php
    $branch=$output['branch_info'];
    $ccy_list=(new currencyEnum())->Dictionary();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Branch Management</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl("treasure","branchList",array(),false,BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a class="current"><span>Branch - <?php echo $branch['branch_code']?></span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
        </div>

        <div class="business-content">
            <div class="business-list">
               <div class="col-sm-6">
                   <div class="panel panel-default">
                       <div class="panel-heading">
                           <h5>
                               <i class="fa fa-money"></i> CASH IN VAULT
                               <a href="<?php echo getUrl("data_center_branch","dailyReport",array('branch_id'=>$_GET['branch_id']),false,BACK_OFFICE_SITE_URL)?>" style="float:right;">Daily Report</a>
                           </h5>


                       </div>
                       <div class="panel-body">
                           <ul class="list-group">
                               <?php foreach($ccy_list as $ccy_k=>$ccy_v){?>
                                   <li class="list-group-item">
                                       <span class="badge">
                                           <?php if($output['balance']['balance_'.$ccy_k]){?>
                                               <a class="btn btn-link btn-xs" href="<?php echo getUrl("common","passbookAccountFlowPage",array('obj_uid'=>$_GET['branch_id'],'obj_type'=>objGuidTypeEnum::SITE_BRANCH,'currency'=>$ccy_k),false,BACK_OFFICE_SITE_URL)?>" style="color: #f0ad4e;font-weight: bold;text-decoration: none;">
                                                   <?php echo $output['balance']['balance_'.$ccy_k];?>
                                               </a>
                                           <?php }else{echo 0;}?>

                                       </span>
                                       <span>
                                           <?php echo $ccy_k;?>
                                       </span>
                                       <span style="padding-left: 10px">Balance</span>
                                   </li>
                               <?php }?>
                               <li class="list-group-item">
                                   <?php foreach($output['civ_recent'] as $ccy_k=>$ccy_recent){?>
                                       <p>RECENT <?php echo $ccy_k;?></p>
                                       <?php if(count($ccy_recent)){?>
                                           <ul class="list-group no-border">
                                               <?php foreach($ccy_recent as $recent_item){?>
                                                   <li class="list-group-item">
                                                       <label style="color: <?php if($recent_item['credit']>0){ echo 'red';}else{ echo 'black';}?>">
                                                           <?php echo $recent_item['credit']>0?'-':'+'?>
                                                           <?php echo $recent_item['credit']+$recent_item['debit']?>
                                                       </label>
                                                       <span style="padding-left: 20px">
                                                           <?php echo $recent_item['subject']?>
                                                       </span>
                                                       <span style="font-size: 8px;font-style: italic">
                                                           <?php echo $recent_item['update_time']?>
                                                       </span>
                                                   </li>
                                               <?php }?>
                                           </ul>
                                       <?php }else{?>
                                           <?php include(template(":widget/no_record"))?>
                                       <?php }?>

                                   <?php }?>



                               </li>
                           </ul>

                       </div>
                   </div>
               </div>
                <div class="col-sm-6">
                    <?php if($output['bank_list']){?>
                        <?php foreach($output['bank_list'] as $bank){?>
                            <?php include(template("treasure/branch.bank.item"))?>
                        <?php }?>
                    <?php }else{?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5><i class="fa fa-credit-card"></i> Bank</h5>
                            </div>
                            <div class="panel panel-body">
                                <?php include(template(":widget/no_record"))?>
                            </div>
                        </div>
                    <?php }?>
                </div>
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
                        <input type="hidden" id = 'bank_id' name="bank_id" value="">
                        <input type="hidden" id = 'branch_id' name="branch_id" value="">
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
        $('#myModal #branch_id').val('<?php echo $branch['uid']?>');
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
            var m = 'bankDepositByHQ';
        } else if (type == 'withdraw_hq') {
            var m = 'bankWithdrawByHQ';
        } else if (type == 'deposit_br') {
            var m = 'bankDepositByBranch';
        } else if (type == 'withdraw_br') {
            var m = 'bankWithdrawByBranch';
        } else {
            var m = 'bankAdjust';
        }
        yo.loadData({
            _c: 'treasure',
            _m: m,
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    $('#myModal').modal('hide');
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
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