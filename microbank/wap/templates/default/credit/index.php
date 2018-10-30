<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=15">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/inc_header.css?v=2">
<?php include_once(template('widget/inc_nav_header'));?>
<div class="wrap credit-wrap">
  <?php $data = $output['data']; $credit_info = $data['credit_info'];?>
  <div class="credit-basic">
    <div class="credit-bar" id="creditBar">
      <canvas class="" id="myCanvas"></canvas>
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/bar-bg.png" class="bg" id="canvasBg" alt="">
      <!--<img src="<?php echo WAP_SITE_URL;?>/resource/image/bar-dot.png" class="dot" id="canvasDot" alt="">-->
      <div class="credit-info">
        <p class="l"><?php echo 'Credit';?></p>
        <p class="b"><?php echo $credit_info['balance']?:'0.00';?></p>
        <p class="t"><?php echo 'Limit'.$credit_info['credit']?:'0.00';?></p>
      </div>
    </div>
    <input type="hidden" name="balance" id="balance" value="<?php echo $credit_info['balance']?:'0.00';?>">
    <input type="hidden" name="credit" id="credit" value="<?php echo $credit_info['credit']?:'0.00';?>">
    <div class="credit-oprt clearfix">
      <div class="oprt-item oprt-his" onclick="javascript:location.href='<?php echo getUrl('credit', 'certList', array(), false, WAP_SITE_URL)?>'">
        <i class="aui-iconfont aui-icon-share"></i>
        <?php echo 'Share';?>
      </div>
      <div class="oprt-item oprt-help">
        <i class="aui-iconfont aui-icon-question"></i>
        <?php echo $lang['label_help'];?>
      </div>
    </div>
  </div>
  <ul class="aui-list aui-media-list aui-margin-t-10">
    <li class="aui-list-item aui-list-item-middle" onclick="javascript:location.href='<?php echo getUrl('credit', 'creditLoan', array('product_id'=>$data['product_id']), false, WAP_SITE_URL)?>'">
      <div class="aui-media-list-item-inner">
        <div class="aui-list-item-media" style="width: 3rem;">
          <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-41.png" class="">
        </div>
        <div class="aui-list-item-inner aui-list-item-arrow">
          <div class="aui-list-item-text">
            <div class="aui-list-item-title aui-font-size-14"><?php echo $lang['label_loan'];?></div>
          </div>
          <div class="aui-list-item-text">
              Monthly Interest: <?php echo $data['monthly_min_rate'];?>
          </div>
        </div>
      </div>
    </li>
    <li class="aui-list-item aui-list-item-middle" onclick="repayment(<?php echo $data['next_repayment_schema']['contract_id'] ?: 0;?>,<?php echo $data['next_repayment_schema']['amount'] ?: 0;?>);">
      <div class="aui-media-list-item-inner">
        <div class="aui-list-item-media" style="width: 3rem;">
          <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-42.png" class="">
        </div>
        <div class="aui-list-item-inner aui-list-item-arrow">
          <div class="aui-list-item-text">
            <div class="aui-list-item-title aui-font-size-14"><?php echo 'Return Loan';?></div>
          </div>
          <div class="aui-list-item-text">
              Next Repayment: <?php echo $data['next_repayment_schema']['amount'] ?: '0.00';?>
          </div>
        </div>
      </div>
    </li>
  </ul>
</div>
<div class="line-width" id="lineWidth"></div>
<div class="dot-width" id="dotWidth"></div>
<?php include_once(template('widget/inc_footer'));?>
<script type="text/javascript">
  function repayment(contract_id, amount){
    if(amount > 0){
      window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=repayment&contract_id="+contract_id;
    }
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
