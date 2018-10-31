<div role="tabpanel" class="tab-pane" id="asset_list">
    <div class="clearfix" style="background-color: #FFF;padding: 10px 15px">
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
                        <td style="font-weight: 600">Function</td>
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
                                    <a class="btn btn-default" href="<?php echo getUrl("web_credit", "showAssetItemDetail", array("asset_id" => $asset_item['uid'], "member_id" => $client_info['uid'], 'source_mark' => $source_mark), false, BACK_OFFICE_SITE_URL) ?>">Detail</a>
                                    <a href="javascript:void(0)" onclick="showMyGoogleMap('<?php echo $asset_item['coord_x']?>','<?php echo $asset_item['coord_y']?>')" style="font-style: italic">Google Map</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5">
                                No Record
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>
    </div>
</div>