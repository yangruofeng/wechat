<table class="table table-bordered table-no-background">
    <tr>
        <td><label for="">CID</label></td>
        <td><?php echo $data['obj_guid'];?></td>
        <td><label for="">Branch Code</label></td>
        <td><?php echo $data['branch_code'];?></td>
        <td><label for="">Branch Name</label></td>
        <td><?php echo $data['branch_name'];?></td>
    </tr>
    <tr>
        <td><label for="">Phone</label></td>
        <td><?php echo $data['contact_phone'];?></td>
        <td><label for="">Address</label></td>
        <td colspan="3"><?php echo $data['address_region'];?></td>
    </tr>
    <tr>
        <td><label for="">Status</label></td>
        <td class="<?php echo $data['status'] ? 'green' : 'red';?>"><?php echo $data ? $data['status'] ? 'Active' : 'Inactive' : '';?></td>
        <td><label for="">Remark</label></td>
        <td><?php echo $data['remark'];?></td>
        <td></td>
        <td></td>
    </tr>
</table>

<div class="data-center-btn">
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $data['uid']?:'disabled';?>" onclick="btn_branch_op(this, 'staff')">Staff List</button>
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $data['uid']?:'disabled';?>" onclick="btn_branch_op(this, 'bank')">Bank List</button>
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $data['uid']?:'disabled';?>" onclick="btn_branch_op(this, 'transactions')">Transactions</button>
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $data['uid']?:'disabled';?>" onclick="btn_branch_op(this, 'voucher')">Journal Voucher</button>
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $data['uid']?:'disabled';?>" onclick="btn_branch_op(this, 'location')">Location</button>
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $data['uid'] && !$data['status']?:'disabled';?>" onclick="activeBranch(this);">Active</button>
    <button type="button" class="btn btn-primary btn-sm btn-center-item <?php echo $data['uid'] && $data['status']?:'disabled';?>" onclick="inactiveBranch(this);">Inactive</button>
</div>

<script>
    function btn_branch_op(el, type){
        if($(el).hasClass('disabled')) return;
        $(el).addClass("btn-success").siblings().removeClass("btn-success");
        $(el).addClass("current").siblings().removeClass("current");
        if(!_BRANCH_ID) return;
        var tpl = "", api = "", method = "", param = {};
        switch(type){
            case "staff":
                tpl = "data_center_branch/branch.staff.index";
                api = "data_center_branch";
                method = "showBranchStaffPage";
                param.branch_id = _BRANCH_ID;
                break;
            case "bank":
                tpl = "data_center_branch/branch.bank.index";
                api = "data_center_branch";
                method = "showBranchBankPage";
                param.branch_id = _BRANCH_ID;
                break;
            case "transactions":
                tpl = "common/passbook.account.flow.index";
                api = "common";
                method = "passbookAccountFlowPage";
                param.obj_uid = _BRANCH_ID;
                param.obj_type = <?php echo objGuidTypeEnum::SITE_BRANCH;?>;
                param.is_ajax = 1;
                break;
            case "voucher":
                tpl = "common/passbook.voucher.index";
                api = "common";
                method = "passbookJournalVoucherPage";
                param.obj_uid = _BRANCH_ID;
                param.obj_type = <?php echo objGuidTypeEnum::SITE_BRANCH;?>;
                param.is_ajax = 1;
                break;
            case "location":
                tpl = "data_center_branch/branch.location";
                api = "data_center_branch";
                method = "showBranchLocation";
                param.branch_id = _BRANCH_ID;
                break;
            default:
                break;
        }

        yo.dynamicTpl({
            tpl: tpl,
            dynamic: {
                api: api,
                method: method,
                param: param
            },
            callback: function (_tpl) {
                $(".data-center-list").html(_tpl);
            }
        });
    }

    function activeBranch(el){
        if($(el).hasClass('disabled')) return;
        $.messager.confirm("Active", "Are you sure to active for this branch?", function (_r) {
            if (!_r) return;
            submitStatus(1);
        });
    }

    function inactiveBranch(el){
        if($(el).hasClass('disabled')) return;
        $.messager.confirm("Inactive", "Are you sure to inactive for this branch?", function (_r) {
            if (!_r) return;
            submitStatus(0);
        });
    }

    function submitStatus(state){
        yo.loadData({
            _c: "data_center_branch",
            _m: 'editBranchStatus',
            param: {branch_id: _BRANCH_ID, status: state},
            callback: function (_o) {
                if(_o.STS){
                    $('.top-user-item.current').click();
                }else{
                    alert(_o.MSG,2)
                }
            }
        });
    }
</script>