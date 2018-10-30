<style>
    .product-info {
        width: 98%;
        background: #FFF;
        margin: 15px;
    }

    .product-info .info {
        padding: 15px;
        position: relative;
        height: 85px;
        /*border-bottom: 1px solid #e7eaec;*/
    }

    .product-info .name {
        font-size: 20px;
        font-weight: 600;
    }

    .product-info .product-report {
        height: 90px;
    }

    .product-info .product-report .item {
        width: 25%;
        text-align: center;
        float: left;
        padding: 20px 0;
        max-height: 110px;
        border-top: 1px solid #e7eaec;
        border-right: 1px solid #e7eaec;
        font-weight: 600;
    }

    .product-info .product-report .item:nth-child(4n) {
        border-right: 0;
    }

    .product-info .product-report .item p {
        margin-bottom: 0;
        font-size: 20px;
        margin-top: 5px;
        color: #f60;
    }

    .product-info .custom-btn-group {
        position: absolute;
        right: 10px;
        top: 20px;
    }

    .custom-btn-group a {
        margin-left: 8px;
    }

    .text-small {
        margin-bottom: 0;
    }

</style>
<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<?php
$certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
?>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container">
        <div class="basic-info">
            <?php include(template("widget/item.member.summary.v2"))?>
        </div>
        <input type="hidden" id="member_id" value="<?php echo $output['member_id'] ?>">
        <div class="business-content" style="margin-top: 15px">
            <div class="business-list">

            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {
        btn_search_onclick();
    });


    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);
        var member_id = $('#member_id').val();


        yo.dynamicTpl({
            tpl: "member_mortgage/client.storage.list",
            control:'counter_base',
            dynamic: {
                api: "member_mortgage",
                method: "getClientStorageList",
                param:{
                    pageNumber:_pageNumber,
                    pageSize:_pageSize,
                    member_id:member_id
                }
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>