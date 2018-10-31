<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Warning Expire Date</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="table-form">
            <div class="business-condition">
                <form class="form-inline input-search-box" id="frm_search_condition">
                    <table class="search-table">
                        <tr>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control input-search" id="search_text" name="search_text" placeholder="Search for name/phone" style="min-width: 200px">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-search" id="btn_search_list" onclick="btn_search_onclick();">
                                            <i class="fa fa-search"></i>
                                        <?php echo 'Search'; ?>
                                        </button>
                                     </span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group" style="margin-left: 10px">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="is_expired" id="is_expired"> Expired
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <hr>
            <div class="business-content">
                <div class="business-list"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
        $('#is_expired').click(function () {
            btn_search_onclick();
        })
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        yo.dynamicTpl({
            tpl: "operator/warning.expire.date.list",
            dynamic: {
                api: "operator",
                method: "getWarningOfExpireDate",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
