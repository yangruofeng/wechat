<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Financial  Statement</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('report_financial_statement', 'financialStatement', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Balance Sheet</span></a></li>
                <li><a class="current"><span>Profit And Loss</span></a></li>
                <li><a href="<?php echo getUrl('report_financial_statement', 'CGAPIndicator', array(), false, BACK_OFFICE_SITE_URL)?>"><span>CGAP Indicator</span></a></li>
                <li><a href="<?php echo getUrl('report_financial_statement', 'weeklyStatementOfCondition', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Weekly Statement Of Condition</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td style="padding-left: 0px!important;">
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for">
                                <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list"
                                      onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
                            </span>
                            </div>
                        </td>
                        <td><span class="label label-danger" style="font-size: 26px;">Sample</span></td>
                    </tr>
                    </tbody>
                </table>
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
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);
        var _search_text = $('#search_text').val();

        yo.dynamicTpl({
            tpl: "report_financial_statement/profit.and.loss.list",
            dynamic: {
                api: "report_financial_statement",
                method: "getProfitAndLossList",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    search_text: _search_text
                }
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>
