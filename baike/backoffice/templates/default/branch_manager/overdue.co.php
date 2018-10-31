<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Overdue</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="table-form">
            <div class="business-content">
                <div class="business-list">

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        btn_search_onclick();
    })
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "branch_manager/overdue.co.list",
            dynamic: {
                api: "branch_manager",
                method: "getOverdueForCo",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize
                }
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
