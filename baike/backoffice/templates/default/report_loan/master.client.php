<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=1.2" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Loan</h3>
            <?php include template('report_loan/sub.menu.item');?>
            <div class="export-div">
                <a onclick="exportExcel()" class="export-excel" title="Excel"><i class="fa-file-excel-o"></i></a>
                <a onclick="printPage()" class="export-pdf" title="Print"><i class="fa-file-pdf-o"></i></a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>

                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control input-search" id="search_text" name="search_text" placeholder="client name/cid/phone">
                                <span class="input-group-btn">
                              <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                                      onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
                            </span>
                            </div>
                        </td>
                        <td>
                            <?php if($output['limit_branch_id']){?>
                                <input type="hidden" name="branch_id" value="<?php echo $output['limit_branch_id']?>">
                            <?php }else{?>
                                <select id="branch_id" class="form-control" name="branch_id" onchange="btn_search_onclick();">
                                    <option value="">All Branch</option>
                                    <?php foreach ($output['branch_list'] as $branch) { ?>
                                        <option value="<?php echo $branch['uid'];?>"><?php echo $branch['branch_name'];?></option>
                                    <?php } ?>
                                </select>
                            <?php }?>

                        </td>
                        <td>
                            <select id="choose_currency" class="form-control" name="currency" onchange="btn_search_onclick();">
                                <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                    <option value="<?php echo $key;?>"><?php echo $currency;?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>

        <div class="business-content">
            <div class="business-list" id="print_area">

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
        showMask();
        yo.dynamicTpl({
            tpl: "report_loan/master.client.list",
            dynamic: {
                api: "report_loan",
                method: "getMasterClientList",
                param: _values
            },
            callback: function (_tpl) {
                hideMask();
                $(".business-list").html(_tpl);
            }
        });
    }
    //导出
    function exportExcel() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_export';
        _values.op = 'exportMasterClient';
        commonExportExcel(_values);
    }
    //打印
    function printPage() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_print';
        _values.op = 'printMasterClient';
        _values.tpl = 'report_loan/master.client.list';
        commonPrintPage(_values);
    }

</script>
