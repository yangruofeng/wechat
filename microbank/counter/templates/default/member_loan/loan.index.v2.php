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

        $('#loan_amount').blur(function(){
            initLoanTime();
        });
    });

    var LOAN_CATEGORY_OPTION = null;
    var GLOBAL_OPTION_LOAN_TERMS_SELECT = '<option value="">Please Select</option>';

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

    function initLoanTime()
    {
        var _currency = $('#loan_currency').val();
        var _amount = $('#loan_amount').val();
        _amount = _amount?parseFloat(_amount):0;

        $('#loan_terms').html(GLOBAL_OPTION_LOAN_TERMS_SELECT);


        // 获取当前组合的loan term
        if( !LOAN_CATEGORY_OPTION || LOAN_CATEGORY_OPTION[_currency]){
            //$('#loan_terms').html(GLOBAL_OPTION_LOAN_TERMS_SELECT);
            alert('No match loan terms for amount: '+_amount+_currency);
            return false;
        }

        var _terms = '';
        var _term_type = '';


        var _currency_list = LOAN_CATEGORY_OPTION['currency_list'];
        if( !_currency_list || _currency_list.length < 1 ){
            alert('No match loan terms for amount: '+_amount+_currency);
            return false;
        }
        var _amount_list = _currency_list[_currency];
        if( !_amount_list || _amount_list.length < 1 ){
            alert('No match loan terms for amount: '+_amount+_currency);
            return false;
        }


        for( var i in _amount_list ){
            var _item = _amount_list[i];
            if( _amount >= _item['loan_size_min'] && _amount <= _item['loan_size_max'] ){
                _term_type = _item['term_type'];
                _terms = _item['terms'];
                break;
            }
        }

        if( !_terms || _terms.length<1 ){
            alert('No match loan terms for amount: '+_amount+_currency);
            return false;
        }


        $('#term_type').val(_term_type);
        var _term_type_str = '';
        if (_term_type == 1) {
            _term_type_str = 'Days';
        } else {
            _term_type_str = 'Months';
        }
        var _option_time = GLOBAL_OPTION_LOAN_TERMS_SELECT;
        for (var i = 0; i < _terms.length; ++i) {
            var _term = _terms[i];
            _option_time += '<option  value="' + _term + '">' + _term + ' ' + _term_type_str + '</option>'
        }
        $('#loan_terms').html(_option_time);

    }

    function showLoan(product_id, m_uid,product_credit,product_name,repayment_way,interest_type, repayment_type,_currency) {
       showMask();
        var _args={};
        _args.member_id=<?php echo $output['member_id']?>;
        _args.product_id=product_id;
        _args.m_uid=m_uid;
        _args.product_credit=product_credit;
        _args.product_name=product_name;
        _args.repayment_way=repayment_way;
        _args.interest_type=interest_type;
        _args.repayment_type=repayment_type;
        _args.currency=_currency;
        _args.sub_product_id=product_id;

        yo.dynamicTpl({
            tpl:'member_loan/loan.any.time.condition',
            control:'counter_base',
            dynamic:{
                api:"member_loanV2",
                method:"ajaxGetLoanCategoryOption",
                param:_args
            },
            callback:function(_tpl){
                $("#loanbox").html(_tpl);
                $('input[name="loan_time"]').val('');
                $('.product_list').hide();
                $('.add_loan').show();
                $('#loan_amount').blur(function(){

                    initLoanTime();
                });

                hideMask();
            }
        });
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