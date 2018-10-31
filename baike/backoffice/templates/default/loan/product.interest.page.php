<!--Staff List	Bank List	Transactions	Journal Voucher-->
<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=7" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product & Interest</h3>
            <ul class="tab-base">
                <li><a  class="current"><span>Main</span></a></li>
            </ul>
        </div>
    </div>
    <?php $product_list = $output['product_list'];?>
    <div class="container" >
        <div class="data-center-top-list" style="border-bottom: 2px solid #DDD">
            <?php foreach($product_list as $v){ ?>
                <button type="button" class="btn btn-primary btn-sm top-user-item" onclick="btn_search_product_info(this, <?php echo $v['uid'];?>);"><?php echo $v['product_name'];?></button>
            <?php }?>
        </div>
        <div class="data-center-base-info"></div>
        <div class="data-center-list"></div>
    </div>

</div>
<script>
    var _PRODUCT_ID = 0;

    function btn_search_product_info(el, product_id){
        if(product_id) {
            $('.btn-center-item').removeClass('btn-success');
        }
        $(el).addClass("btn-success").siblings().removeClass("btn-success");
        $(el).addClass("current").siblings().removeClass("current");
        _PRODUCT_ID = product_id;
        yo.dynamicTpl({
            tpl: "loan/product.interest.sub_product",
            dynamic: {
                api: "loan",
                method: "getSubProductList",
                param: { product_id: product_id }
            },
            callback: function (_tpl) {
                $(".data-center-base-info").html(_tpl);
                $(".data-center-list").html('');
            }
        });
    }

    function btn_detail_op(el, sub_product_id){
        if($(el).hasClass('disabled')) return;
        $(el).addClass("btn-success").siblings().removeClass("btn-success");
        $(el).addClass("current").siblings().removeClass("current");
        if(!sub_product_id) return;

        yo.dynamicTpl({
            tpl: "loan/product.interest.sub_product.interest",
            dynamic: {
                api: "loan",
                method: "getSizeRateList",
                param: { product_id: sub_product_id }
            },
            callback: function (_tpl) {
                $(".data-center-list").html(_tpl);
            }
        });
    }

    function showSpecialRate (rate_id) {
        if(!rate_id) return;

        yo.dynamicTpl({
            tpl: "loan/product.interest.sub_product.special_rate",
            dynamic: {
                api: "loan",
                method: "showSizeSpecialRate",
                param: { rate_id: rate_id }
            },
            callback: function (_tpl) {
                $(".data-center-list").html(_tpl);
            }
        });
    }

</script>

