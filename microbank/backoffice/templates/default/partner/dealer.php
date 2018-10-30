<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Dealer</h3>
            <ul class="tab-base">
<!--                <li><a class="current"><span>Ace</span></a></li>-->
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <div class="business-list">
                Coming soon!
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
//        btn_search_onclick();
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _search_text = $('#search_text').val();

        yo.dynamicTpl({
            tpl: "user/user.list",
            dynamic: {
                api: "user",
                method: "getUserList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
