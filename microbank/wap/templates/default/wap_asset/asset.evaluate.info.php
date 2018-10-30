<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/css/home.css?v=2">
<?php include_once(template('widget/inc_header')); ?>
<?php $data = $output['asset_evaluate']; ?>
<div class="wrap verify-wrap">
    <?php if (!$data) { ?>
        <div class="no-search-member" style="display: block;">No Record.</div>
    <?php } ?>
    <?php if ($data) { ?>
        <ul class="aui-list info-list aui-margin-b-10">
            <li class="aui-list-item info-item">
                <div class="aui-list-item-inner content">
                    <?php echo 'Evaluation'; ?>
                    <div>
                        <?php echo ncPriceFormat($data['evaluation'])?>
                    </div>
                </div>
            </li>
            <li class="aui-list-item info-item">
                <div class="aui-list-item-inner content">
                    <?php echo 'Remark'; ?>
                    <div>
                        <?php echo $data['remark']?>
                    </div>
                </div>
            </li>
            <li class="aui-list-item info-item">
                <div class="aui-list-item-inner content">
                    <?php echo 'Time'; ?>
                    <div>
                        <?php echo timeFormat($data['evaluate_time'])?>
                    </div>
                </div>
            </li>
        </ul>
    <?php } ?>
</div>
