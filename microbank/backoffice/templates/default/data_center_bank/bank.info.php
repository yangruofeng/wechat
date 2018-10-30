<?php $bank_info = $data['data']; ?>
<table class="table table-bordered table-no-background">
    <tbody class="table-body">
    <tr>
        <td><label class="control-label">Bank Name</label></td>
        <td><?php echo $bank_info['bank_name']; ?></td>
        <td><label class="control-label">Bank Code</label></td>
        <td><?php echo $bank_info['bank_code']; ?></td>
        <td><label class="control-label">Create Time</label></td>
        <td><?php echo $bank_info['create_time']; ?></td>
    </tr>
    <tr>
        <td><label class="control-label">Account Name </label></td>
        <td><?php echo $bank_info['bank_account_name']; ?></td>
        <td><label class="control-label">Account No. </label></td>
        <td><?php echo $bank_info['bank_account_no']; ?></td>
        <td><label class="control-label">Branch</label></td>
        <td><?php echo $bank_info['branch_name']; ?></td>
    </tr>
    <tr>
        <td><label class="control-label">Currency</label></td>
        <td><?php echo $bank_info['currency']; ?><input type="hidden" id="currency" value="<?php echo $bank_info['currency']; ?>"/></td>
        <td><label class="control-label">Balance</label></td>
        <td><?php echo $bank_info['bank_balance'][$bank_info['currency']]['balance']; ?></td>
        <td><label class="control-label">Phone</label></td>
        <td><?php echo $bank_info['bank_account_phone']; ?></td>
    </tr>
    <tr>
        <td><label class="control-label">Address</label></td>
        <td colspan="5"><?php echo $bank_info['bank_address']; ?></td>
    </tr>
    </tbody>
</table>
<div class="data-center-btn" style="margin-top: 10px">
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $bank_info['uid'] ? '' : 'disabled'; ?>" onclick="btn_bank_op(this, 'transactions')">Transactions</button>
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $bank_info['uid'] ? '' : 'disabled';?>" onclick="btn_bank_op(this, 'voucher')">Journal Voucher</button>
</div>

<script>
    function btn_bank_op(el, type) {
        if (!_UID) return;
        $(el).addClass("btn-success current").siblings().removeClass("btn-success current");
        var tpl = "", api = "", method = "", param = {};
        switch (type) {
            case "transactions":
                tpl = "common/passbook.account.flow.index";
                api = "common";
                method = "passbookAccountFlowPage";
                param.obj_uid = _UID;
                param.obj_type = <?php echo objGuidTypeEnum::BANK_ACCOUNT;?>;
                param.is_ajax = 1;
                break;
            case "voucher":
                tpl = "common/passbook.voucher.index";
                api = "common";
                method = "passbookJournalVoucherPage";
                param.obj_uid = _UID;
                param.obj_type = <?php echo objGuidTypeEnum::BANK_ACCOUNT;?>;
                param.is_ajax = 1;
                break;
            default:
        }
        $(".data-center-list").waiting();
        yo.dynamicTpl({
            tpl: tpl,
            dynamic: {
                api: api,
                method: method,
                param: param
            },
            callback: function (_tpl) {
                $(".data-center-list").unmask();
                $(".data-center-list").html(_tpl);
            }
        });
    }
</script>
