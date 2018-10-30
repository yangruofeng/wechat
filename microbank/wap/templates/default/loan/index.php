<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/loan.css?v=3">
<div class="wrap index-wrap">
  <?php include_once(template('widget/inc_nav_header'));?>
  <ul class="home-nav aui-margin-b-10">
    <li onclick="window.location.href='<?php echo getUrl('loan', 'pay', array(), false, WAP_SITE_URL)?>'">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/home-pay.png" />
      <p>Pay</p>
    </li>
    <li onclick="window.location.href='<?php echo getUrl('loan', 'collect', array(), false, WAP_SITE_URL)?>'">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/home-collect.png" />
      <p>Collect</p>
    </li>
    <li onclick="window.location.href='<?php echo getUrl('loan', 'transfer', array(), false, WAP_SITE_URL)?>'">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/home-transfer.png" />
      <p>Transfer</p>
    </li>
  </ul>
  <div class="calculator-wrap aui-margin-b-10">
    <a href="<?php echo getUrl('loan', 'calculator', array(), false, WAP_SITE_URL)?>" class="calculator-btn"><?php echo $lang['label_calculator'];?></a>
    <p><?php echo $lang['label_loan_calculator'];?> | <?php echo $lang['label_interest_calculator'];?></p>
  </div>
  
  <div class="mortgage-loan">
    <div class="title">
      Loan
      <a href="<?php echo getUrl('loan', 'productList', array(), false, WAP_SITE_URL)?>">Product List <i class="aui-iconfont aui-icon-menu"></i></a>
    </div>
    <div class="info">
      <div class="item left">
        <div><span class="total" id="tAmount">0</span><span class="unit">USD</span></div>
        <div class="calculator">Interest: <span id="interest">0.00</span></div>
      </div>
      <div class="item right">
      <div><span class="total" id="tMonth">1</span><span class="unit">MONTH</span></div>
        <div class="calculator">Payable: <span id="payable">0</span></div>
      </div>
    </div>
    <div class="loan-bar">
      <div class="sliderBar amount-bar">
        <div class="dataDiv" id="amountDataDiv">
          <div class="item active" amount="0"><span>0</span><span class="scale">|</span></div>
          <div class="item" amount="500"><span>500</span><span class="scale">|</span></div>
          <div class="item" amount="1000"><span>1,000</span><span class="scale">|</span></div>
          <div class="item" amount="1500"><span>1,500</span><span class="scale">|</span></div>
          <div class="item" amount="2000"><span>2,000</span><span class="scale">|</span></div>
          <div class="item" amount="3000"><span>3,000</span><span class="scale">|</span></div>
          <div class="item" amount="5000"><span>5,000</span><span class="scale">|</span></div>
        </div>
        <div class="sliderDiv" id="amountSliderDiv">
          <div class="actionBlock" id="amountActionBlock"></div>
        </div>
      </div>
      <div class="sliderBar month-bar">
        <div class="dataDiv" id="monthDataDiv">
          <div class="item active" month="1"><span class="month">1M</span><span class="scale">|</span></div>
          <div class="item" month="3"><span>3M</span><span class="scale">|</span></div>
          <div class="item" month="6"><span>6M</span><span class="scale">|</span></div>
          <div class="item" month="12"><span>12M</span><span class="scale">|</span></div>
          <div class="item" month="24"><span>24M</span><span class="scale">|</span></div>
          <div class="item" month="36"><span>36M</span><span class="scale">|</span></div>
        </div>
        <div class="sliderDiv" id="monthSliderDiv">
          <div class="actionBlock" id="monthActionBlock"></div>
        </div>
      </div>
      <input type="hidden" id="amount" value="0" />
      <input type="hidden" id="month" value="1" />
    </div>
    <div class="loan-oprt">
      <div class="apply-online" href="<?php echo getUrl('loan', 'apply', array(), false, WAP_SITE_URL)?>" onclick="apply(this);"><?php echo $lang['act_apply_online'];?></div>
      <p class="tip">You can choose a larger or time in apply-form.</p>
    </div>
  </div>
