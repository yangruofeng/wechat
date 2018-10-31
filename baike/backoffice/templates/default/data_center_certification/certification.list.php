<?php
$certification_type = enum_langClass::getCertificationTypeEnumLang();
$asset_type = enum_langClass::getAssetsType();
?>
<div>
    <table class="table verify-table">
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td style="padding-top: 15px">
                    <?php include(template(":widget/no_record")); ?>
                </td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $key => $row) { ?>
                <tr>
                    <td>
                        <?php
                        $cert_image=$row['cert_images'];
                        $image_list=array();
                        foreach($cert_image as $img_item){
                            $image_list[] = array(
                                'url' => $img_item['image_url'],
                                'image_source' => $img_item['image_source'],
                            );
                        }
                        include(template(":widget/item.image.viewer.list"));
                        ?>
                    </td>
                    <td>
                        <div class="cert-info">
                            <p>
                                <label class="lab-name">Member Name :</label>
                                <?php echo $row['display_name']?:$row['login_code'] ?>
                            </p>

                            <p>
                                <label class="lab-name">Source Type :</label>
                                <?php echo $lang['cert_source_type_' . $row['source_type']]?>
                            </p>

                            <p>
                                <label class="lab-name">Submit Time :</label>
                                <?php echo timeFormat($row['create_time']); ?>
                            </p>

                            <p>
                                <label class="lab-name">Remark :</label>
                                <?php echo $row['verify_remark'] ?: '-'; ?>
                            </p>
                        </div>
                    </td>
                    <td>
                        <div class="cert-type">
                            <h3><?php echo $certification_type[$row['cert_type']]; ?></h3>
                            <?php if ($row['cert_type'] == certificationTypeEnum::ID && $row['cert_sn']) { ?>
                                <p><label class="lab-name">Certification Name :</label><?php echo $row['cert_name']; ?></p>
                                <p><label class="lab-name">Certification Sn :</label><?php echo $row['cert_sn']; ?></p>
                            <?php } ?>
                            <?php if ($asset_type[$row['cert_type']]) { ?>
                                <p><label class="lab-name">Asset Name :</label><?php echo $row['asset_name']; ?></p>
                                <p><label class="lab-name">Asset Id :</label><?php echo $row['asset_sn']; ?></p>
                            <?php } ?>
                        </div>
                    </td>
                    <td>
                        <div class="verify-state">
                            <div class="title">
                                Verify State
                            </div>
                            <div class="content">
                                <div class="state"><?php echo $lang['cert_verify_state_' . $row['verify_state']];?></div>
                                <div class="custom-btn-group">
                                    <a title="<?php echo 'Detail'; ?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('data_center_certification', 'showCertificationDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                        <span><i class="fa  fa-vcard-o"></i>View</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
<?php include(template(":widget/item.image.viewer.js"));?>