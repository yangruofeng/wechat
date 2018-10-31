<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>User Point</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text"
                                       placeholder="Search for user name">
                                <span class="input-group-btn">
                                  <button type="button" class="btn btn-default" id="btn_search_list"
                                          onclick="btn_search_onclick();">
                                      <i class="fa fa-search"></i>
                                      <?php echo 'Search'; ?>
                                  </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <select class="form-control" name="branch_id">
                                <option value="0">Please Branch</option>
                                <?php foreach ($output['branch_list'] as $branch) { ?>
                                    <option
                                        value="<?php echo $branch['uid'] ?>"><?php echo $branch['branch_name'] ?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control" name="depart_id" disabled>
                                <option value="0">Please Department</option>
                                <?php foreach ($output['depart_list'] as $depart) { ?>
                                    <option class="branch_<?php echo $depart['branch_id'] ?>"
                                            value="<?php echo $depart['uid'] ?>"><?php echo $depart['depart_name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="checkbox" name="own_department" id="own_department"> Own Department
                            </div>
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

        $('select[name="branch_id"]').change(function () {
            var _branch_id = $(this).val();
            $('select[name="depart_id"]').val(0);
            $('select[name="depart_id"] option[value!=0]').hide();
            if (_branch_id == 0) {
                $('select[name="depart_id"]').attr('disabled', true);
            } else {
                $('select[name="depart_id"] option.branch_' + _branch_id).show();
                $('select[name="depart_id"]').attr('disabled', false);
            }
            btn_search_onclick();
        })

        $('select[name="depart_id"]').change(function () {
            btn_search_onclick();
        })

        $('#need_audit,#own_department').click(function () {
            btn_search_onclick();
        })

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
        _values._pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "point/user.point.list",
            dynamic: {
                api: "point",
                method: "getUserPointList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>
