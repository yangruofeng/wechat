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
            <h3>Branch</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'branch', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a class="current"><span>User</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <input type="hidden" value="<?php echo $output['uid']?>" id = 'uid' >
                <table  class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for...">
                                <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list"
                                      onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
                            </span>
                            </div>
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

        var _uid = $('#uid').val();
        yo.dynamicTpl({
            tpl: "user/branch.user.list",
            dynamic: {
                api: "user",
                method: "getBranchUserList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text, uid: _uid}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>