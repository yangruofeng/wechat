<style>
    .table-no-background{

    }
    .table.table-no-background tbody tr:nth-child(even){
        background: transparent;
    }
    .table-no-background tbody tr td{
        height: 20px;
    }
</style>
<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container" style="min-height: 600px;background-color: #ffffff">
        <div class="business-content">
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

        $(document).waiting();
        yo.dynamicTpl({
            tpl: "gl/voucher.list.page",
            dynamic: {
                api: "gl_voucher",
                method: "getVoucherList",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize
                }
            },
            callback: function (_tpl) {
                $(document).unmask();
                $(".business-list").html(_tpl);
            }
        });
    }


</script>