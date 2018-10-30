<style>
    .btn {
        height: 30px;
        min-width: 80px;
        padding: 5px 12px;
        border-radius: 0px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        background-color: #FFF;
        overflow: hidden;
    }

    .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    .verify-img {
        max-height: 150px;
        margin-right: 15px;
        float: left;
    }
</style>
<?php $client_info = $output['client_info']?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'showClientDetail', array('uid' => $client_info['uid']), false, BACK_OFFICE_SITE_URL) ?>"><span>Credit Process</span></a></li>
                <li><a class="current"><span>Personal Information</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="basic-info">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>ID Information</h5>
            </div>
            <div class="content">
                <table class="table audit-table">
                    <tbody class="table-body">
                    <?php if (!$output['id_cert']) { ?>
                        <tr>
                            <td colspan="4">Unverified</td>
                        </tr>
                    <?php } else { $id_cert = $output['id_cert']; ?>
                        <tr>
                            <td><label class="control-label">Images</label></td>
                            <td colspan="3">
                                <?php foreach ($id_cert['images'] as $value) { ?>
                                    <a target="_blank" href="<?php echo getImageUrl($value['image_url']); ?>">
                                        <img src="<?php echo getImageUrl($value['image_url'], imageThumbVersion::W150); ?>" class="verify-img">
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">English Name</label></td>
                            <td><?php echo implode(' ', my_json_decode($client_info['id_en_name_json'])); ?></td>
                            <td><label class="control-label">Khmer Name</label></td>
                            <td><?php echo implode(' ', my_json_decode($client_info['id_kh_name_json'])); ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Sn Number</label></td>
                            <td><?php echo $client_info['id_sn']; ?></td>
                            <td><label class="control-label">Sn Type</label></td>
                            <td><?php echo $client_info['id_type'] == 1 ? "Foreign Country" : "Homeland"; ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Gender</label></td>
                            <td><?php echo ucwords($client_info['gender']); ?></td>
                            <td><label class="control-label">Date of Birth</label></td>
                            <td><?php echo dateFormat($client_info['birthday']); ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Nationality</label></td>
                            <td><?php echo strtoupper($client_info['nationality']); ?></td>
                            <td><label class="control-label">Sn Address</label></td>
                            <td> <?php echo $id_cert['cert_addr']; ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Expire Time</label></td>
                            <td><?php echo timeFormat($client_info['id_expire_time']); ?></td>
                            <td><label class="control-label">Source Type</label></td>
                            <td>
                                <?php echo $lang['cert_source_type_' . $id_cert['source_type']]?>
                                <?php if ($id_cert['creator_name']) { ?>
                                    <span>【<?php echo $id_cert['creator_name']; ?>】</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Remark</label></td>
                            <td colspan="3"><?php echo $id_cert['verify_remark']; ?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="basic-info">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Family Book</h5>
            </div>
            <div class="content">
                <table class="table audit-table">
                    <tbody class="table-body">
                    <?php if (!$output['family_book_cert']) { ?>
                        <tr>
                            <td colspan="4">Unverified</td>
                        </tr>
                    <?php } else { $family_book_cert = $output['family_book_cert']; ?>
                        <tr>
                            <td><label class="control-label">Images</label></td>
                            <td colspan="3">
                                <?php foreach ($family_book_cert['images'] as $value) { ?>
                                    <a target="_blank" href="<?php echo getImageUrl($value['image_url']); ?>">
                                        <img src="<?php echo getImageUrl($value['image_url'], imageThumbVersion::W150); ?>" class="verify-img">
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Source Type</label></td>
                            <td>
                                <?php echo $lang['cert_source_type_' . $family_book_cert['source_type']]?>
                                <?php if ($family_book_cert['creator_name']) { ?>
                                    <span>【<?php echo $family_book_cert['creator_name']; ?>】</span>
                                <?php } ?>
                            </td>
                            <td><label class="control-label">Remark</label></td>
                            <td><?php echo $family_book_cert['verify_remark']; ?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="basic-info">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Passport</h5>
            </div>
            <div class="content">
                <table class="table audit-table">
                    <tbody class="table-body">
                    <?php if (!$output['passport_cert']) { ?>
                        <tr>
                            <td colspan="4">Unverified</td>
                        </tr>
                    <?php } else { $passport_cert = $output['passport_cert']; ?>
                        <tr>
                            <td><label class="control-label">Images</label></td>
                            <td colspan="3">
                                <?php foreach ($passport_cert['images'] as $value) { ?>
                                    <a target="_blank" href="<?php echo getImageUrl($value['image_url']); ?>">
                                        <img src="<?php echo getImageUrl($value['image_url'], imageThumbVersion::W150); ?>" class="verify-img">
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Source Type</label></td>
                            <td>
                                <?php echo $lang['cert_source_type_' . $passport_cert['source_type']]?>
                                <?php if ($passport_cert['creator_name']) { ?>
                                    <span>【<?php echo $passport_cert['creator_name']; ?>】</span>
                                <?php } ?>
                            </td>
                            <td><label class="control-label">Remark</label></td>
                            <td><?php echo $passport_cert['verify_remark']; ?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="basic-info">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Work</h5>
            </div>
            <div class="content">
                <table class="table audit-table">
                    <tbody class="table-body">
                    <?php if (!$output['work_cert']) { ?>
                        <tr>
                            <td colspan="4">Unverified</td>
                        </tr>
                    <?php } else { $work_cert = $output['work_cert']; ?>
                        <tr>
                            <td><label class="control-label">Images</label></td>
                            <td colspan="3">
                                <?php foreach ($work_cert['images'] as $value) { ?>
                                    <a target="_blank" href="<?php echo getImageUrl($value['image_url']); ?>">
                                        <img src="<?php echo getImageUrl($value['image_url'], imageThumbVersion::W150); ?>" class="verify-img">
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>

                        <tr>
                            <td><label class="control-label">Company Name</label></td>
                            <td><?php echo $work_cert['extend_info']['company_name']; ?></td>
                            <td><label class="control-label">Company Address</label></td>
                            <td><?php echo $work_cert['extend_info']['company_addr']; ?></td>
                        </tr>

                        <tr>
                            <td><label class="control-label">Government Employeee</label></td>
                            <td><?php echo $work_cert['extend_info']['is_government'] ? 'Yes' : 'No' ; ?></td>
                            <td><label class="control-label">Source Type</label></td>
                            <td>
                                <?php echo $lang['cert_source_type_' . $work_cert['source_type']]?>
                                <?php if ($work_cert['creator_name']) { ?>
                                    <span>【<?php echo $work_cert['creator_name']; ?>】</span>
                                <?php } ?>
                            </td>
                        </tr>

                        <tr>
                            <td><label class="control-label">Remark</label></td>
                            <td colspan="3"><?php echo $work_cert['verify_remark']; ?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>