<style>
    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    #branchModal .modal-dialog {
        margin-top: 10px !important;
    }

    #branchModal .easyui-panel {
        background-color: #EEE;
    }
</style>
<?php
$client_info = $output['member_info'];
$request_info = $output['request'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Request Detail</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'clientChangeTradingPasswordIndex', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <h4>Client Change Trading Password Request </h4>
        <form class="form-horizontal" id="frm_change_trading_password_check">

            <input type="hidden" name="act" value="operator">
            <input type="hidden" name="op" value="clientChangeTradingPasswordDetail">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $request_info['uid']; ?>">

            <input type="hidden" name="check_result" value="-1">

            <table class="table table-no-background audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Client Photo</label></td>
                    <td>
                        <a target="_blank" href="<?php echo getImageUrl($client_info['member_icon']) ?>">
                            <img style="max-height: 100px;max-width: 200px" src="<?php echo getImageUrl($client_info['member_icon']); ?>">
                        </a>
                    </td>
                    <td><label class="control-label">Request Photo</label></td>
                    <td>
                        <a target="_blank" href="<?php echo getImageUrl($request_info['member_image']) ?>">
                            <img style="max-height: 100px;max-width: 200px" src="<?php echo getImageUrl($request_info['member_image']); ?>">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">CID</label></td>
                    <td><?php echo $client_info['obj_guid'];?></td>
                    <td><label class="control-label">Login Account</label></td>
                    <td><?php echo $client_info['login_code'];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Client Name</label></td>
                    <td><?php echo $client_info['display_name'];?></td>
                    <td><label class="control-label">Phone</label></td>
                    <td><?php echo $client_info['phone_id'];?></td>
                </tr>


                <?php if( $request_info['state'] == commonApproveStateEnum::APPROVING || $request_info['state'] == commonApproveStateEnum::CREATE ){ ?>
                    <tr>
                        <td><label class="control-label">Check Remark</label></td>
                        <td colspan="3">
                            <textarea class="form-control" name="remark" style="width: 400px;height: 100px"></textarea>
                            <div class="error_msg"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td colspan="3">
                            <input type="hidden" name="form_submit" value="ok">
                            <button type="button" class="btn btn-primary" onclick="checkSubmit();"><i class="fa fa-check"></i><?php echo 'Approve' ?></button>
                            <button type="button" class="btn btn-default" onclick="checkAbandon();"><i class="fa fa-close"></i><?php echo 'Reject' ?></button>
                            <button type="button" class="btn btn-danger" onclick="javascript:history.back();"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>

                        </td>

                    </tr>
                <?php }else{ ?>
                    <tr>
                        <td><label class="control-label">Check Remark</label></td>
                        <td >
                            <?php echo $request_info['remark'];?>
                        </td>
                        <td><label class="control-label">Check Time</label></td>
                        <td >
                            <?php echo timeFormat($request_info['update_time']);?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

        </form>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/validform/jquery.validate.min.js?v=2"></script>
<script>
    function checkSubmit(){
        if (!$("#frm_change_trading_password_check").valid()) {
            return false;
        }
        $('input[name="check_result"]').val(1);
        $("#frm_change_trading_password_check").submit();
    }

    function checkAbandon() {

        $('input[name="check_result"]').val(-1);
        $.messager.confirm("Reject", "Are you sure to reject?", function (_r) {
            if (!_r) return;
            $("#frm_change_trading_password_check").submit();
        });
    }

    $('#frm_change_trading_password_check').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            remark: {
                required: false
            }
        },
        messages: {
            remark: {
                required: '<?php echo 'Required'?>'
            }
        }
    });

</script>
