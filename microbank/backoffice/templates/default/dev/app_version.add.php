<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Publish App</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('dev', 'appVersion', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post" enctype="multipart/form-data">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'App Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="app_name" placeholder="" value="<?php echo $_GET['app_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Version'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="version" placeholder="" value="<?php echo $_GET['version']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'File 1'?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <input type="file" name="app_file">
                    <input type="hidden" name="inputName" value="app_file">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'File 2'?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <input type="file" name="app_file_2">
                    <input type="hidden" name="inputName" value="app_file_2">
                    <div class="error_msg"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Remark'?></label>
                <div class="col-sm-9">
                    <textarea name="remark" class="form-control" id="" cols="30" rows="10"></textarea>
                    <!--<input type="text" class="form-control" name="remark" placeholder="" value="<?php /*echo $_GET['remark']*/?>">-->
                    <div class="error_msg"></div>

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Must Updated';?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <label><input type="checkbox" value="1" name="is_required" checked></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger" id="save-version"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back'?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('#save-version').click(function () {
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
            app_name : {
                required : true
            },
            version : {
                required : true
            },
            app_file : {
                required : true
            }
        },
        messages : {
            app_name : {
                required : '<?php echo 'Required'?>'
            },
            version : {
                required : '<?php echo 'Required'?>'
            },
            app_file : {
                required : '<?php echo 'Required'?>'
            }
        }
    });
</script>