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
<?php $data = $output['loan_list'];?>
<div class="wrap verify-wrap assets-evalute-wrap">
    <div class="assets-list">
        <?php if(count($data) > 0){ ?>
        <ul class="aui-list assets-ul aui-margin-b-10">
            <li class="aui-list-item assets-title">
                <div>SN</div>
                <div>Start-Date</div>
                <div>Product</div>
                <div>Principal</div>
                <div>Outstanding</div>
            </li>
            <?php foreach($data as $k => $item){ ?>
                <li class="aui-list-item assets-item">
                    <div><?php echo $item['contract_sn']?></div>
                    <div><?php echo dateFormat($item['start_date'])?></div>
                    <div><?php echo $item['alias']?></div>
                    <div><?php echo ncPriceFormat($item['principal_out'])?></div>
                    <div><?php echo ncPriceFormat($item['principal_outstanding'])?></div>
                </li>
            <?php } ?>
            <li class="aui-list-item assets-title">
                <div><label style="font-weight: 600">Total Outstanding Principal：<?php echo ncPriceFormat($output['principal_outstanding'])?></label></div>
            </li>
        </ul>
        <?php }else{ ?>
            <div class="no-record"><?php echo $lang['label_no_data'];?></div>
        <?php } ?>
    </div>
</div>
