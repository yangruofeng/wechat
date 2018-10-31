<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=7">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=1">
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
    <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
    <h2 class="title"><?php echo $output['header_title'];?></h2>
    <span class="right-search-btn" onclick="window.location.href='<?php echo getUrl('home', 'suggestHistory', array('id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>'"><i class="aui-iconfont aui-icon-menu"></i></span>
</header>

<div class="wrap loan-wrap" style="padding: 20px">
    <h5>Not allow to edit credit:</h5>
    <h3>Please add credit-request of client at first</h3>
</div>

<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/aui/aui-tab.js"></script>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/common.js?v=1"></script>
<script type="text/javascript">

    if(window.operator){
        window.operator.showTitle('<?php echo $output['header_title'];?>');
    }
</script>
