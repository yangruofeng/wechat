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

</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <?php require_once template('widget/branch.balance'); ?>
    <div class="row" style="max-width: 1300px">
        <div class="col-sm-12 col-md-8 col-lg-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Cash Out</h5>
                </div>
                <div class="content">
                    <form id="cash_out">
                        <div class="mincontent">
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label" ><span class="required-options-xing">*</span><?php echo 'Currency'?></label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="currency">
                                        <?php $ccy_list=(new currencyEnum())->Dictionary();
                                        foreach($ccy_list as $k=>$v){?>
                                            <option value="<?php echo $v;?>"><?php echo $k;?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label" ><span class="required-options-xing">*</span><?php echo 'Extra Type'?></label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="extra_type">
                                        <?php $ccy_list=(new currencyEnum())->Dictionary();
                                        foreach($output['extra_type'] as $k=>$v){?>
                                            <option value="<?php echo $k;?>"><?php echo $v['trade_type'];?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Amount'?></label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="amount" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Remark'?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="remark" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group" style="text-align: center;margin-bottom: 0px">
                                <button type="button" class="btn btn-primary" onclick="submit_cash_out()">
                                    <i class="fa fa-arrow-right"></i>Submit
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
        <div class="col-sm-12 col-md-8 col-lg-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5>History</h5>
                </div>
                <div class="business-content">
                    <div class="business-list">

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });



    //  展示成功cash out 列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl:"cash_in_vault/cash.out.list",
            control:'counter_base',
            dynamic: {
                api: "cash_in_vault",
                method: "getCashOutList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }



    function submit_cash_out() {
        if (!$("#cash_out").valid()) {
            return;
        }
        var values = $('#cash_out').getValues();
        var _amount = values.amount;
        var _currency = values.currency;
        if(Number(_amount) > Number($('#'+_currency).val())){
            alert('Over System Account Balance');
            return
        }
        $(document).waiting();
        yo.loadData({
            _c: 'cash_in_vault',
            _m: 'submitCashOut',
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert("Submit Success!");
//                    var _biz_id=_o.DATA.biz_id;
                    setTimeout(function () {
                        window.location.reload();
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#cash_out').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules : {
            currency : {
                required : true
            },
            amount : {
                required : true
            },
            remark : {
                required : true
            }
        },
        messages : {
            currency: {
                required: 'Required'
            },
            amount: {
                required: 'Required'
            },
            remark: {
                required: 'Required'
            }
        }
    });
</script>



