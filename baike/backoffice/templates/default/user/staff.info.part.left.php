<div class="col-sm-6">
    <div class="panel panel-primary panel-item">
        <div class="panel-heading">
            <p class="panel-title">
               Staff Info
                <a href="<?php echo getBackOfficeUrl('user', 'editStaff', array('uid' => $staff_info['uid'])); ?>"
                   class="btn btn-default btn-xs">
                    <i class="fa fa-edit"></i>
                </a>
            </p>
        </div>
        <div class="panel-body" style="padding: 5px">
            <dl class="account-basic clearfix" style="width: 100%">
                <dt class="pull-left">
                    <img id="member-icon" src="<?php echo getImageUrl($staff_info['staff_icon'], null, null); ?>" class="avatar-lg">
                </dt>
                <dd class="pull-left margin-large-left" style="margin-right: 30px;">
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">CID :</span>
                        <span class="marginleft10" id="login-account"><?php echo $staff_info['obj_guid'];?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">Display Name :</span>
                        <span class="marginleft10" id="khmer-name"><?php echo $staff_info['display_name'];?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">Phone :</span>
                        <span class="marginleft10" id="english-name"><?php echo $staff_info['mobile_phone'];?></span>
                    </p>
                    <?php $staff_status = $staff_info['staff_status'];?>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">Status :</span>
                        <span class="marginleft10" id="english-name"><?php echo $lang['staff_status_' . $staff_status]; ?></span>
                    </p>
                </dd>
                <dd class="pull-left margin-large-left">
                    <?php if ($staff_status < staffStatusEnum::NORMAL_DIMISSION) { ?>
                        <?php foreach ($staff_status_list as $key => $status) {
                            if($key < $staff_status) continue;
                            ?>
                            <?php if ($staff_status == $key) { ?>
                                <button class="btn btn-success change-state" title="Current State"><?php echo $lang['staff_status_' . $key]; ?></button>
                            <?php } else { ?>
                                <a class="btn btn-default change-state" onclick="changeStaffState(<?php echo $key; ?>)"><?php echo $lang['staff_status_' . $key]; ?></a>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </dd>
            </dl>
        </div>

        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Branch/ Department
                </p>
            </div>
            <div class="panel-body">
                <?php echo $staff_info['branch_name'] . '/ ' . $staff_info['depart_name'];?>
            </div>
        </div>
        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Position
                </p>
            </div>
            <div class="panel-body">
                <?php echo ucwords(str_replace('_', ' ', $staff_info['staff_position']));?>
            </div>
        </div>
        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Entry Time
                </p>
            </div>
            <div class="panel-body">
                <?php echo dateFormat($staff_info['entry_time']);?>
            </div>
        </div>
        <?php if ($staff_info['um_account']) { ?>
            <div class="panel panel-default panel-item">
                <div class="panel-heading">
                    <p class="panel-title">
                        Um-Account
                    </p>
                </div>
                <div class="panel-body">
                    <?php echo $staff_info['um_account']; ?>
                </div>
            </div>
        <?php } ?>
        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Residence
                </p>
            </div>
            <div class="panel-body">
                <?php echo $staff_info['staff_address'];?>
            </div>
        </div>
        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Identity Information
                </p>
            </div>
            <div class="panel-body">
                <table class="table table-no-background">
                    <?php foreach ($identity_list as $key => $val) { ?>
                        <tr>
                            <td style="width: 150px"><?php echo $val['name']?></td>
                            <td style="width: 100px">
                                <?php if (empty($val['detail'])) { ?>
                                    <span class="label label-default">None</span>
                                <?php } elseif ($val['detail']['identity_state'] == 1) { ?>
                                    <span class="label label-success">Uploaded</span>
                                <?php } else { ?>
                                    <span class="label label-danger">Expired</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($val['detail']) { ?>
                                    <a href="<?php echo getUrl("user", "editStaffIdentity", array("staff_id" => $staff_info['uid'], 'identity_id'=>$val['detail']['uid'], "identity_type" => $key), false, BACK_OFFICE_SITE_URL) ?>"
                                       class="btn btn-default"><?php echo 'Edit' ?></a>
                                    <a href="<?php echo getUrl("user", "showStaffIdentity", array("uid" => $val['detail']['uid']), false, BACK_OFFICE_SITE_URL) ?>"
                                       class="btn btn-default">Detail</a>
                                <?php } else { ?>
                                    <a href="<?php echo getUrl("user", "addStaffIdentity", array("staff_id" => $staff_info['uid'], "identity_type" => $key), false, BACK_OFFICE_SITE_URL) ?>"
                                       class="btn btn-default"><?php echo 'Add'?></a>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($val['detail']['identity_state'] == 1 && $val['expired_time']) { ?>
                                    <span style="color: #c7254e;">Expired at <?php echo $val['expired_time']?></span>
                                <?php } ?>
                                <?php if ($val['detail']['identity_state'] == 1 && $val['will_be_expired']) { ?>
                                    <span style="color: #c7254e;">Will be expired at <?php echo $val['will_be_expired']?></span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Remark
                </p>
            </div>
            <div class="panel-body">
                <?php echo $staff_info['remark'];?>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Input Remark</h4>
            </div>
            <div class="modal-body">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="state_form">
                        <input type="hidden" name="staff_id" value="<?php echo $staff_info['uid']; ?>">
                        <input type="hidden" name="staff_state" value="">
                        <div class="form-group">
<!--                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Remark</label>-->
                            <div class="col-sm-12">
                                <textarea class="form-control" name="remark"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="submitChange()"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script>

    function changeStaffState(memberState) {
        $('#myModal [name="staff_state"]').val(memberState);
        $('#myModal').modal('show');
    }

    function submitChange() {
        var _values = $('#state_form').getValues();
        if(!_values.remark){
            alert('Please input remark.');
            return;
        }

        yo.loadData({
            _c: "user",
            _m: "changeStaffStatus",
            param: _values,
            callback: function (_o) {
                if (_o.STS) {
                    $('#myModal').modal('hide');
                    alert(_o.MSG, 1, function () {
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

</script>