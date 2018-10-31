<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=7" rel="stylesheet" type="text/css"/>
<style>
    .empty_tr td {
        height: 20px;
        padding: 15px;
    }

    .size_tr td {
        height: 30px;
    }

    .category-info tr td {
        width: 16.66%;
        padding: 10px !important;
    }

    .category-info tr td label{
        margin-bottom: 0;
    }

    .data-center-list li a .fa-angle-down {
        transition: all .25s ease;
        -webkit-transition: all .25s ease;
        -moz-transition: all .25s ease;
        -ms-transition: all .25s ease;
        -o-transition: all .25s ease;
    }

    .data-center-list li a.collapsed .fa-angle-down {
        transform: rotate(-90deg);
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -ms-transform: rotate(-90deg);
        -o-transform: rotate(-90deg);
        transition: all .25s ease;
        -webkit-transition: all .25s ease;
        -moz-transition: all .25s ease;
        -ms-transition: all .25s ease;
        -o-transition: all .25s ease;
    }

</style>
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
                <button type="button" class="btn btn-primary btn-sm top-user-item" onclick="btn_detail_op(this, <?php echo $v['uid'];?>);"><?php echo $v['category_name'];?></button>
            <?php }?>
        </div>
        <div class="data-center-list"></div>
    </div>

</div>
<script>
    function btn_detail_op(el, category_id){
        if($(el).hasClass('disabled')) return;
        $(el).addClass("btn-success").siblings().removeClass("btn-success");
        $(el).addClass("current").siblings().removeClass("current");
        if(!category_id) return;

        yo.dynamicTpl({
            tpl: "data_center_product/product.interest.sub_product.interest",
            dynamic: {
                api: "data_center_product",
                method: "getSizeRateList",
                param: {category_id: category_id }
            },
            callback: function (_tpl) {
                $(".data-center-list").html(_tpl);
            }
        });
    }

</script>