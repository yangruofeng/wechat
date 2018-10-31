<div>
    <?php $info = $data['info'];$reset_pwd = $data['reset_pwd'];?>
    <?php if($info){?>
        <div class="info">
            <div class="item">
                <label for="">Member State: </label>
                <?php $str = '';
                    switch ($info['member_state']) {
                        case memberStateEnum::CANCEL :
                            $str = 'Cancel';
                            break;
                        case memberStateEnum::CREATE :
                            $str = 'Create';
                            break;
                        case memberStateEnum::CHECKED :
                            $str = 'Checked';
                            break;
                        case memberStateEnum::TEMP_LOCKING :
                            $str = 'Temp Locking';
                            break;
                        case memberStateEnum::SYSTEM_LOCKING :
                            $str = 'System Locking';
                            break;
                        case memberStateEnum::VERIFIED :
                            $str = 'Verified';
                            break;
                        default:
                            # code...
                            break;
                    }
                    echo $str;
                ?>
            </div>
            <div class="item">
                <label for="">Credit: </label>
                <?php echo ncPriceFormat($info['credit']);?>
            </div>
            <div class="item">
                <label for="">Credit Balance: </label>
                <?php echo ncPriceFormat($info['credit_balance']);?>
            </div>
        </div>
        <div style="margin-top: 10px;">
            <button type="button" class="btn btn-info" onclick="adjustState()"><i class="fa fa-check"></i> Adjust State</button>
            <button type="button" class="btn btn-success" <?php if($reset_pwd['close_reset_password']){?>disabled<?php }?> onclick="resetPassword()"><i class="fa fa-check"></i> Reset Password</button>
            <button type="button" class="btn btn-danger" onclick="deleteMember()"><i class="fa fa-trash"></i> Delete</button>
        </div>
    <?php }else{ ?>
        <div class="no-search">please search member.</div>
    <?php } ?>
</div>

<div class="modal" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo 'Reset Password'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="resetPasswordForm">
                        <input type="hidden" name="uid" value="">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Password'?></label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="password" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Confirm Password'?></label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="confirm_password" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="submitNewPassword();"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="adjustStateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo 'Adjust Member State'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="adjustStateForm">
                        <input type="hidden" name="uid" value="">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo 'State'?></label>
                            <div class="col-sm-8">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="member_state" value="<?php echo memberStateEnum::CANCEL;?>">
                                        Cancel
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="member_state"  value="<?php echo memberStateEnum::CREATE;?>">
                                        Create
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="member_state"  value="<?php echo memberStateEnum::CHECKED;?>">
                                        Checked
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="member_state"  value="<?php echo memberStateEnum::TEMP_LOCKING;?>">
                                        Locking
                                    </label>
                                </div>

                                <div class="radio">
                                    <label>
                                        <input type="radio" name="member_state"  value="<?php echo memberStateEnum::VERIFIED;?>">
                                        Verified
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="submitMemberState();"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script>
    var uid = '<?php echo $data['info']['uid'];?>', state = '<?php echo $data['info']['member_state'];?>';

    function adjustState(){
        if(!uid) return;
        $('#adjustStateForm input[name="uid"]').val(uid);
        $('#adjustStateForm :radio[name="member_state"][value="'+state+'"]').prop('checked',true);
        $('#adjustStateModal').modal('show');
    }
    function submitMemberState(){
        var values = $('#adjustStateForm').getValues();
        yo.loadData({
            _c: 'dev',
            _m: 'submitAdjustStateAccount',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    $('#adjustStateModal').modal('hide');
                    search_click();
                } else {
                    alert(_o.MSG);
                }
            }
        });

    }

    function resetPassword(){
        if(!uid) return;
        $('#resetPasswordForm input[name="uid"]').val(uid);
        $('#resetPasswordModal').modal('show');
    }
    function submitNewPassword(){
        var values = $('#resetPasswordForm').getValues();
        var pwd = values.password, c_pwd = values.confirm_password;
        if(pwd != c_pwd){
            alert('Please input the same password.')
            return;
        }
        yo.loadData({
            _c: 'dev',
            _m: 'submitResetPassword',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    $('#resetPasswordModal').modal('hide');
                    search_click();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function deleteMember(){
        if(!uid) return;
        $.messager.confirm("Confirm", "<?php echo 'Are you sure you want to delete this member?' ?>", function(r){
            if (r) {
                yo.loadData({
                    _c: 'dev',
                    _m: 'deleteMember',
                    param: {uid: uid},
                    callback: function (_o) {
                        if (_o.STS) {
                            search_click();
                        } else {
                            alert(_o.MSG);
                        }
                    }
                });
            }
        });
    }
</script>