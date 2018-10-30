<div role="tabpanel" class="tab-pane" id="credit_history" style="padding: 5px;background-color: #FFF"></div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);
        var member_id = $('input[name="member_id"]').val();
        yo.dynamicTpl({
            tpl: "branch_manager/hq.credit.list",
            dynamic: {
                api: "branch_manager",
                method: "getCreditGrantList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, member_id: member_id}
            },
            callback: function (_tpl) {
                $("#credit_history").html(_tpl);
            }
        });
    }
</script>