<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/request.css?v=4">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap verify-wrap">
  <div class="search-wrapper">
    <p class="title">Please Bind To Client</p>
    <form action="" method="post" class="search-from search-phone" id="searchPhoneForm">
      <span class="s-icon"><i class="aui-iconfont aui-icon-phone"></i></span>
      <select name="country_code" id="country_code">
        <option value="66">+66</option>
        <option value="84">+84</option>
        <option value="86">+86</option>
        <option value="855">+855</option>
      </select>
      <i class="aui-iconfont aui-icon-down"></i>
      <input type="search" name="phone_number" id="phone_number" placeholder="Phone Number">
      <span class="s-search"><i class="aui-iconfont aui-icon-search"></i></span>
    </form >
  </div>
  <div class="no-search-tip">Please input the member phone number search.</div>
  <div class="no-search-member">No Member.</div>
  <div class="verify-wrapper" style="display: none;">
    <ul class="aui-list request-detail-ul" id="memberInfo"></ul>
    <div style="padding: .2rem .8rem;">
      <div class="aui-btn aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" id="bind">Bind</div>
    </div>
  </div>
</div>
<script type="text/html" id="tpl_client_info">
  {{ if( it ){ }}
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Client ID
        </div>
        <div class="aui-list-item-input label-on">
          {{=it['uid']}}
          <input type="hidden" name="uid" id="uid" value="{{=it['uid']}}">
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Client Name
        </div>
        <div class="aui-list-item-input label-on">
          {{=it['login_code']}}
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Client Phone
        </div>
        <div class="aui-list-item-input label-on">
          {{=it['phone_id']}}
        </div>
      </div>
    </li>
  {{ } }}
</script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/doT.min.js"></script>
<script>
  var back = '<?php echo $_GET['back'];?>';
  if(back){
    $('#header .back').attr('onclick', "javascript:location.href='<?php echo getUrl('request', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>'");
  }

  $('#searchPhoneForm').on('submit', function(event){
    //拦截表单默认提交事件
     event.preventDefault();
    //获取input框的值，用ajax提交到后台
    var param = {}, reg = new RegExp('^[0-9]*$'), country_code = $('#country_code').val(), phone_number = $('#phone_number').val();
    if(!reg.test(phone_number)){
      verifyFail('请输入数字!');
    }
    param.type = 2;
    param.country_code = country_code;
    param.phone_number = phone_number;
    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    $.ajax({
      url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=searchClient',
      type: 'post',
      data: param,
      dataType: 'json',
      success: function(ret){
        toast.hide();
        if(ret.STS){
          $('.verify-wrapper').show();
          data = ret.DATA;
          console.log(data)
          $('.no-search-tip').hide();
          $('.no-search-member').hide();
          var interText = doT.template($('#tpl_client_info').text());
          $('#memberInfo').html(interText(data));
        }else{
          $('.verify-wrapper').hide();
          $('.no-search-tip').hide();
          $('.no-search-member').show();
        }
      }
    });
  });
  $('#bind').on('click', function(){
    var request_id = '<?php echo $_GET['uid'];?>', member_id = $('#uid').val();
    if(!request_id){
      verifyFail('数据错误');
      return;
    }
    if(!member_id){
      verifyFail('Please select client.');
      return;
    }
    $.ajax({
      url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=request&op=ajaxBindClient',
      type: 'post',
      data: {request_id: request_id, member_id: member_id},
      dataType: 'json',
      success: function(ret){
        toast.hide();
        if(ret.STS){
          toast.success({
            title: 'Bind Success',
            duration: 2000
          });
          setTimeout(function(){
            window.location.href = '<?php echo getUrl('request', 'detail', array('uid'=>$_GET['uid']), false, WAP_OPERATOR_SITE_URL)?>';
          }, 2000);
        }else{
          verifyFail(ret.MSG);
        }
      }
    });
  });
</script>