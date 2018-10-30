<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .business-condition {
        margin-bottom: 10px;
    }
</style>
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <input type="hidden" name="branch_id" value="<?php echo $output['branch_id'];?>">
                <div class="form-group">
                    <label for="exampleInputName2">Staff</label>
                    <input type="text" class="form-control input-search" name="staff" onkeydown="keyup_submit(event);">
                </div>
                <div class="form-group">
                    <label for="exampleInputName2">Phone</label>
                    <select class="form-control" name="country_code">
                        <?php print_r(tools::getCountryCodeOptions());?>
                    </select>
                    <input type="text" class="form-control input-search" name="phone_number" onkeydown="keyup_submit(event);">
                </div>
                <div class="form-group">
                    <label for="exampleInputName2">Status</label>
                    <select id="user_status" class="form-control" name="user_status">
                        <option value="-1">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>

                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                              onclick="btn_search_onclick();">
                          <i class="fa fa-search"></i>
                          <?php echo 'Search'; ?>
                      </button>
                    </span>
                </div>
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
    function keyup_submit(e){
        var evt = window.event || e;
           if (evt.keyCode == 13){
               btn_search_onclick();
           }
    }
    $(document).ready(function () {
        btn_search_onclick();
    });

    $("#user_status").change(function () {
        if (typeof(btn_search_onclick) != "undefined") {
            btn_search_onclick(1, 10);
        }
    })

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
            tpl: "data_center_branch/branch.staff.list",
            dynamic: {
                api: "data_center_branch",
                method: "getBranchStaffList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
