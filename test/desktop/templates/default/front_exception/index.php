<button onclick="test()">点击</button>
<?php require_once template('widget/app.common.js'); ?>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jq.json.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/yo.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/yo.extend.js"></script>
<script>
    function test(){
        yo.loadData({
            _c: "front_exception",
            _m: 'test',
            param: {},
            callback: function (_o) {

            }
        })
    }
</script>