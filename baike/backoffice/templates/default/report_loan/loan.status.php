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
        <div class="business-content">
            <div class="business-list">
                <?php $data = $output['data']; ?>
                <?php include_once(template("report_loan/loan.status.list")); ?>
            </div>
        </div>
    </div>
</div>
<script>
    function exportExcel() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_export';
        _values.op = 'exportLoanStatus';
        commonExportExcel(_values);
    }

    function printPage() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_print';
        _values.op = 'printLoanStatus';
        _values.tpl = 'report_loan/loan.status.list';
        commonPrintPage(_values);
    }
</script>