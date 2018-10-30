<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Department</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('user', 'department', array('uid'=>$output['depart_info']['branch_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
<!--                <li>-->
<!--                    <a href="--><?php //echo getUrl('user', 'addDepartment', array(), false, BACK_OFFICE_SITE_URL) ?><!--"><span>Add</span></a>-->
<!--                </li>-->
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['depart_info']['uid']?>">
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Department Code' ?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="depart_code" placeholder="" value="<?php echo $output['depart_info']['depart_code'] ?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Department Name' ?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="depart_name" placeholder="" value="<?php echo $output['depart_info']['depart_name'] ?>">
                    <div class="error_msg"></div>
                </div>
            </div>
<!--            <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Leader' ?></label>
                <div class="col-sm-8">
                    <select class="form-control" name="leader">
                        <option value="0">Please Select</option>
                        <?php foreach($output['user_list'] as $user){?>
                            <option value="<?php echo $user['uid']?>" <?php echo $output['depart_info']['leader'] == $user['uid']?'selected':''?>><?php echo $user['user_code']?></option>
                        <?php }?>
                    </select>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Assistant' ?></label>
                <div class="col-sm-8">
                    <select class="form-control" name="assistant">
                        <option value="0">Please Select</option>
                        <?php foreach($output['user_list'] as $user){?>
                            <option value="<?php echo $user['uid']?>" <?php echo $output['depart_info']['assistant'] == $user['uid']?'selected':''?>><?php echo $user['user_code']?></option>
                        <?php }?>
                    </select>
                    <div class="error_msg"></div>
                </div>
           </div>-->
            <div class="form-group">
                <div class="col-sm-offset-4 col-col-sm-8" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.next());
        },
        rules: {
            depart_code: {
                required: true
            },
            depart_name: {
                required: true
            },
            branch_id: {
                checkRequired: true
            },
            assistant: {
                checkRepetition: true
            }
        },
        messages: {
            depart_code: {
                required: '<?php echo 'Required'?>'
            },
            depart_name: {
                required: '<?php echo 'Required'?>'
            },
            branch_id: {
                checkRequired: '<?php echo 'Required'?>'
            },
            assistant: {
                checkRepetition: '<?php echo 'Repetition'?>'
            }
        }
    });

    jQuery.validator.addMethod("checkRequired", function (value, element) {
        if (value == 0) {
            return false;
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("checkRepetition", function (value, element) {
        if (value == 0) {
            return true;
        }
        var _leader = $('select[name="leader"]').val();
        if (_leader == value) {
            return false;
        } else {
            return true;
        }
    });
</script>