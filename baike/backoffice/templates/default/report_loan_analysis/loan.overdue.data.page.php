<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<?php
$condition = $output['search_condition'];
?>
<div class="page">
    <?php include template('report_loan_analysis/sub.menu.list');?>
    <div class="container">
        <div class="business-condition">
            <form role="form" style="margin: 10px 0;" class="form-inline input-search-box" id="frm_search_condition">

                <input type="hidden" name="act" value="report_loan_analysis">
                <input type="hidden" name="op" value="getOverdueData">
                <table class="search-table">
                    <tbody>

                    <tr>
                        <td>
                            <?php if($output['limit_branch_id']){ ?>
                                <input type="hidden" name="branch_id" value="<?php echo $output['limit_branch_id']?>">
                            <?php }else{?>
                                <select name="branch_id" id="" class="form-control">
                                    <option value="0">All Branch</option>
                                    <?php foreach( $output['branch_list'] as $branch ){ ?>
                                        <option value="<?php echo $branch['uid']; ?>" <?php if( $branch['uid'] == $condition['branch_id'] ){ echo 'selected';} ?> ><?php echo $branch['branch_name']; ?></option>
                                    <?php } ?>
                                </select>
                            <?php }?>
                        </td>
                        <td>
                            Date
                            <input type="text" name="day" class="form-control search_date" id="search_day">
                        </td>
                        <td>
                            <select name="currency" id="" class="form-control">
                                <option value="<?php echo currencyEnum::USD; ?>" <?php if( $condition['currency'] == currencyEnum::USD ){ echo 'selected';} ?> ><?php echo currencyEnum::USD; ?></option>
                                <option value="<?php echo currencyEnum::KHR; ?>" <?php if( $condition['currency'] == currencyEnum::KHR ){ echo 'selected';} ?> > <?php echo currencyEnum::KHR; ?></option>

                            </select>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-btn">
                                  <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                                          onclick="btn_search_onclick();">
                                      <i class="fa fa-search"></i>
                                      <?php echo 'Search'; ?>
                                  </button>
                                </span>
                            </div>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="business-content">
            <div class="business-list">
                <?php $data = $output['data']; ?>
                <?php include_once(template("report_loan_analysis/loan.overdue.print")); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".search_date").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
        });
        $("#search_day").datepicker("update", "<?php echo date("Y-m-d",strtotime($condition['day']))?>");

    });

    function btn_search_onclick()
    {
        $('#frm_search_condition').submit();
    }
    //导出
    function exportExcel() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_export';
        _values.op = 'exportOverdue';
        commonExportExcel(_values);
    }
    //打印
    function printPage() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_print';
        _values.op = 'printOverdue';
        _values.tpl = 'report_loan_analysis/loan.overdue.print';
        commonPrintPage(_values);
    }


</script>
