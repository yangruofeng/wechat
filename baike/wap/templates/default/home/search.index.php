<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=2">
<?php include_once(template('widget/inc_header'));?>
  <?php $data = $output['data'];?>
<div class="wrap verify-wrap">
  <?php if(!$data){?>
    <div class="no-search-member" style="display: block;">No Member.</div>
  <?php } ?>
  <?php if($data){?>
    <div class="verify-wrapper">
      <div class="member-info" id="memberInfo">
        <?php if($data['member_icon']){?>
          <img src="<?php echo getImageUrl($data['member_icon']);?>" class="avatar">
        <?php }else{ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL.'/resource/image/default_avatar1.png'; ?>" class="avatar">
        <?php }?>
        <div class="main">
          <input type="hidden" id="cid" value="<?php echo $data['obj_guid'];?>">
          <input type="hidden" id="client_uid" value="<?php echo $data['uid'];?>">
          <input type="hidden" id="client_name" value="<?php echo $data['login_code'];?>">
          <p class="name"><?php echo $data['login_code'];?> (<?php echo $data['obj_guid'];?>)</p>  
          <p class="phone"><?php echo $data['phone_id'];?></p>  
          <p class="credit">Credit: 1,000</p>  
        </div>
      </div>
      <ul class="aui-list operator-list aui-margin-b-10">
        <li class="aui-list-item operator-item" onclick="clientOperator('information');">
          <div class="aui-list-item-label-icon icon">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/icon-information.png" alt="" class="icon-item">
          </div>
          <div class="aui-list-item-inner content aui-list-item-arrow">
            <?php echo 'Information';?><span class="desc"><!--Create New Client--></span>
          </div>
        </li>
        <li class="aui-list-item operator-item" onclick="clientOperator('verify');">
          <div class="aui-list-item-label-icon icon">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/icon-credit-verfication.png" alt="" class="icon-item">
          </div>
          <div class="aui-list-item-inner content aui-list-item-arrow">
            <?php echo 'Credit Verfication';?><span class="desc"><!--Create New Client--></span>
          </div>
        </li>
        <li class="aui-list-item operator-item" onclick="clientOperator('assetsEvaluate');">
          <div class="aui-list-item-label-icon icon">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/icon-assert-evaluation.png" alt="" class="icon-item">
          </div>
          <div class="aui-list-item-inner content aui-list-item-arrow">
            <?php echo 'Assets Evaluate';?><span class="desc"><!--Create New Client--></span>
          </div>
        </li>
        <li class="aui-list-item operator-item" onclick="clientOperator('businessEvaluate');">
          <div class="aui-list-item-label-icon icon">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/icon-business-evaluate.png" alt="" class="icon-item">
          </div>
          <div class="aui-list-item-inner content aui-list-item-arrow">
            <?php echo 'Income Research';?><span class="desc"><!--Create New Client--></span>
          </div>
        </li>
        <li class="aui-list-item operator-item" onclick="clientOperator('addCreditRequest');">
          <div class="aui-list-item-label-icon icon">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/icon-request-credit.png" alt="" class="icon-item" style="width: 1.2rem;">
          </div>
          <div class="aui-list-item-inner content fontweight700 aui-list-item-arrow">
            <?php echo 'Suggest For Credit';?><span class="desc"><!--Create New Client--></span>
          </div>
        </li>
        <li class="aui-list-item operator-item" onclick="clientOperator('addLoanRequest');">
          <div class="aui-list-item-label-icon icon">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/icon-request-loan.png" alt="" class="icon-item" style="width: 1.2rem;">
          </div>
          <div class="aui-list-item-inner content fontweight700 aui-list-item-arrow">
            <?php echo 'Request For Loan';?><span class="desc"><!--Create New Client--></span>
          </div>
        </li>
        <li class="aui-list-item operator-item" onclick="clientOperator('clientReport');">
          <div class="aui-list-item-label-icon icon">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/icon-request-loan.png" alt="" class="icon-item" style="width: 1.2rem;">
          </div>
          <div class="aui-list-item-inner content aui-list-item-arrow">
            <?php echo 'Report';?><span class="desc"><!--Create New Client--></span>
          </div>
        </li>
      </ul>
    </div>
  <?php } ?>
  
</div>
<script>
  var back = '<?php echo $_GET['back'];?>';
  if(back){
    $('#header .back').attr('onclick', "javascript:location.href='<?php echo getUrl('client', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>'");
  }
  
  function clientOperator(op){
    var cid = $('#cid').val(),client_uid = $('#client_uid').val(), client_name = $('#client_name').val();
    window.location.href = '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op='+op+'&cid='+cid+'&id='+client_uid+'&name='+client_name;

  }
</script>