</div>
<?php include_once(template('widget/inc_footer'));?>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/common.js"></script>
<script type="text/javascript">
  function apply(e){
    window.location.href = $(e).attr('href') + '&a=' + $('#amount').val()+ '&m=' + $('#month').val();
  }
  window.onload = function() {
    //公用param
    var ifBool = false, //判断鼠标是否按下
        actionBlockWidth = 20; //滑块宽度
    //Amount刻度
    var amountDataDiv = document.getElementById('amountDataDiv'), //总刻度
        amountDataItemDiv = amountDataDiv.getElementsByClassName('item'),
        amountDataItemDivLength = amountDataItemDiv.length, //刻度个数
        amountFirstItem = amountDataItemDiv[0], //得到第一个刻度
        amountDataItemWidth = amountFirstItem.offsetWidth,//得到单个刻度宽度
        amountActionBlock = document.getElementById('amountActionBlock'), //滑块
        amountInitPosition = parseInt(amountDataItemWidth/2 - actionBlockWidth/2) + 'px', //小方块初始位置（第一个刻度中间位置）
        amountLastPosition = 0; 
        amountActionBlock.style.marginLeft = amountInitPosition;
    //Month刻度
    var monthDataDiv = document.getElementById('monthDataDiv'), //总刻度
        monthDataItemDiv = monthDataDiv.getElementsByClassName('item'),
        monthDataItemDivLength = monthDataItemDiv.length, //刻度个数
        monthFirstItem = monthDataItemDiv[0], //得到第一个刻度
        monthDataItemWidth = monthFirstItem.offsetWidth,//得到单个刻度宽度
        monthActionBlock = document.getElementById('monthActionBlock'), //滑块
        monthInitPosition = parseInt(monthDataItemWidth/2 - actionBlockWidth/2) + 'px', //小方块初始位置（第一个刻度中间位置）
        monthLastPosition = 0; 
        monthActionBlock.style.marginLeft = monthInitPosition;
    //事件
    var amountStart = function(e) {
      e.stopPropagation();
      ifBool = true;
    }
    var monthStart = function(e) {
      e.stopPropagation();
      ifBool = true;
    }
    var move = function(e) {
      var ele = e.target.getAttribute('id');
      if(ifBool) {
        var x = (!e.touches) ? e.clientX : e.touches[0].pageX; ////兼容移动端和PC端
        if(ele == 'amountActionBlock'){
          var dataDiv_left = getPosition(amountDataDiv).left; //长线条的横坐标
          var actionBlock_left = x - dataDiv_left; //小方块相对于父元素（长线条）的left值 
          //滑块在最右边
          if(actionBlock_left >= amountDataDiv.offsetWidth - actionBlockWidth) {
            actionBlock_left = amountDataDiv.offsetWidth - actionBlockWidth;
          }
          //滑块在最左边
          if(actionBlock_left < 0) {
            actionBlock_left = 0;
          }
          //设置拖动后小方块的left值
          amountActionBlock.style.marginLeft = actionBlock_left + "px";
          amountLastPosition = actionBlock_left;
        }else if(ele == 'monthActionBlock'){
          var dataDiv_left = getPosition(monthDataDiv).left; //长线条的横坐标
          var actionBlock_left = x - dataDiv_left; //小方块相对于父元素（长线条）的left值 
          //滑块在最右边
          if(actionBlock_left >= monthDataDiv.offsetWidth - actionBlockWidth) {
            actionBlock_left = monthDataDiv.offsetWidth - actionBlockWidth;
          }
          //滑块在最左边
          if(actionBlock_left < 0) {
            actionBlock_left = 0;
          }
          //设置拖动后小方块的left值
          monthActionBlock.style.marginLeft = actionBlock_left + "px";
          monthLastPosition = actionBlock_left;
        }
        
      }
    }
    var end = function(e) {
      ifBool = false;
      var ele = e.target.getAttribute('id');
      if(ele == 'amountActionBlock'){
        var i = 0;
        for(i; i<amountDataItemDivLength; i++){
          removeClass(amountDataItemDiv[i], 'active');
        }
        amountLastPosition = amountLastPosition + actionBlockWidth/2;
        var index = Math.floor(amountLastPosition/amountDataItemWidth), remainder = parseInt(amountLastPosition%amountDataItemWidth);
        if(amountDataItemWidth - remainder > 0){
          index += 1;
        }
        index = index == 0 ? index : index-1;
        addClass(amountDataItemDiv[index], 'active');
        var indexLeft = amountDataItemDiv[index].offsetLeft, amount = amountDataItemDiv[index].getAttribute('amount');
        var actionBlock_left = indexLeft + parseInt(amountDataItemWidth/2 - actionBlockWidth/2);
        amountActionBlock.style.marginLeft = actionBlock_left + 'px';
        $('#amount').val(amount);
        toast.loading({
          title: '<?php echo $lang['label_loading'];?>'
        });
        ajaxGetLoanInfo();
      }else if(ele == 'monthActionBlock'){
        var i = 0;
        for(i; i<monthDataItemDivLength; i++){
          removeClass(monthDataItemDiv[i], 'active');
        }
        monthLastPosition = monthLastPosition + actionBlockWidth/2;
        var index = Math.floor(monthLastPosition/monthDataItemWidth), remainder = parseInt(monthLastPosition%monthDataItemWidth);
        if(monthDataItemWidth - remainder > 0){
          index += 1;
        }
        index = index == 0 ? index : index-1;
        addClass(monthDataItemDiv[index], 'active');
        var indexLeft = monthDataItemDiv[index].offsetLeft, month = monthDataItemDiv[index].getAttribute('month');
        var actionBlock_left = indexLeft + parseInt(monthDataItemWidth/2 - actionBlockWidth/2);
        monthActionBlock.style.marginLeft = actionBlock_left + 'px';
        $('#month').val(month);
        toast.loading({
          title: '<?php echo $lang['label_loading'];?>'
        });
        ajaxGetLoanInfo();
      }
      
      
    }
    //获取元素的绝对位置
    function getPosition(node) {
      var left = node.offsetLeft; //获取元素相对于其父元素的left值var left
      var top = node.offsetTop;
      current = node.offsetParent; // 取得元素的offsetParent
      　 // 一直循环直到根元素
      　　
      while(current != null) {　　
        left += current.offsetLeft;　　
        top += current.offsetTop;　　
        current = current.offsetParent;　　
      }
      return {
        "left": left,
        "top": top
      };
    }
    //鼠标按下方块
    amountActionBlock.addEventListener('touchstart', amountStart);
    amountActionBlock.addEventListener('mousedown', amountStart);
    //鼠标按下方块
    monthActionBlock.addEventListener('touchstart', monthStart);
    monthActionBlock.addEventListener('mousedown', monthStart);
    //拖动
    window.addEventListener('touchmove', move);
    window.addEventListener('mousemove', move);
    //鼠标松开
    window.addEventListener('touchend', end);
    window.addEventListener('mouseup', end);
  }

  function ajaxGetLoanInfo(){
    var amount = $('#amount').val(), loan_time = $('#month').val();
    $.ajax({
      url: '<?php echo getUrl('loan', 'ajaxLoanApplyPreview', array(), false, WAP_SITE_URL)?>',
      type: 'get',
      data: {amount: amount, loan_time: loan_time},
      dataType: 'json',
      success: function(ret){
        toast.hide();
        if(ret.STS){
          var data = ret.DATA;
          $('#tAmount').text(amount);
          $('#interest').text(data.total_interest_rate);
          $('#tMonth').text(loan_time);
          $('#payable').text(data.total_repayment_amount);
        }else{
          verifyFail('Please retry.');
        }
        
      }
    });
  }
</script>
