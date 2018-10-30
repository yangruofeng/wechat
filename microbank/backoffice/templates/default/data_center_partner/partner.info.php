<?php $partner_info = $data['data']; ?>
<table class="table table-bordered table-no-background">
    <tbody class="table-body">
    <tr>
        <td><label class="control-label">Partner Name</label></td>
        <td><?php echo $partner_info['partner_name']; ?></td>
        <td><label class="control-label">Partner Code</label></td>
        <td><?php echo $partner_info['partner_code']; ?></td>
        <td><label class="control-label">Guid</label></td>
        <td><?php echo $partner_info['obj_guid']; ?></td>
    </tr>
    <tr>
        <td><label class="control-label">Balance</label></td>
        <td>
            <?php foreach ($data['currency_list'] as $key => $currency) { ?>
                <span class="currency"><?php echo ncPriceFormat($partner_info['balance'][$key]['balance']) ?></span>
                <span><?php echo $currency ?></span>
                <br/>
            <?php } ?>
        </td>
        <td><label class="control-label">Create Time</label></td>
        <td><?php echo timeFormat($partner_info['create_time']); ?></td>
        <td><label class="control-label">Status</label></td>
        <td class="<?php echo $partner_info['is_active'] ? 'green' : 'red';?>"><?php echo $partner_info ? ($partner_info['is_active'] ? 'Active' : 'Inactive') : ''; ?></td>
    </tr>
    </tbody>
</table>
<div class="data-center-btn">
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $partner_info['uid'] ? '' : 'disabled'; ?>" onclick="btn_partner_op(this, 'transactions')">Transactions</button>
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $partner_info['uid'] ? '' : 'disabled';?>" onclick="btn_partner_op(this, 'voucher')">Journal Voucher</button>
</div>
<script>
    function btn_partner_op(el, type) {
        if (!_UID) return;
        $(el).addClass("btn-success current").siblings().removeClass("btn-success current");
        var tpl = "", api = "", method = "", param = {};
        switch (type) {
            case "transactions":
                tpl = "common/passbook.account.flow.index";
                api = "common";
                method = "passbookAccountFlowPage";
                param.obj_uid = _UID;
                param.obj_type = <?php echo objGuidTypeEnum::PARTNER;?>;
                param.is_ajax = 1;
                break;
            case "voucher":
                tpl = "common/passbook.voucher.index";
                api = "common";
                method = "passbookJournalVoucherPage";
                param.obj_uid = _UID;
                param.obj_type = <?php echo objGuidTypeEnum::PARTNER;?>;
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
