<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=6">
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
  <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
  <h2 class="title">Error</h2>
</header>
<style>
.tip {
  font-size: .7rem;
  text-align: center;
  padding: 1rem .4rem;
}
</style>
<div class="tip">
  <?php echo $output['msg'];?>
</div>
<script type="text/javascript">
  
</script>
