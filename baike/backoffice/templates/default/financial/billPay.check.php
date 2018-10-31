<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    em {
        font-weight: 500;
        font-size: 15px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px !important;
        color: #000;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        background-color: #FFF;
        overflow: hidden;
        min-height: 40px;
    }

    /*.content td {*/
        /*padding-left: 15px !important;*/
        /*padding-right: 15px !important;*/
    /*}*/

    #search_text {
        min-width: 200px;
    }

    input {
        height: 30px!important;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>BillPay</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Index</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-height: 1300px">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control input-search" id="search_text" name="search_text" placeholder="Search for bill code">
                                <span class="input-group-btn">
                                  <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                                          onclick="btn_search();">
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

        <div class="contract-info col-sm-12 col-md-10 col-lg-6" style="padding-left: 0px">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Contract Information</h5>
                </div>
                <div class="content">
                </div>
            </div>
        </div>
        <div class="history-list col-sm-12 col-md-10 col-lg-6" style="padding-left: 0px">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5>Pay History</h5>
                </div>
                <div class="content">

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        btn_search_onclick();
    })

    function paybill_submit() {
        if (!$("#billpay_form").valid()) {
            return;
        }

        var _values = $("#billpay_form").getValues();
        $('.contract-info').waiting();
        yo.loadData({
            _c: "financial",
            _m: "submitBillPay",
            param: _values,
            callback: function (_o) {
                $('.contract-info').unmask();
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

    function btn_search() {
        var _search_text = $('#search_text').val();
        if (!_search_text) {
            return;
        }
        yo.dynamicTpl({
            tpl: "financial/contract.info",
            dynamic: {
                api: "financial",
                method: "getContractInfoByBillCode",
                param: {bill_code: _search_text}
            },
            callback: function (_tpl) {
                $(".contract-info .content").html(_tpl);
            }
        });
    }

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "financial/billpay.check.list",
            dynamic: {
                api: "financial",
                method: "getBillPayCheckList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".history-list .content").html(_tpl);
            }
        });
    }
</script>
