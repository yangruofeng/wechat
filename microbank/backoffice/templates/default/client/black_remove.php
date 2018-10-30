<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.css?v=1" rel="stylesheet" />
<?php
$black_type = (new blackTypeEnum())->Dictionary();
?>
<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Client</h3>
          <ul class="tab-base">
            <li><a href="<?php echo getUrl('client', 'blackList', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
            <li><a class="current"><span>Remove</span></a></li>
          </ul>
      </div>
  </div>
    <div class="container">
      <div class="table-form">
          <div class="alert alert-success" style="font-size: 16px;">
              Black list of <kbd><?php echo $black_type[$output['type']]; ?></kbd>
          </div>
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
              <input type="hidden" name="type" id="type" value="<?php echo $output['type'];?>">
                <table class="search-table">
                    <tr>
                      <td>
                        <div class="form-group">
                          <label for="exampleInputName2">Member CID</label>
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

        <div class="business-content">
            <div class="business-list"></div>
        </div>
      </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js?v=1"></script>
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

      var type = $('#type').val(), obj_guid = $('#obj_guid').val(), member_name =  $('#username').val();

      yo.dynamicTpl({
          tpl: "client/black_remove.list",
          dynamic: {
              api: "client",
              method: "getRemoveBlackClient",
              param: {pageNumber: _pageNumber, pageSize: _pageSize, type: type, obj_guid: obj_guid, member_name: member_name}
          },
          callback: function (_tpl) {
              $(".business-list").html(_tpl);
          }
      });
  }

  function removeBlack(uid){
    var type = $('#type').val();
    yo.confirm('Remove', 'Are you sure to remove from black list?',function (_r) {
        if(!_r) return false;
      yo.loadData({
         _c: 'client',
         _m: 'updateBlackClientType',
         param: {uid: uid, type: type, state: 0},
         callback: function (_o) {
           if (_o.STS) {
             alert('Removed Successfully!',1,function(){
                 window.location.reload();
             });
           } else {
             alert('Failed to Remove!',2);
           }
         }
     });
    });
  }
</script>
