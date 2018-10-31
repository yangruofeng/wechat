<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Short Code</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('setting', 'shortCode', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['definition']['uid']?>">

            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Category' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="category" placeholder="" value="<?php echo $output['definition']['category'] ?>" readonly>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Item Name' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="item_name" placeholder="" value="<?php echo $output['definition']['item_name'] ?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Item Code' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="item_code" placeholder="" value="<?php echo $output['definition']['item_code'] ?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Item Value' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="item_value" placeholder="" value="<?php echo $output['definition']['item_value'] ?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Item Description' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="item_desc" placeholder="" value="<?php echo $output['definition']['item_desc'] ?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
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
            return;
        }

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.next());
        },
        rules: {
            item_name: {
                required: true
            },
            item_code: {
                required: true
            }
        },
        messages: {
            item_name: {
                required: '<?php echo 'Required'?>'
            },
            item_code: {
                required: '<?php echo 'Required'?>'
            }
        }
    });


</script>