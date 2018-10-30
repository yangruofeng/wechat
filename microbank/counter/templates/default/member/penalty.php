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
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="row" style="max-width: 1300px">
        <div class="col-sm-12 col-md-10 col-lg-7">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Basic Information</h5>
                </div>
                <div class="content">
                    <div class="col-sm-6 mincontent">
                        <div class="input-group" style="width: 300px">
                        <span class="input-group-addon" style="padding: 0;border: 0;">
                            <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                <option value="855" <?php echo $client_info['phone_country'] == 855 ? 'selected' : '' ?>>+855</option>
                                <option value="66" <?php echo $client_info['phone_country'] == 66 ? 'selected' : '' ?>>+66</option>
                                <option value="86" <?php echo $client_info['phone_country'] == 86 ? 'selected' : '' ?>>+86</option>
                            </select>
                        </span>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $client_info['phone_number']; ?>" placeholder="">
                            <span class="input-group-btn">
                            <button type="button" class="btn btn-default" id="btn_search" style="height: 30px;line-height: 14px;border-radius: 0">
                                <i class="fa fa-search"></i>
                                Search
                            </button>
                        </span>
                        </div>
                        <div class="search-other">
                            <img src="resource/img/member/phone.png">
                            <img src="resource/img/member/qr-code.png">
                            <img src="resource/img/member/bank-card.png">
                        </div>
                    </div>
                    <div class="col-sm-6 mincontent">
                        <dl class="account-basic clearfix">
                            <dt class="pull-left">
                            <p class="account-head">
                                <img id="member-icon" src="resource/img/member/bg-member.png" class="avatar-lg">
                            </p>
                            </dt>
                            <dd class="pull-left margin-large-left">
                                <input type="hidden" id="client_id" name="client_id" value="<?php echo intval($client_info['uid']) ?>">

                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Login Account</span>:
                                    <span class="marginleft10" id="login-account"><?php echo $client_info['login_code'] ?></span>
                                </p>

                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Khmer Name</span>:
                                    <span class="marginleft10" id="khmer-name"><?php echo $client_info['kh_display_name'] ?></span>
                                </p>

                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Member State</span>:
                                    <span class="marginleft10" id="member_state"><?php echo $memberStateLang[$output['client_info']["member_state"]] ?></span>
                                </p>

                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Member Grade</span>:
                                    <span class="marginleft10" id="member-grade"><?php echo $client_info['member_grade'] ?></span>
                                </p>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="basic-info" id="plan-detail">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Due Penalty</h5>
                </div>
                <div class="penalty-list">

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

                </div>
            </div>
            <div class="basic-info" id="receive_money" style="display: none;margin-bottom: 80px">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Receive Money</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showPendingPenalty()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"></i>
                </div>
                <div class="content" style="padding: 5px">
                    <div class="receive_money">

                    </div>
                </div>
            </div>
            <div class="basic-info" id="penalty-two" style="display: none;">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Penalty Authorize</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showReceiveMoney()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"></i>
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
        search_click();
        btn_search_onclick();
        $('#btn_search').click(function () {
            search_click()
        })
    })

    function search_click() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('#phone').val();
        if (!$.trim(phone)) {
            return;
        }

        yo.loadData({
            _c: 'member',
            _m: 'getClientInfo',
            param: {
                country_code: country_code,
                phone: phone
            },
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $('#member-icon').attr('src', data.member_icon ? data.member_icon : 'resource/img/member/bg-member.png');
                    $('#client_id').val(data.uid);
                    $('#login-account').html(data.login_code);
                    $('#khmer-name').html(data.kh_display_name);
                    $('#member_state').html(data.member_state_text);
                    $('#member-grade').html(data.grade_code);

                    yo.dynamicTpl({
                        tpl: "member/penalty.plan.list",
                        control:'counter_base',
                        dynamic: {
                            api: "member",
                            method: "getPenaltyPlan",
                            param: {member_id: data.uid}
                        },
                        callback: function (_tpl) {
                            $(".penalty-list").html(_tpl);
                        }
                    });

                    yo.dynamicTpl({
                        tpl: "member/pending.penalty.list",
                        control:'counter_base',
                        dynamic: {
                            api: "member",
                            method: "getPendingPenaltyPlan",
                            param: {member_id: data.uid}
                        },
                        callback: function (_tpl) {
                            $(".pending-list").html(_tpl);
                        }
                    });

                    showPenaltyDetail();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#phone').focus(function(){
        $('#phone').val('');
    });

    $('#phone').bind('keydown', function (event) {
        if (event.keyCode == "13") {
            search_click();
        }
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
            tpl: "member/penalty.list",
            control: 'counter_base',
            dynamic: {
                api: "member",
                method: "getPenaltyList",
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
            tpl: "member/penalty.step.one",
            control: 'counter_base',
            dynamic: {
                api: "member",
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
            _c: 'member',
            _m: 'submitReducePenaltyApply',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member', 'penalty', array(), false, ENTRY_COUNTER_SITE_URL) ?>";
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
            tpl: "member/receive.money",
            control: 'counter_base',
            dynamic: {
                api: "member",
                method: "receiveMoney",
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
        if (!values.usd_amount && !values.khr_amount) {
            alert('Please input amount.');
            return;
        }
        yo.dynamicTpl({
            tpl: "member/penalty.step.two",
            control: 'counter_base',
            dynamic: {
                api: "member",
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

    //刷卡验证
    function cashierPassword() {
        var card = window.external.swipeCard();
        var card_info=card.split("|");
        $("#penalty-two input[name='cashier_card_no']").val(card_info[0]);
        $("#penalty-two input[name='cashier_key']").val(card_info[1]);
        $('#notCheckCashier').hide();
        $('#checkCashierDone').show();
    }

    //刷卡验证
    function verifyManger() {
        var card = window.external.swipeCard();
        var card_info=card.split("|");
        $("#penalty-two input[name='chief_teller_card_no']").val(card_info[0]);
        $("#penalty-two input[name='chief_teller_key']").val(card_info[1]);
        $('#notCheckManager').hide();
        $('#checkDoneManager').show();
    }

    //member密码
    function clientPassword() {
        var client_password = window.external.inputPasswordWithKeyInfo('');
        $("input[name='client_trade_pwd']").val(client_password);
        $('#notCheckPassword').hide();
        $('#checkPasswordDone').show();
    }

    function submit_Penalty() {
        if (!$("#penalty-form-two").valid()) {
            return
        }
        var values = $('#penalty-form-two').getValues();
        yo.loadData({
            _c: 'member',
            _m: 'submitPenalty',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member', 'penalty', array(), false, ENTRY_COUNTER_SITE_URL) ?>";
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
            },
            chief_teller_card_no : {
                required: true
            },
            chief_teller_key : {
                required: true
            }
        },
        messages: {
            cashier_card_no : {
                required: '<?php echo 'Required'?>'
            },
            cashier_key : {
                required: '<?php echo 'Required'?>'
            },
            chief_teller_card_no : {
                required: '<?php echo 'Required'?>'
            },
            chief_teller_key : {
                required: '<?php echo 'Required'?>'
            }
        }
    });

</script>



