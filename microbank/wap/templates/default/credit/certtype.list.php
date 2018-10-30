<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap certtpye-wrap">
  <?php $list = $output['list'];$extend_info = $list['extend_info'];$count = count($extend_info);?>
  <?php if($count <= 0){?>
    <div class="certtpye-no-content">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/no-content.png" alt="">
      <p>no content</p>
    </div>
  <?php }else{ ?>
    <ul class="aui-list aui-media-list">
      <?php foreach ($extend_info as $key => $value) { ?>
        <li class="aui-list-item aui-list-item-middle" onclick="">
          <div class="aui-media-list-item-inner">
            <div class="aui-list-item-media"><img src="<?php echo getImageUrl($value['cert_images']['car_front']); ?>" class="aui-img-round aui-list-img-sm"></div>
            <div class="aui-list-item-inner">
              <p>
                <?php echo 'Asset Number: '.($key+1); ?>
                <?php if($value['verify_state'] == 0){?>
                  <a href="<?php echo getUrl('credit', 'cerification', array('type'=>$_GET['type'],'cert_id'=>$value['uid']), false, WAP_SITE_URL)?>" class="edit-btn"><?php echo $lang['act_edit'];?></a>
                <?php }?>
              </p>
              <p><?php echo 'Valuation: '.$value['valuation']; ?></p>
              <p><?php
                $state;
                switch ($value['verify_state']) {
                  case -1:
                    $state = 'in Review';
                    break;
                  case 0:
                    $state = 'Pending Review';
                    break;
                  case 10:
                    $state = 'Approve';
                    break;
                  case 100:
                    $state = 'Unapprove';
                    break;
                  default:
                    # code...
                    break;
                }
                echo 'Audit Status: '.$state;
              ?></p>
              <p><?php $str = $value['mortgage_state'] == 1 ? 'Pledged' : 'Un-pledged';echo 'Mortgage Status: '.$str; ?></p>
            </div>
          </div>
        </li>
      <?php } ?>
    </ul>
  <?php }?>
  <div class="add-btn" onclick="javascript:location.href='<?php echo getUrl('credit', 'cerification', array('type'=>$_GET['type']), false, WAP_SITE_URL)?>'">
    <span><img src="<?php echo WAP_SITE_URL;?>/resource/image/iocn-add.png" alt=""><?php echo $lang['act_add'];?></span>
  </div>

</div>
<script type="text/javascript">
  var back = '<?php echo $_GET['back'];?>';
  if(back){
    $('#header .back').attr('onclick', "javascript:location.href='<?php echo getUrl('credit', 'certList', array('back'=>'credit'), false, WAP_SITE_URL)?>'");
  }
</script>
