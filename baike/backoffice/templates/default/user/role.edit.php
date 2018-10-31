<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    #auth-list .list-group-item {
        border-radius: 0px;
        font-size: 14px;
        padding: 7px 15px;
    }

    #auth-list .auth_group {
        margin-bottom: 10px;
    }

    .auth-list .list-group-item label {
        width: 100%;
        padding-top: 0px;
        overflow: hidden;
        text-overflow:ellipsis;
        white-space: nowrap;
    }
</style>
<?php $allow_back_office = $output['role_info']['allow_back_office'];?>
<?php $allow_counter = $output['role_info']['allow_counter'];?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Role</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'role', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
<!--                <li><a href="--><?php //echo getUrl('user', 'add', array(), false, BACK_OFFICE_SITE_URL)?><!--"><span>Add</span></a></li>-->
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['role_info']['uid']?>">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="role_name" placeholder="" value="<?php echo $output['role_info']['role_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Back Office Auth'?></label>
                <div class="col-sm-9 auth-list" id="auth-list" style="margin-top: 10px">
                    <?php foreach($output['auth_group_back_office'] as $k_1=>$v_1){?>
                        <div style="font-size: 16px" class="auth_group clearfix">
                            <input type="checkbox" name="auth_group[]" value="<?php echo $k_1 ?>" <?php echo in_array($k_1, $allow_back_office['role_group']) ? 'checked' : '' ?> style="margin-right: 5px;"><?php echo L('auth_'.strtolower($k_1))?>
                            <ul class="list-group">
                                <?php foreach($v_1 as $v_2){?>
                                    <li class="list-group-item col-sm-6">
                                        <label class="col-sm-4 checkbox-inline" title="<?php echo L('auth_'.strtolower($v_2))?>"><input type="checkbox" name="auth_select[]" value="<?php echo $v_2?>" <?php echo in_array($v_2, $allow_back_office['allow_auth']) ? 'checked' : '' ?>>
                                            <?php echo L('auth_'.strtolower($v_2))?:(ucwords(str_replace("_"," ",$v_2)))?>
                                        </label>
                                    </li>
                                <?php }?>
                            </ul>
                        </div>
                    <?php }?>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Counter Auth'?></label>
                <div class="col-sm-9 auth-list" id="auth-list" style="margin-top: 10px">
                    <?php foreach($output['auth_group_counter'] as $k_1=>$v_1){?>
                        <div style="font-size: 16px" class="auth_group clearfix">
                            <input type="checkbox" name="auth_group_counter[]" value="<?php echo $k_1?>" <?php echo in_array($k_1, $allow_counter['role_group']) ? 'checked' : '' ?> style="margin-right: 5px;"><?php echo L('auth_counter_'.strtolower($k_1))?>
                            <ul class="list-group">
                                <?php foreach($v_1 as $v_2){?>
                                    <li class="list-group-item col-sm-6">
                                        <label class="col-sm-4 checkbox-inline" title="<?php echo L('auth_counter_'.strtolower($v_2))?>"><input type="checkbox" name="auth_select_counter[]" value="<?php echo $v_2?>" <?php echo in_array($v_2, $allow_counter['allow_auth']) ? 'checked' : ''?>>
                                            <?php echo L('auth_counter_'.strtolower($v_2))?:(ucwords(str_replace("_"," ",$v_2)))?>
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
                    <input type="text" class="form-control" name="remark" placeholder="" value="<?php echo $output['role_info']['remark']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Status';?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <label><input type="radio" value="1" name="role_status" <?php echo $output['role_info']['role_status'] == 1 ?'checked':''?>><?php echo 'Valid'?></label>
                    <label style="margin-left: 10px"><input type="radio" value="0" name="role_status" <?php echo $output['role_info']['role_status'] == 0 ?'checked':''?>><?php echo 'Invalid'?></label>
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
        $('input[name="auth_group[]"],input[name="auth_group_counter[]"]').click(function () {
            if ($(this).is(':checked')) {
                $(this).next().find('input').prop('checked', true);
            } else {
                $(this).next().find('input').prop('checked', false);
            }
        })

        $('.list-group input[name="auth_select[]"]').click(function () {
            if ($(this).closest('.list-group').find('input[name="auth_select[]"]').length == $(this).closest('.list-group').find('input[name="auth_select[]"]:checked').length) {
                $(this).closest('.auth_group').find('input[name="auth_group[]"]').prop('checked', true);
            } else if ($(this).closest('.list-group').find('input[name="auth_select[]"]:checked').length == 0) {
                $(this).closest('.auth_group').find('input[name="auth_group[]"]').prop('checked', false);
            }
        })

        $('.list-group input[name="auth_select_counter[]"]').click(function () {
            if ($(this).closest('.list-group').find('input[name="auth_select_counter[]"]').length == $(this).closest('.list-group').find('input[name="auth_select_counter[]"]:checked').length) {
                $(this).closest('.auth_group').find('input[name="auth_group_counter[]"]').prop('checked', true);
            } else if ($(this).closest('.list-group').find('input[name="auth_select_counter[]"]:checked').length == 0) {
                $(this).closest('.auth_group').find('input[name="auth_group_counter[]"]').prop('checked', false);
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
        errorPlacement: function(error, element){
            error.appendTo(element.next());
        },
        rules : {
            role_name : {
                required : true
            }
        },
        messages : {
            role_name : {
                required : '<?php echo 'Required'?>'
            }
        }
    });


</script>