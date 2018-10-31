<style>
    .search_date {
        width: 100px!important;
    }
    #search_text {
        width: 120px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Repayment</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                          <div class="input-group">
                            <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for sn.">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list"
                                      onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
                            </span>
                          </div>
                        </td>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
                        <td>
                            <select class="form-control" name="type">
                                <option value="0">Select Type</option>
                                <option value="schema">Schema</option>
                                <option value="prepayment">Prepayment</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="state">
                                <option value="-1">Select State</option>
                                <?php foreach ($output['request_state'] as $key => $val) { ?>
                                    <option value="<?php echo $key ?>"><?php echo $lang['request_repayment_state_' . $key]?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
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

    $('#frm_search_condition select[name="state"]').change(function () {
        btn_search_onclick();
    });

    $('#frm_search_condition select[name="type"]').change(function () {
        btn_search_onclick();
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "loan/request.repayment.list",
            dynamic: {
                api: "loan",
                method: "getRequestRepaymentList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
