<td style="width: 450px;">
    <ul class="list-inline" style="margin-left: inherit;margin-top: 8px;">
        <li><?php echo $lang['common_from']?></li>
        <li>
            <input id="date_search_from" style="width: 120px" name="date_search_from" type="text" class="form-control search_date search_date_from" >
        </li>
        <li><?php echo $lang['common_to']?></li>
        <li>
            <input id="date_search_to"  style="width:120px" type="text" name="date_search_to" class="form-control search_date search_date_to" >
        </li>
    </ul>
</td>
<script>
    $(document).ready(function(){
        $(".search_date").datepicker({format:"yyyy-mm-dd"});
        $("#date_search_from").datepicker("update",app.before30days());
        $("#date_search_to").datepicker("update",app.today());
    });
</script>