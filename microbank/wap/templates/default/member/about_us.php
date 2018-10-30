<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/member.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap aboutus-wrap">
  <?php $detail = $output['detail'];?>
  <div class="about-logo">
    <img src="<?php echo WAP_SITE_URL;?>/resource/image/logo.png" class="logo">
    <div class="">
      <?php echo $detail['company_name'];?>
    </div>
  </div>
  <div class="about-profile aui-margin-b-10">
    <ul class="profile-model-list">
      <li class="profile-model-item profile-model-title">
        <div class="title">
          <?php echo $lang['label_company_profile'];?>
        </div>
      </li>
      <li class="profile-model-item profile-model-content">
        <?php echo $detail['description'];?>
      </li>
    </ul>
  </div>
  <div class="about-branch aui-margin-b-10">
    <ul class="aui-list user-item">
      <li class="aui-list-item" onclick="javascript:location.href='<?php echo getUrl('member', 'aboutUs', array(), false, WAP_SITE_URL)?>'">
        <div class="aui-list-item-inner aui-list-item-arrow">
          <?php echo $lang['label_branch_list'];?>
        </div>
      </li>
    </ul>
  </div>
  <div class="about-contact aui-margin-b-10">
    <ul class="aui-list user-item">
      <li class="aui-list-item">
        <div class="aui-list-item-inner about-label">
          <?php echo $lang['label_contact_number'];?>
          <div class="number">
            028-66666666
          </div>
        </div>
      </li>
    </ul>
  </div>
  <div class="about-address">
    <ul class="aui-list user-item">
      <li class="aui-list-item">
        <div class="aui-list-item-inner about-label">
          <?php echo $lang['label_address'];?>
          <div class="">
            <?php echo $detail['address_detail'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item about-map">
        <div class="aui-list-item-inner map-cont">
          <img src="<?php echo WAP_SITE_URL;?>/resource/image/map.jpg">
        </div>
      </li>
    </ul>
  </div>
</div>
<script type="text/javascript">
var type = '<?php echo $_GET['source']?>';
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
</script>
