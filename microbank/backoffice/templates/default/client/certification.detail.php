<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=10" rel="stylesheet" type="text/css"/>
<style>
    .custom-btn-group {
        float: inherit;
    }

    .cerification-history {
        margin-top: 20px;
    }

    .verify-table img {
        width: 80px;
    }

    .cerification-history .table .table-header {
        background: none;
    }

    .verify-img {
        width: 300px;
        margin-bottom: 5px;
    }

    #select_area .col-sm-6 {
        width: 200px;
        padding-left: 0;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($_GET['source_mark'] == 'grant_committee') { ?>
                <h3>Committee</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                    <li><a onclick="javascript:history.go(-1)"><span>Credit Grant</span></a></li>
                    <li><a class="current"><span>Certificate Info</span></a></li>
                </ul>
            <?php } elseif ($_GET['source_mark'] == 'fast_grant') { ?>
                <h3>Fast Grant Credit</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('loan_committee', 'fastGrantCredit', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Client List</span></a></li>
                    <li><a onclick="javascript:history.go(-1)"><span>Credit Grant</span></a></li>
                    <li><a class="current"><span>Certificate Info</span></a></li>
                </ul>
            <?php } elseif($_GET['source_mark'] == 'bm_suggest') { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Client List</span></a></li>
                    <li><a onclick="javascript:history.go(-1)"><span>Request Credit</span></a></li>
                    <li><a class="current"><span>Certificate Info</span></a></li>
                </ul>
            <?php } elseif ($_GET['source_mark'] == 'op_suggest') {?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Client List</span></a></li>
                    <li><a id="anchor_back" href="<?php echo getUrl('web_credit', 'creditClient', array("uid"=>$output['info']['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Client Detail</span></a></li>
                    <li><a class="current"><span>Certificate Info</span></a></li>
                </ul>
            <?php } elseif ($_GET['source_mark'] == 'client_detail') {?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('client', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                    <li><a onclick="javascript:history.go(-1)"><span>Detail</span></a></li>
                    <li><a class="current"><span>Certificate Info</span></a></li>
                </ul>
            <?php } elseif ($_GET['source_mark'] == 'tools_client_detail') {?>
                <h3>Search Client</h3>
                <ul class="tab-base">
                    <li><a onclick="javascript:history.go(-1)"><span>Search</span></a></li>
                    <li><a class="current"><span>Certificate Info</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>Verification</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('client', 'cerification', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                    <li><a class="current"><span>Detail</span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <div class="container">
        <?php $info = $output['info'];$client_info = $output['client_info']; ?>
        <table class="table">
            <tbody class="table-body">
            <tr>
                <td><label class="control-label">Member Name</label></td>
                <td colspan="3"><?php echo $client_info['display_name']?:$client_info['login_code']; ?></td>
            </tr>
            <?php if( $output['cert_sample_images'][$info['cert_type']] ){ ?>
                <tr>
                    <td><label class="control-label">Sample</label></td>
                    <td colspan="3">
                        <?php foreach( $output['cert_sample_images'][$info['cert_type']] as $sample ){  ?>
                            <div style="display:inline-block;width: 200px;text-align: center;margin-right: 5px;">
                                <a target="_blank" href="<?php echo $sample['image']; ?>">
                                    <img src="<?php echo $sample['image']; ?>" alt="" style="width: 200px;height: 200px" />
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
                    $viewer_width = 460;
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

            <?php if ($info['cert_type'] == certificationTypeEnum::ID) { ?>
                <tr>
                    <td><label class="control-label">English Name</label></td>
                    <td>
                        <?php echo implode(' ', my_json_decode($client_info['id_en_name_json'])); ?>
                    </td>
                    <td>
                        <label class="control-label">Khmer Name</label>
                    </td>
                    <td>
                        <?php echo implode(' ', my_json_decode($client_info['id_kh_name_json'])); ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Certification Sn</label></td>
                    <td>
                        <?php echo $client_info['id_sn']; ?>
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
                        <?php echo dateFormat($info['cert_expire_time']); ?>
                    </td>

                </tr>
                <tr>
                    <td><label class="control-label">Birth Address</label></td>
                    <td colspan="3">
                        <?php echo $client_info['address_detail']; ?>
                    </td>
                </tr>
            <?php } else { ?>
                <?php if ($output['asset_info']) { ?>
                    <tr>
                        <td><label class="control-label">Asset Name</label></td>
                        <td>
                            <?php echo $output['asset_info']['asset_name'];?>
                        </td>
                        <td><label class="control-label">Asset Id</label></td>
                        <td>
                            <?php echo $output['asset_info']['asset_sn'];?>
                        </td>
                    </tr>
                <?php } ?>
            <?php }?>
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
            <?php if ($info['verify_state'] || $info['verify_state'] == -1) { ?>
            <tr>
                <td><label class="control-label">Auditor Name</label></td>
                <td><?php echo $info['auditor_name'] ?></td>
                <td><label class="control-label">Auditor Time</label></td>
                <td><?php echo timeFormat($info['auditor_time']) ?></td>
            </tr>
            <?php }?>
            <?php if ($output['asset_owner']) { ?>
                <tr>
                    <td><label class="control-label">Type</label></td>
                    <td>
                        <?php echo $output['verify_field'][$info['cert_type']]; ?>
                    </td>
                    <td><label class="control-label">Owner</label></td>
                    <td>
                        <?php foreach ($output['asset_owner'] as $relative) { ?>
                            <span style="display: inline-block;margin: 0 15px 0 0">
                            <?php echo $relative['relative_name'];?>
                        </span>
                        <?php } ?>
                    </td>
                </tr>
                <?php if (trim($info['verify_remark'])) { ?>
                    <tr>
                        <td><label class="control-label">Remark</label></td>
                        <td colspan="3">
                            <?php echo trim($info['verify_remark']); ?>
                        </td>
                    </tr>
                <?php }?>
            <?php } else { ?>
                <tr>
                    <td><label class="control-label">Type</label></td>
                    <td>
                        <?php echo $output['verify_field'][$info['cert_type']]; ?>
                    </td>
                    <td><label class="control-label">Remark</label></td>
                    <td>
                        <?php echo trim($info['verify_remark']); ?>
                    </td>
                    </tr>
            <?php } ?>

            <tr>
                <td colspan="4" style="text-align: center">
                    <div class="custom-btn-group approval-btn-group">
                        <?php if ($_GET['source_mark'] == 'op_suggest') {?>
                            <?php
                                $info_uid = $output['info']['uid'];
                                $cert_type = $output['info']['cert_type'];
                            ?>
                            <?php include(template('widget/certification.expired'));?>
                        <?php } ?>
                        <button type="button" class="btn btn-normal" onclick="javascript:history.go(-1);"><i
                                class="fa fa-vcard-o"></i>Back
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="cerification-history">
            <?php $history = $output['history'];
            $count = count($history); ?>
            <div class="ibox-title">
                <h5>Certification History</h5>
            </div>
            <div class="ibox-content" style="padding:0;">
                <?php if ($count > 0) { ?>
                    <table class="table verify-table">
                        <thead>
                        <tr class="table-header">
                            <td style="text-align: left;width: 300px;"><?php echo 'Images'; ?></td>
                            <?php if ($output['asset_info']) { ?>
                                <td><?php echo 'Asset Name'; ?></td>
                                <td><?php echo 'Asset Id'; ?></td>
                            <?php } else { ?>
                                <td><?php echo 'Certification Name'; ?></td>
                                <td><?php echo 'Certification Sn'; ?></td>
                            <?php } ?>
                            <td><?php echo 'Verify State'; ?></td>
                            <td><?php echo 'Source Type'; ?></td>
                            <td><?php echo 'Auditor Name'; ?></td>
                            <td><?php echo 'Auditor Time'; ?></td>
                            <td><?php echo 'Remark'; ?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php foreach ($history as $row) { ?>
                            <tr>
                                <td>
                                    <?php
                                    $image_list=array();
                                    foreach($row['cert_images'] as $img_item){
                                        $image_list[] = array(
                                            'url' => $img_item['image_url'],
                                            'image_source' => $img_item['image_source'],
                                        );
                                    }
                                    include(template(":widget/item.image.viewer.list"));
                                    ?>
                                </td>
                                <?php if ($output['asset_info']) { ?>
                                    <td><?php echo $row['asset_name'] ?></td>
                                    <td><?php echo $row['asset_sn'] ?></td>
                                <?php } else { ?>
                                    <td><?php echo $row['cert_name'] ?></td>
                                    <td><?php echo $row['cert_sn'] ?></td>
                                <?php } ?>
                                <td><?php echo $lang['cert_state_' . $row['verify_state']];?></td>
                                <td><?php if ($row['source_type'] == 0) {
                                        echo 'Self Submission';
                                    } else {
                                        echo 'Teller Submission';
                                    } ?></td>
                                <td><?php echo $row['auditor_name'] ?></td>
                                <td><?php echo timeFormat($row['auditor_time']) ?></td>
                                <td><?php echo $row['verify_remark'] ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="no-record">
                        No Record
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>


