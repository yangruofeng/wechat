<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Member</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('data_center_member', 'index', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="javascript:history.go(-1)"><span>Detail</span></a></li>
                <li><a class="current"><span>Client Credit Log</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="table-form">
            <div class="business-content">
                <div class="business-list"></div>
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

        var member_id = <?php echo $output['member_id']?>;
        yo.dynamicTpl({
            tpl: "data_center_member/client.credit.log.list",
            dynamic: {
                api: "data_center_member",
                method: "getClientCreditLogList",
                param: {
                    pageNumber:_pageNumber,
                    pageSize:_pageSize,
                    member_id: member_id
                }
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
