<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .business-condition{
        margin-bottom: 10px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Withdraw</h3>
            <ul class="tab-base">
                <li><a  class="current"><span>Log</span></a></li>
                <li><a onclick="javascript:history.back(-1);">Back</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <div class="form-group" style="margin-left: 10px">
                    <label for="exampleInputName2">Type</label>
                    <select class="form-control" name="obj_type">
                        <option value="-1">All</option>
                        <?php foreach ($output['type'] as $k => $v) { ?>
                            <option value="<?php echo $v;?>"><?php echo $k;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <?php include(template("widget/inc_condition_datetime")); ?>
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

    $('select[name=obj_type]').change(function(){
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
            tpl: "data_center_business/exchange.log.list",
            dynamic: {
                api: "data_center_business",
                method: "getExchangeLogList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
