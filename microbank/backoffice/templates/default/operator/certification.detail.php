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

    .cerification-form .n-label {
        width: 150px;
    }

    .verify-img {
        width: 200px;
        margin-right: 5px;
    }
</style>
<?php $info = $output['info'];
$IDInfo = $output['IDInfo']; ?>
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
        <?php if ($info['cert_type'] == certificationTypeEnum::ID) { ?>
            <input type="hidden" name="submit" id="submit" value="1">
        <?php } else { ?>
            <input type="hidden" name="submit" id="submit" value="2">
        <?php } ?>
        <form class="demoform" id="validIDForm" method="post" action="<?php echo getUrl('operator', 'certificationConfirm', array(), false, BACK_OFFICE_SITE_URL) ?>">
            <input type="hidden" name="uid" value="<?php echo $info['uid']; ?>"/>
            <table class="table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Member Name/Account</label></td>
                    <td><?php echo trim($info['display_name']) ?: $info['login_code']; ?></td>
                </tr>
                <?php if ($output['cert_sample_images'][$info['cert_type']]) { ?>
                    <tr>
                        <td><label class="control-label">Sample</label></td>
                        <td>

                            <?php foreach ($output['cert_sample_images'][$info['cert_type']] as $sample) { ?>
                                <div style="display:inline-block;width: 100px;text-align: center;margin-right: 5px;">
                                    <a target="_blank" href="<?php echo $sample['image']; ?>">
                                        <img src="<?php echo $sample['image']; ?>" style="width: 100px;height: 100px"/>
                                    </a>
                                </div>
                            <?php } ?>
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
                <?php if ($info['cert_type'] == certificationTypeEnum::ID) { ?>
                    <tr>
                        <td><label class="control-label">Certification Sn</label></td>
                        <td>
                            <?php echo $info['cert_sn']; ?>
                        </td>
                    </tr>
                    <?php $cert_name_json = my_json_decode($info['cert_name_json'])?>
                    <tr>
                        <td><label class="control-label">Certification Name(English)</label></td>
                        <td>
                            <?php echo $cert_name_json['en']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Certification Name(Khmer)</label></td>
                        <td>
                            <?php echo $cert_name_json['kh']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">English Name</label></td>
                        <td>
                            <?php if (!$info['verify_state'] || $info['verify_state'] == -1) { ?>
                                <div class="cerification-form clearfix" style="margin-top: 5px;">
                                    <span class="n-label">Family Name</span>
                                    <input type="text" class="form-control" name="en_family_name" value="">
                                    <span class="error_msg"></span>
                                </div>
                                <div class="cerification-form clearfix" style="margin-top: 5px;">
                                    <span class="n-label">Given Name</span>
                                    <input type="text" class="form-control" name="en_given_name" value="">
                                    <span class="error_msg"></span>
                                </div>
                            <?php } else { ?>
                                <?php echo implode(' ', my_json_decode($info['id_en_name_json'])); ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Khmer Name</label>
                        </td>
                        <td>
                            <?php if ($output['is_handle']) { ?>
                                <div class="cerification-form clearfix" style="margin-top: 5px;">
                                    <span class="n-label">Family Name</span>
                                    <input type="text" class="form-control" name="kh_family_name" value="">
                                    <span class="error_msg"></span>
                                </div>
                                <div class="cerification-form clearfix" style="margin-top: 5px;">
                                    <span class="n-label">Given Name</span>
                                    <input type="text" class="form-control" name="kh_given_name" value="">
                                    <span class="error_msg"></span>
                                </div>
                            <?php } else { ?>
                                <?php echo implode(' ', my_json_decode($info['id_kh_name_json'])); ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Certification Type</label></td>
                        <td>
                            <?php if ($output['is_handle']) { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="id_type" value="0" checked/> Homeland
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="id_type" value="1"/> Foreign Country
                                </label>
                            <?php } else { ?>
                                <?php echo $info['id_type'] == 1 ? "Foreign Country" : "Homeland"; ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Gender</label></td>
                        <td>
                            <?php if ($output['is_handle']) { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="gender" value="<?php echo memberGenderEnum::MALE; ?>" checked/> Male
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="gender" value="<?php echo memberGenderEnum::FEMALE; ?>"/> Female
                                </label>
                            <?php } else { ?>
                                <?php echo ucwords($info['gender']); ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Date of Birth</label></td>
                        <td>
                            <?php if ($output['is_handle']) { ?>
                                <div class="cerification-form">
                                    <input type="text" class="form-control" name="birthday" id="birthday">
                                    <span class="error_msg"></span>
                                </div>
                            <?php } else { ?>
                                <?php echo dateFormat($info['birthday']); ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Nationality</label></td>
                        <td>
                            <?php if ($output['is_handle']) { ?>
                                <div class="cerification-form">
<!--                                    <input type="text" class="form-control" name="nationality" value="">-->
                                    <select class="form-control" name="nationality" style="width: 300px">
                                        <?php foreach ($output['country_code'] as $key => $code) { ?>
                                            <option value="<?php echo $key; ?>"><?php echo $code; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="error_msg"></span>
                                </div>
                            <?php } else { ?>
                                <?php echo strtoupper($info['nationality']); ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Birth Address</label></td>
                        <td>
                            <?php if ($output['is_handle']) { ?>
                                <div class="cerification-form" id="select_area">
                                    <div id="select_area"></div>
                                    <input type="hidden" name="id_address1" id="id_address1" value="">
                                    <input type="hidden" name="id_address2" id="id_address2" value="">
                                    <input type="hidden" name="id_address3" id="id_address3" value="">
                                    <input type="hidden" name="id_address4" id="id_address4" value="">
                                    <span class="error_msg"></span>
                                </div>
                            <?php } else { ?>
                                <?php echo $info['cert_addr']; ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php if ($output['is_handle']) { ?>
                        <tr>
                            <td><label class="control-label"></label></td>
                            <td>
                                <div class="cerification-form">
                                    <input type="text" class="form-control" name="cert_addr" id="cert_addr" value="" placeholder="Detailed Address">
                                    <input type="hidden" name="cert_addr_detail" id="cert_addr_detail" value="">
                                    <span class="error_msg"></span>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td><label class="control-label">Expire Time</label></td>
                        <td>
                            <?php if ($output['is_handle']) { ?>
                                <div class="cerification-form">
                                    <input type="text" class="form-control" name="cert_expire_time" id="date">
                                    <span class="error_msg"></span>
                                </div>
                            <?php } else { ?>
                                <?php echo timeFormat($info['cert_expire_time']); ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } else { ?>
                    <?php if ($output['asset_info']) { ?>
                        <tr>
                            <td><label class="control-label">Asset Name</label></td>
                            <td>
                                <?php echo $output['asset_info']['asset_name'];?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Asset Id</label></td>
                            <td>
                                <?php echo $output['asset_info']['asset_sn'];?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                <tr>
                    <td><label class="control-label">Certification Type</label></td>
                    <td>
                        <?php echo $output['certification_type'][$info['cert_type']]; ?>
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
                <?php if ($output['asset_owner']) { ?>
                    <tr>
                        <td><label class="control-label">Owner</label></td>
                        <td>
                            <?php foreach ($output['asset_owner'] as $relative) { ?>
                                <span style="display: inline-block;margin: 0 15px 0 0">
                                <?php echo $relative['relative_name'];?>
                            </span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>

                <tr>
                    <td><label class="control-label">Verify State</label></td>
                    <td>
                        <?php echo $lang['cert_verify_state_' . $info['verify_state']]; ?>
                    </td>
                </tr>
                <?php if (!$output['is_handle']) { ?>
                <tr>
                    <td><label class="control-label">Auditor Name</label></td>
                    <td><?php echo $info['auditor_name'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Auditor Time</label></td>
                    <td><?php echo timeFormat($info['auditor_time']) ?></td>
                </tr>
                <?php }?>
                <?php if ($output['is_handle']) { ?>
                    <tr>
                        <td><label class="control-label">Verify State</label></td>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" name="verify_state" value="<?php echo certStateEnum::PASS?>" checked/> Approved
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="verify_state" value="<?php echo certStateEnum::NOT_PASS?>"/> Rejected
                            </label>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><label class="control-label">Remark</label></td>
                    <td>
                        <?php if ($output['is_handle']) { ?>
                            <div class="cerification-form">
                                <textarea name="remark" class="form-control"></textarea><span
                                    class="error_msg"></span>
                            </div>
                        <?php } else { ?>
                            <?php echo $info['verify_remark']; ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <?php if ($info['verify_state'] == 10) { ?>
                            <?php if($_GET['source'] == 'credit'){?>
                                <button type="button" class="btn btn-info" onclick="javascript:history.go(-1);">
                                    <i class="fa fa-vcard-o"></i>Back
                                </button>
                            <?php } else { ?>
                                <button type="button" class="btn btn-info" onclick="javascript:window.location.href = '<?php echo getUrl('operator', 'certificationFile', array('type' => $info['cert_type']), false, BACK_OFFICE_SITE_URL) ?>';">
                                    <i class="fa fa-vcard-o"></i>Back
                                </button>
                            <?php } ?>
                        <?php } elseif ($info['verify_state'] == 100) { ?>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-info" onclick="javascript:window.location.href = '<?php echo getUrl('operator', 'certificationFile', array('type' => $info['cert_type']), false, BACK_OFFICE_SITE_URL) ?>';">
                                    <i class="fa fa-vcard-o"></i>Back
                                </button>
                            </div>
                        <?php } else { ?>
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
                            <td><?php echo 'Member Name'; ?></td>
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
                            <td><?php echo 'Audit Time'; ?></td>
                            <td><?php echo 'Remark'; ?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php foreach ($history as $row) { ?>
                            <tr>
                                <td><?php echo $info['display_name'] ?></td>
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

                                <td>
                                    <?php echo $lang['cert_verify_state_' . $row['verify_state']]; ?>
                                </td>
                                <td>
                                    <?php if ($row['source_type'] == 0) {
                                        echo 'Self Submission';
                                    } else {
                                        echo 'Teller Submission';
                                    } ?>
                                </td>
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
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/validform/jquery.validate.min.js?v=2"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js?v=20"></script>
<script>

    $(function () {
        getArea(0);
        $('#birthday').datepicker({
            format: 'yyyy-mm-dd'
        });
        $('#date').datepicker({
            format: 'yyyy-mm-dd'
        });

        $('#validIDForm').validate({
            errorPlacement: function (error, element) {
                element.nextAll('.error_msg').first().html(error)
            },
            rules: {
                remark: {
                    required: true
                },
            },
            messages: {
                remark: {
                    required: '<?php echo 'Required'?>'
                }
            }
        });

        var submit = $('#submit').val();
        if (submit == 1) {
            addIdFormRules();
        }
    });

    

    $('#select_area').delegate('select', 'change', function () {
        var _value = $(this).val(), len = $('#select_area select').index(this) + 1;
        _value == 0 ? $('#id_address' + len).val('') : $('#id_address' + len).val(_value);
        len < 4 ? $('#id_address4').val('') : '';
        $('input[name="address_id"]').val(_value);
        $(this).closest('div').nextAll().remove();
        if (_value != 0 && $(this).find('option[value="' + _value + '"]').attr('is-leaf') != 1) {
            getArea(_value);
        }
    })

    function getArea(uid) {
        yo.dynamicTpl({
            tpl: "setting/area.list",
            dynamic: {
                api: "setting",
                method: "getAreaList",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $("#select_area").append(_tpl);
            }
        });
    }

    $("input[name='verify_state']").change(function(){
        $("#validIDForm").validate().resetForm();
        var submit = $('#submit').val();
        $(this).val() == 10 && submit == 1 ? addIdFormRules() : removeIdFormRules();
    });

    function submitIDForm() {
        if (!$("#validIDForm").valid()) {
            return;
        }
        var _address_region = [];
        $('#select_area select').each(function () {
            if ($(this).val() != 0) {
                _address_region.push($(this).find('option:selected').text());
            }
        })
        var _cert_addr = $.trim($('input[name="cert_addr"]').val());
        if (_cert_addr) {
            _address_region.push(_cert_addr);
        }
        var _address = _address_region.reverse().join(', ');
        $('#cert_addr_detail').val(_address);

        $('#validIDForm').submit();
    }

    //添加某个规则
    function addIdFormRules(){
        removeIdFormRules();
        $('input[name=en_family_name]').rules('add',{required:true,messages:{required:'Please input the english family name.'}});
        $('input[name=en_given_name]').rules('add',{required:true,messages:{required:'Please input the english given name.'}});
        $('input[name=kh_family_name]').rules('add',{required:true,messages:{required:'Please input the khmer family name.'}});
        $('input[name=kh_given_name]').rules('add',{required:true,messages:{required:'Please input the khmer given name.'}});
        $('input[name=birthday]').rules('add',{required:true,messages:{required:'Please input the birthday.'}});
        $('input[name=nationality]').rules('add',{required:true,messages:{required:'Please input the nationality.'}});
        $('input[name=id_address1]').rules('add',{required:true,messages:{required:'Please select the birth address.'}});
        $('input[name=cert_expire_time]').rules('add',{required:true,messages:{required:'Please select the certification expire time.'}});
    }
    //删除某个规则
    function removeIdFormRules(){
        $('input[name=en_family_name]').rules('remove');
        $('input[name=en_given_name]').rules('remove');
        $('input[name=kh_family_name]').rules('remove');
        $('input[name=kh_given_name]').rules('remove');
        $('input[name=birthday]').rules('remove');
        $('input[name=nationality]').rules('remove');
        $('input[name=id_address1]').rules('remove');
        $('input[name=cert_expire_time]').rules('remove');
    }
</script>
