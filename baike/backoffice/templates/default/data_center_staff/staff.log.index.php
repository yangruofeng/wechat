<style>
    .business-condition {
        margin-bottom: 10px;
    }
</style>
<div class="business-condition">
    <form class="form-inline input-search-box" id="frm_search_condition">
        <input type="hidden" name="uid" value="<?php echo $output['uid']; ?>">
        <table class="search-table">
            <tbody>
            <tr>
                <td><?php include(template("widget/inc_condition_datetime")); ?></td>
                <td>
                    <div class="form-group" style="margin-left: 10px">
                        <label for="exampleInputName2">Branch</label>
                        <select class="form-control" name="branch_id">
                            <option value="0">All</option>
                            <?php foreach ($output['client_type'] as $type) { ?>
                                <option value="<?php echo $type['client_type'];?>"><?php echo ucwords($type['client_type']);?></option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="business-content">
                <div class="business-list"></div>
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
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        yo.dynamicTpl({
            tpl: "data_center_staff/staff.log.list",
            dynamic: {
                api: "data_center_staff",
                method: "getStaffLogList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
