<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .auth-list .list-group-item {
        border-radius: 0px;
        font-size: 14px;
        padding: 5px 15px;
    }

    .auth-list .auth_group {
        margin-bottom: 10px;
    }

    .btn {
        min-width: 80px;
    }

    .radio-inline, .checkbox-inline {
        margin-left: 0px!important;
    }

    .auth-list .list-group-item label {
        width: 100%;
        padding-top: 0px;
        overflow: hidden;
        text-overflow:ellipsis;
        white-space: nowrap;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>User</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'user', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
<!--                <li><a href="--><?php //echo getUrl('user', 'addUser', array(), false, BACK_OFFICE_SITE_URL) ?><!--"><span>Add</span></a></li>-->
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['user_info']['uid']?>">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Code'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="user_code" placeholder="" value="<?php echo $output['user_info']['user_code']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="user_name" placeholder="" value="<?php echo $output['user_info']['user_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Password'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="password" value="" placeholder="Empty don\'t modify">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Phone'?></label>
                <div class="col-sm-9">
                    <div class="input-group">
                         <span class="input-group-addon" style="padding: 0;border: 0;">
                            <select class="form-control valid" name="country_code" style="min-width: 100px;border-right: 0">
                                <option value="855" <?php echo $output['phone_arr'][0] == 855 ? 'checked' : ''; ?>>+855</option>
                                <option value="66" <?php echo $output['phone_arr'][0] == 66 ? 'checked' : ''; ?>>+66</option>
                                <option value="86" <?php echo $output['phone_arr'][0] == 86 ? 'checked' : ''; ?>>+86</option>
                            </select>
                         </span>
                        <input type="number" class="form-control" name="phone" value="<?php echo $output['phone_arr'][1]; ?>">
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Department'?></label>
                <div class="col-sm-9">
                    <div class="col-sm-6" style="padding-left: 0;padding-right: 10px">
                        <select name="branch_id" class="form-control">
                            <option value="0">Select Branch</option>
                            <?php foreach($output['branch_list'] as $branch){?>
                                <option value="<?php echo $branch['uid']?>" <?php echo $output['user_info']['branch_id'] == $branch['uid']?'selected':''?>><?php echo $branch['branch_name']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-sm-6" style="padding-left: 10px;padding-right: 0">
                        <select name="depart_id" class="form-control" <?php echo empty($output['depart_list']) ? 'disabled' : ''?>>
                            <option value="0">Select Department</option>
                            <?php foreach($output['depart_list'] as $depart){?>
                                <option value="<?php echo $depart['uid']?>" <?php echo $output['user_info']['depart_id'] == $depart['uid']?'selected':''?>><?php echo $depart['depart_name']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Position'?></label>
                <div class="col-sm-9">
                    <?php foreach ($output['user_position'] as $key => $val) { ?>
                        <label class="col-sm-5 radio-inline"><input type="radio" name="user_position" value="<?php echo $key?>" <?php echo$key == $output['user_info']['user_position'] ? 'checked' : '' ?>><?php echo ucwords(strtolower($val))?></label>
                    <?php }?>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group role-select" style="display: <?php echo in_array($output['user_info']['user_position'],array(userPositionEnum::BACK_OFFICER)) ? "block" : "none"?>">
                <label class="col-sm-3 control-label"><?php echo 'Role'?></label>
                <div class="col-sm-9">
                    <?php foreach($output['role_list'] as $val){?>
                        <label class="col-sm-4 checkbox-inline">
                            <input type="checkbox"
                                   name="role_select[]"
                                   allow-back-office="<?php echo implode(',', $val['allow_back_office']['allow_auth']) ?>"
                                   allow-counter="<?php echo implode(',', $val['allow_counter']['allow_auth']) ?>"
                                   value="<?php echo $val['uid'] ?>" <?php echo in_array($val['uid'], $output['user_info']['role_arr']) ? 'checked' : '' ?>>
                            <?php echo $val['role_name'] ?>
                        </label>
                    <?php }?>
                </div>
            </div>
            <div class="form-group back_office_auth" style="display: <?php echo in_array($output['user_info']['user_position'], array(userPositionEnum::BACK_OFFICER)) ? "block" : "none"?>">
                <label class="col-sm-3 control-label"><?php echo 'Back Office Auth'?></label>
                <div class="col-sm-9 auth-list" style="margin-top: 10px">
                    <?php foreach($output['auth_group_back_office'] as $k_1=>$v_1){?>
                        <div style="font-size: 16px;margin-bottom: 5px" class="back_office_auth_group clearfix">
                            <span style="margin-right: 5px;"><?php echo L('auth_'.strtolower($k_1))?></span>
                            <ul class="list-group">
                                <?php foreach($v_1 as $v_2){?>
                                    <li class="list-group-item col-sm-6">
                                        <label class="col-sm-4 checkbox-inline" title="<?php echo L('auth_'.strtolower($v_2))?>"><input type="checkbox" name="auth_select[]"
                                               value="<?php echo $v_2 ?>" <?php echo in_array($v_2, $output['user_info']['back_office_auth']) ? 'checked' : '' ?>>
                                            <?php echo L('auth_' . strtolower($v_2))?:ucwords(str_replace("_"," ",$v_2)) ?>
                                            </label>
                                    </li>
                                <?php }?>
                            </ul>
                        </div>
                    <?php }?>
                </div>
            </div>
            <div class="form-group counter_auth" style="display: <?php echo in_array($output['user_info']['user_position'],array(userPositionEnum::CHIEF_TELLER, userPositionEnum::TELLER)) ? "none" : "none"?>">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Counter Auth'?></label>
                <div class="col-sm-9 auth-list" style="margin-top: 10px">
                    <?php foreach($output['auth_group_counter'] as $k_1=>$v_1){?>
                        <div style="font-size: 16px;margin-bottom: 5px" class="counter_auth_group clearfix">
                            <span><?php echo L('auth_counter_'.strtolower($k_1))?></span>
                            <ul class="list-group">
                                <?php foreach($v_1 as $v_2){?>
                                    <li class="list-group-item col-sm-6">
                                        <label class="col-sm-4 checkbox-inline" title="<?php echo L('auth_counter_'.strtolower($v_2))?>">
                                            <input type="checkbox" name="auth_select_counter[]" value="<?php echo $v_2?>" <?php echo in_array($v_2, $output['user_info']['counter_auth']) ? 'checked' : '' ?>>
                                            <?php echo L('auth_counter_'.strtolower($v_2))?:ucwords(str_replace("_"," ",$v_2))?>
                                        </label>
                                    </li>
                                <?php }?>
                            </ul>
                        </div>
                    <?php }?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Remark'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="remark" placeholder="" value="<?php echo $output['user_info']['remark']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Status';?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <label><input type="radio" value="1" name="user_status" <?php echo $output['user_info']['user_status'] == 1 ? "checked" : ""?>><?php echo 'Valid'?></label>
                    <label style="margin-left: 10px"><input type="radio" value="0" name="user_status" <?php echo $output['user_info']['user_status'] == 0 ? "checked" : ""?>><?php echo 'Invalid'?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger"><i class="fa fa-check"></i> <?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back'?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(function () {
        $('.role-select input').click(function () {
            $('.auth-list input').prop('checked', false);
            $('.role-select input:checked').each(function () {
                var allow_back_office = $(this).attr('allow-back-office');
                var allow_counter = $(this).attr('allow-counter');
                if (allow_back_office) {
                    allow_back_office = allow_back_office.split(',');
                    for (var i = 0; i < allow_back_office.length; i++) {
                        $('.back_office_auth_group input[name="auth_select[]"][value="' + allow_back_office[i] + '"]').prop('checked', true);
                    }
                }
                if (allow_counter) {
                    allow_counter = allow_counter.split(',');
                    for (var i = 0; i < allow_counter.length; i++) {
                        $('.counter_auth_group input[name="auth_select_counter[]"][value="' + allow_counter[i] + '"]').prop('checked', true);
                    }
                }
            })
        })

        $('select[name="branch_id"]').change(function () {
            var _branch_id = $(this).val();
            $('select[name="depart_id"]').html('<option value="0" selected="selected">Select Department</option>').attr('disabled', true);
            if(_branch_id == 0){
                return;
            }
            yo.dynamicTpl({
                tpl: "user/depart.option",
                dynamic: {
                    api: "user",
                    method: "getDepartList",
                    param: {branch_id:_branch_id}
                },
                callback: function (_tpl) {
                    $('select[name="depart_id"]').html(_tpl).attr('disabled', false);
                }
            });
        })

        $('input[name="user_position"]').change(function () {
            var user_position = $(this).val();
            if (user_position == 'chief_teller' || user_position == 'teller') {
                $('.role-select').hide();
                $('.back_office_auth').hide();
                $('.counter_auth').hide();
                /*
                $('.role-select').show();
                $('.back_office_auth').hide();
                $('.counter_auth').show();
                */
            } else if (user_position == 'back_officer' || user_position == 'committee_member') {
                $('.role-select').show();
                $('.back_office_auth').show();
                $('.counter_auth').hide();
            } else {
                $('.role-select').hide();
                $('.back_office_auth').hide();
                $('.counter_auth').hide();
            }
        })

    })

    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            user_code: {
                required: true,
                checkNumAndStr: true
            },
            user_name: {
                required: true
            },
            branch_id: {
                checkRequired: true
            },
            depart_id: {
                checkRequired: true
            },
            user_position: {
                required: true
            }
        },
        messages: {
            user_code: {
                required: '<?php echo 'Required'?>',
                checkNumAndStr: '<?php echo 'It can only be Numbers or letters!'?>'
            },
            user_name: {
                required: '<?php echo 'Required'?>'
            },
            branch_id: {
                checkRequired: '<?php echo 'Required'?>'
            },
            depart_id: {
                checkRequired: '<?php echo 'Required'?>'
            },
            user_position: {
                required: true
            }
        }
    });

    jQuery.validator.addMethod("checkNumAndStr", function (value, element) {
        value = $.trim(value);
        if (!/^[A-Za-z0-9]+$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("checkRequired", function (value, element) {
        if (value == 0) {
            return false;
        } else {
            return true;
        }
    });

</script>