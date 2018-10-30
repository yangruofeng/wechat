<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/loan.css?v=8">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap apply-wrap">
  <div class="cert-tip">
    <img src="<?php echo WAP_SITE_URL;?>/resource/image/hint.png" alt="">
    <ul class="cert-tip-ul">
      <li class="fontweight">• The request here is only for large loans.It needs asset mortgages for manual review.</li>
      <li>• <?php echo $lang['label_tip'].$lang['label_colon'];?> After successful application,please keep your contact way unimpeded. Our staff will contact you as soon as possible.Thank you!</li>
    </ul>
  </div>
  <p class="verify"><a href="<?php echo getUrl('credit', 'certList', array(), false, WAP_SITE_URL)?>">Credit Verfication</a></p>
  <div class="apply-form">
    <div class="form-item">
      <label for=""><?php echo $lang['label_loan_amount'];?></label>
      <input type="number" name="amount" id="amount" value="<?php echo $_GET['a']?:'';?>" placeholder="Enter">
    </div>
    <div class="form-item input-time">
      <label for=""><?php echo 'Loan Time';?></label>
      <input type="number" name="loan_time" id="loan_time" value="<?php echo $_GET['m']?:'';?>" placeholder="Enter">
      <select class="" name="loan_time_unit" id="loan_time_unit">
        <option value="year"><?php echo $lang['label_year'];?></option>
        <option value="month" selected><?php echo $lang['label_month'];?></option>
        <option value="day"><?php echo $lang['label_day'];?></option>
      </select>
      <i class="aui-iconfont aui-icon-down"></i>
    </div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple apply-next-btn" id="confirm"><?php echo $lang['act_confirm'];?></div>
  </div>
</div>
<div class="mortgage-list-wrap" style="display: none;">
  <div class="content">
    <div class="title">
      <?php echo 'Mortgage';?>
      <span class="cancel" id="iCancel"><?php echo $lang['act_cancel'];?></span>
      <span class="confirm" id="iConfirm"><?php echo $lang['act_confirm'];?></span>
    </div>
    <div class="list">
      <?php $mortgage_type = $output['mortgage_type'];?>
      <ul class="aui-list aui-media-list mortgage-ul">
        <?php foreach ($mortgage_type as $key => $value) { ?>
          <li class="aui-list-item">
            <div class="aui-list-item-label-icon mortgage-icon">
              <span class="item-ck" data-uid="<?php echo $value['uid'];?>" data-name="<?php echo $value['item_name'];?>"></span>
            </div>
            <div class="aui-list-item-inner mortgage-text">
              <div class="name"><?php $item_name_json = json_decode($value['item_name_json'],true); echo $item_name_json[Language::currentCode()];?></div>
            </div>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</div>
<div class="apply-success">
  <div class="content">
    <img src="<?php echo WAP_SITE_URL;?>/resource/image/gou.png" alt="">
    <p class="title"><?php echo $lang['tip_apply_succeeded'];?></p>
    <p class="tip"><?php echo $lang['label_tip'].$lang['label_colon'];?> <?php echo $lang['tip_total_interest'];?></p>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn apply-succ-btn custom-btn-purple" id="applyDone"><?php echo $lang['act_confirm'];?></div>
  </div>
</div>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-dialog.js"></script>
<script type="text/javascript">
  $('#select_mortgage').on('click', function(){
    $('.mortgage-list-wrap').show();
  });
  $('#iCancel').on('click', function(){
    $('.mortgage-list-wrap').hide();
  });
  $('#iConfirm').on('click', function(){
    $('.mortgage-list-wrap').hide();
    var ck = document.getElementsByClassName('item-ck'), i = 0, len = ck.length, uids = [], names = [];
    for (i; i < len; i++) {
      var flag = $(ck[i]).hasClass('active');
      if(flag){
        var uid = $(ck[i]).attr('data-uid'), name = $(ck[i]).attr('data-name');
        uids.push(uid);
        names.push(name);
      }
    }
    uids = uids.join(',');
    names = names.join(',');
    $('#mortgage').val(uids);
    $('#select_mortgage').val(names);
  });
  $('.mortgage-icon').on('click', function(e){
    $(this).find('.item-ck').hasClass('active') ? $(this).find('.item-ck').removeClass('active') : $(this).find('.item-ck').addClass('active');
  });
  $('#confirm').on('click', function(){
    var param = {}, amount = $.trim($('#amount').val()),
        loan_time = $.trim($('#loan_time').val()),
        loan_time_unit = $.trim($('#loan_time_unit').val());
    if(!amount){
      verifyFail('<?php echo $lang['tip_please_input_loan_amount'];?>');
      return;
    }
    if(!loan_time){
      verifyFail('<?php echo 'please input loan time';?>');
      return;
    }
    
    param.amount = amount;
    param.loan_time = loan_time;
    param.loan_time_unit = loan_time_unit;
    
    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo WAP_SITE_URL;?>/index.php?act=loan&op=applyConfirm',
      data: param,
      dataType: 'json',
      success: function(data){
        toast.hide();
        if(data.STS){
          $('.apply-success').show();
        }else{
          if(data.DATA == 10){
            dialog.alert({
              title: '<?php echo $lang['label_tip'];?>',
              msg: '<?php echo $lang['tip_please_relogin'];?>',
              buttons:['<?php echo $lang['act_confirm'];?>']
            },function(ret){
              if(ret.buttonIndex){
                setTimeout(function(){
                  window.location.href = "<?php echo getUrl('login', 'index', array(), false, WAP_SITE_URL)?>";
                },300);
              }
            });
          }else{
            verifyFail(data.MSG);
          }
        }
      },
      error: function(xhr, type){
        toast.hide();
        verifyFail('<?php echo $lang['tip_get_data_error'];?>');
      }
    });
  });
  $('#applyDone').on('click', function(){
      window.location.href = "<?php echo getUrl('loan', 'index', array(), false, WAP_SITE_URL)?>";
  });
  function verifyFail(msg){
    toast.fail({
      title: msg,
      duration: 2000
    });
  }
</script>
