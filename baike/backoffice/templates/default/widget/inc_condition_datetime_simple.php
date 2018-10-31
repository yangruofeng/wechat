<ul class="list-inline select-datetime-parent" style="margin-left: inherit;margin-top: 9px;" report_key="<?php echo $report_key ?>">
    <li>
        <input id="date_search_from"  style="width: 120px" name="date_end" type="text" class="form-control search_date search_date_from" >
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
        $(".search_date_from").datepicker("update", "<?php echo date("Y-m-d",strtotime($output['condition']['date_end']?:date('Y-m-d')))?>");


        $(".search_date_from").change(function () {
            if (typeof(btn_search_onclick) != "undefined") {
                btn_search_onclick(1, 20);
            }
        })


    });
    function btn_select_date_range_onclick(_e) {
        var _date1 = app.today();
        var _date2 = app.today();
        var key = $(_e).closest('ul.select-datetime-parent').attr('report_key');
        if (key) {
            $(_e).closest('ul.select-datetime-parent').find("#date_search_from").datepicker("update", _date1);
        } else {
            $("#date_search_from").datepicker("update", _date1);
        }
        if (typeof(btn_search_onclick) != "undefined") {
            btn_search_onclick(1, 20, key);
        }
    }
</script>