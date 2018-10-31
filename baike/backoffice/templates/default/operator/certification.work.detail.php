<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
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
        margin-bottom:5px;
    }
</style>
<?php $extend_info = $output['extend_info']; $info = $output['info'];?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Verification</h3>
            <ul class="tab-base">
                <?php if($_GET['source'] == 'credit'){?>
                    <li>
                        <a href="<?php echo getUrl('operator', 'grantCredit', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                    </li>
                    <li><a class="current"><span>Detail</span></a></li>
                <?php } else { ?>
                    <li>
                        <a href="<?php echo getUrl('operator', 'certificationFile', array('type' => $info['cert_type']), false, BACK_OFFICE_SITE_URL) ?>"><span><?php echo $output['title'] . ' List'?></span></a>
                    </li>
                    <li><a class="current"><span>Detail</span></a></li>
                <?php }?>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php if(!$output['lock'] && $info['cert_type'] == certificationTypeEnum::ID){ ?>
            <input type="hidden" name="submit" id="submit" value="1">
        <?php }else{ ?>
            <input type="hidden" name="submit" id="submit" value="2">
        <?php } ?>
        <form class="demoform" id="validIDForm" action="<?php echo getUrl('operator', 'certificationConfirm', array(), false, BACK_OFFICE_SITE_URL) ?>" method="post">
            <input type="hidden" name="validate" value="" />
            <input type="hidden" name="uid" value="<?php echo $info['uid'];?>" />
            <table class="table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Member Name</label></td>
                    <td><?php echo trim($info['display_name'])? : $info['login_code']; ?></td>
                </tr>

                <?php if( $output['cert_sample_images'][$info['cert_type']] ){ ?>
                    <tr>
                        <td><label class="control-label">Sample</label></td>
                        <td>
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
                    <td>
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

                <tr>
                    <td>
                        <label for="" class="control-label">Company Name</label>
                    </td>
                    <td>
                        <?php echo $extend_info['company_name']; ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="" class="control-label">Company Address</label>
                    </td>
                    <td>
                        <?php echo $extend_info['company_addr']; ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="" class="control-label">Government Employee</label>
                    </td>
                    <td>
                        <?php echo $extend_info['is_government']?'Yes':'No'; ?>
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
                </tr>
                <tr>
                    <td><label class="control-label">Verify State</label></td>
                    <td><?php if($info['verify_state'] == -1){echo 'Auditing...';}elseif($info['verify_state'] == 0){echo 'Not Verified';}elseif($info['verify_state'] == 10){echo 'Passed';}else{echo 'No Pass';} ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Certification Type</label></td>
                    <td>
                        <?php echo $output['certification_type'][$info['cert_type']]; ?>
                    </td>
                </tr>
                <?php if (!$info['verify_state'] || $info['verify_state'] == -1) { ?>
                <tr>
                    <td><label class="control-label">Auditor Name</label></td>
                    <td><?php echo $info['auditor_name'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Auditor Time</label></td>
                    <td><?php echo timeFormat($info['auditor_time']) ?></td>
                </tr>
                <?php }?>
                <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                    <?php if(!$output['lock']){ ?>
                        <tr>
                            <td><label class="control-label">Verify State</label></td>
                            <td>
                                <label class="radio-inline">
                                    <input type="radio" name="verify_state" value="10" checked/> Pass
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="verify_state" value="100" /> Refuse
                                </label>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                <tr>
                    <td><label class="control-label">Remark</label></td>
                    <td>
                        <?php if(!$info['verify_state'] || $info['verify_state'] == -1){ ?>
                            <?php if(!$output['lock']){ ?>
                                <div class="cerification-form">
                                    <textarea name="remark" class="form-control"></textarea><span class="validate-checktip"></span>
                                </div>
                            <?php }else{ ?>
                                <?php echo $info['verify_remark'];?>
                            <?php } ?>
                        <?php }else{ ?>
                            <?php echo $info['verify_remark'];?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <?php if($output['lock']){?>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-danger" onclick="javascript:history.go(-1);"><i class="fa fa-vcard-o"></i>Back</button>
                            </div>
                        <?php }elseif($info['verify_state'] == 10){?>
                            <?php if($_GET['source'] == 'credit'){?>
                                <div class="custom-btn-group approval-btn-group">
                                    <button type="button" class="btn btn-danger" onclick="javascript:history.go(-1);"><i class="fa fa-vcard-o"></i>Back</button>
                                </div>
                            <?php } else {?>
                                <div class="custom-btn-group approval-btn-group">
                                    <button type="button" class="btn btn-danger" onclick="javascript:window.location.href = '<?php echo getUrl('operator', 'certificationFile', array('type' => $info['cert_type']), false, BACK_OFFICE_SITE_URL) ?>';"><i class="fa fa-vcard-o"></i>Back</button>
                                </div>
                            <?php } ?>

                        <?php }elseif($info['verify_state'] == 100){?>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-danger" onclick="javascript:window.location.href = '<?php echo getUrl('operator', 'certificationFile', array('type' => $info['cert_type']), false, BACK_OFFICE_SITE_URL) ?>';"><i class="fa fa-vcard-o"></i>Back</button>
                            </div>
                        <?php }else{?>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-primary" onclick="submitIDForm();"><i class="fa fa-check"></i>Submit</button>
                                <button type="button" class="btn btn-default" onclick="javascript:window.location.href = '<?php echo getUrl('operator', 'certificationFile', array('type' => $info['cert_type']), false, BACK_OFFICE_SITE_URL) ?>';"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <div class="cerification-history">
            <?php $history = $output['history'];$count = count($history);?>
            <div class="ibox-title">
                <h5>Cerification History</h5>
            </div>
            <div class="ibox-content" style="padding:0;">
                <?php if($count > 0){ ?>
                    <table class="table verify-table">
                        <thead>
                        <tr class="table-header">
<!--                            <td>--><?php //echo 'Member Name';?><!--</td>-->
                            <td  style="text-align: left;width: 300px;"><?php echo 'Images';?></td>
                            <td><?php echo 'Certification Name';?></td>
                            <td><?php echo 'Certification Sn';?></td>
                            <td><?php echo 'Verify State';?></td>
                            <td><?php echo 'Certification Type';?></td>
                            <td><?php echo 'Source Type';?></td>
                            <td><?php echo 'Auditor Name';?></td>
                            <td><?php echo 'Auditor Time';?></td>
                            <td><?php echo 'Remark';?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php foreach($history as $row){?>
                            <tr>
<!--                                <td>--><?php //echo $info['display_name'] ?><!--</td>-->
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
                                <td><?php echo $row['cert_name'] ?></td>
                                <td><?php echo $row['cert_sn'] ?></td>
                                <td><?php if($row['verify_state'] == 0){echo 'Not Verified';}elseif($row['verify_state'] == 10){echo 'Have Passed';}elseif($row['verify_state'] == 100){echo 'Refuse';}else{echo 'Audit...';} ?></td>
                                <td><?php if($row['cert_type'] == certificationTypeEnum::ID){echo 'ID';}elseif($row['cert_type'] == certificationTypeEnum::FAIMILYBOOK){echo 'Faimily Book';}elseif($row['cert_type'] == certificationTypeEnum::PASSPORT){echo 'Passport';}elseif($row['cert_type'] == certificationTypeEnum::HOUSE){echo 'Housing & Store';}elseif($row['cert_type'] == certificationTypeEnum::CAR){echo 'Car';} ?></td>
                                <td><?php if($row['source_type'] == 0){echo 'Self Submission';}else{echo 'Teller Submission';} ?></td>
                                <td><?php echo $row['auditor_name'] ?></td>
                                <td><?php echo timeFormat($row['auditor_time']) ?></td>
                                <td><?php echo $row['verify_remark'] ?></td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="no-record">
                        No Record
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/validform/jquery.validate.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js?v=20"></script>
<script>
    $(function () {
        $('#date').datepicker({
            format: 'yyyy-mm-dd'
        });
    });

    function submitIDForm(){
        $('#validIDForm').submit();
    }

    var submit = $('#submit').val();
    var validIDParam = {
        ele: '#validIDForm', //表单id
        params: [{
            field: 'remark',
            rules: {
                required: true
            },
            messages: {
                required: 'Please input remark'
            }
        }]
    };
    var validParam = {
        ele: '#validIDForm', //表单id
        params: [{
            field: 'remark',
            rules: {
                required: true
            },
            messages: {
                required: 'Please input remark'
            }
        }]
    };

    validform(validParam);
</script>
