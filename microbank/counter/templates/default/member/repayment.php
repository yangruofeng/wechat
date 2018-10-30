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
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="row" style="max-width: 1300px;margin-bottom: 50px">
        <div class="col-sm-12 col-md-10 col-lg-7">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Client Information</h5>
                </div>
                <div class="content" style="padding-bottom: 0px">
                    <div class="col-sm-6 mincontent">
                        <div class="input-group" style="width: 300px">
                            <span class="input-group-addon" style="padding: 0;border: 0;">
                                <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                    <option value="855" <?php echo $client_info['phone_country'] == 855 ? 'selected' : ''?>>+855</option>
                                    <option value="66" <?php echo $client_info['phone_country'] == 66 ? 'selected' : ''?>>+66</option>
                                    <option value="86" <?php echo $client_info['phone_country'] == 86 ? 'selected' : ''?>>+86</option>
                                </select>
                            </span>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $client_info['phone_number'];?>" placeholder="">
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
                                <input type="hidden" id="client_id" name="client_id" value="<?php echo intval($client_info['uid'])?>">
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Login Account</span>:
                                    <span class="marginleft10" id="login-account"><?php echo $client_info['login_code']?></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Khmer Name</span>:
                                    <span class="marginleft10" id="khmer-name"><?php echo $client_info['kh_display_name']?></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">English Name</span>:
                                    <span class="marginleft10" id="english-name"><?php echo $client_info['display_name']?></span>
                                </p>
<!--                                <p class="text-small">-->
<!--                                    <span class="show pull-left base-name marginright3">Member Grade</span>:-->
<!--                                    <span class="marginleft10" id="member-grade">--><?php //echo $client_info['grade_code']?><!--</span>-->
<!--                                </p>-->
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Member State</span>:
                                    <span class="marginleft10" id="member_state"></span>
                                </p>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="basic-info" id="product-list">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Repayment Product</h5>
                </div>
                <div class="content" style="padding: 5px">
                    <div class="product-list">

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
        $('#btn_search').click(function () {
            getMemberRepaymentPlan()
        })

        $('#phone').bind('keydown',function(event){
            if(event.keyCode == "13") {
                getMemberRepaymentPlan();
            }
        })

        $('#phone').focus(function(){
            $('#phone').val('');
        })

    })

    //获取还款计划及member信息
    function getMemberRepaymentPlan() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('#phone').val();
        if (!$.trim(phone)) {
            return;
        }

        yo.dynamicTpl({
            tpl: "member/repayment.product.list",
            control:'counter_base',
            dynamic: {
                api: "member",
                method: "getMemberRepaymentProduct",
                param: {country_code: country_code, phone: phone}
            },
            callback: function (_tpl) {
                $(".product-list").html(_tpl);
                var client_icon = $('#client_icon').val();
                var client_account = $('#client_account').val();
                var client_kh_name = $('#client_kh_name').val();
                var client_en_name = $('#client_en_name').val();
                var client_grade = $('#client_grade').val();
                var client_state = $('#client_state').val();
                if(client_icon){
                    $('#member-icon').attr('src', client_icon);
                }
                $('#login-account').text(client_account);
                $('#khmer-name').text(client_kh_name);
                $('#english-name').text(client_en_name);
//                $('#member-grade').text(client_grade);
                $('#member_state').text(client_state);
                showRepaymentOne();
                showPlanList();
                showProductList();
            }
        });
    }

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

    function showRepaymentProductDetail(member_id, sub_product_code) {
        if (!member_id && !sub_product_code) {
            return;
        }
        yo.dynamicTpl({
            tpl: "member/repayment.plan.list",
            control: 'counter_base',
            dynamic: {
                api: "member",
                method: "getRepaymentProductDetail",
                param: {member_id: member_id, sub_product_code: sub_product_code}
            },
            callback: function (_tpl) {
                $(".plan-list").html(_tpl);
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
        if ($('.checkbox-amount:checked').length == 0) {
            alert('Please select scheme');
            return;
        }
        var values = getFormJson('#repayment-scheme');
        values.member_id = member_id;
        $('#plan-list').waiting();
        yo.dynamicTpl({
            tpl: "member/repayment.step.one",
            control: 'counter_base',
            dynamic: {
                api: "member",
                method: "repaymentStepOne",
                param: values
            },
            callback: function (_tpl) {
                $('#plan-list').unmask();
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
        yo.dynamicTpl({
            tpl: "member/repayment.step.two",
            control: 'counter_base',
            dynamic: {
                api: "member",
                method: "repaymentStepTwo",
                param: values
            },
            callback: function (_tpl) {
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
        $('#notCheckCashier').hide();
        $('#checkCashierDone').show();
    }

    //刷卡验证
    function verifyManger() {
        var card = window.external.swipeCard();
        var card_info=card.split("|");
        $("#repayment-two input[name='chief_teller_card_no']").val(card_info[0]);
        $("#repayment-two input[name='chief_teller_key']").val(card_info[1]);
        $('#notCheckManager').hide();
        $('#checkDoneManager').show();
    }

    //确认还款
    function submit_repayment() {
        if (!$("#repayment-form-two").valid()) {
            return
        }
        var values = $('#repayment-form-two').getValues();
        $("#repayment-form-two").waiting();
        yo.loadData({
            _c: 'member',
            _m: 'submitRepayment',
            param: values,
            callback: function (_o) {
                $("#repayment-form-two").unmask();
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member', 'repayment', array(), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
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

    // 还款列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "member/repayment.list",
            control:'counter_base',
            dynamic: {
                api: "member",
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



