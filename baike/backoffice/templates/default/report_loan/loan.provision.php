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
                                <?php if($output['limit_branch_id']){?>
                                    <input type="hidden" name="branch_id" value="<?php echo $output['limit_branch_id']?>">
                                <?php }else{?>
                                    <select id="branch_id" class="form-control" name="branch_id" onclick="btn_search_onclick();">
                                        <option value="">All Branch</option>
                                        <?php foreach ($output['branch_list'] as $branch) { ?>
                                            <option value="<?php echo $branch['uid'];?>"><?php echo $branch['branch_name'];?></option>
                                        <?php } ?>
                                    </select>
                                <?php }?>

                            </td>
                            <td>
                                <select id="choose_currency" class="form-control" name="currency" onclick="btn_search_onclick();">
                                    <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                        <option value="<?php echo $key;?>"><?php echo $currency;?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td><a href="<?php echo getUrl('report_loan', 'loanProvisionContract', array(), false, BACK_OFFICE_SITE_URL)?>" class="btn btn-success">Detail</a></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="business-content">
            <div class="business-list"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        var _values = $('#frm_search_condition').getValues();
        yo.dynamicTpl({
            tpl: "report_loan/loan.provision.list",
            dynamic: {
                api: "report_loan",
                method: "getLoanProvision",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function exportExcel() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_export';
        _values.op = 'exportLoanProvision';
        commonExportExcel(_values);
    }

    function printPage() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_print';
        _values.op = 'printLoanProvision';
        _values.tpl = 'report_loan/loan.provision.list';
        commonPrintPage(_values);
    }
</script>