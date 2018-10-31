<div>
    <table class="table verify-table">
        <tbody class="table-body">
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
//                            $image_list[]=$img_item['image_url'];
                        }
                        include(template(":widget/item.image.viewer.list"));
                        ?>
                </td>
                <td>
                    <div class="cert-info">
                        <p>
                            <label class="lab-name">Member Name :</label>
                            <a href="<?php echo getUrl('client', 'clientDetail', array('uid' => $row['member_id'], 'show_menu' => 'client-client'), false, BACK_OFFICE_SITE_URL) ?>"><?php echo $row['login_code'] ?></a>
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
                            <?php echo $row['verify_remark'] ?: '/'; ?>
                        </p>
                    </div>
                </td>
                <td>
                    <div class="cert-type">
                        <h3><?php echo $output['verify_field'][$row['cert_type']]; ?></h3>
                        <?php if ($row['cert_type'] == certificationTypeEnum::ID && $row['cert_sn']) { ?>
                            <p><label class="lab-name">Certification Name :</label><?php echo $row['cert_name']; ?></p>
                            <p><label class="lab-name">Certification Sn :</label><?php echo $row['cert_sn']; ?></p>
                        <?php } ?>
                        <?php if ($data['asset_type'][$row['cert_type']]) { ?>
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
                            <div class="state"><?php echo $lang['cert_state_' . $row['verify_state']];?></div>
                            <div class="custom-btn-group">
                                <?php
                                    $params = array();
                                    $params['uid'] = $row['uid'];
                                    if($row['verify_state'] == certStateEnum::PASS){
                                        $params['source_mark'] = 'op_suggest';
                                    }
                                ?>
                                <a title="<?php echo 'Detail'; ?>" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('client', 'cerificationDetail', $params, false, BACK_OFFICE_SITE_URL) ?>">
                                    <span><i class="fa  fa-vcard-o"></i>View</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
<?php include(template(":widget/item.image.viewer.js"));?>
