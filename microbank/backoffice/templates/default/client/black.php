<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/loan.css?v=1" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.css?v=1" rel="stylesheet" />
<?php
    $blackTypeList=(new blackTypeEnum())->Dictionary();
?>
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
      <table class="table verify-table">
        <thead>
        <tr class="table-header">
          <td><?php echo 'Black Type';?></td>
          <td><?php echo 'Count';?></td>
          <td><?php echo 'Functions';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($output['types'] as $row){ ?>
            <tr>
              <td>
                <?php $label=ucwords($blackTypeList[$row['type']]);

                ?>
                <span class="label label-success"><?php echo $label;?></span>
              </td>
              <td>
                <?php echo $row['count'] ?:0; ?>
              </td>
              <td>
                <div class="custom-btn-group">
                  <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('client', 'removeBlackClient', array('type'=>$row['type']), false, BACK_OFFICE_SITE_URL)?>" style="color: red;margin-right: 8px!important;">
                    <span><i class="fa fa-check"></i>Black List</span>
                  </a>
                  <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('client', 'addBlackClient', array('type'=>$row['type']), false, BACK_OFFICE_SITE_URL)?>">
                    <span><i class="fa fa-edit"></i>Add Black</span>
                  </a>
                </div>
              </td>
            </tr>
        <?php }?>
        </tbody>
      </table>
    </div>
</div>
<div class="modal" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Base Info'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="info_form">
                      <input type="hidden" id="editType" value="">
                        <div class="" id="memberList"></div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save();"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Base Info'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="info_form">
                      <input type="hidden" id="editType" value="">
                        <div class="" id="memberList"></div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save();"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/html" id="tpl_black_list">
  {{ if( it && it.length > 0 ){ }}
    {{ for(var i = 0; i< it.length; i++) { }}
      <label class="checkbox-inline">
        <input type="checkbox" name="ck_member" value="{{= it[i]['uid'] }}" {{ if( it[i]['check']){ }} checked {{ } }}> {{= it[i]['display_name'] }}
      </label>
    {{ } }}
    {{ }else{ }}
      None
  {{ } }}
</script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/doT.min.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js?v=1"></script>
<script>
  function removeBlack(){
    $('#removeModal').modal('show');
  }
  function editBlack(type){
    yo.loadData({
     _c: 'client',
     _m: 'getBlackClientList',
     param: {type: type},
     callback: function (_o) {
       var data = _o.DATA;
       var evalText = doT.template($("#tpl_black_list").text());
       html = evalText(data);
　　   $("#memberList").html(html);
       $('#editType').val(type);
     }
   });
    $('#infoModal').modal('show');
  }
  function save(){
    var id_array = new Array();
    $('input[name=ck_member]:checked').each(function(){
        id_array.push($(this).val());//向数组中添加元素
    });
    var ids = id_array.join(',');//将数组元素连接起来以构建一个字符串
    var type = $('#editType').val();
    yo.loadData({
       _c: 'client',
       _m: 'updateBlackClientList',
       param: {type: type, list: ids},
       callback: function (_o) {
         $('#infoModal').modal('hide');
         if (_o.STS) {
           alert('Saved Successfully', 1,function(){
               window.location.reload();
           });
         } else {
           alert(_o.MSG,2);
         }
       }
    });
  }
</script>
