<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<?php $level_info = $output['level_info']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Department</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('setting', 'creditLevel', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
<!--                <li>-->
<!--                    <a href="--><?php //echo getUrl('setting', 'addCreditLevel', array(), false, BACK_OFFICE_SITE_URL) ?><!--"><span>Add</span></a>-->
<!--                </li>-->
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form  class="form-horizontal" method="post">

            <input type="hidden" name="form_submit" value="ok">

            <input type="hidden" name="uid" value="<?php echo $level_info['uid']; ?>" >

            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Type'; ?></label>
                <div class="col-sm-8">
                    <select name="level_type" class="form-control">
                        <option value="<?php echo creditLevelTypeEnum::MEMBER; ?>" <?php if( $level_info['level_type'] == creditLevelTypeEnum::MEMBER ) echo 'selected'; ?>>Member</option>
                        <option value="<?php echo creditLevelTypeEnum::MERCHANT; ?>" <?php if( $level_info['level_type'] == creditLevelTypeEnum::MERCHANT ) echo 'selected'; ?>>Merchant</option>
                    </select>
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Min Amount'; ?></label>
                <div class="col-sm-8">
                    <input type="number" class="form-control" name="min_amount" placeholder="" value="<?php echo $level_info['min_amount']; ?>">
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Max Amount'; ?></label>
                <div class="col-sm-8">
                    <input type="number" class="form-control" name="max_amount" placeholder="" value="<?php echo $level_info['max_amount']; ?>">
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Disburse Time'; ?></label>
                <div class="col-sm-8">
                    <div class="input-group" style="width: 100%;">
                        <input type="number" class="form-control" name="disburse_time" placeholder="" value="<?php echo $level_info['disburse_time']; ?>" style="width: 60%;">
                        <select name="disburse_time_unit" class="form-control" id="" style="width: 40%;">
                            <option value="1" <?php if( $level_info['disburse_time_unit'] == 1 ) echo 'selected'; ?> >Minute</option>
                            <option value="2" <?php if( $level_info['disburse_time_unit'] == 2 ) echo 'selected'; ?> >Hour</option>
                            <option value="3" <?php if( $level_info['disburse_time_unit'] == 3 ) echo 'selected'; ?> >Day</option>

                        </select>
                        <div class="error_msg"></div>
                    </div>


                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Certification List'; ?></label>
                <div class="col-sm-8">
                    <div class="clearfix" style="background-color: #fff;">
                        <?php foreach( (new certificationTypeEnum())->toArray() as $type ){ ?>
                            <div class="col-sm-6">
                                <input type="checkbox" class="" name="cert_list[]" placeholder="" value="<?php echo $type; ?>" <?php if( in_array($type,$level_info['cert_list']) ) echo 'checked'; ?> > <?php echo $output['cert_verify_lang'][$type]; ?>

                            </div>
                        <?php } ?>
                    </div>

                    <div class="error_msg"></div>
                </div>
            </div>


            <div class="form-group">
                <div class="col-sm-offset-4 col-col-sm-8" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger"><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><?php echo 'Back' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>

    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return false;
        }

        $('.form-horizontal').submit();
    });

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            min_amount: {
                required: true
            },
            max_amount: {
                required: true
            },
            disburse_time:{
                required: true
            }
        },
        messages: {
            min_amount: {
                required: '<?php echo 'Required'?>'
            },
            max_amount: {
                required: '<?php echo 'Required'?>'
            },
            disburse_time:{
                required: '<?php echo 'Required'?>'
            }
        }
    });


</script>