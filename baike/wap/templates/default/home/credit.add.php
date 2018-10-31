<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/css/home.css?v=7">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/css/inc_header.css?v=1">
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/yo.extend.js"></script>
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block'; ?>">
    <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>

    <h2 class="title"><?php echo $output['header_title']; ?></h2>
    <span class="right-search-btn"
          onclick="window.location.href='<?php echo getUrl('home', 'suggestHistory', array('id' => $_GET['id']), false, WAP_OPERATOR_SITE_URL) ?>'"><i
            class="aui-iconfont aui-icon-menu"></i></span>
</header>
<style>
    .table-rate {
        margin: 5px;
    }

    .table-rate > tbody > tr > td {
        font-size: 0.5rem;
    }

    .table-rate > tbody > tr > td > input {
        font-size: 0.5rem;
        border: 1px solid #fdb58f;
        border-radius: 4px !important;
        min-height: 0.1rem;
    }

    .analysis-list {
        font-size: .7rem;
    }

    .paddingleft12rem {
        padding-left: 1.2rem !important;
    }

    .paddingleft17rem {
        padding-left: 1.7rem !important;
    }

    .paddingleft22rem {
        padding-left: 2.2rem !important;
    }

    .fontweight500 {
        font-weight: 500;
    }

    .fontweight600 {
        font-weight: 600;
    }

    .business-detail-wrap {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, .4);
        z-index: 100;
    }

    .business-detail {
        background: #fff;
        margin: 0 1.3rem;
        border-radius: 2px;
        height: 18rem;
        margin-top: 4rem;
        position: relative;
        padding-top: 1.5rem;
    }

    .close {
        position: absolute;
        top: 0;
        right: 0;
        padding: .3rem .5rem;
    }

    .close i {
        font-size: .8rem;
    }

    .business-detail-cloumn {
        font-size: .6rem;
    }
</style>
<?php $certification_type = enum_langClass::getCertificationTypeEnumLang(); ?>
<?php $cert_type = array(
    certificationTypeEnum::LAND => 'land_credit_rate',
    certificationTypeEnum::HOUSE => 'house_credit_rate',
    certificationTypeEnum::MOTORBIKE => 'motorbike_credit_rate',
    certificationTypeEnum::CAR => 'car_credit_rate',
) ?>
<?php
$analysis = $output['analysis'];
$member_request = $analysis['member_request'];
$member_income = $analysis['income'];
$member_expense = $analysis['expense'];
$suggest_profile = $analysis['suggest'];
$member_assets = $output['member_assets'];
$last_submit_suggest = $output['last_suggest'];
?>

<div class="wrap loan-wrap">
    <div class="aui-tab aui-margin-b-10" id="tab">
        <div class="aui-tab-item aui-active" type="1">Co Analysis</div>
        <div class="aui-tab-item" type="2">System Analysis</div>
        <div class="aui-tab-item" type="3">Assets Notices</div>
    </div>
    <div class="tab-panel tab-1">
        <?php include(template("home/credit.add.tab1")); ?>
    </div>
    <div class="tab-panel tab-2" style="display: none;">
        <?php include(template("home/credit.add.tab2")); ?>
    </div>
    <div class="tab-panel tab-3" style="display: none;">
        <?php include(template("home/credit.add.tab3")); ?>
    </div>


</div>
<div class="upload-success">
    <div class="content">
        <img src="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/image/gou.png" alt="">

        <p class="title"><?php echo 'Upload Successfully'; ?></p>

        <p class="tip"><?php echo str_replace('xxx', '<em id="count">3</em>', 'It exits automatically xxx seconds later.'); ?></p>
    </div>
