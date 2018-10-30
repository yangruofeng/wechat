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
$staff_info = $output['staff_info'];
$identity_type = $output['identity_type'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Staff</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'staff', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('user', 'showStaffInfo', array('uid'=>$output['staff_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Staff Detail</span></a></li>
                <li><a  class="current"><span>Add <?php echo $output['title']?></span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 1200px">
        <div class="business-condition">
            <?php require_once template("widget/item.staff.summary"); ?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 20px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Add Item</h5>
                </div>
                <div class="content">
                    <form id="identity_form" class="form-horizontal" method="POST" action="<?php echo getUrl('user', 'saveStaffIdentity', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="staff_id" value="<?php echo $staff_info['uid']; ?>">
                        <input type="hidden" name="cert_type" value="<?php echo $identity_type; ?>">
                        <?php if ($output['identity_type'] == certificationTypeEnum::ID) { ?>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Id Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="id_number" value="">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Expire Date</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="expire_date" value="">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Gender</label>
                                    <div class="col-sm-8">
                                        <label class="radio-inline">
                                            <input type="radio" name="gender" value="male" checked>Male
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="gender" value="female">Female
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Marital Status</label>
                                    <div class="col-sm-8">
                                        <label class="radio-inline">
                                            <input type="radio" name="civil_status" value="married" checked>Married
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="civil_status" value="single">Single
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="civil_status" value="divorce">Divorce
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Date Of Birth</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="birthday" value="">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Birth Country</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="birth_country">
                                            <?php foreach ($output['country_code'] as $key => $code) { ?>
                                                <option value="<?php echo $key; ?>"><?php echo $code; ?></option>
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
                                        <input type="text" class="form-control" name="address" value="">
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
                                        <input type="text" class="form-control" name="kh_family_name" value="">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Given Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="kh_given_name" value="">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Second Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="kh_second_name" value="">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Third Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="kh_third_name" value="">
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
                                        <input type="text" class="form-control" name="en_family_name" value="">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Given Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="en_given_name" value="">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Second Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="en_second_name" value="">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span>Third Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="en_third_name" value="">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                        <table class="table">
                            <?php foreach($output['image_structure'] as $item){?>
                                <tr>
                                    <td style="text-align: right"><?php echo $item['des']?></td>
                                    <td>Sample</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right" class="td-key-file">
                                        <div class="image-uploader-item">
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    <img id="show_<?php echo $item['file_key']?>" style="display: none;width: 100px;height: 100px;margin-bottom: 10px" >
                                                </li>
                                                <li class="list-group-item">
                                                    <button type="button" id="<?php echo $item['file_key']?>">Upload</button>
                                                    <input name="<?php echo $item['file_key']?>" type="hidden" value="">
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

                } else if (lev == 2) {
                    $('select[name="birth_district"]').html(_tpl).attr('disabled', false);
                } else if (lev == 3) {
                    $('select[name="birth_commune"]').html(_tpl).attr('disabled', false);
                } else {
                    $('select[name="birth_village"]').html(_tpl).attr('disabled', false);
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
        webuploader2upyun('<?php echo $item['file_key']?>', '<?php echo $output['file_dir']; ?>');
    <?php }?>
</script>
<!--图片上传 end-->






