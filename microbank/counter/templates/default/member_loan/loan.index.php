<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .text-small {
        margin-bottom: 0;
    }
    
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
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container" style="max-width: 1300px;margin-bottom: 40px">
        <div class="col-sm-12 col-md-10 col-lg-7" style="padding-left: 0px">
            <div class="basic-info">
                <?php include_once(template("widget/item.member.summary.v2"))?>
                <?php $client_info=$output['client_info'];$credit_balance=$output['credit_balance'];?>

                <input type="hidden" id="credit_state" value="<?php echo $client_info['credit_is_active']; ?>">

                <input type="hidden" id="member_id" value="<?php echo $output['member_id'] ?>">
                <?php foreach ($credit_balance as $key => $val) { ?>
                    <input type="hidden" id="credit_balance_<?php echo $key?>" value="<?php echo $val?>">
                <?php } ?>
            </div>

            <div class="basic-info product_list" style="margin-top: 20px;">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Product List</h5>
                </div>
                <div class="content">
                   <?php include(template('member_loan/member.product.list'))?>
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
                        <form class="form-horizontal" id="my_form" action="<?php echo getUrl('member_loan', 'addLoanContractStepOne', array(), false, ENTRY_COUNTER_SITE_URL) ?>" method="post">
                            <input type="hidden" id="product_id" name="product_id" value="<?php echo $product['uid'] ?>">
                            <input type="hidden" id="term_type" name="term_type" value="">
                            <input type="hidden" id="member_uid" name="member_uid" value="">
                            <input type="hidden" id="product_credit" name="product_credit" value="">
                            <input type="hidden" id="m_uid" name="m_uid" value="">
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
                            <div class="col-sm-12" style="margin-top: 15px;text-align: center">
                                Max Amount(Balance):<label id="lbl_credit_currency_balance"></label>
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

                    <!--客户产品利率信息-->
                    <div >
                        <table class="table">
                            <thead style="background-color: #ddd;">
                                <tr>
                                    <th>Currency</th>
                                    <th>Loan Days</th>
                                    <th>Loan Amount</th>
                                    <th>Interest Rate</th>
                                    <th>Operation Fee</th>
                                </tr>
                            </thead>
                            <tbody id="interest_rate_list_body">

                            </tbody>
                        </table>
                    </div>



                </div>



            </div>
        </div>
        <div class="col-sm-12 col-md-10 col-lg-5" style="padding-left: 0px">
            <div class="basic-info" style="margin-bottom: 15px">
                <div class="ibox-title">
                    <h5>Pending cancel</h5>
                </div>
                <div class="business-content">
                    <div class="pending-list">

                    </div>
                </div>
            </div>
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
    $(function(){
        btn_search_onclick();
        btn_search_pending_cancel();
    });


    function  btn_search_pending_cancel() {
        var member_id = $('#member_id').val();
        yo.dynamicTpl({
            tpl: "member_loan/loan.pending.cancel.list",
            control:'counter_base',
            dynamic: {
                api: "member_loan",
                method: "getPendingCancelList",
                param: {member_id: member_id}
            },
            callback: function (_tpl) {
                $(".pending-list").html(_tpl);
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
            tpl: "member_loan/contract.add.history.list",
            control:'counter_base',
            dynamic: {
                api: "member_loan",
                method: "getAddContractList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }


    function showLoan(product_id, m_uid,product_credit,product_name,repayment_way,interest_type, repayment_type,_currency) {
        $(document).waiting();
        $('input[name="amount"]').val(product_credit);
        $('input[name="loan_time"]').val('');
        $('#product_id').val(product_id);
        $('#repayment_type').val(interest_type);
        $('#product_credit').val(product_credit);
        $('#repayment_period').val(repayment_type);
        $("#lbl_credit_currency_balance").text(product_credit);
        $('#myModalLabel').html('Product Name ：'+product_name+' ('+repayment_way+')');
        var member_id = '<?php echo $output['member_id']?>';
        $('#member_uid').val(member_id);
        yo.loadData({
            _c: "member_loan",
            _m: "getTimeAndCurrencyChoose",
            param: {sub_product_id: product_id, member_id: member_id,m_uid:m_uid},
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {

                    var m_uid=_o.DATA.m_uid;
                    $('input[name="m_uid"]').val(m_uid);
                    //var _currency=_o.DATA.currency;
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
                    var _option_currency = '<option value="'+_currency+'">'+_currency+'</option>';
                    $('.currency').html(_option_currency);

                    // 利率的信息
                    var _interest_rate_list_html = '';
                    var _rate_list = _o.DATA.rate_list;
                    for( var i in _rate_list){
                        var _current_rate = _rate_list[i];
                        _interest_rate_list_html += '<tr>';
                        _interest_rate_list_html += '<td>'+_current_rate['currency']+'</td>';
                        _interest_rate_list_html += '<td>'+_current_rate['min_term_days']+' - <br />'+_current_rate['max_term_days']+'</td>';
                        _interest_rate_list_html += '<td>'+_current_rate['loan_size_min']+' - <br />'+_current_rate['loan_size_max']+'</td>';
                        _interest_rate_list_html += '<td>'+_current_rate['interest_rate_used']+'%('+_current_rate['interest_rate_unit']+')</td>';
                        _interest_rate_list_html += '<td>'+_current_rate['operation_fee_used']+'%('+_current_rate['operation_fee_unit']+')</td>';
                        _interest_rate_list_html += '</tr>';
                    }
                    $('#interest_rate_list_body').html(_interest_rate_list_html);

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
        $(document).waiting();
        $('#my_form').submit();
        $(document).unmask();
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
        var product_credit = $('#product_credit').val();
        //var credit_balance = $('#credit_balance_' + currency).val();
       // credit_balance = Number(credit_balance);
        product_credit = Number(product_credit);
        value = Number(value);
        value = value.toFixed(2);
        if (product_credit >= value) {
            return true;
        } else {
            return false;
        }
    });

    function cancel(contract_id) {
        yo.loadData({
            _c: 'member_loan',
            _m: 'pendingCancel',
            param: {contract_id:contract_id},
            callback: function (_o) {
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

</script>