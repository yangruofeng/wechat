<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        border-radius: 0px;
    }

    .search-table input {
        height: 34px !important;
    }

    #search_text {
        width: 200px;
    }

    #btn_new {
        margin-left: 20px;
    }

    .info-div {
        margin-bottom: 60px;
    }

    .info-div .content {
        padding: 5px 0 0;
    }

    .info-div .content .table td {
        padding: 8px 20px;
    }

    .info-div .content .table.contract-table td:nth-child(1) {
        width: 20%;
    }

    .info-div .content .table.contract-table td:nth-child(2) {
        width: 30%;
    }

    .info-div .content .table.contract-table td:nth-child(3) {
        width: 20%;
    }

    .info-div .content .table.contract-table td:nth-child(4) {
        width: 30%;
    }

    .info-div .content .table td a {
        margin-left: 10px;
    }

    .info-div .content .table td label {
        margin-bottom: 0px;
    }

    .custom-btn-group {
        float: inherit;
    }

    .loan-exp-wrap {
        filter: alpha(Opacity=0);
        opacity: 0;
        z-index: 99;
        -moz-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -o-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -webkit-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        transition: top .2s ease-in-out, opacity .2s ease-in-out;
        visibility: hidden;
        position: absolute;
        top: 37px;
        right: 3px;
        padding: 7px 10px;
        border: 1px solid #ddd;
        background-color: #f6fcff;
        color: #5b9fe2;
        font-size: 12px;
        font-family: Arial, "Hiragino Sans GB", simsun;
    }

    .loan-exp-wrap .pos {
        position: relative;
    }

    .triangle-up {
        background-position: 0 -228px;
        height: 8px;
        width: 12px;
        display: block;
        position: absolute;
        top: -15px;
        right: 240px;
        bottom: auto;
    }

    .triangle-up {
        background-image: url(./resource/img/member/common-slice-s957d0c8766.png);
        background-repeat: no-repeat;
        overflow: hidden;
    }

    .loan-exp-table .t {
        color: #a5a5a5;
        font-size: 12px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a {
        color: #000;
        font-size: 14px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a .y {
        color: #ea544a;
    }

    tr.t td, tr.a td {
        padding: 4px 0px !important;
    }

    .contract-btn .btn {
        padding: 5px 7px;
    }

    #repaymentModal .modal-dialog {
        margin-top: 10px !important;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }

    .operation {
        margin-top: -30px;
        display: none;
    }
</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container" style="max-width: 1300px;margin-bottom: 40px">
        <div class="business-condition ">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text" onkeydown="if(event.keyCode==13){return false;}" placeholder="Search for CID/name/phone">
                                <span class="input-group-btn">
                                    <a type="button" class="btn btn-success" id="btn_search_list" onclick="_search_client();">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td>
                            <a type="button" class="btn btn-default" id="btn_new" href="<?php echo getUrl('member', 'loan', array(), false, ENTRY_COUNTER_SITE_URL)?>">
                                <i class="fa fa-address-card-o"></i>Contract
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="col-sm-12 col-md-10 col-lg-7" style="padding-left: 0px">
            <div class="basic-info client_info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Client Information</h5>
                </div>
                <div class="content">
                    <div style="padding: 5px 20px"></div>
                </div>
            </div>

            <div class="basic-info product_list" style="margin-top: 20px;display: none">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Product List</h5>
                </div>
                <div class="content">
                    <div style="padding: 5px 20px"></div>
                </div>
            </div>
            <div class="basic-info add_loan" style="margin-top: 20px;display: none">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Add Loan</h5>
                </div>
                <div id="loanbox" style="background-color: white">
                    <div class="modal-header">
                        <label id="myModalLabel"  class="modal-title control-label"></label>
                    </div>
                     <div class="modal-form clearfix" style="margin: 30px">
                            <form class="form-horizontal" id="my_form" action="<?php echo getUrl('member', 'addLoanContractStepOne', array(), false, ENTRY_COUNTER_SITE_URL) ?>" method="post">
                                <input type="hidden" id="product_id" name="product_id" value="<?php echo $product['uid'] ?>">
                                <input type="hidden" id="term_type" name="term_type" value="">
                                <input type="hidden" id="member_uid" name="member_uid" value="">
                                <input type="hidden" id="repayment_type" name="repayment_type" value="<?php echo $product['interest_type'] ?>">
                                <input type="hidden" id="repayment_period" name="repayment_period" value="<?php echo $product['repayment_type'] ?>">
                                <div class="col-sm-12">
                                    <label  class="col-sm-3 control-label"><span class="required-options-xing">*</span>Currency</label>
                                    <div class="col-sm-8">
                                        <select class="form-control currency" name="currency">
                                        </select>
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12" style="margin-top: 15px">
                                    <label  class="col-sm-3 control-label"><span class="required-options-xing">*</span>Amount</label>
                                    <div class="col-sm-8">
                                        <input type="number" class="form-control" name="amount" value="" placeholder="Please Input">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12" style="margin-top: 15px">
                                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Terms</label>
                                    <div class="col-sm-8">
                                        <select class="form-control terms" name="terms"></select>
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    <div style="text-align: center;padding-bottom: 20px">
                        <a onclick="showproduct()" class="btn btn-default"><i class="fa fa-reply"></i><?php echo 'Cancel'?></a>
                        <a class="btn btn-danger" onclick="modal_submit()"><i class="fa fa-check"></i><?php echo 'Next'?></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-10 col-lg-5" style="padding-left: 0px">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5>Add Contract History</h5>
                </div>
                <div class="business-content">
                    <div class="business-list">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        btn_search_onclick();
    })

    function _search_client() {
        var search_text = $.trim($('#search_text').val());
        if (!search_text) {
            return;
        }
        yo.dynamicTpl({
            tpl: "member/member.info",
            control: "counter_base",
            dynamic: {
                api: "member",
                method: "getMemberInfo",
                param: {search_text: search_text}
            },
            callback: function (_tpl) {
                $(".client_info .content").html(_tpl);
                var _member_uid = $('#member_id').val();
                var _credit_state = $('#credit_state').val();
                if (!_member_uid) {
                    return;
                }
                yo.dynamicTpl({
                    tpl: "member/member.product.list",
                    control: "counter_base",
                    dynamic: {
                        api: "member",
                        method: "getMemberProductList",
                        param: {uid: _member_uid}
                    },
                    callback: function (_tpl) {
                        $(".product_list .content").html(_tpl);
                        if(_credit_state == 1){
                            $('.add_loan_next').attr({'disabled':false,
                                                 'title':''
                                                  });
                        }
                        $(".product_list").show();
                    }
                });
            }
        });
    }

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "member/add.contract.list",
            control:'counter_base',
            dynamic: {
                api: "member",
                method: "getAddContractList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function showLoan(product_id, product_name,interest_type, repayment_type) {
        $('input[name="amount"]').val('');
        $('input[name="loan_time"]').val('');
        $('#product_id').val(product_id);
        $('#repayment_type').val(interest_type);
        $('#repayment_period').val(repayment_type);
        $('#myModalLabel').html('Product Name ：'+product_name);
        var member_id = $('#member_id').val();
        $('#member_uid').val(member_id);
        yo.loadData({
            _c: "member",
            _m: "getTimeAndCurrencyChoose",
            param: {sub_product_id: product_id, member_id: member_id},
            callback: function (_o) {
                if (_o.STS) {
                    var _currency=_o.DATA.currency;
                    var _terms = _o.DATA.loan_time.terms;
                    var _term_type = _o.DATA.loan_time.term_type;
                    $('#term_type').val(_term_type);
                    if (_term_type == 1) {
                        var _term_type_str = 'Days';
                    } else {
                        var _term_type_str = 'Months';
                    }
                    var _option_time = '<option value="">Please Select</option>'
                    for (var i = 0; i < _terms.length; ++i) {
                        var _term = _terms[i];
                        _option_time += '<option  value="' + _term + '">' + _term + ' ' + _term_type_str + '</option>'
                    }
                    $('.terms').html(_option_time);
                    var _option_currency = '<option value="">Please Select</option>'
                    for (var i = 0; i < _currency.length; ++i) {
                        var currency = _currency[i].currency;
                        _option_currency += '<option  value="' + currency + '">' + currency + '</option>'
                    }

                    $('.currency').html(_option_currency);
                } else {
                    alert(_o.MSG);
                }
            }
        });

        $('.product_list').hide();
        $('.add_loan').show();
    }


    function showproduct() {
        $('.add_loan').hide();
        $('.product_list').show();
    }

    function modal_submit() {

        if (!$("#my_form").valid()) {
            return;
        }
        $('#my_form').submit();
    }

    $('#my_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.closest('.col-sm-8').find('.error_msg'));
        },
        rules : {
            amount : {
                required : true,
                chkAmount : true
            },
            currency : {
                required : true
            },
            terms : {
                required : true
            }
        },
        messages : {
            amount : {
                required : 'Required',
                chkAmount : 'Out of credit balance'
            },
            currency : {
                required : 'Required'
            },
            terms : {
                required : 'Required'
            }
        }
    });
    jQuery.validator.addMethod("chkAmount", function (value, element) {
        var currency = $('#my_form [name="currency"]').val();
        var credit_balance = $('#credit_balance_' + currency).val();
        credit_balance = Number(credit_balance);
        value = Number(value);
        value = value.toFixed(2);
        if (credit_balance >= value) {
            return true;
        } else {
            return false;
        }
    });

</script>