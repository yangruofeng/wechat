<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Issue IC Card</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('dev', 'issueIcCard', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post" enctype="multipart/form-data">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Card No'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="card_no" placeholder="" value="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group" style="display: none">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Card Key'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="card_key" placeholder="" value="FFFFFFFFFFFF">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Expire Date'?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <input type="text" class="form-control expire_date" name="expire_time" value="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger"><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><?php echo 'Back'?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function(){
        $(".expire_date").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
        });
    });

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
            card_no : {
                required : true
            }
        },
        messages : {
            card_no : {
                required : '<?php echo 'Required'?>'
            }
        }
    });


</script>