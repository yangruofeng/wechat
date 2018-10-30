<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/request.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap request-wrap">
  <div class="aui-refresh-content">
    <ul class="aui-list aui-media-list request-list" id="requestList"></ul>
  </div>
  <div class="data-loading" style="display: none;"><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loading.gif" alt=""></div>
  <div class="no-record" style="display: none;"><?php echo $lang['label_no_data'];?></div>
  <input type="hidden" name="totalpage" id="totalpage" value="0">
</div>
<script type="text/html" id="tpl_request_item">
  {{ if( it && it.length > 0 ){ }}
    {{ for(var i = 0; i< it.length; i++) { }}
      <li class="aui-list-item aui-list-item-middle" onclick="getDetail();">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner aui-list-item-arrow">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title name">{{=it[i]['applicant_name']}}</div>
              <div class="aui-list-item-right amount">{{=it[i]['currency']}} {{=it[i]['apply_amount']}}</div>
            </div>
            <div class="aui-list-item-text">
              <div class="aui-list-item-title"></div>
              <div class="aui-list-item-right time">{{=it[i]['apply_time']}}</div>
            </div>
            <div class="aui-list-item-text">
              <div class="aui-list-item-title phone"><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/verify_phone.png" />{{=it[i]['contact_phone']}}</div>
              <div class="aui-list-item-right state">{{=it[i]['state']}}</div>
            </div>
          </div>
        </div>
      </li>
    {{ } }}
  {{ } }}
</script>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/aui/aui-scroll.js"></script>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/aui/aui-pull-refresh.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/doT.min.js"></script>
<script type="text/javascript">
  $('.data-loading').show();
  var page_num = 1, page_size = 10;
  getPageData();
  //下拉刷新
  var pullRefresh = new auiPullToRefresh({
    container: document.querySelector('.aui-refresh-content'),
    triggerDistance: 100
  },function(ret){
    if(ret.status=='success'){
      setTimeout(function(){
        page_num = 1;
        getPageData();
        pullRefresh.cancelLoading(); //刷新成功后调用此方法隐藏
      },1500);
    }
  });

  function scroll(){
    var range = 100, //距下边界长度/单位px
        elemt = 500, //插入元素高度/单位px
        maxnum = 2, //设置加载最多次数
        num = 1,totalheight = 0,main = $(".aui-refresh-content"); //主体元素
    $(window).scroll(function(){
      var srollPos = $(window).scrollTop(), i = 0, arr_len = 0, page; //滚动条距顶部距离(页面超出窗口的高度)
      var doc_scrolltop = $(document).scrollTop(), //滚动条到顶部的垂直高度
      doc_height = $(document).height(), //页面的文档高度
      win_height = $(window).height(); //浏览器的高度
      totalheight = parseFloat(doc_height) - parseFloat(win_height);
      if((range + $(window).scrollTop()) >= totalheight && num != maxnum) {
        $('.data-loading').show();
        var tPage = $('#totalpage').val();
        if(page_num > tPage){
          loadDataDone('<?php echo $lang['label_no_data'];?>');
          return;
        }
        getPageData();
        num++;
      }
    });
  }

  function getPageData(){
    $.ajax({
      type: 'POST',
      url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=ajaxClientRequest',
      data: {id: '<?php echo $_GET['id'];?>', page_num: page_num, page_size: page_size},
      dataType: 'json',
      success: function(data){
        if(data.STS){
          var list = data.DATA, total_pages = 1;
          $('#totalpage').val(total_pages);
          if(list == null){
            loadDataDone('<?php echo $lang['label_no_data'];?>');
          }else{
            var interText = doT.template($('#tpl_request_item').text());
            if(page_num == 1){
              $('#requestList').html(interText(list));
            }else{
              $('#requestList').append(interText(list));
            }
            if(total_pages <= 1){
              loadDataDone('<?php echo $lang['label_no_data'];?>');
            }else{
              page_num = page_num + 1;
              scroll();
              $('.data-loading').hide();
            }
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
  function getDetail(id){
   // window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=member&op=insuranceContractDetail&id="+id;
  }
  function loadDataDone(msg){
    $('.data-loading').hide();
    $('.no-record').text(msg);
    $('.no-record').show();
  }
</script>
