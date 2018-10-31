<!--Staff List	Bank List	Transactions	Journal Voucher-->
<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=7" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Partner Center</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Main</span></a></li>
            </ul>
        </div>
    </div>
    <?php $partner_list = $output['partner_list'];?>
    <div class="container" >
        <div class="data-center-top-list">
            <?php foreach($partner_list as $v){ ?>
                <button type="button" class="btn btn-primary btn-sm top-user-item" onclick="btn_search_partner_info(this, <?php echo $v['uid'];?>);"><?php echo $v['partner_name'];?></button>
            <?php }?>
        </div>
        <div class="data-center-base-info">
            <?php include_once(template("data_center_partner/partner.info")); ?>
        </div>
        <div class="data-center-list"></div>
    </div>

</div>
<script>
    var _UID = 0;
    function btn_search_partner_info(el, uid) {
        if (uid) {
            $('.btn-center-item').removeClass('disabled');
            $('.btn-center-item').removeClass('btn-success');
        }

        $(el).addClass("btn-success").siblings().removeClass("btn-success");
        _UID = uid;
        $(".data-center-base-info").waiting();
        yo.dynamicTpl({
            tpl: "data_center_partner/partner.info",
            dynamic: {
                api: "data_center_partner",
                method: "getPartnerInfo",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $(".data-center-base-info").unmask();
                $(".data-center-base-info").html(_tpl);
                $(".data-center-list").html('');
            }
        });
    }

</script>

