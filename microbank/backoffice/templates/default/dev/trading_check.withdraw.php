<style>
    #myModal .modal-dialog {
        margin-top: 100px!important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Check Withdraw Trading</h3>
            <ul class="tab-base">
                <li><a class="current"><span>By Partner</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" style="width: 230px" id="search_text" name="search_text" placeholder="Search for ...">
                                <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list"
                                      onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
                            </span>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="business-content">
            <div class="business-list"></div>
        </div>
    </div>
</div>

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
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

        var _search_text = $('#search_text').val();

        yo.dynamicTpl({
            tpl: "dev/trading_check.withdraw.list",
            dynamic: {
                api: "dev",
                method: "getPartnerWithdrawCheckList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function confirm_trading(uid) {
        yo.loadData({
            _c: 'dev',
            _m: 'partnerWithdrawConfirm',
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    btn_search_onclick();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function cancel_trading(uid) {
        yo.loadData({
            _c: 'dev',
            _m: 'partnerWithdrawCancel',
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    btn_search_onclick();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>
