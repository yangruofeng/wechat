<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Client</h3>
          <ul class="tab-base">
              <li><a class="current"><span>List</span></a></li>
          </ul>
      </div>
  </div>
    <div class="container">
      <div class="table-form">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tr>
                      <td>
                        <div class="form-group">
                          <label for="exampleInputName2">Member GUID</label>
                          <input type="text" class="form-control" name="obj_guid" id="obj_guid">
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <label for="exampleInputName2">Member Name</label>
                          <input type="text" class="form-control" name="username" id="username">
                        </div>
                      </td>
                      <td>
                        <div class="input-group">
                          <span class="input-group-btn">
                            <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                <i class="fa fa-search"></i>
                                <?php echo 'Search';?>
                            </button>
                          </span>
                        </div>
                      </td>
                    </tr>
                </table>
            </form>
        </div>
        <hr>

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

        var obj_guid = $('#obj_guid').val(), member_name =  $('#username').val();

        yo.dynamicTpl({
            tpl: "client/black.list",
            dynamic: {
                api: "client",
                method: "getBlackList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, obj_guid: obj_guid, member_name: member_name}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
