<div class="col-sm-6">
    <div class="panel panel-primary panel-item">
        <div class="panel-heading">
            <p class="panel-title">
                State Log
            </p>
        </div>
        <div id="state_log"></div>
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

        var _values = {};
        var _staff_id = '<?php echo $staff_info['uid']?>';
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        _values.staff_id = _staff_id;

        yo.dynamicTpl({
            tpl: "user/staff.state.log.list",
            dynamic: {
                api: "user",
                method: "getStaffStateLogList",
                param: _values
            },
            callback: function (_tpl) {
                $("#state_log").html(_tpl);
            }
        });
    }
</script>