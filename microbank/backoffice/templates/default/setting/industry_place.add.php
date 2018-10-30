<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .page .container {
        margin-top: 80px;
    }

    .survey_info {
        margin-bottom: 10px;
    }

    .survey_info .col-sm-11 {
        padding: 0!important;
    }

    .survey_info .col-sm-11 .col-sm-6:first-child {
        padding-left: 0px!important;
        padding-right: 7px!important;
    }

    .survey_info .col-sm-11 .col-sm-6:last-child {
        padding-left: 7px!important;
        padding-right: 0px!important;
    }

    .survey_info .col-sm-1 .fa {
        margin-top: 10px;
        cursor: pointer;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Industry Place</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('setting', 'industryPlace', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 500px;">
        <form class="form-horizontal" method="post" id="industryPlaceForm">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Place Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="place" placeholder="" value="<?php echo $_GET['place']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Remak'?></label>
                <div class="col-sm-9">
                    <textarea name="remark" class="form-control" rows="5"></textarea>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" id="btn_add_industry" class="btn btn-danger" style="min-width: 80px;"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 10px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(function () {
        $('#btn_add_industry').click(function () {
            var valid = true;
            if (!$("#industryPlaceForm").valid()) valid = false;
            if(!valid) return;
            $('#industryPlaceForm').submit();
        })
    })

    $('#industryPlaceForm').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            place: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            place: {
                required: '<?php echo 'Required!'?>'
            },
            remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>