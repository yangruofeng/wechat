<form class="form-horizontal" id="frm_setting" method="post">
    <input type="hidden" name="uid" value="<?php echo $product_info['uid']?>">
    <input type="hidden" name="act" value="savings">
    <input type="hidden" name="op" value="submitProductSetting">
    <input type="hidden" name="tab" value="page-2">

    <table class="table table-bordered table-hover" style="width: 500px;">
        <tr>
            <td class="text-right">
                <span class="red">*</span>Minimum Deposit(per time)
            </td>
            <td class="text-left">
                <input type="number" class="form-control" value="<?php echo $product_info['limit_deposit_lowest_per_time']?>" name="limit_deposit_lowest_per_time">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Maximum Deposit(per time)
            </td>
            <td class="text-left">
                <input type="number" class="form-control" value="<?php echo $product_info['limit_deposit_highest_per_time']?>" name="limit_deposit_highest_per_time">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Maximum Deposit(per day)
            </td>
            <td class="text-left">
                <input type="number" class="form-control" value="<?php echo $product_info['limit_deposit_highest_per_day']?>" name="limit_deposit_highest_per_day">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Maximum Deposit(per client)
            </td>
            <td class="text-left">
                <input type="number" class="form-control" value="<?php echo $product_info['limit_deposit_highest_per_client']?>" name="limit_deposit_highest_per_client">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Minimum Withdraw(per time)
            </td>
            <td class="text-left">
                <input type="number" class="form-control" value="<?php echo $product_info['limit_withdraw_lowest_per_time']?>" name="limit_withdraw_lowest_per_time">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Maximum Withdraw(per time)
            </td>
            <td class="text-left">
                <input type="number" class="form-control" value="<?php echo $product_info['limit_withdraw_highest_per_time']?>" name="limit_withdraw_highest_per_time">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Maximum Withdraw(per day)
            </td>
            <td class="text-left">
                <input type="number" class="form-control" value="<?php echo $product_info['limit_withdraw_highest_per_day']?>" name="limit_withdraw_highest_per_day">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Allow Auto Renew
            </td>
            <td class="text-left">
                <span class="form-control">
                    <input type="checkbox" value="1" name="is_allow_auto_renew" <?php echo $product_info['is_allow_auto_renew'] ? 'checked' : ''?>>
                </span>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Allow Prior Withdraw
            </td>
            <td class="text-left">
                <span class="form-control">
                    <input type="checkbox" value="1" name="is_allow_prior_withdraw" <?php echo $product_info['is_allow_prior_withdraw'] ? 'checked' : ''?>>
                </span>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Withdraw Need Password
            </td>
            <td class="text-left">
                <span class="form-control">
                    <input type="checkbox" value="1" name="is_withdraw_need_password" <?php echo $product_info['is_withdraw_need_password'] ? 'checked' : ''?>>
                </span>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Withdraw Need Id Card
            </td>
            <td class="text-left">
                <span class="form-control">
                    <input type="checkbox" value="1" name="is_withdraw_need_id_card" <?php echo $product_info['is_withdraw_need_id_card'] ? 'checked' : ''?>>
                </span>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Withdraw Allow Agency
            </td>
            <td class="text-left">
                <span class="form-control">
                    <input type="checkbox" value="1" name="is_withdraw_allow_agency" <?php echo $product_info['is_withdraw_allow_agency'] ? 'checked' : ''?>>
                </span>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Withdraw Need Book
            </td>
            <td class="text-left">
                <span class="form-control">
                    <input type="checkbox" value="1" name="is_withdraw_need_book" <?php echo $product_info['is_withdraw_need_book'] ? 'checked' : ''?>>
                </span>
            </td>
        </tr>

        <tr id="withdraw_book_days" style="display: <?php echo $product_info['is_withdraw_need_book'] ? '' : 'none' ?>;">
            <td class="text-right">
                <span class="red">*</span>Withdraw Book Days
            </td>
            <td class="text-left">
                <input type="number" class="form-control" value="<?php echo $product_info['withdraw_book_days']?>" name="withdraw_book_days">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="text-center" style="padding: 20px">
                <?php if ($product_info['uid']) { ?>
                    <a class="btn btn-primary" style="width: 150px; margin-right: 10px" onclick="settingSubmit('');">Submit</a>
                <?php } else { ?>
                    <a class="btn btn-primary" style="width: 150px; margin-right: 10px" title="Please save base info first." disabled>Submit</a>
                <?php } ?>
                <a type="button" class="btn btn-default" style="width: 150px; margin-left: 10px" onclick="javascript:history.back(-1)">Cancel</a>
            </td>
        </tr>
    </table>
</form>
<script>
    $(function () {
        $('input[name="is_withdraw_need_book"]').click(function () {
            var is_checked = $(this).is(':checked');
            if (is_checked) {
                $('#withdraw_book_days').show();
            } else {
                $('#withdraw_book_days').hide();
            }
        })
    })

    function settingSubmit(tab)
    {
        $('input[name="tab"]').val(tab);
        $('#frm_setting').submit();
//        var _values = $('#frm_setting').getValues();
//        $(document).waiting();
//        yo.loadData({
//            _c: "savings",
//            _m: "submitProductSetting",
//            param: _values,
//            callback: function (_o) {
//                $(document).unmask();
//                alert(_o.MSG);
//            }
//        });
    }

</script>