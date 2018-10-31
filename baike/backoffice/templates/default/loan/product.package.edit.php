<?php $info=$output['package_item']?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Interest Package</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'productPackagePage', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Package List</span></a></li>
                <li><a class="current"><span><?php if($info['uid']){echo 'Edit';}else{echo 'Add';}?> Package</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content" style="width: 600px">
            <form class="form-horizontal" method="post" id="frm_item" action="<?php echo getUrl("loan","submitProductPackageItem",array(),false,BACK_OFFICE_SITE_URL)?>">
                <input type="hidden" name="form_submit" value="ok">
                <input type="hidden" name="uid" value="<?php echo $info['uid']?>">
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Package Name'?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="package" placeholder="" value="<?php echo $info['package']?>">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <?php if(!$info['uid']){?>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Copy Interest From'?></label>
                        <div class="col-sm-9">
                            <select class="form-control" name="copy_from">
                                <option value="0">----Don't Copy------</option>
                                <?php foreach($output['package_list'] as $item){?>
                                    <option value="<?php echo $item['uid']?>"><?php echo $item['package']?></option>
                                <?php }?>
                            </select>

                        </div>
                    </div>
                <?php }?>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Remark'?></label>
                    <div class="col-sm-9">
                        <textarea name="remark" class="form-control" rows="5"><?php echo $info['remark']?></textarea>
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                        <button type="button" class="btn btn-danger" style="min-width: 80px;" id="btnSubmit"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                        <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 10px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        $('#btnSubmit').click(function () {
            var valid = true;
            if (!$("#frm_item").valid()) {
                valid = false;
            }

            if(!valid){
                return;
            }

            $('#frm_item').submit();
        })
    })

    $('#frm_item').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            package: {
                required: true
            }
        },
        messages: {
            package: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>
