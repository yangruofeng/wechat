<?php
$assets_group = $output['assets']['assets_group'];
$assets_evaluate_list = $output['assets']['assets_evaluate_list'];
$rental_research = $output['assets']['rental_research'];
?>
<div class="row">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="business-content">
                <div class="business-list">
                    <?php
                    $at_list = enum_langClass::getAssetsType();
                    foreach ($at_list as $a_key => $a_value) { ?>
                        <div style="padding-top: 15px"><label><?php echo $a_value ?></label></div>
                        <div class="col-sm-12 content" style="padding: 10px 0px 20px 0px">
                            <table class="table table-no-background table-bordered" style="background: #f3f4f6">
                                <tr>
                                    <td style="font-weight: 600">AssetName(Id)</td>
                                    <td style="font-weight: 600">Valuation</td>
                                    <td style="font-weight: 600">Rental</td>
                                    <td style="font-weight: 600">Images</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?php if (count($assets_group[$a_key]) > 0) { ?>
                                    <?php foreach ($assets_group[$a_key] as $asset_item) { ?>
                                        <tr>
                                            <td>
                                                <?php if ($asset_item['relative_id'] > 0) { ?>
                                                    <span><i class="fa fa-legal"></i></span>
                                                <?php } ?>
                                                <?php echo $asset_item['asset_name'] . ($asset_item['asset_sn'] ? '(' . $asset_item['asset_sn'] . ')' : ''); ?>
                                            </td>
                                            <td>
                                                <?php echo $assets_evaluate_list[$asset_item['uid']] ? ncPriceFormat($assets_evaluate_list[$asset_item['uid']]['evaluation']) : '' ?>
                                            </td>
                                            <td>
                                                <?php echo $rental_research[$asset_item['uid']] ? ncPriceFormat($rental_research[$asset_item['uid']]['monthly_rent']) : '' ?>
                                            </td>
                                            <td>
                                                <?php
                                                $image_list=array();
                                                foreach($asset_item['images'] as $img_item){
                                                    $image_list[] = array(
                                                        'url' => $img_item['image_url'],
                                                        'image_source' => $img_item['image_source'],
                                                    );
                                                }
                                                include(template(":widget/item.image.viewer.list"));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $info_uid = $asset_item['cert_id'];
                                                $btn = '.data-center-btn';
                                                ?>
                                                <?php include(template('widget/certification.expired'));?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="4">
                                            No Record
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>