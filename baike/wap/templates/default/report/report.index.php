<?php include_once(template('widget/inc_header_weui'));?>
<div class="weui-cells">
    <a class="weui-cell weui-cell_access js_item" href="<?php echo getUrl('report', 'toBMIndex', array('member_id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>">
        <div class="weui-cell__bd">
            <p>To Branch Manager</p>
        </div>
        <div class="weui-cell__ft"></div>
    </a>
    <a class="weui-cell weui-cell_access js_item" href="<?php echo getUrl('home', 'clientCbc', array(), false, WAP_OPERATOR_SITE_URL)?>">

        <div class="weui-cell__bd">
            <p> CBC</p>
        </div>
        <div class="weui-cell__ft"></div>
    </a>
    <a class="weui-cell weui-cell_access js_item" href="<?php echo getUrl('home', 'creditOfficer', array('id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>">
        <div class="weui-cell__bd">
            <p>Officer List</p>
        </div>
        <div class="weui-cell__ft"></div>
    </a>
    <a class="weui-cell weui-cell_access js_item" href="<?php echo getUrl('home', 'mortgagedAsset', array('member_id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>">
        <div class="weui-cell__bd">
            <p>Mortgaged Asset</p>
        </div>
        <div class="weui-cell__ft"></div>
    </a>
    <a class="weui-cell weui-cell_access js_item" href="<?php echo getUrl('report', 'interestList', array('member_id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>">
        <div class="weui-cell__bd">
            <p>Interest List</p>
        </div>
        <div class="weui-cell__ft"></div>
    </a>
</div>
