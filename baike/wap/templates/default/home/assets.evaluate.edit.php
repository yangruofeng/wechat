<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/css/home.css?v=2">
<?php include_once(template('widget/inc_header')); ?>
<div class="wrap assets-evalute-wrap">
    <?php $data = $output['data'];
    $list = $output['list'];
    $verify_field = enum_langClass::getCertificationTypeEnumLang();
    ?>
    <form class="custom-form" id="" method="post">
        <div class="cerification-input aui-margin-b-10">
            <div class="loan-form">
                <ul class="aui-list aui-form-list loan-item">
                    <li class="aui-list-item">
                        <div class="aui-list-item-inner">
                            <div class="aui-list-item-label label">
                                Type
                            </div>
                            <div class="aui-list-item-input label-on">
                                <?php echo $verify_field[$data['asset_type']]?>
                            </div>
                        </div>
                    </li>
                    <li class="aui-list-item">
                        <div class="aui-list-item-inner">
                            <div class="aui-list-item-label label">
                                Asset Name
                            </div>
                            <div class="aui-list-item-input label-on">
                                <?php echo $data['asset_name'];?>
                            </div>
                        </div>
                    </li>
                    <li class="aui-list-item">
                        <div class="aui-list-item-inner">
                            <div class="aui-list-item-label label">
                                Valuation
                            </div>
                            <div class="aui-list-item-input">
                                <input type="text" class="mui_input" name="valuation" id="valuation" value="<?php echo $data['default_credit'] ?: ''; ?>"/>
                                <span class="p-unit">USD</span>
                            </div>
                        </div>
                    </li>
                    <li class="aui-list-item last-item">
                        <div class="aui-list-item-inner">
                            <div class="aui-list-item-label label label-all">
                                Remark
                            </div>
                        </div>
                    </li>
                    <li class="aui-list-item">
                        <div class="aui-list-item-inner paddingright075" style="margin-top: -0.5rem;">
                            <textarea class="mui_textarea" name="remark" id="remark"><?php echo $data['remark'] ?: ''; ?></textarea>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div style="padding: 0 .8rem;">
            <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" id="submit">
                Submit
            </div>
        </div>
    </form>
    <?php if (count($list) > 0) { ?>
        <ul class="aui-list recently-assets-ul aui-margin-t-15">
            <li class="aui-list-item assets-title">
                <div>Time</div>
                <div>Evaluation</div>
                <div>Remark</div>
            </li>
            <?php foreach ($list as $k => $v) { ?>
                <li class="aui-list-item assets-item">
                    <div><?php echo dateFormat($v['evaluate_time']); ?></div>
                    <div><?php echo ncPriceFormat($v['evaluation']); ?></div>
                    <div><?php echo $v['remark']; ?></div>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <div class="no-record">No Data.</div>
    <?php } ?>
</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/script/common.js?v=1"></script>
<script type="text/javascript">
    $('#submit').on('click', function () {
        var id = '<?php echo $_GET['uid'];?>',
            valuation = $.trim($('#valuation').val()),
            remark = $.trim($('#remark').val());
        if (!valuation) {
            verifyFail('<?php echo 'Please input valuation.';?>');
            return;
        }
        if (!checkMoney(valuation)) {
            verifyFail('<?php echo 'Valuation must be monetary.';?>');
            return;
        }
        if (!remark) {
            verifyFail('<?php echo 'Please input remark.';?>');
            return;
        }
        toast.loading({
            title: '<?php echo $lang['label_loading'];?>'
        });
        $.ajax({
            type: 'get',
            url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=ajaxEditEvalute',
            data: {id: id, valuation: valuation, currency: 'USD', remark: remark},
            dataType: 'json',
            success: function (data) {
                toast.hide();
                if (data.STS) {
                    window.location.href = "<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=assetsEvaluate&cid=<?php $_GET['cid'];?>&id=<?php echo $_GET['mid']?>&back=search&time=" + new Date().getTime();
                } else {
                    if (data.CODE == '<?php echo errorCodesEnum::INVALID_TOKEN;?>' || data.CODE == '<?php echo errorCodesEnum::NO_LOGIN;?>') {
                        reLogin();
                    }
                    verifyFail(data.MSG);
                }

            },
            error: function (xhr, type) {
                toast.hide();
                verifyFail('<?php echo $lang['tip_get_data_error'];?>');
            }
        });
    });
</script>
