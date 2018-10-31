<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/product.css?v=5" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.config.js' ?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.all.js' ?>"></script>
<style>
    body {
        font-size: 14px;
    }

    .form-control {
        border-width: 0;
    }

    .text-right {
        padding-right: 20px !important;
    }

    .page-2 .content {
        padding: 5px 20px 10px;
    }

    .tab-top li {
        width: auto;
        min-width: 80px;
        font-size: 14px;
        padding: 0 5px;
        margin-right: 5px;
    }

    .red {
        color: red;
    }

    .tab-top li.active {
        background: #dff0d8!important;
    }
</style>
<?php $product_info = $output['product_info']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a  href="<?php echo getUrl('savings', 'product', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Product List</span></a></li>
                <li><a class="current"><span><?php echo $output['current_title']?></span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 98%">
        <ul class="tab-top clearfix">
            <li <?php echo $output['tab'] == 'page-1' ? 'class="active"' : ''?> data-page="page-1"><a>Base Info</a></li>
            <li <?php echo $output['tab'] == 'page-2' ? 'class="active"' : ''?> data-page="page-2"><a>Limit&Setting</a></li>
            <li <?php echo $output['tab'] == 'page-3' ? 'class="active"' : ''?> data-page="page-3"><a>Details</a></li>
        </ul>

        <div class="tab-content page-1" style="display: <?php echo $output['tab'] == 'page-1' ? 'block' : 'none'?>; padding-top: 20px; ">
            <?php
            $category_list = $output['category_list'];
            $period_unit = $output['period_unit'];
            $currency_list = $output['currency_list'];
            $state = $output['state'];
            ?>
            <?php include template("savings/savings.product.base.info")?>
        </div>
        <div id="category_temp">
            <div class="tab-content page-2 no_clear_ul_style"  style="display: <?php echo $output['tab'] == 'page-2' ? 'block' : 'none'?>; padding-top: 20px">
                <?php include template("savings/savings.product.setting")?>
            </div>
            <div class="tab-content page-3 no_clear_ul_style"  style="display: <?php echo $output['tab'] == 'page-3' ? 'block' : 'none'?>; padding-top: 20px">
                <?php include template("savings/savings.product.detail")?>
            </div>
        </div>
    </div>
</div>
<script>
    var _uid = '<?php echo  $product_info['uid']; ?>';
    var _WIDTH;
    $(function () {
        $('.tab-top li').click(function () {
            var _page = $(this).data('page');

            var _page_active = $('.tab-top .active').data('page');
            if (_page_active != _page && _page_active == 'page-1') {
                formSubmit(_page);
            } else if (_page_active != _page && _page_active == 'page-2') {
                settingSubmit(_page);
            } else {
                $('.tab-top li').removeClass('active');
                $(this).addClass('active');

                $(".tab-content").hide();
                $('.' + _page).show();

                if (_page == 'page-2') {
                    _WIDTH = $('.content').width();
                }
            }
        })
    })

</script>