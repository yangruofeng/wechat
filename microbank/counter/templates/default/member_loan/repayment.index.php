<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js"></script>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    em {
        font-weight: 600;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .account-basic{
        margin-bottom: 0;
    }

    .text-small {
        margin-bottom: 0;
    }

    .basic-info {
        margin-bottom: 20px;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    .authorize_a {
        position: relative;
        width: 200px;
    }

    #notCheckManager, #notCheckCashier {
        width: 20px;
        position: absolute;
        top: 6px;
        right: 10px;
    }

    #checkCashierFailure, #checkManagerFailure {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 10px;
    }

    #checkDoneManager, #checkCashierDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 10px;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }

</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="row" style="max-width: 1300px;margin-bottom: 50px">
        <div class="col-sm-12 col-md-10 col-lg-7">
            <div class="basic-info">
                <?php include_once(template("widget/item.member.summary.v2"))?>
                <input type="hidden" id="member_id" value="<?php echo $output['member_id'] ?>">

            </div>

            <div class="basic-info" id="product-list">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Repayment Product</h5>
                </div>
                <div class="content" style="padding: 5px">
                    <div class="product-list">
                        <?php include(template("member_loan/repayment.product.list"));?>

                    </div>
                </div>
            </div>
            <div class="basic-info" id="plan-list" style="display: none;">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Repayment Plan</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showProductList()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"></i>
                </div>
                <div class="content" style="padding: 5px">
                    <div class="plan-list">

                    </div>
                </div>
            </div>
            <div class="basic-info" id="repayment-one" style="display: none;">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Repayment Detail</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showPlanList()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"></i>
                </div>
                <div class="content" style="padding: 5px">
                    <div class="repayment-one">

                    </div>
                </div>
            </div>
            <div class="basic-info" id="repayment-two" style="display: none;">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Repayment Authorize</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showRepaymentOne()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"></i>
                </div>
                <div class="content" style="padding: 5px">
                    <div class="repayment-two">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-10 col-lg-5">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5>History</h5>
                </div>
                <div class="business-content">
                    <div class="history-list">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        btn_search_onclick();
        showRepaymentOne();
        showPlanList();
        showProductList();

    });


    //显示产品列表
    function showProductList() {
        $('#product-list').show();
        $('#plan-list').hide();
    }

    //显示还款列表
    function showPlanList() {
        $('#plan-list').show();
        $('#repayment-one').hide();
    }

    //展示还款第一步
    function showRepaymentOne() {
        $('#repayment-one').show();
        $('#repayment-two').hide();
    }
    function showNextRepaymentDetail(_member_id,_member_credit_category_id){
        if (!_member_id && !_member_credit_category_id) {
            return;
        }
        $(document).waiting();
        yo.dynamicTpl({
            tpl: "member_loan/repayment.plan.list",
            control: 'counter_base',
            dynamic: {
                api: "member_loan",
                method: "getNextRepaymentByProduct",
                param: {member_id: _member_id, member_credit_category_id: _member_credit_category_id}
            },
            callback: function (_tpl) {
                $(".plan-list").html(_tpl);
                $(document).unmask();
                scheme_select();
                $('#product-list').hide();
                $('#plan-list').show();
            }
        });
    }
    function showContractRepaymentDetail(_member_id,_contract_id){
        if (!_member_id && !_contract_id) {
            return;
        }
        $(document).waiting();
        yo.dynamicTpl({
            tpl: "member_loan/repayment.plan.list",
            control: 'counter_base',
            dynamic: {
                api: "member_loan",
                method: "getRepaymentSchemaByContract",
                param: {member_id: _member_id, contract_id: _contract_id}
            },
            callback: function (_tpl) {
                $(".plan-list").html(_tpl);
                $(document).unmask();
                scheme_select();
                $('#product-list').hide();
                $('#plan-list').show();
            }
        });
    }
    //选择还款计划
    function scheme_select() {
        $('#plan-list .currency_total').each(function () {
            var currency = $(this).attr('currency');
            var _total = 0;
            $('.checkbox-amount[currency="' + currency + '"]:checked').each(function () {
                var _amount = $(this).attr('total');
                _total += Number(_amount);
            })
            $(this).text(formatCurrency(_total));
        })
    }

    //还款第一步
    function repayment_step1(member_id) {
        if (!member_id) {
            return;
        }
//        if ($('.checkbox-amount:checked').length == 0) {
//            alert('Please select scheme');
//            return;
//        }
        var values = getFormJson('#repayment-scheme');
        values.member_id = member_id;
        $(document).waiting();
        yo.dynamicTpl({
            tpl: "member_loan/repayment.step.one",
            control: 'counter_base',
            dynamic: {
                api: "member_loan",
                method: "repaymentStepOne",
                param: values
            },
            callback: function (_tpl) {
                $(document).unmask();
                $(".repayment-one").html(_tpl);
                $('#plan-list').hide();
                $('#repayment-one').show();
            }
        });
    }

    //还款第二步
    function repayment_step2() {
        var values = $('#repayment_form_one').getValues();
        if (!values.usd_amount && !values.khr_amount) {
            alert('Please input repayment amount.');
            return;
        }

        if(Number(values.chk_over) < Number(values.khr_amount)){
            alert('Amount Over');
            return;
        }
        $(document).waiting();
        yo.dynamicTpl({
            tpl: "member_loan/repayment.step.two",
            control: 'counter_base',
            dynamic: {
                api: "member_loan",
                method: "repaymentStepTwo",
                param: values
            },
            callback: function (_tpl) {
                $(document).unmask();
                $(".repayment-two").html(_tpl);
                $('#repayment-two').show();
                $('#repayment-one').hide();
            }
        });

    }

    //刷卡验证
    function cashierPassword() {
        var card = window.external.swipeCard();
        var card_info=card.split("|");
        $("#repayment-two input[name='cashier_card_no']").val(card_info[0]);
        $("#repayment-two input[name='cashier_key']").val(card_info[1]);
        if($("#repayment-two input[name='cashier_card_no']").val()){
            $('#notCheckCashier').hide();
            $('#checkCashierFailure').hide();
            $('#checkCashierDone').show();
        }else {
            $('#notCheckCashier').hide();
            $('#checkCashierFailure').show();
        }

    }

    //刷卡验证
    function verifyManger() {
        var card = window.external.swipeCard();
        var card_info=card.split("|");
        $("#repayment-two input[name='chief_teller_card_no']").val(card_info[0]);
        $("#repayment-two input[name='chief_teller_key']").val(card_info[1]);
        if($("#repayment-two input[name='chief_teller_card_no']").val()){
            $('#notCheckManager').hide();
            $('#checkManagerFailure').hide();
            $('#checkDoneManager').show();
        }else {
            $('#notCheckManager').hide();
            $('#checkManagerFailure').show();
        }
    }

    //确认还款
    function submit_repayment() {
        if (!$("#repayment-form-two").valid()) {
            return
        }
        var values = $('#repayment-form-two').getValues();
        showMask();
        yo.loadData({
            _c: 'member_loan',
            _m: 'submitRepayment',
            param: values,
            callback: function (_o) {
                hideMask();
                if (_o.STS) {
                    alert("Repaid Successful",1,function(){
                        window.location.href = "<?php echo getUrl('member_loan', 'repaymentIndex', array('member_id'=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL) ?>";
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

    $('#repayment-form-two').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules: {
            cashier_card_no : {
                required: true
            },
            cashier_key : {
                required: true
            }
           /* chief_teller_card_no : {
                required: true
            },
            chief_teller_key : {
                required: true
            }*/
        },
        messages: {
            cashier_card_no : {
                required: '<?php echo 'Required'?>'
            },
            cashier_key : {
                required: '<?php echo 'Required'?>'
            }
            //chief_teller_card_no : {
            //    required: '<?php echo 'Required'?>'
            //},
            //chief_teller_key : {
            //    required: '<?php echo 'Required'?>'
            //}
        }
    });

    // 还款列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "member_loan/repayment.list",
            control:'counter_base',
            dynamic: {
                api: "member_loan",
                method: "getRepaymentList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".history-list").html(_tpl);
            }
        });
    }

    function print_repayment(biz_id) {
        window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printRepayment', array(), false, ENTRY_COUNTER_SITE_URL)?>&biz_id="+biz_id);
    }

</script>