</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/script/aui/aui-tab.js"></script>
<script src="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/script/common.js?v=1"></script>
<script type="text/javascript">
    var tab = new auiTab({
        element: document.getElementById('tab'),
        index: 1,
        repeatClick: false
    }, function (ret) {
        var i = ret.index;
        $('.tab-panel').hide();
        $('.tab-' + i).show();
    });

    if (window.operator) {
        window.operator.showTitle('<?php echo $output['header_title'];?>');
    }

    function checkMaxCredit() {
        //所有抵押credit+default=max_credit
        var _max_credit = parseInt($('input[name="max_credit"]').val());
//        var _default_credit=parseInt($('input[name="default_credit"]').val());
        var _total_increase = 0;
        $(".count_credit").each(function () {
            if ($(this).css("display") != 'none') {
                _total_increase += parseInt($(this).val());
                //判断对应的category-id是否有设置
            }
        });
        if (_max_credit != _total_increase) {
            verifyFail("MaxCredit = DefaultCredit + (Increase Credit By Mortgaged)");
            return false;
        }
        if (!_max_credit > 0) {
            verifyFail("MaxCredit > 0");
            return false;
        }

        return true;
    }
    function checkCurrencyCredit(){
        var _max_credit = parseInt($('input[name="max_credit"]').val());
//        var _default_credit=parseInt($('input[name="default_credit"]').val());
        var _total_ccy = 0;
        var _total_ccy_usd=0;
        var _total_ccy_khr=0;

        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-usd").each(function () {
            _total_ccy_usd+=parseInt($(this).val());
        });


        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-khr").each(function () {
            _total_ccy_khr+=parseInt($(this).val());
        });

        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-total").each(function () {
            _total_ccy+=parseInt($(this).val());
        });

        if (_max_credit != _total_ccy) {
            verifyFail("Required: MaxCredit("+_max_credit+") = Total Credit Of Credit-Category("+_total_ccy+")");
            return false;
        }

        var _total_ccy_sum=parseInt(_total_ccy_usd)+parseInt(_total_ccy_khr)/4000;

        if(_total_ccy!=_total_ccy_sum){
            verifyFail("Required: Total Credit = USD_Credit + KHR_Credit");
            return false;
        }

        if (!_max_credit > 0) {
            verifyFail("MaxCredit > 0");
            return false;
        }

        return true;
    }
    function next_step_onclick(){
        var params = {}, request_type = $.trim($('#request_type').val()), member_id = '<?php echo $_GET['id'];?>',
            officer_id = '<?php echo cookie('member_id');?>', token = '<?php echo cookie('token');?>',
            monthly_repayment_ability = parseInt($.trim($('#monthly_repayment_ability').val())),
            credit_terms = $.trim($('#credit_terms').val()),
            default_credit = $.trim($('#default_credit').val()),
            default_credit_category_id = $.trim($('#default_credit_category_id').val()),
            max_credit = $.trim($('#max_credit').val()),
            client_request_credit = $.trim($('#client_request_credit').val()),
            is_append = $('[name="is_append"]:checked').val(),
            remark = $.trim($('#remark').val());

        if (!member_id) {
            verifyFail('<?php echo 'Please reselect client.';?>');
            return;
        }
        if (monthly_repayment_ability<=0) {
            verifyFail('<?php echo 'Please input monthly repayment ability.';?>');
            return;
        }
        if (!checkMoney(monthly_repayment_ability)) {
            verifyFail('<?php echo 'Monthly Repayment Ability must be monetary.';?>');
            return;
        }
        if (!credit_terms) {
            verifyFail('<?php echo 'Please input credit terms.';?>');
            return;
        }
        if (!checkInteger(credit_terms)) {
            verifyFail('<?php echo 'Credit terms must be positive integer.';?>');
            return;
        }
        if (!default_credit) {
            verifyFail('<?php echo 'Please input default credit.';?>');
            return;
        }
        if (!checkMoney(default_credit)) {
            verifyFail('<?php echo 'Default credit must be monetary.';?>');
            return;
        }
        if(!default_credit_category_id){
            verifyFail('<?php echo 'Required to choose Default-Credit-Category.';?>');
            return;
        }
        if (!max_credit) {
            verifyFail('<?php echo 'Please input max credit.';?>');
            return;
        }

        if (!checkMaxCredit()) {
            return;
        }
        $("#frm_credit").hide();
        $("#frm_credit_currency").show();
    }

    function submit() {
        var params = {}, request_type = $.trim($('#request_type').val()), member_id = '<?php echo $_GET['id'];?>',
            officer_id = '<?php echo cookie('member_id');?>', token = '<?php echo cookie('token');?>',
            monthly_repayment_ability = parseInt($.trim($('#monthly_repayment_ability').val())),
            credit_terms = $.trim($('#credit_terms').val()),
            default_credit = $.trim($('#default_credit').val()),
            default_credit_category_id = $.trim($('#default_credit_category_id').val()),
            max_credit = $.trim($('#max_credit').val()),
            client_request_credit = $.trim($('#client_request_credit').val()),
            is_append = $('[name="is_append"]:checked').val(),
            remark = $.trim($('#remark').val());

        if(!checkCurrencyCredit()){
            return;
        }


        //取资产信用
        var _chk_increase = [];
        $("#frm_credit").find(".chk-increase-value").each(function () {
            _chk_increase.push($(this).val());
        });
        var _chk_increase_json = encodeURI(JSON.stringify(_chk_increase));

        var _arr_assets = [];
        $("#frm_credit").find(".suggest-item-asset").each(function () {
            _arr_assets.push({
                "asset_id": $(this).data('asset-id'),
                "credit": $(this).val() ? parseInt($(this).val()) : 0
            });
        });
        var _arr_assets_json = encodeURI(JSON.stringify(_arr_assets));

        var _credit_category = [];
        $("#frm_credit").find(".credit_category").each(function () {
            _credit_category.push($(this).val());
        });
        var _credit_category_json = encodeURI(JSON.stringify(_credit_category));

        var _credit_ccy_id = [];
        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-id").each(function () {
            _credit_ccy_id.push($(this).val());
        });
        var _credit_ccy_id_json = encodeURI(JSON.stringify(_credit_ccy_id));

        var _credit_ccy_chk = [];
        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-chk").each(function () {
            _credit_ccy_chk.push($(this).val());
        });
        var _credit_ccy_chk_json = encodeURI(JSON.stringify(_credit_ccy_chk));

        var _credit_ccy_usd = [];
        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-usd").each(function () {
            _credit_ccy_usd.push($(this).val());
        });
        var _credit_ccy_usd_json = encodeURI(JSON.stringify(_credit_ccy_usd));

        var _credit_ccy_khr = [];
        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-khr").each(function () {
            _credit_ccy_khr.push($(this).val());
        });
        var _credit_ccy_khr_json = encodeURI(JSON.stringify(_credit_ccy_khr));

        var _credit_ccy_total = [];
        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-total").each(function () {
            _credit_ccy_total.push($(this).val());
        });
        var _credit_ccy_total_json = encodeURI(JSON.stringify(_credit_ccy_total));

        params.request_type = request_type;
        params.member_id = member_id;
        params.officer_id = officer_id;
        params.token = token;
        params.monthly_repayment_ability = monthly_repayment_ability;
        params.credit_terms = credit_terms;
        params.default_credit = default_credit;
        params.default_credit_category_id = default_credit_category_id;
        params.client_request_credit = client_request_credit;
        params.chk_increase = _chk_increase_json;
        params.asset_credit = _arr_assets_json;
        params.credit_category = _credit_category_json;
        params.max_credit = max_credit;
        params.is_append = is_append;
        params.remark = remark;
        params.credit_ccy_id=_credit_ccy_id_json;
        params.credit_ccy_chk=_credit_ccy_chk_json;
        params.credit_ccy_usd=_credit_ccy_usd_json;
        params.credit_ccy_khr=_credit_ccy_khr_json;
        params.credit_ccy_total=_credit_ccy_total_json;

        showMask();
        $.ajax({
            type: 'POST',
            url: '<?php echo getUrl('home', 'saveRequestCredit', array(), false, WAP_OPERATOR_SITE_URL)?>',
            data: params,
            dataType: 'json',
            success: function (data) {
                toast.hide();
                if (data.STS) {
                    $('.upload-success').show();
                    var count = $('#count').text();
                    var times = setInterval(function () {
                        count--;
                        $('#count').text(count);
                        if (count <= 1) {
                            clearInterval(times);
                            if (window.operator) {
                                window.operator.memberInfo();
                                return;
                            }
                            $('.back').click();
                        }
                    }, 1000);
                } else {
                    if (data.CODE == '<?php echo errorCodesEnum::INVALID_TOKEN;?>' || data.CODE == '<?php echo errorCodesEnum::NO_LOGIN;?>') {
                        reLogin();
                    }
                    verifyFail(data.MSG);
                }

            },
            error: function (xhr, type) {
                toast.hide();
                verifyFail('<?php echo $lang['tip_get_data_error'];?>');
            }
        });
    }
    /**
     * 将二维数组转为 json 字符串
     */
    function encodeArray2D(obj) {
        var array = [];
        for (var i = 0; i < obj.length; i++) {
            array[i] = '[' + obj[i].join(',') + ']';
        }
        return '[' + array.join(',') + ']';
    }

    function showBusinessDetail(item) {
        $('.business-detail-wrap').hide();
        $('.business-detail-' + item).show();
    }
    $('.business-detail .close').on('click', function () {
        $('.business-detail-wrap').hide();
    });
</script>
