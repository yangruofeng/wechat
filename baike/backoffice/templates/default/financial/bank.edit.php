<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['bank_info'] == 0) { ?>
                <h3>HQ Bank</h3>
                <ul class="tab-base">
                    <li>
                        <a href="<?php echo getUrl('financial', 'hqBank', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                    </li>
                    <li><a class="current"><span>Edit</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>Branch Bank</h3>
                <ul class="tab-base">
                    <li>
                        <a href="<?php echo getUrl('financial', 'branchBank', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                    </li>
                    <li><a class="current"><span>Edit</span></a></li>
                </ul>
            <?php } ?>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form  class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['bank_info']['uid']?>">
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Bank Name'; ?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" value="<?php echo $output['bank_info']['bank_name']?>" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Account No'; ?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="bank_account_no" value="<?php echo $output['bank_info']['bank_account_no']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Account Name'; ?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="bank_account_name" value="<?php echo $output['bank_info']['bank_account_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Account Phone'; ?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="bank_account_phone" value="<?php echo $output['bank_info']['bank_account_phone']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Currency'; ?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" value="<?php echo $output['bank_info']['currency']?>" readonly>
                </div>
            </div>
            <?php if($output['bank_info']['branch_id']) { ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo 'Branch';?></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo $output['branch_info']['branch_name']?>" readonly>
                    </div>
                </div>
            <?php } else { ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo 'Allow Client Deposit';?></label>
                    <div class="col-sm-8">
                        <label class="checkbox-inline"><input type="checkbox" value="1" name="allow_client_deposit" <?php echo intval($output['bank_info']['allow_client_deposit']) == 1 ? 'checked' : ''?>></label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo 'Allow billpay';?></label>
                    <div class="col-sm-8">
                        <label class="checkbox-inline"><input type="checkbox" value="1" name="is_allow_billpay" <?php echo intval($output['bank_info']['is_allow_billpay']) == 1 ? 'checked' : ''?>></label>
                    </div>
                </div>
            <?php } ?>

            <div class="form-group">
                <label class="col-sm-4 control-label"><?php echo 'State ';?></label>
                <div class="col-sm-8" style="margin-top: 7px">
                    <label><input type="radio" value="1" name="account_state" <?php echo !isset($output['bank_info']['account_state']) ? 'checked' : ($output['bank_info']['account_state'] == 1 ? 'checked' : '')?>><?php echo 'Valid'?></label>
                    <label style="margin-left: 10px"><input type="radio" value="0" name="account_state" <?php echo (isset($output['bank_info']['account_state']) && $output['bank_info']['account_state'] == 0) ? 'checked' : '' ?>><?php echo 'Invalid'?></label>
                </div>
            </div>

            <div class="form-group">
                <div style="text-align: center">
                    <button type="button" class="btn btn-danger" style="min-width: 80px"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" style="min-width: 80px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
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
            bank_account_no: {
                required: true
            },
            bank_account_name: {
                required: true
            }
        },
        messages: {
            bank_account_no: {
                required: '<?php echo 'Required'?>'
            },
            bank_account_name: {
                required: '<?php echo 'Required'?>'
            }
        }
    });


</script>