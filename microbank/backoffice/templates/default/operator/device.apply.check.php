<style>
    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    #branchModal .modal-dialog {
        margin-top: 10px!important;
    }

    #branchModal .easyui-panel {
        /*border: 1px solid #DDD;*/
        background-color: #EEE;
    }
</style>
<?php
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>New Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'deviceApply', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Audit</span></a></li>
            </ul>
        </div>
    </div>
    <?php $info = $output['info']?>
    <div class="container">
        <form class="form-horizontal">
            <table class="table audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Avatar</label></td>
                    <td>
                        <a target="_blank" href="<?php echo getImageUrl($info['member_icon']) ?>">
                            <img style="max-height: 100px;max-width: 200px" src="<?php echo getImageUrl($info['member_icon'], imageThumbVersion::MAX_240) ?>">
                        </a>
                    </td>
                    <td><label class="control-label">Member Image</label></td>
                    <td>
                        <a target="_blank" href="<?php echo getImageUrl($info['member_image']) ?>">
                            <img style="max-height: 100px;max-width: 200px" src="<?php echo getImageUrl($info['member_image'], imageThumbVersion::MAX_240) ?>">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">CID</label></td>
                    <td><?php echo $info['obj_guid'];?></td>
                    <td><label class="control-label">Login Account</label></td>
                    <td><?php echo $info['login_code'];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Device Id</label></td>
                    <td><?php echo $info['device_id'];?></td>
                    <td><label class="control-label">Device Name</label></td>
                    <td><?php echo $info['device_name'];?></td>
                    
                </tr>
                <tr>
                    <td><label class="control-label">Phone</label></td>
                    <td><?php echo $info['contact_phone'];?></td>
                    <td><label class="control-label">Create Time</label></td>
                    <td><?php echo timeFormat($info['create_time']);?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Check Remark</label></td>
                    <td colspan="3">
                        <textarea class="form-control" name="remark" style="width: 400px;height: 100px"></textarea>
                        <div class="error_msg"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center">
                        <button type="button" class="btn btn-primary" onclick="checkSubmit();"><i class="fa fa-check"></i><?php echo 'Approve' ?></button>
                        <button type="button" class="btn btn-warning" onclick="checkAbandon();"><i class="fa fa-close"></i><?php echo 'Reject' ?></button>
                        <button type="button" class="btn btn-default" onclick="javascript:window.location.href = '<?php echo getUrl('operator', 'deviceApply', array(), false, BACK_OFFICE_SITE_URL) ?>';"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="uid" value="<?php echo $info['uid']; ?>">
        </form>
    </div>
</div>
<script>

    function checkSubmit(){
        var values = $(".form-horizontal").getValues();
        values.verify_state = '<?php echo commonApproveStateEnum::PASS;?>';
        submitCheck(values);
    }

    function checkAbandon() {
        var values = $(".form-horizontal").getValues();
        values.verify_state = '<?php echo commonApproveStateEnum::REJECT;?>';
        $.messager.confirm("Reject", "Are you sure to reject?", function (_r) {
            if (!_r) return;
            submitCheck(values);
        });
    }

    function submitCheck(values){
        yo.loadData({
            _c: 'operator',
            _m: 'submitCheckDeviceApply',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.href = '<?php echo getUrl('operator', 'deviceApply', array(), false, BACK_OFFICE_SITE_URL);?>';
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

</script>
