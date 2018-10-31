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

<?php
$bank_info = $output['bank_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>BillPay</h3>
            <ul class="tab-base">
                <li >
                    <a href="<?php echo getBackOfficeUrl('financial','checkBillPayIndex'); ?>">
                        <span>Bank List</span>
                    </a>
                </li>
                <li><a class="current"><span>Check</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-height: 1300px">

        <div class="col-sm-12">



        </div>


        <div class="business-condition col-sm-12 ">

            <form  method="post" role="form" class="form-inline" id="frm_search">

                <input type="hidden" name="act" value="financial">
                <input type="hidden" name="op" value="checkBillPayStepTwo">
                <input type="hidden" name="form_submit" value="ok">
                <input type="hidden" name="bank_id" value="<?php echo $output['bank_info']['uid']; ?>">
                <div class="input-group">
                    <input type="text" name="bill_code" value="" placeholder="Search by bill code" class="form-control">

                    <span class="input-group-btn">
                        <span class="btn btn-info" onclick="form_submit();">
                            Search
                        </span>
                    </span>
                </div>

            </form>

        </div>

        <div id="bill_code_schema_div" class="col-sm-12">


        </div>


        <div class="history-list col-sm-12 " style="margin-top: 20px;">
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

    var BANK_ID = '<?php echo $bank_info['uid']; ?>';

    $(function(){
        btn_search_onclick();
    });




    function form_submit()
    {
        //$('#frm_search').submit();
        var _bill_code = $('input[name="bill_code"]').val();
        $('body').waiting();
        yo.dynamicTpl({
            tpl: "financial/billpay.schema.list",
            dynamic: {
                api: "financial",
                method: "getContractSchemasByBillCode",
                param: {bank_id: BANK_ID, bill_code: _bill_code}
            },
            callback: function (_tpl) {
                $('body').unmask();
                $("#bill_code_schema_div").html(_tpl);
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
                param: {pageNumber: _pageNumber, pageSize: _pageSize,bank_id:BANK_ID}
            },
            callback: function (_tpl) {
                $(".history-list .content").html(_tpl);
            }
        });
    }
</script>
