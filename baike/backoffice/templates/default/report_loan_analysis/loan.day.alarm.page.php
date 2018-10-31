<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
<style>
.table-bordered > thead > tr .td-border-right000,
.table-bordered > tbody > tr .td-border-right000 {
    border-right: 1px solid #000;
}
</style>
<div class="page">
    <?php include template('report_loan_analysis/sub.menu.list');?>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <div class="form-group">
                    <label for="exampleInputName2">Category</label>
                    <select class="form-control category" name="category" style="margin-bottom: 15px;">
                        <option value="0">All</option>
                        <?php foreach($output['category'] as $v){?>
                            <option value="<?php echo $v['uid'];?>"><?php echo $v['category_name'];?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="form-group">
                    <?php if($output['limit_branch_id']){ ?>
                        <input type="hidden" name="branch_id" value="<?php echo $output['limit_branch_id']?>">
                    <?php }else{?>
                        <select name="branch_id" id="" class="form-control" onclick="btn_search_onclick()">
                            <option value="0">All Branch</option>
                            <?php foreach( $output['branch_list'] as $branch ){ ?>
                                <option value="<?php echo $branch['uid']; ?>" <?php if( $branch['uid'] == $condition['branch_id'] ){ echo 'selected';} ?> ><?php echo $branch['branch_name']; ?></option>
                            <?php } ?>
                        </select>
                    <?php }?>
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

    $(".category").change(function () {
        if (typeof(btn_search_onclick) != "undefined") {
            btn_search_onclick();
        }
    });

    function btn_search_onclick() {
        var _values = $('#frm_search_condition').getValues();
        yo.dynamicTpl({
            tpl: "report_loan_analysis/loan.day.alarm.list",
            dynamic: {
                api: "report_loan_analysis",
                method: "getDayAlarm",
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
        _values.op = 'exportDayAlarm';
        commonExportExcel(_values);
    }
    //打印
    function printPage() {
        var _values = $('#frm_search_condition').getValues();
        _values.act = 'report_print';
        _values.op = 'printDayData';
        _values.tpl = 'report_loan_analysis/loan.day.data.list';
        commonPrintPage(_values);
    }
</script>
