<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .container {
        width: 800px !important;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    .collection-div {
        margin-bottom: 70px;
    }

    .authorize_input {
        margin-top: -8px !important;
        margin-bottom: 10px;
    }

    .account-basic {
        margin-bottom: 0;
    }

    .text-small {
        margin-bottom: 0;
    }

    .basic-info {
        margin-bottom: 20px;
    }
    .limitbox td{
        background-color: white;
    }

</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="rows">
        <div class="basic-info">
            <?php include(template("widget/item.member.summary.v2"))?>
        </div>
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
                <div class="ibox-title">
                    <h5>Voucher List</h5>
                </div>
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
            tpl: "member_voucher/client.voucher.list",
            control:'counter_base',
            dynamic: {
                api: "member_voucher",
                method: "getClientVoucherList",
                param: _values
            },
            callback: function (_tpl) {
                $(document).unmask();
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
