
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>

<?php $setting_info = $output['setting_info']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Loan Fee & Admin Fee</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'loanFeeSetting', array('category_id' => $output['category_info']['uid']), false, BACK_OFFICE_SITE_URL); ?>"><span>Back</span></a></li>
                <li ><a class="current"><span><?php echo $setting_info?'Edit':'Add'; ?></a></span></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content form-box" >

            <div class=" clearfix">
                <form  method="post" class="form-horizontal" id="frm_setting_rate">
                    <input type="hidden" name="uid" value="<?php echo $setting_info['uid']; ?>">
                    <input type="hidden" name="category_id" value="<?php echo $setting_info['category_id']?:$_GET['category_id']?>">
                    <input type="hidden" name="form_submit" value="ok">
                    <input type="hidden" name="act" value="loan">
                    <input type="hidden" name="op" value="editLoanFeeSetting">
                    <table class="table table-hover table-bordered" style="width: 500px">
                        <tr>
                            <td class="text-right">Product</td>
                            <td class="text-left">
                                <input type="text" class="form-control" value="<?php echo $output['category_info']['category_name']; ?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right">Currency</td>
                            <td class="text-left">
                                <select class="form-control" name="currency">
                                    <?php echo system_toolClass::getCurrencyOption($setting_info['currency']); ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right">
                                Min Amount
                            </td>
                            <td class="text-left">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="min_amount" value="<?php echo $setting_info['min_amount']; ?>">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">Amount</span>
                                </div>
                                <div class="error_msg"></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right">
                                Max Amount
                            </td>
                            <td class="text-left">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="max_amount" value="<?php echo $setting_info['max_amount']; ?>">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">Amount</span>
                                </div>
                                <div class="error_msg"></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right">
                                Admin Fee
                            </td>
                            <td class="text-left">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control"  name="admin_fee" value="<?php echo $setting_info['admin_fee']; ?>" style="width:70%;">
                                    <select class="form-control" name="admin_fee_type" style="width: 30%">
                                        <option value="1" <?php echo $setting_info['admin_fee_type']==1?'selected':'';  ?>>Value</option>
                                        <option value="0" <?php echo $setting_info['admin_fee_type']==0?'selected':'';  ?>>%</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </td>
                        </tr>
                        <tr>
                           <td class="text-right">
                               Loan Fee
                           </td>
                            <td class="text-left">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control"  name="loan_fee" value="<?php echo $setting_info['loan_fee']; ?>" style="width:70%;">
                                    <select class="form-control" name="loan_fee_type" style="width: 30%">
                                        <option value="1" <?php echo $setting_info['loan_fee_type']==1?'selected':'';  ?>>Value</option>
                                        <option value="0" <?php echo $setting_info['loan_fee_type']==0?'selected':'';  ?> >%</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right">
                                Annual Fee
                            </td>
                            <td class="text-left">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control"  name="annual_fee" value="<?php echo $setting_info['annual_fee']; ?>" style="width:70%;">
                                    <select class="form-control" name="annual_fee_type" style="width: 30%">
                                        <option value="1" <?php echo $setting_info['annual_fee_type']==1?'selected':'';  ?>>Value</option>
                                        <option value="0" <?php echo $setting_info['annual_fee_type']==0?'selected':'';  ?> >%</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4" class="text-center">
                                <button type="button" class="btn btn-primary" onclick="formSubmit()"><?php echo 'Submit'?></button>
                                <a  class="btn btn-default" href="<?php echo getUrl('loan', 'loanFeeSetting', array('category_id' => $output['category_info']['uid']), false, BACK_OFFICE_SITE_URL); ?>">Cancel</a>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

    function formSubmit()
    {
        if( !$('#frm_setting_rate').valid() ){
            return false;
        }

        $('#frm_setting_rate').submit();

    }



    $('#frm_setting_rate').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            min_amount: {
                required: true
            },
            max_amount: {
                required: true
            }
        },
        messages: {
            min_amount: {
                required: '<?php echo 'Required!'?>'
            },
            max_amount: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

</script>
