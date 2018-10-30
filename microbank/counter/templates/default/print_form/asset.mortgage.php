<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    body {
        width: 1000px;
        margin: 20px auto;
        font-size: 15px;
    }

    .member-info span, .guarantee-list span {
        display: inline-block;
    }

    .down_line {
        height: 19px;
        line-height: 9px;
        border-bottom: 1px dashed black;
        padding: 5px;
    }

    td {
        border: 1px solid black;
        padding: 15px;
    }
</style>

<?php
$assets_info = $output['info'];
$certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
?>
<div>
    <div>
        <img src="resource/img/counter-icon/print_logo.png">

    </div>
    <div style="text-align: center;position: relative">

        <p style="font-size: 18px;font-weight:600;margin-top: 25px"><?php echo 'Mortgage Asset Info'; ?></p>
    </div>
    <div class="col-sm-12 member-info" style="padding:5px;margin-top: 15px;position: relative">
        <div>
            <span><?php echo 'Asset Name'; ?></span>
            <span class="down_line" style="width: 180px;"><?php echo $assets_info['asset_name'] ?></span>

            <span class="down_line" style="width: 150px;float:right;"><?php echo $assets_info['asset_sn'] ?></span>
            <span style="float:right;"><?php echo 'Asset No.'; ?></span>
        </div>

        <div style="margin-top: 12px;margin-left: 0px">
            <span><?php echo 'Asset Type'; ?></span>
            <span class="down_line" style="width: 244px;">
                <?php echo $certificationTypeEnumLang[$assets_info['asset_type']]; ?>
            </span>
            <span><?php echo 'Owner'; ?></span>
            <span class="down_line" style="width: 618px;">
               <?php echo implode('<span style="color: #34bf49;padding-right: 5px">/</span>', $assets_info['relative_list']); ?>
            </span>
        </div>

        <div style="margin-top: 12px;margin-left: 0px">
            <span><?php echo 'Product Name'; ?></span>
            <span class="down_line" style="width: 220px;">
                <?php echo $assets_info['product_alias']; ?>
            </span>
            <span><?php echo 'Authorize Contract'; ?></span>
            <span class="down_line" style="width: 220px;">
                <?php echo $assets_info['contract_no']; ?>
            </span>
            <span><?php echo 'Store Branch'; ?></span>
            <span class="down_line" style="width: 222px;">
                 <?php echo $assets_info['branch_name']; ?>
            </span>
        </div>
        <div style="margin-top: 12px;margin-left: 0px">
            <span><?php echo 'Evaluation'; ?></span>
            <span class="down_line" style="width: 247px;">
                <?php echo ncPriceFormat($assets_info['evaluation']); ?>
            </span>
            <span><?php echo 'Mortgage'; ?></span>
            <span class="down_line" style="width: 280px;">
                <?php echo ncPriceFormat($assets_info['credit']); ?>
            </span>
            <span><?php echo 'Mortgage Time'; ?></span>
            <span class="down_line" style="width: 205px;">
                <?php echo timeFormat($assets_info['keep_time']); ?>
            </span>
        </div>
    </div>


    <div class="col-sm-12" style="padding:30px 0px 0px 10px">
        <div class="col-sm-6" style="padding:0px">
            <table class="col-sm-11" style="padding:0px">
                <tr style="height: 15px">
                    <td colspan="2">____________________/____________________/____________________</td>
                </tr>
                <tr style="height: 30px">
                    <td class="col-sm-9"
                        style="padding:0px;text-align: center"><?php echo $lang['print_withdrawal_thumb']; ?></td>
                    <td class="col-sm-3"
                        style="padding:0px;text-align: center"><?php echo $lang['print_giver_signature']; ?></td>
                </tr>
                <tr style="height:120px">
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
        <div class="col-sm-6" style="padding:0px">
            <table class="col-sm-11">
                <tr style="height: 15px">
                    <td colspan="2">____________________/____________________/____________________</td>
                </tr>
                <tr style="height: 30px">
                    <td class="col-sm-9"
                        style="padding:0px;text-align: center"><?php echo $lang['print_withdrawal_thumb']; ?></td>
                    <td class="col-sm-3"
                        style="padding:0px;text-align: center"><?php echo $lang['print_giver_signature']; ?></td>
                </tr>
                <tr style="height:120px">
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-sm-12" style="padding:10px;margin-top:10px;margin-bottom: 50px;position: relative">
        <p>
            <?php echo 'Note：You can scan the Qr-Code to view the details of the assets.'; ?>
        </p>

        <div style="text-align: center">
            <img id="qr-small" style="width:200px;height:200px;"
                 src="<?php echo getUrl('print_form', 'getQrCode', array('url' => $output['wap_url']), false, ENTRY_COUNTER_SITE_URL) ?>">
        </div>
    </div>
</div>