<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php include template('report_loan_analysis/sub.menu.list');?>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition" style="padding-bottom: 10px">
                <div class="form-group">
                    <input type="text" class="form-control" name="search_text" placeholder="Client CID/Phone/Name">
                    <label for="exampleInputName2">Branch </label>
                    <?php if($output['limit_branch_id']){?>
                        <input type="hidden" name="branch_id" value="<?php echo $output['limit_branch_id']?>">
                        <span> Current Branch </span>
                    <?php }else{?>
                        <select class="form-control" name="branch_id">
                            <option value="0">All</option>
                            <?php foreach($output['branch_list'] as $v){?>
                                <option value="<?php echo $v['uid'];?>"><?php echo $v['branch_name'];?></option>
                            <?php }?>
                        </select>
                    <?php }?>

                </div>
                <div class="form-group">
                    <?php include(template("widget/inc_condition_datetime")); ?>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-default" onclick="btn_search_onclick()"><i class="fa fa-search"></i> Search</button>
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
            tpl: "report_super_loan/trx.loan.list",
            dynamic: {
                api: "report_super_loan",
                method: "getTrxLoanList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
    //导出
    function exportExcel() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_export';
        _values.op = 'exportLoanTransaction';
        commonExportExcel(_values);
    }
    //打印
    function printPage() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_print';
        _values.op = 'printLoanTransaction';
        _values.tpl = 'report_super_loan/trx.loan.list';
        commonPrintPage(_values);
    }

</script>