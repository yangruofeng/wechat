<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <?php $image_structure = $output['image_structure']; ?>
    <div class="container">
        <div class="authenticate-div">
            <?php if ($output['cert_type'] == certificationTypeEnum::ID) { ?>
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i><?php echo $output['title']?></h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="basic-info">
                        <input type="hidden" name="client_id" value="<?php echo $output['client_info']['uid']; ?>">
                        <input type="hidden" name="cert_type" value="<?php echo $output['cert_type']; ?>">
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
                                <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Birth District</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="birth_district" disabled>
                                        <option>Please Select</option>
                                    </select>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Birth Commune</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="birth_commune" disabled>
                                        <option>Please Select</option>
                                    </select>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Birth Village</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="birth_village" disabled>
                                        <option>Please Select</option>
                                    </select>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Address</label>
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
                        <?php foreach ($image_structure as $structure) { ?>
                            <input type="hidden" name="<?php echo $structure['key']?>" value="">
                        <?php }?>
                    </form>
                </div>
            </div>
            <?php } else { ?>
                <form class="form-horizontal" id="basic-info" style="display: none">
                    <input type="hidden" name="client_id" value="<?php echo $output['client_info']['uid']; ?>">
                    <input type="hidden" name="cert_type" value="<?php echo $output['cert_type']; ?>">
                    <?php foreach ($image_structure as $structure) { ?>
                        <input type="hidden" name="<?php echo $structure['key']?>" value="">
                    <?php }?>
                </form>
            <?php } ?>
            <div class="scene-photo">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i><?php echo $output['cert_type'] == certificationTypeEnum::ID ? 'Scene Photo' : $output['title']?></h5>
                </div>
                <div class="content">
                    <?php foreach ($image_structure as $structure) { ?>
                        <?php if (strpos($structure['key'], 'handheld') === false) { ?>
                            <div class="snapshot_div" id="<?php echo $structure['key']?>" onclick="callWin_snapshot_master('<?php echo $structure['key']?>');">
                                <img src="<?php echo $structure['image']?>">
                                <div><?php echo $structure['des']?></div>
                            </div>
                        <?php } else { ?>
                            <div class="snapshot_div" id="<?php echo $structure['key']?>" onclick="callWin_snapshot_slave('<?php echo $structure['key']?>');">
                                <img src="<?php echo $structure['image']?>">
                                <div><?php echo $structure['des']?></div>
                            </div>
                        <?php }?>
                    <?php } ?>
                    <div class="snapshot_msg error_msg">
                        <div class="handheld_img"></div>
                        <div class="frontal_img"></div>
                        <div class="back_img"></div>
                    </div>
                </div>
            </div>
            <div class="operation">
                <a class="btn btn-default" href="<?php echo getUrl('member', 'documentCollection', array('client_id' => $output['client_info']['uid']), false, ENTRY_COUNTER_SITE_URL); ?>">Back</a>
                <button class="btn btn-primary" onclick="submit_form()">Submit</button>
            </div>
        </div>
    </div>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script>

    function callWin_snapshot_slave(type) {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $("#" + type + " img").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('input[name="' + type + '"]').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);
            }
        }
    }

    function callWin_snapshot_master(type){
        if(window.external){
            try{
                var _img_path= window.external.getSnapshot("0");
                if(_img_path!="" && _img_path!=null){
                    $("#" + type + " img").attr("src",getUPyunImgUrl(_img_path, "180x120"));
                    $('input[name="' + type + '"]').val(_img_path);
                }
            }catch (ex){
                alert(ex.Message);
            }
        }
    }

    <?php if($output['cert_type'] == certificationTypeEnum::ID){?>
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
            tpl: "member/area.list",
            control: "counter_base",
            dynamic: {
                api: "member",
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

    $('#basic-info').validate({
        errorPlacement: function(error, element){
            var name = $(element).attr('name');
            if (name == 'handheld_img' || name == 'frontal_img' || name == 'back_img') {
                error.appendTo($('.snapshot_msg .' + name));
            } else {
                error.appendTo(element.closest('.form-group').find('.error_msg'));
            }
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
            },
            birth_district : {
                chkSelect : true
            },
            birth_commune : {
                chkSelect : true
            },
            birth_village : {
                chkSelect : true
            },
            address : {
                required : true
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
            },
            birth_district : {
                chkSelect : 'Required'
            },
            birth_commune : {
                chkSelect : 'Required'
            },
            birth_village : {
                chkSelect : 'Required'
            },
            address : {
                required : 'Required'
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
    <?php }?>


    function submit_form() {
        <?php if($output['cert_type'] == certificationTypeEnum::ID) {?>
            if (!$("#basic-info").valid()) {
                return;
            }
        <?php }?>

        var values = $('#basic-info').getValues();
        yo.loadData({
            _c: 'member',
            _m: 'saveMemberDocument',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(window.location.href = _o.DATA.url, 2000);
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>