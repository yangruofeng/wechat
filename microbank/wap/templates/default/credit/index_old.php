<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=15">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/inc_header.css?v=2">
<?php include_once(template('widget/inc_nav_header'));?>
<div class="wrap credit-wrap">
  <?php $credit_info = $output['credit_info'];print_r($credit_info);$product_id = $output['product_id'];?>
  <div class="credit-basic">
    <div class="credit-bar" id="creditBar">
      <canvas class="" id="myCanvas"></canvas>
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/bar-bg.png" class="bg" id="canvasBg" alt="">
      <!--<img src="<?php echo WAP_SITE_URL;?>/resource/image/bar-dot.png" class="dot" id="canvasDot" alt="">-->
      <div class="credit-info" onclick="javascript:location.href='<?php echo getUrl('credit', 'history', array(), false, WAP_SITE_URL)?>'">
        <p class="l"><?php echo 'Credit';?></p>
        <p class="b"><?php echo $credit_info['credit']?:'0.00';?></p>
        <p class="t"><?php echo 'Limit'.$credit_info['credit']?:'0.00';?></p>
      </div>
    </div>
    <input type="hidden" name="balance" id="balance" value="<?php echo $credit_info['balance']?:'0.00';?>">
    <input type="hidden" name="credit" id="credit" value="<?php echo $credit_info['credit']?:'0.00';?>">
    <div class="credit-oprt clearfix">
      <div class="oprt-item oprt-his" onclick="javascript:location.href='<?php echo getUrl('credit', 'certList', array(), false, WAP_SITE_URL)?>'">
        <?php echo 'Get Credit';?>
      </div>
      <div class="oprt-item oprt-help" onclick="javascript:location.href='<?php echo getUrl('credit', 'creditLoan', array(), false, WAP_SITE_URL)?>'">
        <?php echo $lang['label_get_money'];?>
      </div>
    </div>
  </div>
  <div class="">
    <?php $rate_info = $output['rate_list']; $rate_list = $rate_info['list']; $page_size = $rate_info['page_size']; $total_num = $rate_info['total_num']; $total_pages = $rate_info['total_pages']; $current_page = $rate_info['current_page'];?>
    <ul class="aui-list aui-media-list rate-list" id="rateList">
      <?php foreach ($rate_list as $key => $value) {?>
        <li class="aui-list-item aui-list-item-middle" onclick="creditLevel(<?php echo $value['uid'];?>);">
          <div class="aui-media-list-item-inner">
            <div class="aui-list-item-inner aui-list-item-arrow">
              <div class="aui-list-item-text">
                <div class="aui-list-item-title title"><?php echo 'Loan Size:'.$value['loan_size_min'].','.$value['loan_size_max'];?></div>
              </div>
              <div class="aui-list-item-text">
                <div class="aui-list-item-title title"><?php echo 'Time:'.$value['loan_term_time'];?></div>
                <div class="aui-list-item-right text"><?php echo $value['interest_rate_des_value'].$value['interest_rate_unit'];?></div>
              </div>
              <div class="aui-list-item-text">
                <div class="aui-list-item-title title"><?php echo $value['repayment_type'];?></div>
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
<div class="line-width" id="lineWidth"></div>
<div class="dot-width" id="dotWidth"></div>
<?php include_once(template('widget/inc_footer'));?>
<script type="text/html" id="tpl_credit_item">
  {{ if( it && it.length > 0 ){ }}
    {{ for(var i = 0; i< it.length; i++) { }}
      <li class="aui-list-item aui-list-item-middle" onclick="creditLevel({{=it[i]['uid']}});">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner aui-list-item-arrow">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title title"><?php echo 'Loan Size:';?>{{=it[i]['loan_size_min']}},{{=it[i]['loan_size_max']}}</div>
            </div>
            <div class="aui-list-item-text">
              <div class="aui-list-item-title title"><?php echo 'Time:';?>{{=it[i]['loan_term_time']}}</div>
              <div class="aui-list-item-right text">{{=it[i]['interest_rate_des_value']}}{{=it[i]['interest_rate_unit']}}</div>
            </div>
            <div class="aui-list-item-text">
              <div class="aui-list-item-title title">{{=it[i]['repayment_type']}}</div>
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
        num = 1,totalheight = 0,main = $(".rate-list"); //主体元素
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
      url: '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=getRatetData',
      data: {page_num: page_num, page_size: page_size},
      dataType: 'json',
      success: function(data){
        if(data.STS){
          var list = data.DATA.rate_list.list;
          if(list == null){
            loadDataDone('<?php echo $lang['label_no_data'];?>');
          }else{
            var interText = doT.template($('#tpl_credit_item').text());
            $('#rateList').append(interText(list));
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

  function creditLevel(id){
    window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=loan&op=loanLevelDetail&type=credit&id="+id;
  }



   let canvas = document.getElementById('myCanvas');
   let ctx = canvas.getContext('2d');
   var divWidth = $('#creditBar').css('width'), canvasWidth = divWidth = divWidth.replace(/px/,''),
      divHeight = $('#creditBar').css('height'), divHeight = divHeight.replace(/px/,''),
      lineWidth = $('#lineWidth').css('width'), lineWidth = lineWidth.replace(/px/,'');
      dotWidth = $('#dotWidth').css('width'), dotWidth = dotWidth.replace(/px/,'');
  var innerR = canvasWidth/2-lineWidth/2, outR = canvasWidth/2;
  let startPI = 1.25
  let totalPI = 1.5
  let delePI = 0.75
   canvas.setAttribute('width', canvasWidth+'px');
   canvas.setAttribute('height', canvasWidth+'px');
   const drawCircle = (percentage = 50, total = 100) => {
     // 清画布
     ctx.clearRect(0, 0, canvasWidth, canvasWidth);
      //背景
       ctx.strokeStyle = 'rgba(255, 255, 255, .4)';
       ctx.lineWidth = lineWidth;
       ctx.beginPath();
       ctx.arc(outR, outR, innerR, -1.25 * Math.PI, 0.25 * Math.PI);// 圆心横坐标，圆心纵坐标，半径，起始，结束
       ctx.stroke();
       ctx.save();
       //进度条
       ctx.strokeStyle = 'rgba(255, 255, 255, 1)'
       ctx.lineWidth = lineWidth;
       ctx.beginPath();
       let rate = totalPI / total * percentage - 1.25
       ctx.arc(outR, outR, innerR, -1.25 * Math.PI, rate * Math.PI);
       ctx.stroke();
       ctx.save();
       // 圆点
       ctx.beginPath();
       ctx.lineWidth = dotWidth;
       ctx.translate(outR, outR);//圆心
       ctx.strokeStyle = 'rgba(255, 255, 255, 1)';
       ctx.arc(innerR * Math.cos(((percentage / 100) * 1.5 - 1.25) * Math.PI), innerR * Math.sin(((percentage / 100) * 1.5 - 1.25) * Math.PI), dotWidth/2, 0, 2 * Math.PI)
       ctx.stroke();
       ctx.restore();
       // 画刻度
       for (var i = 0; i <= 10; i++) {
           ctx.save();
           ctx.translate(outR, outR);//圆心
           ctx.rotate(totalPI * Math.PI / 10 * i - delePI * Math.PI);
           ctx.lineWidth = lineWidth/2;
           ctx.strokeStyle = 'rgba(255, 255, 255, .8)'
           ctx.beginPath();
           ctx.moveTo(0, -50);
           ctx.lineTo(0, -50);
           ctx.stroke();
           ctx.restore();
       }
       for (var i = 0; i <= 50; i++) {
           ctx.save();
           ctx.translate(outR, outR);//圆心
           ctx.rotate(totalPI * Math.PI / 50 * i - delePI * Math.PI);
           ctx.lineWidth = lineWidth/2;
           ctx.strokeStyle = 'rgba(255, 255, 255, 1)'
           ctx.beginPath();
           ctx.stroke();
           ctx.restore();
       }
   }
   var credit = parseFloat($.trim($('#credit').val())), balance = parseFloat($.trim($('#balance').val()));
   var percent = balance ? balance/credit*100 : 0;
   let s = 0, max = percent > 100 ? 100 : percent;
   //drawCircle(40);
   var timer = setInterval(function(){
     if(s>max){
       clearInterval(timer)
     }else {
       drawCircle(s++)
     }
   },10);


</script>
