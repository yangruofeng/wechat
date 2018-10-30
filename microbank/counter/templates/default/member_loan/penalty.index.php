<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js"></script>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .container {
        width: 800px !important;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    .collection-div {
        margin-bottom: 70px;
    }

    .authorize_input {
        margin-top: -8px !important;
        margin-bottom: 10px;
    }

    .account-basic {
        margin-bottom: 0;
    }

    .text-small {
        margin-bottom: 0;
    }

    .basic-info {
        margin-bottom: 20px;
    }
    .limitbox td{
        background-color: white;
    }

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

    #notCheckManager, #notCheckCashier,#notCheckPassword{
        width: 20px;
        position: absolute;
        top: 6px;
        right: 6px;
    }

    #checkCashierFailure, #checkManagerFailure, #checkPasswordFailure{
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 6px;
    }

    #checkDoneManager, #checkCashierDone,#checkPasswordDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 6px;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }

</style>
<?php $memberStateLang =  enum_langClass::getMemberStateLang() ?>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="row" style="max-width: 1300px">
        <div class="col-sm-12 col-md-10 col-lg-7">
            <div class="basic-info">
                <?php include(template("widget/item.member.summary.v2"))?>
                <input type="hidden" id="client_id" value="<?php echo $output['member_id']?>">
            </div>
            <div class="basic-info" id="plan-detail">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Due Penalty</h5>
                </div>
                <div class="penalty-list">
                    <?php include(template("member_loan/penalty.plan.list"))?>
                </div>
            </div>
            <div class="basic-info" id="penalty-one" style="display: none;">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Penalty Detail</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showPenaltyDetail()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"></i>
                </div>
                <div class="content" style="padding: 5px">
                    <div class="penalty-one">

                    </div>
                </div>
            </div>

            <div class="basic-info" id="pending-detail" style="margin-bottom: 80px">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Pending Penalty</h5>
                </div>
                <div class="pending-list">
                    <?php include(template("member_loan/penalty.pending.list"))?>
                </div>
            </div>
            <div class="basic-info" id="receive_money" style="display: none;margin-bottom: 80px">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Receive Money</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showPendingPenalty()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"> <span style="font-size: 13px">Back</span></i>
                </div>
                <div class="content" style="padding: 5px">
                    <div class="receive_money">

                    </div>
                </div>
            </div>
            <div class="basic-info" id="penalty-two" style="display: none;">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Penalty Authorize</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showReceiveMoney()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"> <span style="font-size: 13px">Back</span></i>
                </div>
                <div class="content" style="padding: 5px">
                    <div class="penalty-two">

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
                    <div class="business-list">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        btn_search_onclick();
        showPenaltyDetail();
    });

    //  展示penalty列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "member_loan/penalty.history.list",
            control: 'counter_base',
            dynamic: {
                api: "member_loan",
                method: "getPenaltyHistoryList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });

    }

    //减免罚金第一步
    function submit_apply_one() {
        var _member_id = $('#client_id').val();
        if (!_member_id) {
            return;
        }
        yo.dynamicTpl({
            tpl: "member_loan/penalty.step.one",
            control: 'counter_base',
            dynamic: {
                api: "member_loan",
                method: "reducePenaltyApplyOne",
                param: {member_id: _member_id}
            },
            callback: function (_tpl) {
                $(".penalty-one").html(_tpl);
                $('#plan-detail').hide();
                $('#pending-detail').hide();
                $('#penalty-one').show();
            }
        });
    }

    //选择货币
    function selectCurrency(_e) {
        var currency = $(_e).val();
        $('.convert_total').hide();
        $('#' + currency + '_total').show();
        $('input[name="deducting"]').val('');
        if (currency != 0) {
            var _total = $('#' + currency + '_total_hidden').val();
            $('#actual_amount').val(formatCurrency(_total));
        } else {
            $('#actual_amount').val('');
        }
    }

    //计算减免金额
    function reduceChange() {
        var _currency = $('select[name="currency"]').val();
        if (_currency != 0) {
            var _total = Number($('#' + _currency + '_total_hidden').val());
            var _reduce = Number($('input[name="deducting"]').val());
            var _actual = _total - _reduce;
            if (_actual < 0) {
                $('input[name="deducting"]').val('');
                alert('Deducting amount not > penalty amount.');
            } else {
                $('#actual_amount').val(formatCurrency(_actual));
            }
        }
    }

    //提交减免申请
    function submit_apply_two() {
        var _member_id = $('#client_id').val();
        var values = $('#penalty-form-one').getValues();
        values.member_id = _member_id;
        yo.loadData({
            _c: 'member_loan',
            _m: 'submitReducePenaltyApply',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member_loan', 'penaltyIndex', array('member_id'=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function showPenaltyDetail() {
        $("#penalty-one").hide();
        $("#plan-detail").show();
        $("#pending-detail").show();
    }

    function showPendingPenalty() {
        $("#receive_money").hide();
        $("#plan-detail").show();
        $("#pending-detail").show();
    }

    function showReceiveMoney() {
        $("#penalty-two").hide();
        $("#receive_money").show();
    }

    function receive_money(receipt_id,member_id) {
        yo.dynamicTpl({
            tpl: "member_loan/penalty.receive.money",
            control: 'counter_base',
            dynamic: {
                api: "member_loan",
                method: "penaltyReceiveMoney",
                param: {receipt_id:receipt_id,member_id:member_id}
            },
            callback: function (_tpl) {
                $(".receive_money").html(_tpl);
                $('#receive_money').show();
                $('#plan-detail').hide();
                $('#pending-detail').hide();
            }
        });
    }

    function chooseCurrency() {
        var currency = $('#confirm_currency').val();
        $('.space').hide();
        $('.convert_amount').hide();
        $('.convert_amount').attr('disabled',true);
        $('#' + currency + '_amount').attr('disabled',false);
        $('#' + currency + '_amount').show();
    }

    function penalty_apply_two() {
        var values = $('#penalty_two').getValues();
//        if (!values.usd_amount && !values.khr_amount) {
//            alert('Please input amount.');
//            return;
//        }
        yo.dynamicTpl({
            tpl: "member_loan/penalty.step.two",
            control: 'counter_base',
            dynamic: {
                api: "member_loan",
                method: "penaltyStepTwo",
                param: values
            },
            callback: function (_tpl) {

                $(".penalty-two").html(_tpl);
                $('#penalty-two').show();
                $('#receive_money').hide();
            }
        });
    }


    function callWin_snapshot_slave() {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $('input[name="member_image"]').val(_img_path);
                    $("#img_slave").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                }
            } catch (ex) {
                alert(ex.Message);

            }
        }
    }

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

    function clientPassword() {
        var client_password = window.external.inputPasswordWithKeyInfo('');
        $("input[name='client_trade_pwd']").val(client_password);
        if($("input[name='client_trade_pwd']").val()){
            $('#notCheckPassword').hide();
            $('#checkPasswordFailure').hide();
            $('#checkPasswordDone').show();
        }else{
            $('#notCheckPassword').hide();
            $('#checkPasswordFailure').show();
        }
    }

    function verifyManger() {
        var card = window.external.swipeCard();
        var card_info=card.split("|");
        $("input[name='chief_teller_card_no']").val(card_info[0]);
        $("input[name='chief_teller_key']").val(card_info[1]);
        if($("input[name='chief_teller_card_no']").val()){
            $('#notCheckManager').hide();
            $('#checkManagerFailure').hide();
            $('#checkDoneManager').show();
        }else{
            $('#notCheckManager').hide();
            $('#checkManagerFailure').show();
        }
    }


    function submit_Penalty() {
        if (!$("#penalty-form-two").valid()) {
            return
        }
        var values = $('#penalty-form-two').getValues();
        yo.loadData({
            _c: 'member_loan',
            _m: 'submitPenalty',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member_loan', 'penaltyIndex', array('member_id'=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#penalty-form-two').validate({
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
            /*chief_teller_card_no : {
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

</script>



