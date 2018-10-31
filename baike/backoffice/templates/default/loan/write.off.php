<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Write Off</h3>
            <ul class="tab-base">
                <?php if ($output['type'] == 'unprocessed') { ?>
                    <li><a class="current"><span>Unprocessed</span></a></li>
                    <li>
                        <a href="<?php echo getUrl('loan', 'writeOff', array('type' => 'processed'), false, BACK_OFFICE_SITE_URL) ?>"><span>Processed</span></a>
                    </li>
                <?php } else { ?>
                    <li>
                        <a href="<?php echo getUrl('loan', 'writeOff', array('type' => 'unprocessed'), false, BACK_OFFICE_SITE_URL) ?>"><span>Unprocessed</span></a>
                    </li>
                    <li><a class="current"><span>Processed</span></a></li>
                <?php } ?>
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
                            <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for sn/client">
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
    var type = '<?php echo $output['type']?>';
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

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        _values.type = type;

        yo.dynamicTpl({
            tpl: "loan/write.off.list",
            dynamic: {
                api: "loan",
                method: "getWriteOffList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
