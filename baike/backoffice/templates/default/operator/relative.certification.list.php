<div>
    <table class="table verify-table">
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
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
                                <label class="lab-name">Client Name:</label><?php echo $row['display_name']?:$row['login_code'] ?>
                            </p>
                            <p>
                                <label class="lab-name">Client CID:</label><?php echo $row['member_guid']; ?>
                            </p>

                            <p>
                                <label class="lab-name">Relative Name:</label><?php echo $row['relative_name']; ?>
                                (<?php echo $row['relation_name']; ?>)
                            </p>


                            <p>
                                <label class="lab-name">Source Type:</label>
                                <?php echo $lang['cert_source_type_' . $row['source_type']]?>
                            </p>

                            <p>
                                <label class="lab-name">Submit Time:</label><?php echo timeFormat($row['create_time']); ?>
                            </p>

                            <p><label class="lab-name">Remark:</label><?php echo $row['verify_remark'] ?: '--'; ?></p>
                        </div>
                    </td>
                    <td>
                        <div class="verify-state">
                            <div class="title">Verify State</div>
                            <div class="content">
                                <div class="state">
                                    <?php echo $lang['cert_verify_state_' . $row['verify_state']]?>
                                </div>

                                <?php if ($row['verify_state'] == 0) { ?>
                                    <div class="custom-btn-group">
                                        <a class="custom-btn custom-btn-secondary"
                                           href="<?php echo getUrl('operator', 'getTaskOfRelativeCertification', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                            <span><i class="fa fa-vcard-o"></i>Get</span>
                                        </a>
                                    </div>
                                <?php } elseif ($row['verify_state'] == certStateEnum::LOCK && $data['cur_uid'] == $row['auditor_id']) { ?>
                                    <div class="custom-btn-group">
                                        <a title="Operator：Owner" class="custom-btn custom-btn-secondary"
                                           href="<?php echo getUrl('operator', 'showRelativeCertificationDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                            <span><i class="fa fa-vcard-o"></i>Handle</span>
                                        </a>
                                    </div>
                                <?php }else { ?>
                                    <div class="custom-btn-group">
                                        <a title="Operator：<?php echo $row['auditor_name']?>" class="custom-btn custom-btn-secondary"
                                           href="<?php echo getUrl('operator', 'showRelativeCertificationDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                            <span><i class="fa fa-vcard-o"></i>View</span>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td><?php include(template(":widget/no_record"));?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
<?php include(template(":widget/item.image.viewer.js"));?>
