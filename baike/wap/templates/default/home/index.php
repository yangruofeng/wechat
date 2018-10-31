<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=2">
<div class="wrap index-wrap">
  <?php include_once(template('widget/inc_simple_header'));?>
  <div class="top-wrapper">
    <div class="top-bg"></div>
    <div class="top-avatar">
    <img src="<?php echo getImageUrl($info['member_icon'])?:WAP_OPERATOR_SITE_URL.'/resource/image/ic_1.png'; ?>" />
    </div>
    <div class="top-info">
      <div class="main">
        <p class="name"><?php echo $output['user_name'];?>(<?php echo $output['user_code'];?>)</p>
        <p class="point">Point: 1,000</p>
      </div>
    </div>
  </div>
  <div class="operator-wrapper aui-margin-t-10">
    <ul class="aui-list operator-list aui-margin-b-10">
      <li class="aui-list-item operator-item" onclick="javascript:location.href='<?php echo getUrl('home', 'regFirst', array('cpage'=>'1'), false, WAP_OPERATOR_SITE_URL)?>'">
        <div class="aui-list-item-label-icon icon">
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/home_registry.png" alt="" class="icon-item">
        </div>
 <div class="aui-list-item-inner content aui-list-item-arrow">
          <?php echo 'Register for client';?><span class="desc"><!--Create New Client--></span>
        </div>
      </li>
    </ul>
    <ul class="aui-list operator-list aui-margin-b-10">
      <li class="aui-list-item operator-item" onclick="javascript:location.href='<?php echo getUrl('home', 'search', array('cpage'=>'1'), false, WAP_OPERATOR_SITE_URL)?>'">
        <div class="aui-list-item-label-icon icon">
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/home_verify.png" alt="" class="icon-item">
        </div>
        <div class="aui-list-item-inner content aui-list-item-arrow">
          <?php echo 'Search Client';?><span class="desc"><!--Verifying Client Information--></span>
        </div>
      </li>
    </ul>
  </div>
</div>
<?php include_once(template('widget/inc_footer'));?>

