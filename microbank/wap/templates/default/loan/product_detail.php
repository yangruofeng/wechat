<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/loan.css?v=4">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap product-detail-wrap">
  <?php $info = $output['info'];$rate_list = $output['rate_list']; ?>
  <div class="description aui-margin-b-10">
    <div class="img"><div><img src="<?php echo WAP_SITE_URL;?>/resource/image/product1.png" alt=""></div></div>
    <div class="info"><?php echo strip_tags(urldecode($info['product_description']));?></div>
  </div>
  <div class="table aui-margin-b-10">
    <div class="title">
      <div class="clearfix">
        <div class="item">Loan Size</div>
        <div class="item">Interest Rate</div>
        <div class="item">Loan Fees</div>
        <div class="item">Operation Fees</div>
        <div class="item">Loan Period</div>
        <div class="item">Repayment Type</div>
      </div>
    </div>
    <div class="content">
      <?php foreach($rate_list as $v){ ?>
        <div class="row">
          <div class="clearfix">
            <div class="item"><?php echo $v['loan_size_min'];?>~<?php echo $v['loan_size_max'];?> <?php echo $v['currency'];?></div>
            <div class="item"><?php echo $v['interest_rate'];?>% (<?php echo $v['interest_rate_unit'];?>)</div>
            <div class="item"><?php echo $v['loan_fee'];?>%</div>
            <div class="item"><?php echo $v['operation_fee'];?>% (<?php echo $v['operation_fee_unit'];?>)</div>
            <div class="item"><?php echo $v['loan_term_time'];?></div>
            <div class="item"><?php echo $v['repayment_type'];?></div>
          </div>
        </div>
      <?php }?>
    </div>
  </div> 
  <ul class="aui-list product-item aui-margin-b-10">
    <li class="aui-list-item" onclick="toggleDiv('qualificationDetail', 'qualification');">
      <div class="title qualification">
        <div class="aui-list-item-label-icon">
          <img src="<?php echo WAP_SITE_URL;?>/resource/image/product_detail_client_qualification.png" alt="" class="icon-item icon-item4">
        </div>
        <div class="aui-list-item-inner">
          <?php echo 'Client Qualification';?>
        </div>
        <i class="aui-iconfont aui-icon-down"></i>
      </div>
      <div class="detail" id="qualificationDetail"><?php echo urldecode($info['product_qualification']);?></div>
    </li>
  </ul>
  <ul class="aui-list product-item aui-margin-b-10">
    <li class="aui-list-item" onclick="toggleDiv('requiredDetail', 'required');">
      <div class="title required">
        <div class="aui-list-item-label-icon">
          <img src="<?php echo WAP_SITE_URL;?>/resource/image/product_detail_document_required.png" alt="" class="icon-item icon-item4">
        </div>
        <div class="aui-list-item-inner">
          <?php echo 'Documents Required';?>
        </div>
        <i class="aui-iconfont aui-icon-down"></i>
      </div>
      <div class="detail" id="requiredDetail"><?php echo urldecode($info['product_required']);?></div>
    </li>
  </ul>
  <ul class="aui-list product-item aui-margin-b-10">
    <li class="aui-list-item" onclick="toggleDiv('noticeDetail', 'notice');">
      <div class="title notice">
        <div class="aui-list-item-label-icon">
          <img src="<?php echo WAP_SITE_URL;?>/resource/image/product_detail_notice.png" alt="" class="icon-item icon-item4">
        </div>
        <div class="aui-list-item-inner">
          <?php echo 'Notice';?>
        </div>
        <i class="aui-iconfont aui-icon-down"></i>
      </div>
      <div class="detail" id="noticeDetail"><?php echo urldecode($info['product_notice']);?></div>
    </li>
  </ul>
  <div style="padding: .8rem;">
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" onclick="javascript:location.href='<?php echo getUrl('loan', 'apply', array(), false, WAP_SITE_URL)?>'"><?php echo $lang['act_apply_online'];?></div>
  </div>
</div>
<script type="text/javascript">
  function toggleDiv(el, li){
    $('#' + el).toggle();
    var icon = $('.' + li + ' i');
    icon.hasClass('aui-icon-down') ? icon.addClass('aui-icon-top').removeClass('aui-icon-down') : icon.addClass('aui-icon-down').removeClass('aui-icon-top');
  }
</script>
