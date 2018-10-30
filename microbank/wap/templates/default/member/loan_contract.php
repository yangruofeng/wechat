<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/member.css?v=4">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-pull-refresh.css">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap loan-contract-wrap">
  <div class="aui-tab contract-tab" id="tab">
    <div class="aui-tab-item aui-active"><?php echo $lang['label_processing'];?></div>
    <div class="aui-tab-item"><div></div><?php echo $lang['label_overdue'];?></div>
    <div class="aui-tab-item"><div></div><?php echo $lang['label_all'];?></div>
  </div>
  <div class="aui-refresh-content">
    <div class="limit-calculation tab-panel" id="tab-1" type="2">
      <div class="aui-content aui-margin-b-15">
        <ul class="aui-list aui-media-list contract-list" id="contract-list-1"></ul>
      </div>
    </div>
    <div class="limit-calculation tab-panel" id="tab-2" type="3" style="display: none;">
      <div class="aui-content aui-margin-b-15">
        <ul class="aui-list aui-media-list contract-list" id="contract-list-2"></ul>
      </div>
    </div>
    <div class="limit-calculation tab-panel" id="tab-3" type="1" style="display: none;">
      <div class="aui-content aui-margin-b-15">
        <ul class="aui-list aui-media-list contract-list" id="contract-list-3"></ul>
      </div>
    </div>
  </div>
  <div class="data-loading" style="display: none;"><img src="<?php echo WAP_SITE_URL;?>/resource/image/loading.gif" alt=""></div>
  <div class="no-record" style="display: none;"><?php echo $lang['label_no_data'];?></div>
  <input type="hidden" name="totalpage" id="totalpage" value="0">
</div>
<script type="text/html" id="tpl_contract_item">
  {{ if( it && it.length > 0 ){ }}
    {{ for(var i = 0; i< it.length; i++) { }}
      <li class="aui-list-item aui-list-item-middle" onclick="getDetail({{=it[i]['uid']}});">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner aui-list-item-arrow">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title title">{{=it[i]['contract_sn']}}</div>
              <div class="aui-list-item-right text">{{=it[i]['create_time']}}</div>
            </div>
            <div class="aui-list-item-text">
              <div class="aui-list-item-title title name">{{=it[i]['product_name']}}</div>
            </div>
            <div class="aui-list-item-text">
              <div class="aui-list-item-title title"><?php echo $lang['label_apply_amount'].$lang['label_colon'];?>{{=it[i]['apply_amount']}}</div>
            </div>
            <div class="aui-list-item-text">
              <div class="aui-list-item-title title"><?php echo $lang['label_balance'].$lang['label_colon'];?>{{=it[i]['left_principal']}}</div>
              <div class="aui-list-item-right text">Left Period:{{=it[i]['left_period']}}</div>
            </div>
          </div>
        </div>
      </li>
    {{ } }}
  {{ } }}
</script>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-tab.js"></script>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-scroll.js"></script>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-pull-refresh.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/doT.min.js"></script>
<script type="text/javascript">
  $('#contract-list-1').html('');
  $('.data-loading').show();
  var type = $('#tab-1').attr('type'), index = 1, page_num = 1, page_size = 10;
  getPageData();
  var tab = new auiTab({
    element: document.getElementById('tab'),
    index: 1,
    repeatClick: false
  },function(ret){
    var i = ret.index;
    $('.tab-panel').hide();
    $('#tab-' + i).show();
    index = i, type = $('#tab-' + i).attr('type'), page_num = 1;
    $('#contract-list-' + i).html('');
    $('.no-record').hide();
    $('.data-loading').show();
    getPageData();
  });
  //下拉刷新
  var pullRefresh = new auiPullToRefresh({
    container: document.querySelector('.aui-refresh-content'),
    triggerDistance: 100
  },function(ret){
    if(ret.status == 'success'){
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
        var srollPos = $(window).scrollTop(), i = 0, arr_len = 0, page, c_p = $('#c_p').text(); //滚动条距顶部距离(页面超出窗口的高度)
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
      url: '<?php echo WAP_SITE_URL;?>/index.php?act=member&op=getLoanContractData',
      data: {type: type, page_num: page_num, page_size: page_size},
      dataType: 'json',
      success: function(data){
        if(data.STS){
          var list = data.DATA.list, total_pages = data.DATA.total_pages;
          $('#totalpage').val(total_pages);
          if(list == null){
            loadDataDone('<?php echo $lang['label_no_data'];?>');
          }else{
            var interText = doT.template($('#tpl_contract_item').text());
            if(page_num == 1){
              $('#contract-list-' + index).html(interText(list));
            }else{
              $('#contract-list-' + index).append(interText(list));
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
    window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=member&op=loanContractDetail&id="+id;
  }

  function loadDataDone(msg){
    $('.data-loading').hide();
    $('.no-record').text(msg);
    $('.no-record').show();
  }
</script>
