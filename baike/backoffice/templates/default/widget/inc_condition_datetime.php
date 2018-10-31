<ul class="list-inline select-datetime-parent" style="margin-left: inherit;margin-top: 9px;" report_key="<?php echo $report_key ?>">
    <li><?php echo $lang['common_from']?></li>
    <li>
        <input id="date_search_from"  style="width: 120px" name="date_start" type="text" class="form-control search_date search_date_from" >
    </li>
    <li><?php echo $lang['common_to']?></li>
    <li>
        <input id="date_search_to"  style="width:120px" type="text" name="date_end" class="form-control search_date search_date_to" >
    </li>
    <li>
         <select class="form-control" name="select_date_range" id="select_date_range">
            <option value="default"><?php echo $lang['common_choose_date_range']?></option>
            <option value="all"><?php echo $lang['common_all']?></option>
            <option value="today"><?php echo $lang['common_today']?></option>
            <option value="yesterday"><?php echo $lang['common_yesterday']?></option>
            <option value="thisweek"><?php echo $lang['common_this_week']?></option>
            <option value="lastweek"><?php echo $lang['common_last_week']?></option>
            <option value="thismonth"><?php echo $lang['common_this_month']?></option>
            <option value="lastmonth"><?php echo $lang['common_last_month']?></option>
        </select>
        <!--<div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="width: 120px">
                <span class="btn-group-text"><?php echo $lang['common_choose_date_range']?></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="javascript:void(0)" data_key="all" onclick="btn_select_date_range_onclick(this)"><?php echo $lang['common_all']?></a></li>
                <li><a href="javascript:void(0)" data_key="today" onclick="btn_select_date_range_onclick(this)"><?php echo $lang['common_today']?></a></li>
                <li><a href="javascript:void(0)" data_key="yesterday" onclick="btn_select_date_range_onclick(this)"><?php echo $lang['common_yesterday']?></a></li>
                <li><a href="javascript:void(0)" data_key="thisweek" onclick="btn_select_date_range_onclick(this)"><?php echo $lang['common_this_week']?></a></li>
                <li><a href="javascript:void(0)" data_key="lastweek" onclick="btn_select_date_range_onclick(this)"><?php echo $lang['common_last_week']?></a></li>
                <li><a href="javascript:void(0)" data_key="thismonth" onclick="btn_select_date_range_onclick(this)"><?php echo $lang['common_this_month']?></a></li>
                <li><a href="javascript:void(0)" data_key="lastmonth" onclick="btn_select_date_range_onclick(this)"><?php echo $lang['common_last_month']?></a></li>
            </ul>
        </div>-->
    </li>
</ul>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script>
    $(document).ready(function () {
        $(".search_date").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
        });
        $(".search_date_from").datepicker("update", "<?php echo date("Y-m-d",strtotime($output['condition']['date_start']))?>");
        $(".search_date_to").datepicker("update", "<?php echo date("Y-m-d",strtotime($output['condition']['date_end']))?>");

        $(".search_date_from").change(function () {
            if (typeof(btn_search_onclick) != "undefined") {
                btn_search_onclick(1, 50);
            }
        })

        $(".search_date_to").change(function () {
            if (typeof(btn_search_onclick) != "undefined") {
                btn_search_onclick(1, 50);
            }
        });

        $("#select_date_range").change(function(){
            var key = $(this).val();
            btn_select_date_range_onclick(key, this);
        });
    });
    function btn_select_date_range_onclick(key, el) {
        var _date1 = app.today();
        var _date2 = app.today();
        var _key = key;
        switch (_key) {
            case "default":
                _date1 = app.before30days();
                break;
            case "all":
                _date1 = "2015-06-30";
                break;
            case "today":
                _date1 = app.today();
                break;
            case "yesterday":
                _date1 = app.yesterday();
                _date2 = app.yesterday();
                break;
            case "thisweek":
                _date1 = app.monday();
                break;
            case "lastweek":
                _date1 = app.lastMonday();
                _date2 = app.lastSunday();
                break;
            case "thismonth":
                _date1 = app.firstDayOfMonth();
                break;
            case "lastmonth":
                _date1 = app.firstDayOfLastMonth();
                _date2 = app.lastDayOfLastMonth();
                break;
            default :
                break;
        }

        var key = $(el).closest('ul.select-datetime-parent').attr('report_key');
        if (key) {
            $(el).closest('ul.select-datetime-parent').find("#date_search_from").datepicker("update", _date1);
            $(el).closest('ul.select-datetime-parent').find("#date_search_to").datepicker("update", _date2);
        } else {
            $("#date_search_from").datepicker("update", _date1);
            $("#date_search_to").datepicker("update", _date2);
        }

        if (typeof(btn_search_onclick) != "undefined") {
            btn_search_onclick(1, 50, key);
        }
    }
</script>