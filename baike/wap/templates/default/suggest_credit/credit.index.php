<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/yo.extend.js"></script>
<?php include_once(template('widget/inc_header_weui'));?>
<style>
    .weui-navbar__item {
        cursor: pointer;
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


<div class="navbar js_show">
    <div class="page__bd" style="height: 100%;font-size: 0.8rem">
        <div class="weui-tab">
            <div class="weui-navbar">
                <div data-link="#tab_1" class="weui-navbar__item weui-bar__item_on" onclick="btn_switch_tab_onclick(this)">
                    CO Analysis
                </div>
                <div data-link="#tab_2" class="weui-navbar__item" onclick="btn_switch_tab_onclick(this)">
                    System Analysis
                </div>
                <div data-link="#tab_3" class="weui-navbar__item" onclick="btn_switch_tab_onclick(this)">
                    Coefficient
                </div>
            </div>
            <div class="weui-tab__panel">
                <div id="tab_1" class="weui-tab__content" style="display: block">
                   <?php include(template("suggest_credit/credit.tab1"))?>
                </div>
                <div id="tab_2" class="weui-tab__content">
                    <?php include(template("suggest_credit/credit.tab2"))?>
                </div>
                <div id="tab_3" class="weui-tab__content">
                    <?php include(template("suggest_credit/credit.tab3"))?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (window.operator) {
        window.operator.showTitle('<?php echo $output['header_title'];?>');
    }
    function btn_switch_tab_onclick(_e){
        $('.weui-navbar__item').removeClass('weui-bar__item_on');
        $(_e).addClass('weui-bar__item_on');
        $('.weui-tab__content').hide();
        var _link_content=$(_e).data("link");
        $(_link_content).show();
    }
</script>