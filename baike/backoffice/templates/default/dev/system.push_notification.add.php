<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Message</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('dev', 'pushNotification', array(), false, BACK_OFFICE_SITE_URL ); ?>"><span>List</span></a></li>
                <li><a class="current"><span>Push</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Message Title'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="message_title" placeholder="" value="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Message Content'?></label>
                <div class="col-sm-9">
                    <textarea name="message_body" class="form-control" id="" cols="30" rows="10"></textarea>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger" id="push_btn"><i class="fa fa-check"></i><?php echo 'Push' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back'?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('#push_btn').click(function () {
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
            message_title : {
                required : true
            },
            message_body : {
                required : true
            }
        },
        messages : {
            message_title : {
                required : '<?php echo 'Required'?>'
            },
            message_body : {
                required : '<?php echo 'Required'?>'
            }
        }
    });
</script>