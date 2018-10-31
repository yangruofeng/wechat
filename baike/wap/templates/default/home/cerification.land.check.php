<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap cert-check-wrap">
  <?php $state = $output['state'];?>
  <?php if($state == certStateEnum::PASS){ ?>
    <div class="check-pic">
      <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/check-1.png" alt="">
      <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/check-3.png" alt="" class="icon-state">
    </div>
    <div class="check-tip color5cb85c">
      恭喜您审核已通过！
    </div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" onclick="javascript:location.href='<?php echo getUrl('credit', 'cerification', array('type'=>$output['type']), false, WAP_OPERATOR_SITE_URL)?>'">重新上传</div>
  <?php }elseif($state == certStateEnum::CREATE){ ?>
    <div class="check-pic">
      <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/check-1.png" alt="">
    </div>
    <div class="check-tip color333">
      文件已上传成功，待审核！
      <em>请耐心等待！</em>
    </div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" onclick="javascript:location.href='<?php echo getUrl('credit', 'cerification', array('type'=>$output['type'], 'cert_id'=>$output['cert_id']), false, WAP_OPERATOR_SITE_URL)?>'">Edit</div>
  <?php }elseif($state == certStateEnum::LOCK){ ?>
    <div class="check-pic">
      <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/check-1.png" alt="">
    </div>
    <div class="check-tip color9ea09e">
      File has been uploaded successfully,is under review new...
      <em>Please wait patiently!</em>
    </div>
  <?php }elseif($state == certStateEnum::NOT_PASS){ ?>
    <div class="check-pic">
      <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/check-1.png" alt="">
      <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/check-2.png" alt="" class="icon-state">
    </div>
    <div class="check-tip colord83e00">
      对不起！您提交的材料有误
      <em>审核未通过</em>
    </div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" onclick="javascript:location.href='<?php echo getUrl('credit', 'cerification', array('type'=>$output['type']), false, WAP_OPERATOR_SITE_URL)?>'">重新上传</div>
  <?php }elseif($state == certStateEnum::EXPIRED){ ?>
    <div class="check-pic">
      <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/check-1.png" alt="">
      <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/check-2.png" alt="" class="icon-state">
    </div>
    <div class="check-tip colord83e00">
      对不起！您的材料已过期
      <em>请重新上传</em>
    </div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" onclick="javascript:location.href='<?php echo getUrl('credit', 'cerification', array('type'=>$output['type']), false, WAP_OPERATOR_SITE_URL)?>'">重新上传</div>
  <?php } ?>
</div>
<script type="text/javascript">
var back = '<?php echo $_GET['back'];?>';
if(back){
  $('#header .back').attr('onclick', "javascript:location.href='<?php echo getUrl('credit', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>'");
}

</script>
