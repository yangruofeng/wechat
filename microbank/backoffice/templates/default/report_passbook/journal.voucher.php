<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .total{
        background-color: red !important;
    }
    .total td{
        font-size: 18px;
        color:#fff;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Journal Voucher</h3>
            <ul class="tab-base">
                <li><a  class="current"><span>Main</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <div class="form-group">
                    <label for="exampleInputName2">ID</label>
                    <input type="text" class="form-control input-search" name="trade_id">
                </div>
                <div class="form-group">
                    <label for="exampleInputName2">Remark</label>
                    <input type="text" class="form-control input-search" name="remark">
                </div>
                <div class="form-group">
                    <label for="exampleInputName2">Trade Type</label>
                    <select id="branch_id" class="form-control" name="trade_type" onclick="btn_search_onclick();">
                        <option value="">All Type</option>
                        <?php foreach ($output['trade_type'] as $k => $v) { ?>
                            <option value="<?php echo $k;?>"><?php echo $v;?></option>
                        <?php } ?>
                    </select>
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
                <div class="form-group">
                    <?php include(template("widget/inc_condition_datetime")); ?>
                </div>


            </form>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="basic-info">
                    <div class="business-content">
                        <div class="business-list">

                        </div>
                    </div>
                </div>
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

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        yo.dynamicTpl({
            tpl: "report_passbook/journal.voucher.list",
            dynamic: {
                api: "report_passbook",
                method: "getJournalVoucherData",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
