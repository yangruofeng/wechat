<?php
$item = $output['detail'];
$credit_info = $output['credit_info'];
$contract_info = $output['contract_info'];
$loan_summary = $output['loan_summary'];
$savings_balance = $output['savings_balance']
?>
<?php if ($item) { ?>
    <div class="data-center-base-info">
        <?php include(template("common/client.detail.top")); ?>
    </div>
    <div class="data-center-btn">
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'loan_list')">
            Loan List
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'transactions')">
            Transactions
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'voucher')">
            Journal Voucher
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'business_income')">
            Business Income
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'salary_income')">
            Salary Income
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'attachment')">
            Attachment
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'identity')">
            Identity
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'asset_list')">
            Asset List
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'credit_history')">
            Credit History
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'credit_log')">
            Credit Log
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'credit_agreement')">
            Credit Agreement
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'mortgage')">
            Mortgage
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'cbc')">
            CBC Result
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'change_state_log')">
            Change State Log
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'register_by')">
            Register By
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'credit_category')">
            Credit Category
        </button>
        <button type="button" class="btn btn-primary btn-sm btn-center-item" onclick="btn_member_op(this, 'scene_image')">
            Scene Image
        </button>
    </div>

    <div class="data-center-list"></div>
    <script>
        _UID = '<?php echo $item['uid']?>';
    </script>
<?php } else { ?>
    <div style="width: 300px">
        <?php include(template(":widget/no_record")); ?>
    </div>
<?php } ?>
<script>
    function btn_member_op(el, type) {
        if (!_UID) return;
        $(el).addClass("btn-success current").siblings().removeClass("btn-success current");
        var tpl = "", api = "common", method = "", param = {};
        param.uid = _UID;
        switch (type) {
            case "register_by":
                tpl = "common/client.register.index";
                method = "showClientRegisterPage";
                break;
            case "loan_list":
                tpl = "common/client.loan.index";
                method = "showClientLoanPage";
                break;
            case "transactions":
                tpl = "common/passbook.account.flow.index";
                method = "passbookAccountFlowPage";
                param.obj_uid = _UID;
                param.obj_type = <?php echo objGuidTypeEnum::CLIENT_MEMBER;?>;
                param.is_ajax = 1;
                break;
                break;
            case "voucher":
                tpl = "common/passbook.voucher.index";
                method = "passbookJournalVoucherPage";
                param.obj_uid = _UID;
                param.obj_type = <?php echo objGuidTypeEnum::CLIENT_MEMBER;?>;
                param.is_ajax = 1;
                break;
            case "business_income":
                tpl = "common/client.business.index";
                method = "showClientBusinessPage";
                break;
            case "salary_income":
                tpl = "common/client.salary.index";
                method = "showClientSalaryPage";
                break;
            case "attachment":
                tpl = "common/client.attachment.index";
                method = "showClientAttachmentPage";
                break;
            case "identity":
                tpl = "common/client.identity.index";
                method = "showClientIdentityPage";
                break;
            case "asset_list":
                tpl = "common/client.assets.index";
                method = "showClientAssetsPage";
                break;
            case "credit_history":
                tpl = "common/client.credit_history.index";
                method = "showClientCreditHistoryPage";
                break;
            case "credit_log":
                tpl = "common/client.credit_log.index";
                method = "showClientCreditLogPage";
                break;
            case "credit_agreement":
                tpl = "common/client.credit_agreement.index";
                method = "showClientCreditAgreementPage";
                break;
            case "mortgage":
                tpl = "common/client.mortgage.index";
                method = "showClientMortgagePage";
                break;
            case "cbc":
                tpl = "common/client.cbc.index";
                method = "showClientCbcPage";
                break;
            case "change_state_log":
                tpl = "common/client.change_state_log.index";
                method = "showChangeStateLogPage";
            case "credit_category":
                tpl = "common/client.credit.category.index";
                method = "showCreditCategory";
                break;
            case "scene_image":
                tpl = "common/client.scene.image.index";
                method = "showSceneImage";
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
