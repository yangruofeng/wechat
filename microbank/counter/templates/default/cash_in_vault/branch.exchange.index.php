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
        padding-left: 0 !important;
        margin-bottom: 60px;
    }

    .page .col-sm-5 {
        padding-right: 0 !important;
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
                    <h5><i class="fa fa-id-card-o"></i>Exchange Currency</h5>
                </div>
                <div class="content">
                    <form id="exchange_currency">
                        <div class="mincontent">
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label" ><span class="required-options-xing">*</span><?php echo 'From Currency'?></label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="from_currency" onchange="chooseCurrencyForChange(this)" id="from_currency">
                                        <?php $ccy_list=(new currencyEnum())->Dictionary();
                                        foreach($ccy_list as $k=>$v){?>
                                            <option value="<?php echo $v;?>" ><?php echo $k;?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label" ><span class="required-options-xing">*</span><?php echo 'To Currency'?></label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="to_currency">
                                        <option id="to_currency" value="<?php echo 'KHR'?>"><?php echo 'KHR'?></option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Amount'?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control pitch" name="amount" value="" onfocus="select()">
                                    <div class="error_msg"></div>
                                </div>
                            </div>

<!--                            <div class="col-sm-12 form-group">-->
<!--                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>--><?php //echo 'Exchange Rate';?><!--</label>-->
<!--                                <div class="col-sm-9">-->
<!--                                    <input type="text" id="exchange_rate" class="form-control" name="exchange_rate" value="" onfocus="select()">-->
<!--                                    <div class="error_msg"></div>-->
<!--                                </div>-->
<!--                            </div>-->
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><?php echo 'Remark'?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="remark" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group" style="text-align: center;margin-bottom: 0px">
                                <button type="button" class="btn btn-primary" onclick="submit_form()">
                                    <i class="fa fa-arrow-right"></i>Next
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div class="basic-info" style="margin-top: 10px">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Branch Balance</h5>
                </div>
                <div class="content">
                    <?php $currency_list = (new currencyEnum())->Dictionary(); ?>
                    <?php foreach ($currency_list as $key => $currency) { ?>
                        <span style="font-weight: 600"><?php echo $currency;?> : </span><span style="margin-right: 50px;font-weight: 600" class="cash-in-hand" currency="cash_<?php echo $key?>"></span>
                        <input id="<?php echo $currency?>" class="cash-in-hand_input" type="hidden" value="" currency="cash_<?php echo $key?>">
                    <?php }?>

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
//        if($('#from_currency').val() == 'USD'){
//            $('#exchange_rate').val("<?php //echo $output['usd_exchange_khr']?>//")
//        }else {
//            $('#exchange_rate').val("<?php //echo $output['khr_exchange_usd']?>//")
//        }
    });


    function chooseCurrencyForChange(_e) {
        if($(_e).val()=='USD'){
            $('#to_currency').val('KHR');
            $('#to_currency').text('KHR');
//            $('#exchange_rate').val("<?php //echo $output['usd_exchange_khr']?>//")
        }else {
            $('#to_currency').val('USD');
            $('#to_currency').text('USD');
//            $('#exchange_rate').val("<?php //echo $output['khr_exchange_usd']?>//")
        }
    }


    //  展示成功cash out 列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl:"cash_in_vault/branch.exchange.history",
            control:'counter_base',
            dynamic: {
                api: "cash_in_vault",
                method: "getExchangeHistory",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }



    function submit_form() {
        if (!$("#exchange_currency").valid()) {
            return;
        }
        var values = $('#exchange_currency').getValues();
        var _amount = values.amount;
        var _currency = values.from_currency;
        if(Number(_amount) > Number($('#'+_currency).val())){
            alert('Over System Account Balance');
            return
        }
        $(document).waiting();
        yo.loadData({
            _c: 'cash_in_vault',
            _m: 'submitExchange',
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    var biz_id=_o.DATA.biz_id;
                    window.location.href= "<?php echo getUrl('cash_in_vault', 'exchangeCheck', array(), false, ENTRY_COUNTER_SITE_URL)?>&biz_id="+biz_id;
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#exchange_currency').validate({
        errorPlacement: function(error, element){
            element.closet('.form-group').find('.error_msg').html(error);
            //element.next().html(error);
        },
        rules : {
            currency : {
                required : true
            },
            amount : {
                required : true
            }
        },
        messages : {
            currency: {
                required: 'Required'
            },
            amount: {
                required: 'Required'
            }
        }
    });

    $(function () {
        getBalance();
    });

    function getBalance() {
        yo.loadData({
            _c: 'cash_in_vault',
            _m: 'getBranchBalance',
            param: '',
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $('.cash-in-hand').each(function () {
                        var currency = $(this).attr('currency');
                        $(this).text(data[currency]);
                    })
                    $('.cash-in-hand_input').each(function () {
                        var currency = $(this).attr('currency');
                        $(this).val(data[currency]);
                    })

                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

</script>



