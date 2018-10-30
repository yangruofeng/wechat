<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/message.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap message-wrap">
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list aui-media-list message-list" id="messageList"></ul>
    <div class="data-loading" style="display: none;"><img src="<?php echo WAP_SITE_URL;?>/resource/image/loading.gif" alt=""></div>
    <div class="no-record" style="display: none;"><?php echo $lang['label_no_data'];?></div>
  </div>
</div>
<script type="text/html" id="tpl_message_item">
  {{ if( it && it.length > 0 ){ }}
    {{ for(var i = 0; i< it.length; i++) { }}
      <li class="aui-list-item aui-list-item-middle {{ if( it[i]['is_read'] != 1 ){ }}unread{{ } }}" onclick="readMsg({{=it[i]['message_id']}});">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner aui-list-item-arrow">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title aui-font-size-14">{{=it[i]['message_title']}}</div>
              <div class="aui-list-item-right">{{=formatDate(it[i]['message_time'])}}</div>
            </div>
            <div class="aui-list-item-text message-text">
              {{=it[i]['message_body']}}
            </div>
          </div>
        </div>
      </li>
    {{ } }}
  {{ } }}
</script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/doT.min.js"></script>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/common.js"></script>
<script type="text/javascript">
var page_num = 1, page_size = 10;
$('.data-loading').show();
getPageData();
function getPageData(){
  $.ajax({
    type: 'POST',
    url: '<?php echo WAP_SITE_URL;?>/index.php?act=message&op=getmessageData',
    data: {page_num: page_num, page_size: page_size},
    dataType: 'json',
    success: function(data){
      if(data.STS){
        var list = data.DATA.list;
        if(list == null){
          loadDataDone('<?php echo $lang['label_no_data'];?>');
        }else{
          var interText = doT.template($('#tpl_message_item').text());
          $('#messageList').html(interText(list));
          $('.data-loading').hide();
        }
      }else{
        loadDataDone(data.MSG);
      }
    },
    error: function(xhr, type){
      toast.hide();
      verifyFail('<?php echo $lang['tip_get_data_fail'];?>');
    }
  });
}

function loadDataDone(msg){
  $('.data-loading').hide();
  $('.no-record').text(msg);
  $('.no-record').show();
}

function readMsg(message_id){
  window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=message&op=read&msg="+message_id;
}
</script>
