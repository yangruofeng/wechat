<style>
    .business-content > .title{
        font-size: 15px;
        font-weight: 600;
        margin: 10px 0px;
        padding: 0px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Department</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('user', 'addDepartment', array('branch_id' => $output['branch_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 1000px">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                          <div class="input-group">
                            <input type="text" class="form-control" style="width: 200px" id="search_text" name="search_text" placeholder="Search for name/code">
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
                            <a class="btn btn-default" href="<?php echo getUrl('user', 'branch', array(), false, BACK_OFFICE_SITE_URL)?>"><i class="fa fa-reply"></i><?php echo 'Branch List' ?></a>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="business-content">
            <div class="col-sm-12 title">
                <span><?php echo 'Branch : '?></span>
                <span><?php echo $output['branch_info']['branch_name'] ?></span>
            </div>
            <div class="business-list">

            </div>
        </div>
    </div>
</div>
<script>
    var uid = '<?php echo $output['branch_id'];?>';
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

        var _search_text = $('#search_text').val();

        yo.dynamicTpl({
            tpl: "user/department.list",
            dynamic: {
                api: "user",
                method: "getDepartmentList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text, branch_id: uid}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function delDepart(uid){
        if (!uid) {
            return;
        }
        $.messager.confirm("<?php echo $lang['common_delete']?>", "<?php echo $lang['common_confirm_delete']?>", function (_r) {
            if (!_r) return;
            $(".business-content").waiting();
            yo.loadData({
                _c: "user",
                _m: "deleteDepartment",
                param: {uid: uid},
                callback: function (_o) {
                    $(".business-content").unmask();
                    if (_o.STS) {
                        alert(_o.MSG);
                        btn_search_onclick();
                    } else {
                        alert(_o.MSG);
                    }
                }
            });
        });
    }
</script>
