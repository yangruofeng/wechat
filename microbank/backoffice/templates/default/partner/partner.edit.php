<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Branch</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('partner', 'bank', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
<!--                <li><a href="--><?php //echo getUrl('partner', 'addPartner', array(), false, BACK_OFFICE_SITE_URL) ?><!--"><span>Add</span></a></li>-->
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['partner_info']['uid']?>">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Bank Code'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="partner_code" placeholder="" value="<?php echo $output['partner_info']['partner_code']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Bank Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="partner_name" placeholder="" value="<?php echo $output['partner_info']['partner_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>

            <?php foreach($output['currency_list'] as $key => $currency){?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'Init Balance(' . $key . ')'?></label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" name="<?php echo $key;?>" value="<?php echo $output['partner_info']['init_balance'][$key]?:0?>" readonly>
                        <div class="error_msg"></div>
                    </div>
                </div>
            <?php }?>

            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Status';?></label>
                <div class="col-sm-5">
                    <label class="radio-inline"><input type="radio" value="1" name="is_active" <?php echo $output['partner_info']['is_active'] == 1 ? "checked" : ''?>><?php echo 'Valid'?></label>
                    <label class="radio-inline"><input type="radio" value="0" name="is_active" <?php echo $output['partner_info']['is_active'] == 0 ? "checked" : ''?>><?php echo 'Invalid'?></label>

                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
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
            partner_code: {
                required: true
            },
            partner_name: {
                required: true
            }
        },
        messages: {
            partner_code: {
                required: '<?php echo 'Required!'?>'
            },
            partner_name: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

</script>