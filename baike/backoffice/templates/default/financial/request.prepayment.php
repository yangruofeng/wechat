<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Request To Prepayment</h3>
            <ul class="tab-base">
<!--                <li>-->
<!--                    <a href="--><?php //echo getUrl('loan', 'requestToRepayment', array('type' => 'schema'), false, BACK_OFFICE_SITE_URL) ?><!--"><span>Schema</span></a>-->
<!--                </li>-->
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
                            <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for...">
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
                            <select class="form-control" name="state">
                                <option value="-1">Select State</option>
                                <?php foreach ($output['request_state'] as $key => $val) { ?>
                                    <option value="<?php echo $key ?>"><?php echo $lang['request_prepayment_state_' . $key]?></option>
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
    })

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
            tpl: "financial/request.prepayment.list",
            dynamic: {
                api: "financial",
                method: "getRequestPrepaymentList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
