<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<link href="<?php echo ENTRY_COUNTER_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>


<style>
    .btn {
        padding: 5px 12px;
    }
</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="business-condition">

                <form class="form-inline input-search-box" id="frm_search_condition">

                    <input type="hidden" name="user_id" value="<?php echo $output['user_id']; ?>">

                    <table class="search-table">
                        <tbody>
                        <tr>
                            <td>
                                <select id="choose_currency" class="form-control" name="currency" >
                                    <?php echo system_toolClass::getCurrencyOption($output['condition']['currency']);  ?>

                                </select>
                            </td>
                            <td>
                                <ul class="list-inline select-datetime-parent" style="margin-left: inherit;margin-top: 9px;" >
                                    <li>
                                        <input id="date_search_from"  style="width: 120px" name="day" type="text" class="form-control search_date search_date_from" >
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                                                onclick="btn_search_onclick(1,null);">
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

        <div id="day_voucher_list" class="business-content" style="margin-bottom: 5px">
            <div class="business-list">

            </div>
        </div>

        <div style="text-align: center">
            <a class="btn btn-danger" onclick="print_daily_report()">
                <i class="fa fa-print"></i>
                Print
            </a>
        </div>
    </div>
</div>
<script>



    $(document).ready(function () {

        $(".search_date").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
        });

        $(".search_date_from").datepicker("update", "<?php echo date("Y-m-d",strtotime($output['condition']['day']))?>");


        btn_search_onclick();

    });



    //  分页展示贷款申请列表
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
            tpl: "cash_on_hand/teller.daily.voucher.list",
            control:'counter_base',
            dynamic: {
                api: "cash_on_hand",
                method: "getDayVoucherList",
                param: _values
            },
            callback: function (_tpl) {
                $(document).unmask();
                $("#day_voucher_list .business-list").html(_tpl);
            }
        });
    }

    function print_cod_flow() {
        var currency =  $("select[name='currency']").val();
        var date_start =  $("input[name='date_start']").val();
        var date_end =  $("input[name='date_end']").val();
        window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printCODFlow', array(), false, ENTRY_COUNTER_SITE_URL)?>&currency="+ currency+ "&date_start="+ date_start+ "&date_end="+ date_end);
    }

    function print_daily_report(){
        var _day = $('#date_search_from').val();
        var _currency = $('#choose_currency').val();
        if(window.external){
            window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printTellerDailyReport', array(
                'user_id' => $output['user_id']
            ), false, ENTRY_COUNTER_SITE_URL);?>&day="+_day+'&currency='+_currency);
        }
    }

</script>