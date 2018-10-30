<style>
    .btn {
        border-radius: 0;
    }

    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

    .ibox-title {
        padding-top: 12px!important;
        min-height: 40px;
    }

</style>
<?php
$client_info = $output['client_info'];
$cert_info = $output['cert_info'];
$id_kh_name_arr = json_decode($client_info['id_kh_name_json'], true);
$id_en_name_arr = json_decode($client_info['id_en_name_json'], true);
$image_version = $image_version?:imageThumbVersion::SMALL_IMG;
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Edit <?php echo $output['title']?></span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Add <?php echo $output['title']?></span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 1200px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 20px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Edit Item</h5>
                </div>
                <div class="content">
                    <input type="hidden" name="id_address1" value="<?php echo $client_info['id_address1']?>">
                    <input type="hidden" name="id_address2" value="<?php echo $client_info['id_address2']?>">
                    <input type="hidden" name="id_address3" value="<?php echo $client_info['id_address3']?>">
                    <input type="hidden" name="id_address4" value="<?php echo $client_info['id_address4']?>">

                    <form id="identity_form" class="form-horizontal" method="POST" action="<?php echo getUrl('web_credit', 'submitClientNewIdentity', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="client_id" value="<?php echo $client_info['uid']?>">
                        <input type="hidden" name="cert_id" value="<?php echo $cert_info['uid']?>">
                        <input type="hidden" name="cert_type" value="<?php echo $output['identity_type']?>">
                        <input type="hidden" name="edit" value="1">
                        <?php if ($output['identity_type'] == certificationTypeEnum::ID) { ?>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Id Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="id_number" value="<?php echo $cert_info['cert_sn'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Expire Date</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="expire_date" value="<?php echo $cert_info['cert_expire_time'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Gender</label>
                                    <div class="col-sm-8">
                                        <label class="radio-inline">
                                            <input type="radio" name="gender" value="<?php echo memberGenderEnum::MALE; ?>" <?php echo $client_info['gender']==memberGenderEnum::MALE?'checked':''; ?> >Male
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="gender" value="<?php echo memberGenderEnum::FEMALE; ?>" <?php echo $client_info['gender']==memberGenderEnum::FEMALE?'checked':''; ?> >Female
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Marital Status</label>
                                    <div class="col-sm-8">
                                        <label class="radio-inline">
                                            <input type="radio" name="civil_status" value="<?php echo memberMaritalStatusEnum::MARRIED; ?>" <?php echo $client_info['civil_status']==memberMaritalStatusEnum::MARRIED?'checked':''; ?>  >Married
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="civil_status" value="<?php echo memberMaritalStatusEnum::SINGLE; ?>" <?php echo $client_info['civil_status']==memberMaritalStatusEnum::SINGLE?'checked':''; ?> >Single
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="civil_status" value="<?php echo memberMaritalStatusEnum::Divorce; ?>" <?php echo $client_info['civil_status']==memberMaritalStatusEnum::Divorce?'checked':''; ?> >Divorce
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Date Of Birth</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="birthday" value="<?php echo $client_info['birthday'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Birth Country</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="birth_country">
                                            <?php foreach ($output['country_code'] as $key => $code) { ?>
                                                <option value="<?php echo $key; ?>" <?php if($client_info['nationality'] == $key){echo 'selected';}?>><?php echo $code; ?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Birth Province</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="birth_province" disabled>
                                            <option value="0">Please Select</option>
                                        </select>
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Birth District</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="birth_district" disabled>
                                            <option>Please Select</option>
                                        </select>
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Birth Commune</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="birth_commune" disabled>
                                            <option>Please Select</option>
                                        </select>
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Birth Village</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="birth_village" disabled>
                                            <option>Please Select</option>
                                        </select>
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Address</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="address" value="<?php echo $client_info['address'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label" style="color: #c49e35;text-align: left;font-size: 14px"><span class="required-options-xing"></span>Khmer Name</label>
                                    <div class="col-sm-8" style="height: 30px">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Family Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="kh_family_name" value="<?php echo $id_kh_name_arr['kh_family_name'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Given Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="kh_given_name" value="<?php echo $id_kh_name_arr['kh_given_name'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Second Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="kh_second_name" value="<?php echo $id_kh_name_arr['kh_second_name'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Third Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="kh_third_name" value="<?php echo $id_kh_name_arr['kh_third_name'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label" style="color: #c49e35;text-align: left;font-size: 14px"><span class="required-options-xing"></span>English Name</label>
                                    <div class="col-sm-8" style="height: 30px">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Family Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="en_family_name" value="<?php echo $id_en_name_arr['en_family_name'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Given Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="en_given_name" value="<?php echo $id_en_name_arr['en_given_name'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Second Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="en_second_name" value="<?php echo $id_en_name_arr['en_second_name'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Third Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="en_third_name" value="<?php echo $id_en_name_arr['en_third_name'];?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                            </div>

                        <?php }?>
                        <table class="table">
                            <?php foreach ($output['image_structure'] as $k => $item) { $file_key = $item['file_key']; ?>
                                <tr>
                                    <td style="text-align: right"><?php echo $item['des']?></td>
                                    <td>Sample</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right" class="td-key-file">
                                        <div class="image-uploader-item">
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    <img id="show_<?php echo $file_key; ?>" style="width: 100px;height: 100px;margin-bottom: 10px" src="<?php echo getImageUrl($cert_info['cert_images'][$file_key]['image_url'],$image_version);?>">
                                                </li>
                                                <li class="list-group-item">
                                                    <button type="button" id="<?php echo $file_key; ?>">Upload</button>
                                                    <input name="<?php echo $file_key; ?>" type="hidden" value="<?php echo $cert_info['cert_images'][$file_key]['image_url'];?>">
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td style="vertical-align: top">
                                        <a target="_blank" href="<?php echo $item['image']?>">
                                            <img style="width: 100px;height: 100px" src="<?php echo $item['image']?>">
                                        </a>
                                    </td>
                                </tr>
                            <?php }?>
                        </table>
                        <div class="col-sm-12 form-group" style="text-align: center;margin-top: 20px">
                            <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                            <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_submit_onclick() {
        if (!$("#identity_form").valid()) {
            return;
        }
        <?php foreach($output['image_structure'] as $item){?>
        var img_name = '<?php echo $item['file_key']?>';
        var img_url = $('input[name="' + img_name + '"]').val();
        if (!img_url) {
            alert('Please upload the images.');
            return;
        }
        <?php }?>
        $("#identity_form").waiting();
        $("#identity_form").submit();
    }

    $(function () {
        getArea(0, 1);
        $('[name="birthday"]').datepicker({
            format: 'yyyy-mm-dd'
        });
        $('[name="expire_date"]').datepicker({
            format: 'yyyy-mm-dd'
        });
        $('select[name="birth_province"]').change(function () {
            var val = $(this).val();
            getArea(val, 2);
        });
        $('select[name="birth_district"]').change(function () {
            var val = $(this).val();
            getArea(val, 3);
        });
        $('select[name="birth_commune"]').change(function () {
            var val = $(this).val();
            getArea(val,4);
        });
    })

    function getArea(uid, lev) {
        var _option = '<option>Please Select</option>'
        if (lev == 1) {
            $('select[name="birth_province"]').html(_option).attr('disabled', true);
            $('select[name="birth_district"]').html(_option).attr('disabled', true);
            $('select[name="birth_commune"]').html(_option).attr('disabled', true);
            $('select[name="birth_village"]').html(_option).attr('disabled', true);
        } else if (lev == 2) {
            $('select[name="birth_district"]').html(_option).attr('disabled', true);
            $('select[name="birth_commune"]').html(_option).attr('disabled', true);
            $('select[name="birth_village"]').html(_option).attr('disabled', true);
        } else if (lev == 3) {
            $('select[name="birth_commune"]').html(_option).attr('disabled', true);
            $('select[name="birth_village"]').html(_option).attr('disabled', true);
        } else {
            $('select[name="birth_village"]').html(_option).attr('disabled', true);
        }
        yo.dynamicTpl({
            tpl: "setting/area.list",
            dynamic: {
                api: "setting",
                method: "getAreaList",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                if (lev == 1) {
                    $('select[name="birth_province"]').html(_tpl).attr('disabled', false);
                    $('select[name="birth_province"]').val($('input[name=id_address1]').val());
                    getArea($('input[name=id_address1]').val(), 2);
                } else if (lev == 2) {
                    $('select[name="birth_district"]').html(_tpl).attr('disabled', false);
                    $('select[name="birth_district"]').val($('input[name=id_address2]').val());
                    getArea($('input[name=id_address2]').val(), 3);
                } else if (lev == 3) {
                    $('select[name="birth_commune"]').html(_tpl).attr('disabled', false);
                    $('select[name="birth_commune"]').val($('input[name=id_address3]').val());
                    getArea($('input[name=id_address3]').val(), 4);
                } else {
                    $('select[name="birth_village"]').html(_tpl).attr('disabled', false);
                    $('select[name="birth_village"]').val($('input[name=id_address4]').val());
                }
            }
        })
    }

    $('#identity_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules : {
            id_number : {
                required : true
            },
            expire_date : {
                required : true
            },
            birthday : {
                required : true
            },
            birth_country : {
                required : true
            },
            birth_province : {
                chkSelect : true
            }
        },
        messages : {
            id_number : {
                required : 'Required'
            },
            expire_date : {
                required : 'Required'
            },
            birthday : {
                required : 'Required'
            },
            birth_country : {
                required : 'Required'
            },
            birth_province : {
                chkSelect : 'Required'
            }
        }
    });

    jQuery.validator.addMethod("chkSelect", function (value, element) {
        if (value > 0) {
            return true;
        } else {
            return false;
        }
    });

</script>
<!--图片上传 start-->
<?php require_once template(':widget/inc_upload_upyun');?>
<script type="text/javascript">
    <?php foreach($output['image_structure'] as $item){?>
        webuploader2upyun('<?php echo $item['file_key']?>', '<?php echo fileDirsEnum::ID?>');
    <?php }?>
</script>
<!--图片上传 end-->






