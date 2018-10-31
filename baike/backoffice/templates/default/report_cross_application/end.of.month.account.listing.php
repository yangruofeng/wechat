<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Cross Application</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('report_cross_application', 'crossApplication', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Porfolio Summary Report</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'authorizedSystemUsers', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Authorized System Users</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'transactionListing', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Transaction</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'todayOpenAccounts', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Today Open Accounts</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'dailyTransactionListing', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Daily Transaction</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'summaryAccountByGL', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Summary Account By GL</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'GLAccountAndCustomerBalance', array(), false, BACK_OFFICE_SITE_URL)?>"><span>GL Account and Customer Balance</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'GLAccountMovement', array(), false, BACK_OFFICE_SITE_URL)?>"><span>GL Account Movement</span></a></li>
                <li><a class="current"><span>End of Month Account</span></a></li>
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
            tpl: "report_cross_application/end.of.month.account.list",
            dynamic: {
                api: "report_cross_application",
                method: "getEndOfMonthAccountListingList",
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

