<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL; ?>/resource/css/help.css?v=4">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL; ?>/resource/script/aui/aui-pull-refresh.css">
<?php include_once(template('widget/inc_header')); ?>
<div class="wrap loan-contract-wrap">
    <div class="aui-refresh-content">
        <div class="limit-calculation tab-panel">
            <div class="aui-content aui-margin-b-15">
                <ul class="aui-list aui-media-list contract-list">
                    <?php foreach($output['list'] as $key => $val){?>
                    <li class="aui-list-item aui-list-item-middle" onclick="getList('<?php echo $key?>');">
                        <div class="aui-media-list-item-inner">
                            <div class="aui-list-item-inner">
                                <div class="aui-list-item-text">
                                    <div class="aui-list-item-title title"><?php echo ucwords(strtolower($val));?></div>
                                    <div class="aui-list-item-right"><i class="aui-iconfont aui-icon-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php }?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo WAP_SITE_URL; ?>/resource/script/aui/aui-tab.js"></script>
<script>
    function getList(category) {
        window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=help&op=helpList&category=" + category;
    }
</script>
