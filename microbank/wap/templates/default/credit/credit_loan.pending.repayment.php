<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<?php $data = $output['data'];
    $total = $data['pending_repayment_total'][0];
    $list = $data['list'];
    $total_pages = $data['total_pages'];
    $total_num = $data['total_num'];
    $current_page = $data['current_page'];
    $page_size = $data['page_size'];
    ?>
<div class="wrap pending-repayment-wrap">
  <div class="pending-amount">
    <p class="amount"><?php echo ncPriceFormat($total['amount']);?> <em><?php echo $total['currency'];?></em></p>
    <p class="title">Total Amount</p>
  </div>
  <div class="pending-repayment-list">
    <p class="title">Pending Repayment:</p> 
    <ul class="aui-list aui-media-list pending-ul">
      <?php foreach ($list as $key => $value) {?>
        <li class="aui-list-item aui-list-item-middle">
          <div class="aui-media-list-item-inner">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-text">
                <div class="aui-list-item-title time"><?php echo dateFormat($value['receivable_date']);?></div>
                <div class="aui-list-item-right amount"><?php echo $value['currency'];?><em><?php echo ncPriceFormat($value['pending_repayment_amount']);?></em></div>
              </div>
              <div class="aui-list-item-text sn">
                <?php echo 'Contract Sn: '.$value['contract_sn'];?>
              </div>
            </div>
          </div>
        </li>
      <?php }?>
    </ul>
    <div class="no-record" style="<?php if($total_num <= 0 || $total_pages == 1){ ?>display: block;<?php }else{?>display: none;<?php }?>"><?php echo $lang['label_no_data'];?></div>
    <input type="hidden" name="page_size" id="page_size" value="<?php echo $page_size;?>">
    <input type="hidden" name="totalpage" id="totalpage" value="<?php echo $total_pages;?>">
    <input type="hidden" name="currentpage" id="currentpage" value="<?php echo $current_page;?>">
  </div>
</div>
<script type="text/html" id="tpl_credit_item">
  {{ if( it && it.length > 0 ){ }}
    {{ for(var i = 0; i< it.length; i++) { }}
      <li class="aui-list-item aui-list-item-middle">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title time">{{=it[i]['receivable_date']}}</div>
              <div class="aui-list-item-right amount">{{=it[i]['currency']}}<em>{{=it[i]['pending_repayment_amount']}}</em></div>
            </div>
            <div class="aui-list-item-text sn">
              Contract Sn: {{=it[i]['contract_sn']}}
            </div>
          </div>
        </div>
      </li>
    {{ } }}
  {{ } }}
</script>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-scroll.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/doT.min.js"></script>
<script type="text/javascript">
  var page_num = parseInt($.trim($('#currentpage').val())) + 1, page_size = $.trim($('#page_size').val());
  scroll();
  function scroll(){
    var range = 100, //距下边界长度/单位px
        elemt = 500, //插入元素高度/单位px
        maxnum = 2, //设置加载最多次数
        num = 1,totalheight = 0,main = $(".pending-ul"); //主体元素
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
      type: 'get',
      url: '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=ajaxGetPendingRepaymentData',
      data: {page_num: page_num, page_size: page_size},
      dataType: 'json',
      success: function(data){
        if(data.STS){
          var list = data.DATA.list;
          if(list == null){
            loadDataDone('<?php echo $lang['label_no_data'];?>');
          }else{
            var interText = doT.template($('#tpl_credit_item').text());
            $('.pending-ul').append(interText(list));
            page_num = page_num + 1;
            scroll();
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
</script>
