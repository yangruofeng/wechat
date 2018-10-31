<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
<style>
    .business-condition {
        margin-bottom: 15px;
    }
</style>
<div class="page">

    <div class="fixed-bar">
        <div class="item-title">
            <h3>Account Balance</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Index</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <div class="form-group">
                    <label for="">Book Code</label>
                    <input type="text" class="form-control input-search" name="book_code">
                </div>
                <div class="form-group">
                    <label for="">Book Name</label>
                    <input type="text" class="form-control input-search" name="book_name">
                </div>
                <div class="input-group">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                              onclick="btn_search_onclick();">
                          <i class="fa fa-search"></i>
                          <?php echo 'Search'; ?>
                      </button>
                    </span>
                </div>
            </form>
        </div>
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

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        yo.dynamicTpl({
            tpl: "report_passbook/account.balance.list",
            dynamic: {
                api: "report_passbook",
                method: "getAccountBalanceList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
