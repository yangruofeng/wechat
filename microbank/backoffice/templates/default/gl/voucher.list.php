<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Manual-Voucher</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('gl_tree', 'voucherIndex', array(), false, BACK_OFFICE_SITE_URL)?>"><span>New Voucher</span></a></li>
                <li><a  class="current"><span>Voucher List</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container" style="background-color: white;min-height: 600px">
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
                api: "gl_tree",
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