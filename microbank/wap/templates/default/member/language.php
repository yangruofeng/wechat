<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/member.css?v=1">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/inc_header.css?v=2">
<header class="top-header" id="header">
  <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  <span class="right-btn" id="confirm" onclick="confirm();" style="display: none;"><?php echo $lang['act_confirm'];?></span>
</header>
<div class="wrap setting-wrap">
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list user-item">
      <li class="aui-list-item" onclick="change('en');">
        <div class="aui-list-item-inner">
          <?php echo $lang['label_english'];?>
          <span class="check check_en" style="<?php if(Language::currentCode() == 'en'){ ?>display:block;<?php }else{ ?>display:none;<?php }?>"><i class="aui-iconfont aui-icon-correct"></i></span>
        </div>
      </li>
      <li class="aui-list-item" onclick="change('kh');">
        <div class="aui-list-item-inner">
          <?php echo $lang['label_khmer'];?>
          <span class="check check_kh" style="<?php if(Language::currentCode() == 'kh'){ ?>display:block;<?php }else{ ?>display:none;<?php }?>"><i class="aui-iconfont aui-icon-correct"></i></span>
        </div>
      </li>
      <li class="aui-list-item" onclick="change('zh_cn');">
        <div class="aui-list-item-inner">
          <?php echo $lang['label_simplified_chinese'];?>
          <span class="check check_zh_cn" style="<?php if(Language::currentCode() == 'zh_cn'){ ?>display:block;<?php }else{ ?>display:none;<?php }?>"><i class="aui-iconfont aui-icon-correct"></i></span>
        </div>
      </li>
    </ul>
  </div>
</div>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/common.js?v=1"></script>
<script type="text/javascript">
var cur;
function change(lang){
  cur = lang;
  $('.check').hide();
  $('.check_'+lang).show();
  if(CURRENT_LANGUAGE_CODE == lang){
    $('#confirm').hide();
    return;
  }
  $('#confirm').show();

}
function confirm(){
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  changeLang(cur, 2);
}
</script>
