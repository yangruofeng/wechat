<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/request.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap handle-wrap">
  <div class="handle-nav">
    <ul class="nav-ul clearfix">
      <li class="nav-item">
        <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up1.png"></p>
        <p class="text">Comment</p>
      </li>
      <li class="nav-item">
        <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up2_press.png"></p>
        <p class="text">Product</p>
      </li>
      <li class="nav-item">
        <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up3_1.png"></p>
        <p class="text">Rate</p>
      </li>
    </ul>
  </div>  
  <?php $product = $output['product'];?>
  <div class="handle-form">
    <div class="base-form">
      <div class="form-item form-select aui-margin-b-10">
        <label for=""><?php echo 'Product';?></label>
        <select name="product_id" id="product_id">
          <?php foreach ($product as $key => $value) { ?>
            <option value="<?php echo $value['product_id'];?>"><?php echo $value['product_name'];?></option>
          <?php }?>
        </select>
        <i class="aui-iconfont aui-icon-down"></i>
      </div>
      <div class="form-item form-select aui-margin-b-10">
        <label for=""><?php echo $lang['label_repayment_method'];?></label>
        <select class="" name="repayment_type" id="repayment_type">
          <option value="0"><?php echo $lang['label_select'];?></option>
          <option value="single_repayment"><?php echo $lang['label_bullet_repayment'];?></option>
          <option value="fixed_principal"><?php echo $lang['label_average_capital'];?></option>
          <option value="annuity_scheme"><?php echo $lang['label_average_capital_plus_interest'];?></option>
          <option value="flat_interest"><?php echo $lang['label_fixed_interest'];?></option>
          <option value="balloon_interest"><?php echo $lang['label_pay_first_return_later'];?></option>
        </select>
        <i class="aui-iconfont aui-icon-down"></i>
      </div>
      <div class="form-item form-select aui-margin-b-10" id="repaymentFrequency">
        <label for=""><?php echo $lang['label_repayment_frequency'];?></label>
        <select name="repayment_period" id="repayment_period">
          <option value="0"><?php echo $lang['label_select'];?></option>
          <option value="monthly"><?php echo $lang['label_once_a_month'];?></option>
          <option value="weekly"><?php echo $lang['label_once_a_week'];?></option>
        </select>
        <i class="aui-iconfont aui-icon-down"></i>
      </div>
    </div>
  </div>
  <div style="padding: 0 .8rem;">
    <div class="aui-btn aui-btn-block custom-btn custom-btn-purple" id="continue">Continue</div>
    <div class="aui-btn aui-btn-block custom-btn aui-margin-t-10 reject-btn" id="cancel">Cancel</div>
  </div>
</div>
<script type="text/javascript">
  $('#continue').on('click', function(){
    handle();
  });
  $('#repayment_type').on('change', function(){
    var repayment_type = $.trim($('#repayment_type').val());
    if(repayment_type == 'single_repayment'){
      $('#repaymentFrequency').hide();
    }else{
      $('#repaymentFrequency').show();
    }
  });
  function handle(){
    var param = {}, product_id = $('#product_id').val(), repayment_type = $('#repayment_type').val(), repayment_period = $('#repayment_period').val();
      param.product_id = product_id;
    if(repayment_type == 0){
      verifyFail('<?php echo 'Please select repayment method.';?>');
      return;
    }
    param.repayment_type = repayment_type;
    if(repayment_type != 'single_repayment'){
      if(repayment_period == 0){
        verifyFail('<?php echo 'Please select repayment frequency.';?>');
        return;
      }
      param.repayment_period = repayment_period;
    }
    param.request_id = '<?php echo $_GET['id'];?>';
    $.ajax({
      url: '<?php echo getUrl('request', 'ajaxHandleSecond', array(), false, WAP_OPERATOR_SITE_URL)?>',
      type: 'post',
      data: param,
      dataType: 'json',
      success: function(ret){
        type = 1;
        if(ret.DATA.interest_info == null){
          type = 2;
        }
        if(ret.STS){
          window.location.href = '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=request&op=handleThird&request_id=<?php echo $_GET['id'];?>&type=' + type;
        }else{
          verifyFail(ret.MSG);
        }
      }
    });
  }
</script>
