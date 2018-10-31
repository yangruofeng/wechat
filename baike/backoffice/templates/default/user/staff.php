<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Staff</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('user', 'addStaff', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 99%">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                          <div class="input-group">
                            <input type="text" class="form-control input-search" id="search_text" name="search_text" placeholder="Search for...">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                                      onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
                            </span>
                          </div>
                        </td>
                        <td>
                            <select class="form-control" name="branch_id" id="branch_id" onchange="btn_search_onclick();">
                                <option value="0">Select Branch</option>
                                <?php foreach ($output['branch_list'] as $branch) { ?>
                                    <option value="<?php echo $branch['uid']?>"><?php echo $branch['branch_name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="user_status" id="user_status" onchange="btn_search_onclick();">
                                <option value="0">Select Status</option>
                                <?php foreach ($output['staff_status'] as $key => $branch) { ?>
                                    <option value="<?php echo $key?>"><?php echo $branch; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
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
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "user/staff.list",
            dynamic: {
                api: "user",
                method: "getStaffList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
