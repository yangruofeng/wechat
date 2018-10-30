<?php
$check_list = $output['identity'];
?>
<div class="row">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="business-content">
                <div class="business-list">
                    <?php
                    $at_list = memberIdentityClass::getIdentityType();
                    foreach ($at_list as $a_key => $a_value) { ?>
                        <div style="padding-top: 15px"><label><?php echo $a_value ?></label></div>
                        <div class="col-sm-12 content" style="padding: 10px 0px 20px 0px">
                            <?php if ($check_list[$a_key]) { ?>
                                <div style="background-color: #f3f4f6;padding: 10px">
                                    <div>
                                        <span style="font-weight: 600;padding-right: 30px">Cert-Id: <?php echo $check_list[$a_key]['cert_sn']?></span>
                                        <span style="font-weight: 600;padding-right: 30px">Audit Time: <?php echo timeFormat($check_list[$a_key]['auditor_time'])?></span>
                                        <?php
                                            $info_uid = $check_list[$a_key]['uid'];
                                            $btn = '.data-center-btn';
                                        ?>
                                        <?php include(template('widget/certification.expired'));?>
                                    </div>
                                    <?php
                                    $image_list=array();
                                    foreach($check_list[$a_key]['images'] as $img_item){
                                        $image_list[] = array(
                                            'url' => $img_item['image_url'],
                                            'image_source' => $img_item['image_source'],
                                        );
                                    }
                                    include(template(":widget/item.image.viewer.list"));
                                    ?>

                                </div>
                            <?php } else { ?>
                                <div style="background-color: #f3f4f6;padding: 10px">
                                    <div style="width: 250px">
                                        <?php include(template(":widget/no_record")); ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>