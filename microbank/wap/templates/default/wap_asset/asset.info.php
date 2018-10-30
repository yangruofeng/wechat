<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/css/home.css?v=2">
<?php include_once(template('widget/inc_simple_header')); ?>
<?php $data = $output['asset_info']; ?>
<div class="wrap verify-wrap">
    <?php if (!$data) { ?>
        <div class="no-search-member" style="display: block;">No Record.</div>
    <?php } ?>
    <?php if ($data) { ?>
        <div class="verify-wrapper">
            <div class="member-info" id="memberInfo">
                <?php if ($output['asset_image']) { ?>
                    <img src="<?php echo getImageUrl($output['asset_image']['image_url']); ?>" class="avatar"
                         style="width: 4rem;height: 4rem;flex: 0 0 4rem">
                <?php } else { ?>
                    <img src="<?php echo WAP_OPERATOR_SITE_URL . '/resource/image/default_avatar1.png'; ?>"
                         class="avatar">
                <?php } ?>
                <div class="main">
                    <p class="name">
                        Name：
                        <?php echo $data['asset_name']; ?>
                        <span style="color: #4cae4c">(<?php echo $data['asset_sn']; ?>)</span>
                    </p>
                    <?php $asset_type_list = (new certificationTypeEnum())->Dictionary(); ?>
                    <p class="name" style="">
                        Owner：
                        <?php foreach ($output['asset_owner'] as $owner) { ?>
                            <span style="padding-right: 7px"><?php echo $owner['relative_name'] ?></span>
                        <?php } ?>
                    </p>

                    <p class="name">Asset Type：<?php echo $asset_type_list[$data['asset_type']] ?></p>

                    <p class="name">Certification Type：<?php echo $data['asset_cert_type'] ?></p>
                </div>
            </div>
            <ul class="aui-list operator-list aui-margin-b-10">
                <li class="aui-list-item operator-item" onclick="clientOperator('evaluateInfo');">
                    <div class="aui-list-item-label-icon icon">
                        <img src="<?php echo WAP_SITE_URL; ?>/resource/image/icon-assert-evaluation.png" alt=""
                             class="icon-item">
                    </div>
                    <div class="aui-list-item-inner content aui-list-item-arrow">
                        <?php echo 'Evaluate Info'; ?>
                    </div>
                </li>
                <li class="aui-list-item operator-item" onclick="clientOperator('rentalInfo');">
                    <div class="aui-list-item-label-icon icon">
                        <img src="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/image/icon-credit-verfication.png"
                             alt="" class="icon-item">
                    </div>
                    <div class="aui-list-item-inner content aui-list-item-arrow">
                        <?php echo 'Rental Info'; ?>
                    </div>
                </li>
                <li class="aui-list-item operator-item" onclick="clientOperator('storageFLow');">
                    <div class="aui-list-item-label-icon icon">
                        <img src="<?php echo WAP_SITE_URL; ?>/resource/image/icon-contracts.png" alt=""
                             class="icon-item">
                    </div>
                    <div class="aui-list-item-inner content aui-list-item-arrow">
                        <?php echo 'Storage FLow'; ?>
                    </div>
                </li>
                <li class="aui-list-item operator-item" onclick="clientOperator('loanContract');">
                    <div class="aui-list-item-label-icon icon">
                        <img src="<?php echo WAP_SITE_URL; ?>/resource/image/icon-request-loan.png" alt=""
                             class="icon-item">
                    </div>
                    <div class="aui-list-item-inner content aui-list-item-arrow">
                        <?php echo 'Loan Contract'; ?>
                    </div>
                </li>
                <li class="aui-list-item operator-item" onclick="clientOperator('moreImage');">
                    <div class="aui-list-item-label-icon icon">
                        <img src="<?php echo WAP_SITE_URL; ?>/resource/image/icon-image.png" alt=""
                             class="icon-item">
                    </div>
                    <div class="aui-list-item-inner content aui-list-item-arrow">
                        <?php echo 'More Images'; ?>
                    </div>
                </li>
            </ul>
        </div>
    <?php } ?>

</div>
<script>
    var uid = '<?php echo $data['uid']?>';
    function clientOperator(op) {
        window.location.href = '<?php echo WAP_SITE_URL;?>/index.php?act=wap_asset&op=' + op + '&uid=' + uid;
    }
</script>