<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/client.css?v=2">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=1">
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  <span class="right-btn" onclick="window.location.href='<?php echo getUrl('home', 'regFirst', array('cpage'=>1), false, WAP_OPERATOR_SITE_URL)?>'"><i class="aui-iconfont aui-icon-plus"></i></span>
  <span class="right-search-btn" id="showSearch"><i class="aui-iconfont aui-icon-search"></i></span>
</header>
<?php $list = $output['list'];?>
<div class="wrap client-wrap">
  <div class="client-top-wrapper">
    <div class="count"><?php echo count($list);?></div>
    <div class="label">Customer Quantity</div>
  </div>
  <div class="client-data-wrapper aui-margin-t-10">
    <?php if(count($list) > 0){ ?>
      <ul class="aui-list aui-media-list cash-list flow-list">
        <?php foreach($list as $val){ ?>
        <li class="aui-list-item aui-list-item-middle" onclick="javascript:location.href='<?php echo getUrl('home', 'search', array('type'=>1,'guid'=>$val['obj_guid']), false, WAP_OPERATOR_SITE_URL)?>'">
          <div class="aui-media-list-item-inner">
            <div class="aui-list-item-inner aui-list-item-arrow">
              <div class="aui-list-item-text">
                <div class="aui-list-item-title type"><?php echo $val['login_code'];?></div>
                <div class="aui-list-item-right type">Credit: <?php echo $val['credit']?:0;?></div>
                <div class="aui-list-item-right text colorcc0000"><?php echo $val['credit_balance']?:0;?></div>
              </div>
              <div class="aui-list-item-text">
                <div class="aui-list-item-title title color999"><?php echo $val['obj_guid']?></div>
                <div class="aui-list-item-right text color999"><?php echo $val['update_time']?></div>
              </div>
            </div>
          </div>
        </li>
        <?php }?>
      </ul>
    <?php }else{?>
      <div class="no-record"><?php echo $lang['label_no_data'];?></div>
    <?php }?>
  </div>                        
</div>
<div class="search-wrap" style="display: none;">
  <div class="content">
    <div class="search-wrapper">
      <input type="hidden" name="type" id="type" value="1">
      <form action="" method="get" class="search-from search-cid" id="searchForm">
        <span class="s-icon"><i class="aui-iconfont aui-icon-my"></i></span>
        <input type="search" name="guid" id="guid" value="<?php echo $_GET['cid']?:$_GET['cid'];?>" placeholder="CID">
        <span class="s-search"><i class="aui-iconfont aui-icon-search"></i></span>
      </form >
      <form action="" method="get" class="search-from search-phone" id="searchPhoneForm" style="display: none;">
        <span class="s-icon"><i class="aui-iconfont aui-icon-phone"></i></span>
        <select name="country_code" id="country_code">
          <option value="66">+66</option>
          <option value="84">+84</option>
          <option value="86">+86</option>
          <option value="855" selected>+855</option>
        </select>
        <i class="aui-iconfont aui-icon-down"></i>
        <input type="search" name="phone_number" id="phone_number" placeholder="Phone Number">
        <span class="s-search"><i class="aui-iconfont aui-icon-search"></i></span>
      </form>
    </div>
    <div class="cancel-search">Cancel</div>
  </div>
  <div class="tip">Input phone number or login code to search customer.</div>
</div>
<?php include_once(template('widget/inc_footer'));?>
<script type="text/javascript">
  var key = '<?php echo $output['token'];?>', login = '<?php echo $_GET['login'];?>';
  if(!key){
    if(window.operator){
      window.operator.reLogin();
    }
  }
  if(window.operator){
    window.operator.showTitle('<?php echo $output['header_title'];?>');
  }
  var type = '<?php echo $_GET['source']?>', l = '<?php echo $_GET['lang']?>';
  if (type == 'app') {
    app_show(type);
  }
  function app_show(type) {
    if (type == 'app') {
      $('#header').hide();
    } else {
      $('#header').show();
    }
  }

  $('.s-icon').on('click', function(){
    $('.search-cid').toggle();
    $('.search-phone').toggle();
    var type = $('#type').val() == 1 ? 2 : 1;
    $('#type').val(type);
  });

  $('#searchForm,#searchPhoneForm').on('submit', function(event){
    //拦截表单默认提交事件
    event.preventDefault();
    //获取input框的值，用ajax提交到后台
    var reg = new RegExp('^[0-9]*$'), type = $('#type').val(), guid = $('#guid').val(), 
        country_code = $('#country_code').val(), phone_number = $('#phone_number').val();
        param = '&type='+type;
    if(type == 1){
      if(!guid){
        verifyFail('Please input member cid');
        return;
      }
      if(!reg.test(guid)){
        verifyFail('请输入数字!');
        return;
      }
      param += '&guid='+guid;
    }else if(type == 2){
      if(!reg.test(phone_number)){
        verifyFail('请输入数字!');
        return;
      }
      param += '&country_code='+country_code;
      param += '&phone_number='+phone_number;
    }else{
      return;
    }
    window.location.href = "<?php echo WAP_OPERATOR_SITE_URL?>/index.php?act=home&op=search"+param;
  });

  $('#showSearch').on('click', function(){
    $('.search-wrap').show();
    $('#guid').focus();
  });
  $('.cancel-search').on('click', function(){
    $('.search-wrap').hide();
    $('#guid').blur();
  });
</script>
