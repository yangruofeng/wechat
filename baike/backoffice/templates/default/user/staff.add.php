<?php $staff_info = $output['staff_info'] ?: $_GET; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>User</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'staff', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span><?php echo $staff_info['uid'] ? 'Edit' : 'Add'; ?></span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;padding-top: 20px">
        <form id="form_staff" class="form-horizontal" method="post">
            <input type="hidden" name="uid" value="<?php echo $staff_info['uid']; ?>">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'First Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="first_name" placeholder="" value="<?php echo $staff_info['first_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Last Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="last_name" placeholder="" value="<?php echo $staff_info['last_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Staff Address'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="staff_address" placeholder="" value="<?php echo $staff_info['staff_address']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Staff Icon'?></label>
                <div class="col-sm-9">
                    <div class="image-uploader-item">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <img id="show_staff_icon"
                                     style="display: <?php echo $staff_info['staff_icon'] ? 'block' : 'none'?>;width: 100px;height: 100px;"
                                     src="<?php echo getImageUrl($staff_info['staff_icon'], null, null); ?>">
                            </li>
                            <li class="list-group-item">
                                <button type="button" id="staff_icon">Upload</button>
                                <input name="staff_icon" type="hidden" value="<?php echo $staff_info['staff_icon']; ?>">
                            </li>
                        </ul>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'User Account'?></label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="text" class="form-control" name="um_account" value="<?php echo $staff_info['um_account']?>">
                        <span class="input-group-addon" style="padding: 0;border: 0;">
                            <a class="btn btn-default" style="width: 80px;height: 34px;border-radius: 0" onclick="settingAccount(this)">Setting</a>
                        </span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Phone'?></label>
                <div class="col-sm-9">
                    <div class="input-group">
                         <span class="input-group-addon" style="padding: 0;border: 0;">
                            <select class="form-control" name="country_code" style="min-width: 100px;border-right: 0">
                                <?php print_r(tools::getCountryCodeOptions($staff_info['country_code'] ?: ''));?>
                            </select>
                         </span>
                         <input type="number" class="form-control" name="phone_number" value="<?php echo $staff_info['phone_number']?>">
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Branch'?></label>
                <div class="col-sm-9">
                    <div class="col-sm-6" style="padding-left: 0;padding-right: 10px">
                        <select id="branch_id" name="branch_id" class="form-control" onchange="selectBranch(this)">
                            <option value="" selected="selected">Select Branch</option>
                            <?php foreach($output['branch_list'] as $branch){?>
                                <option value="<?php echo $branch['uid']?>"><?php echo $branch['branch_name']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-sm-6" style="padding-left: 10px;padding-right: 0">
                        <select id="depart_id" name="depart_id" class="form-control" disabled>
                            <option value="" selected="selected">Select Department</option>
                        </select>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Position'?></label>
                <div class="col-sm-9">
                    <?php foreach ($output['user_position'] as $key => $val) { ?>
                        <label class="col-sm-5 radio-inline" style="margin-left: 0">
                            <input type="radio" name="staff_position" value="<?php echo $key?>" <?php echo $key == $staff_info['staff_position'] ? 'checked' : ''?>>
                            <?php echo ucwords(strtolower($val))?>
                        </label>
                    <?php }?>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Entry Time'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="entry_time" placeholder="" value="<?php echo $staff_info['entry_time']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Remark'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="remark" placeholder="" value="<?php echo $staff_info['remark']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group col-sm-12">
                <div style="text-align: center">
                    <button type="button" id="submit_save" class="btn btn-danger"><i class="fa fa-check"></i> <?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back'?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script>
    $(function () {
        $('[name="entry_time"]').datepicker({
            format: 'yyyy-mm-dd'
        });

        var _depart_id = '<?php echo $staff_info['depart_id'];?>';
        var _branch_id = '<?php echo $staff_info['branch_id'];?>';
        if (_depart_id > 0) {
            changeBranch(_branch_id, _depart_id);
        }

        $('#submit_save').click(function () {
            if (!$("#form_staff").valid()) {
                return;
            }

            $('#form_staff').submit();
        })
    })

    function selectBranch(_e) {
        var _branch_id = $(_e).val();
        changeBranch(_branch_id, 0);
    }

    function changeBranch(_branch_id, _depart_id) {
        $('#branch_id').val(_branch_id);
        var depart_html = '<option value="" selected="selected">Select Department</option>';
        $('#depart_id').html(depart_html).attr('disabled', true);
        if(_branch_id == 0){
            return;
        }
        yo.dynamicTpl({
            tpl: "user/depart.option",
            dynamic: {
                api: "user",
                method: "getDepartList",
                param: {branch_id: _branch_id, depart_id: _depart_id}
            },
            callback: function (_tpl) {
                $('#depart_id').html(_tpl).attr('disabled', false);
            }
        });
    }

    function settingAccount(_e) {
        var _user_account = $.trim($('input[name="um_account"]').val());
        var _uid = '<?php echo $staff_info['uid']?>';
        if (!_user_account) {
            return;
        }
        $('business-content').waiting();
        yo.loadData({
            _c: 'user',
            _m: 'settingAccount',
            param: {user_code: _user_account, staff_id: _uid},
            callback: function (_o) {
                $('business-content').unmask();
                if (_o.STS) {
                    var data = _o.DATA;
                    if (data.country_code) $('input[name="country_code"]').val(data.country_code);
                    if (data.phone_number) $('input[name="phone_number"]').val(data.phone_number);
                    if (data.user_position) $('input[name="staff_position"][value="' + data.user_position + '"]').attr('checked', true);
                    if (data.depart_id) {
                        changeBranch(data.branch_id, data.depart_id);
                    }
                    $(_e).closest('.form-group').find('.error_msg').text('');
                } else {
                    $(_e).closest('.form-group').find('.error_msg').text(_o.MSG);
                }
            }
        })
    }

    $('#form_staff').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            staff_address: {
                required: true
            },
            staff_icon: {
                required: true
            },
            phone_number: {
                required: true
            },
            branch_id: {
                required: true
            },
            depart_id: {
                required: true
            },
            staff_position: {
                required: true
            },
            entry_time: {
                required: true
            }
        },
        messages: {
            first_name: {
                required: '<?php echo 'Required'?>'
            },
            last_name: {
                required: '<?php echo 'Required'?>'
            },
            staff_address: {
                required: '<?php echo 'Required'?>'
            },
            staff_icon: {
                required: '<?php echo 'Required'?>'
            },
            phone_number: {
                required: '<?php echo 'Required'?>'
            },
            branch_id: {
                required: '<?php echo 'Required'?>'
            },
            depart_id: {
                required: '<?php echo 'Required'?>'
            },
            staff_position: {
                required: '<?php echo 'Required'?>'
            },
            entry_time: {
                required: '<?php echo 'Required'?>'
            }
        }
    });
</script>
<?php require_once template(':widget/inc_upload_upyun');?>
<script>
    var _file_dir = '<?php echo fileDirsEnum::STAFF_ICON; ?>';
    webuploader2upyun('staff_icon', _file_dir);
</script>