<style>
    #top_counter {
        width: 100%;
        height: 80px;
        border: 2px solid #FFE499;
        background-color: white;
        margin-bottom: 20px;
        padding-left: 10px !important;
        padding-top: 10px !important;
        position: relative;
    }

    #top_counter .balance {
        position: absolute;
        top: 13px;
    }

    #top_counter .balance tr td {
        padding: 2px 8px 4px !important;
        background-color: #FFF !important;
        min-width: 200px;
    }

    #top_counter .balance tr td span.cash-in-hand, #top_counter .balance tr td span.cash-outstanding {
        font-weight: 600;
        font-size: 16px;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Journal Voucher</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Main</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <input type="hidden" name="member_id" value="<?php echo $_GET['member_id'];?>">
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
                    <select id="trade_type" class="form-control trade_type" name="trade_type">
                        <option value="">All Type</option>
                        <?php foreach ($output['trade_type'] as $k => $v) { ?>
                            <option value="<?php echo $k;?>"><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-primary btn-search" id="btn_search"
                              onclick="btn_search_onclick();" style="height: 30px;line-height: 14px;border-radius: 0">
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

        <div class="col-sm-12 col-md-12 col-lg-12">

            <div class="basic-info">
                <div class="business-content">
                    <div class="business-list">

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

    $(".trade_type").change(function () {
        if (typeof(btn_search_onclick) != "undefined") {
            btn_search_onclick(1, 20);
        }
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

        $(document).waiting();
        yo.dynamicTpl({
            tpl: "branch_manager/bm.journal.voucher.list",
            dynamic: {
                api: "branch_manager",
                method: "getJournalVoucherData",
                param: _values
            },
            callback: function (_tpl) {
                $(document).unmask();
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
