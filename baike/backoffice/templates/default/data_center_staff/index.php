<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .business-condition{
        margin-bottom: 10px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Staff Center</h3>
            <ul class="tab-base">
                <li><a  class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <div class="form-group">
                    <label for="exampleInputName2">Staff</label>
                    <input type="text" class="form-control input-search" name="staff">
                </div>
                <div class="form-group" style="margin-left: 10px">
                    <label for="exampleInputName2">Phone</label>
                    <select class="form-control" name="country_code">
                        <?php print_r(tools::getCountryCodeOptions());?>
                    </select>
                    <input type="text" class="form-control input-search" name="phone_number">
                </div>
                <div class="form-group" style="margin-left: 10px">
                    <label for="exampleInputName2">Branch</label>
                    <select class="form-control" name="branch_id">
                        <option value="0">All</option>
                        <?php foreach ($output['branch_list'] as $branch) { ?>
                            <option value="<?php echo $branch['uid'];?>"><?php echo $branch['branch_name'];?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="input-group" style="margin-left: 10px">
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
            tpl: "data_center_staff/staff.list",
            dynamic: {
                api: "data_center_staff",
                method: "getStaffList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
