<?php
$info = $data['data'];
$client_info = $data['client_info'];
$cert_sample_images = $data['cert_sample_images'];
$verify_field = enum_langClass::getCertificationTypeEnumLang();
?>
<?php if($info){?>
<table class="table">
    <tbody class="table-body">
    <tr>
        <td><label class="control-label">Member Name</label></td>
        <td colspan="3"><?php echo $client_info['display_name']?:$client_info['login_code']; ?></td>
    </tr>
    <?php if ($cert_sample_images[$info['cert_type']]) { ?>
        <tr>
            <td><label class="control-label">Sample</label></td>
            <td colspan="3">
                <?php foreach ($cert_sample_images[$info['cert_type']] as $sample) { ?>
                    <div style="display:inline-block;width: 150px;text-align: center;margin-right: 5px;">
                        <a target="_blank" href="<?php echo $sample['image']; ?>">
                            <img src="<?php echo $sample['image']; ?>" alt="" style="width: 150px;height: 150px" />
                        </a>
                        <h5 style="color:red;">
                            <?php echo $sample['des']; ?>
                        </h5>
                    </div>
                <?php }  ?>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td><label class="control-label">Images</label></td>
        <td colspan="3">
            <?php
            $cert_image=$info['cert_images'];
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
    </tr>

    <?php if ($info['verify_state'] == certStateEnum::PASS) { ?>
        <tr>
            <td><label class="control-label">Certification Sn</label></td>
            <td>
                <?php echo $info['cert_sn']; ?>
            </td>
            <td><label class="control-label">Certification Type</label></td>
            <td>
                <?php echo $client_info['id_type'] == 1 ? "Foreign Country" : "Homeland"; ?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Gender</label></td>
            <td>
                <?php echo ucwords($client_info['gender']); ?>
            </td>
            <td><label class="control-label">Date of Birth</label></td>
            <td>
                <?php echo dateFormat($client_info['birthday']); ?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Nationality</label></td>
            <td>
                <?php echo strtoupper($client_info['nationality']); ?>
            </td>
            <td><label class="control-label">Expire Time</label></td>
            <td>
                <?php echo timeFormat($info['cert_expire_time']); ?>
            </td>

        </tr>
        <tr>
            <td><label class="control-label">Birth Address</label></td>
            <td>
                <?php echo $client_info['address_detail']; ?>
            </td>
            <td><label class="control-label">Audit Time</label></td>
            <td>
                <?php echo timeFormat($client_info['auditor_time']); ?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Source Type</label></td>
            <td>
                <?php echo $lang['cert_source_type_' . $info['source_type']]?>
                <?php if ($info['creator_name']) { ?>
                    <span>【<?php echo $info['creator_name']; ?>】</span>
                <?php } ?>
            </td>
            <td><label class="control-label">Verify State</label></td>
            <td><?php echo $lang['cert_state_' . $info['verify_state']];?></td>
        </tr>
        <tr>
            <td><label class="control-label">Remark</label></td>
            <td colspan="3">
                <?php echo trim($info['verify_remark']); ?>
            </td>
        </tr>
    <?php } else { ?>
        <tr>
            <td><label class="control-label">Certification Sn</label></td>
            <td>
                <?php echo $info['cert_sn']; ?>
            </td>
            <td><label class="control-label">Certification Name</label></td>
            <td>
                <?php echo $info['cert_name']; ?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Source Type</label></td>
            <td>
                <?php echo $lang['cert_source_type_' . $info['source_type']]?>
                <?php if ($info['creator_name']) { ?>
                    <span>【<?php echo $info['creator_name']; ?>】</span>
                <?php } ?>
            </td>
            <td><label class="control-label">Verify State</label></td>
            <td><?php echo $lang['cert_state_' . $info['verify_state']];?></td>
        </tr>
        <tr>
            <td><label class="control-label">Create Time</label></td>
            <td>
                <?php echo timeFormat($client_info['create_time']); ?>
            </td>
        </tr>
    <?php } ?>

    </tbody>
</table>
<?php include(template(":widget/item.image.viewer.js"));?>
<?php } else { ?>
    <div style="padding: 10px 10px"><?php require template(":widget/no_record")?></div>
<?php } ?>


