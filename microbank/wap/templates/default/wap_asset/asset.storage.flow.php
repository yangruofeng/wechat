<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/css/home.css?v=2">
<style>
    .assets-evalute-wrap .assets-list li > div {
        flex: 1;
        padding: .4rem .2rem;
        text-align: left;
    }

    .assets-evalute-wrap .assets-list .assets-title {
        font-weight: 500;
    }
</style>
<?php include_once(template('widget/inc_header')); ?>
<?php $data = $output['storage_list']; $flow_type = (new assetStorageFlowType())->Dictionary();?>
<div class="wrap verify-wrap assets-evalute-wrap">
    <div class="assets-list">
        <?php if(count($data) > 0){ ?>
        <ul class="aui-list assets-ul aui-margin-b-10">
            <li class="aui-list-item assets-title">
                <div>From</div>
                <div>To(Holder)</div>
                <div>Type</div>
                <div>Time</div>
            </li>
            <?php foreach($data as $k => $item){ ?>
                <li class="aui-list-item assets-item">
                    <div>
                        <label><?php echo $item['from_operator_name']?:$output['asset_info']['login_code']?></label><em style="font-size: 0.7rem;color: #808080;padding-left: 10px"><?php echo $item['from_branch_name']?:'client'?></em>
                    </div>
                    <div>
                        <label><?php echo $item['to_operator_name']?></label><em style="font-size: 0.7rem;color: #808080;padding-left: 10px"><?php echo $item['to_branch_name']?></em>
                    </div>
                    <div><?php echo $flow_type[$item['flow_type']]?></div>
                    <div><?php echo timeFormat($item['create_time'])?></div>
                </li>
            <?php } ?>
        </ul>
        <?php }else{ ?>
            <div class="no-record"><?php echo $lang['label_no_data'];?></div>
        <?php } ?>
    </div>
</div>
