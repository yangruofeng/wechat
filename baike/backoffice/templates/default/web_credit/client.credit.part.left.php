<style>
    #mapModal .modal-dialog {
        margin-top: 20px!important;
    }
</style>
<div class="col-sm-6">
    <div class="panel panel-primary panel-item">
        <div class="panel-heading">
            <p class="panel-title">
               Client Info
                <a href="<?php echo getBackOfficeUrl('common', 'showClientDetail', array('country_code'=>$client_info['phone_country'],'phone_number'=>$client_info['phone_number'],'search_by'=>1));?>" class="btn btn-default btn-xs"><i class="fa fa-list"></i></a>
            </p>
        </div>
        <div class="panel-body" style="padding: 5px">
            <dl class="account-basic clearfix" style="width: 100%">
                <dt class="pull-left">
                    <img id="member-icon" src="<?php echo $client_info['member_icon'];?>" class="avatar-lg">
                </dt>
                <dd class="pull-left margin-large-left" style="margin-right: 30px;">
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">CID</span>:
                        <span class="marginleft10" id="login-account"><?php echo $client_info['obj_guid'];?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">Login Code</span>:
                        <span class="marginleft10" id="khmer-name"><?php echo $client_info['login_code'];?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">Client Name</span>:
                        <span class="marginleft10" id="client-name"><?php echo $client_info['display_name'].' / '.$client_info['kh_display_name'] ;?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">Phone</span>:
                        <span class="marginleft10" id="english-name"><?php echo $client_info['phone_id'];?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright3">Status</span>:
                        <span class="marginleft10" id="member-grade"><?php echo $lang['client_member_state_' . $client_info['member_state']];?></span>
                    </p>
                </dd>
                <dd class="pull-left margin-large-left">
                    <?php if ($is_bm) { $member_state = $client_info['member_state'];?>
                        <?php if ($member_state == memberStateEnum::CREATE) { ?>
                            <a class="btn btn-success change-state" title="Current State">New</a>
                        <?php } else { ?>
                            <a class="btn btn-default change-state" onclick="changeMemberState(<?php echo memberStateEnum::CREATE?>)">New</a>
                        <?php } ?>

                        <?php if ($member_state == memberStateEnum::CHECKED) { ?>
                            <a class="btn btn-success change-state" title="Current State">Checked</a>
                        <?php } else { ?>
                            <a class="btn btn-default change-state" onclick="changeMemberState(<?php echo memberStateEnum::CHECKED?>)">Checked</a>
                        <?php } ?>

                        <?php if ($member_state == memberStateEnum::VERIFIED) { ?>
                            <a class="btn btn-success change-state" title="Current State">Verified</a>
                        <?php } else { ?>
                            <a class="btn btn-default change-state" disabled>Verified</a>
                        <?php } ?>

                        <?php if ($member_state == memberStateEnum::TEMP_LOCKING || $member_state == memberStateEnum::SYSTEM_LOCKING) { ?>
                            <a class="btn btn-success change-state" title="Current State">Locked</a>
                        <?php } else { ?>
                            <a class="btn btn-default change-state" onclick="changeMemberState(<?php echo memberStateEnum::TEMP_LOCKING?>)">Locked</a>
                        <?php } ?>

                        <?php if ($member_state == memberStateEnum::CANCEL) { ?>
                            <a class="btn btn-success change-state" title="Current State">Cancel</a>
                        <?php } else { ?>
                            <a class="btn btn-default change-state" onclick="changeMemberState(<?php echo memberStateEnum::CANCEL?>)">Cancel</a>
                        <?php } ?>
                    <?php } ?>
                </dd>
            </dl>
        </div>

        <div class="panel panel-default panel-item">
            <div class="panel-heading" style="padding-top: 2px;padding-bottom: 2px">
                <p class="panel-title">
                    Work
                    <a type="button" class="btn btn-default" href='<?php echo getBackOfficeUrl('web_credit', 'editMemberWorkTypeAndIndustryPage', array('uid'=>$client_info['uid']));?>'>
                        <i class="fa fa-edit"></i>
                    </a>
                </p>
            </div>
            <div class="panel-body">
                <div class="item">
                    <span class="col-first">Work Type: </span>
                    <span class="col-second"><?php echo $work_type_lang[$client_info['work_type']];?></span>
                </div>
                <div class="item">
                    <span class="col-first">Own Business: </span>
                        <span class="col-second">
                            <?php
                            if($client_info['is_with_business'] && count($client_info['member_industry']) > 0){
                                $str = '';$i = 0;
                                foreach ($client_info['member_industry'] as $v) {
                                    $i < (count($client_info['member_industry']) - 1) ? $str .= $v['industry_name'].', ' : $str .= $v['industry_name'];
                                    $i++;
                                }
                            }
                            ?>
                            <?php echo $client_info['is_with_business']?$str:'None';?>
                        </span>
                </div>
            </div>
        </div>
        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Residence
                    <a type="button" class="btn btn-default" href='<?php echo getUrl('web_credit', 'editMemberResidencePage', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>'>
                        <i class="fa fa-edit"></i></a>
                </p>
            </div>
            <div class="panel-body">
                <?php echo $residence ? $residence['full_text'] : 'None'; ?>
                <?php if ($map_detail) { ?>
                    <a href="javascript:void(0)" onclick="showGoogleMap()" style="margin-left: 10px;font-style: italic">Google Map</a>
                <?php } ?>
            </div>
        </div>
        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Branch
                    <a type="button" class="btn btn-default" href='<?php echo getUrl('web_credit', 'editMemberBranchPage', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>'>
                        <i class="fa fa-edit"></i></a>
                </p>
            </div>
            <div class="panel-body">
                <?php echo $client_info['branch_name']?:'None';?>
            </div>
        </div>
        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Operator
                    <button type="button" class="btn btn-default" onclick="javascript:location.href='<?php echo getUrl('web_credit', 'editMemberOperatorPage', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>'">
                        <i class="fa fa-edit"></i></button>
                </p>
            </div>
            <div class="panel-body">
                <?php if($member_operator['user_name']){?>
                    User Code: &nbsp;&nbsp;<?php echo $member_operator['user_code'];?>&nbsp;&nbsp;
                    User Name: &nbsp;&nbsp;<?php echo $member_operator['user_name'];?>&nbsp;&nbsp;
                    Contact Phone: &nbsp;&nbsp;<?php echo $member_operator['mobile_phone'];?>&nbsp;&nbsp;
                <?php }?>
            </div>
        </div>
        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    CO-List
                    <?php if($is_bm){?>
                        <button type="button" class="btn btn-default" <?php if($is_bm){?>onclick="javascript:location.href='<?php echo getUrl('web_credit', 'editMemberCoPage', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>'"<?php }?>>
                            <i class="fa fa-edit"></i>
                        </button>
                    <?php }?>
                </p>
            </div>
            <div class="panel-body">
                <?php if($member_co_list){ ?>
                    <?php foreach ($member_co_list as $v) { ?>
                        <div class="item">
                            <input type="radio" value="<?php echo $v['uid'];?>" name="primary_co" <?php echo  $v['is_primary']? 'checked': ''?>>
                            <span class="col-first"><?php echo $v['user_code'];?></span>&nbsp;&nbsp;
                            <span class="col-first"><?php echo $v['officer_name'];?></span>&nbsp;&nbsp;
                            <span class="col-second"><?php echo $v['mobile_phone'];?></span>
                            <?php if( $v['is_primary'] ){ ?>
                                <label for="" class="label label-info">Primary CO</label>
                            <?php } ?>
                        </div>
                        <?php if($output['co_submit_task'][$v['officer_id']]){?>
                            <div>
                                <?php $co_task=$output['co_submit_task'][$v['officer_id']]; ?>
                                <blockquote style="font-size: 10px">
                                    <p><?php echo $co_task['submit_comment']?></p>
                                    <footer>
                                        Submit Time: <?php echo $co_task['submit_time']?>
                                        <?php if($output['is_bm'] && $co_task['state']==commonApproveStateEnum::APPROVING){?>
                                            <button class="btn btn-default btn-xs" onclick="btn_handle_co_submit('<?php echo $co_task["uid"]?>',1)"  style="line-height: 1;font-size: 10px">Accept</button>
                                            <button class="btn btn-default btn-xs" onclick="btn_handle_co_submit('<?php echo $co_task["uid"]?>',-1)" style="line-height: 1;font-size: 10px">Reject</button>
                                        <?php }?>

                                    </footer>
                                </blockquote>

                            </div>
                        <?php }?>
                    <?php  } ?>
                <?php }else{?>
                    No Record
                <?php }?>
                <br/>
                <a class="btn btn-default" onclick="setPrimary('<?php echo $client_info['uid'];?>')">Set Primary</a>
                <?php if($is_bm){?>
                    <?php if ($client_info['member_property'][memberPropertyKeyEnum::LOCK_FOR_CO] != 1) { ?>
                        <a class="btn btn-default" href="#" onclick="lock_credit_officer('<?php echo $client_info['uid'];?>')" style="margin-left: 10px">Lock For Co</a>
                    <?php } else { ?>
                        <a class="btn btn-default" href="#" onclick="unlock_credit_officer('<?php echo $client_info['uid'];?>')" style="margin-left: 10px">Unlock  For Co</a>
                    <?php } ?>
                <?php }?>
            </div>

        </div>

        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Credit-Controller
                    <?php if($is_bm){?>
                        <button type="button" class="btn btn-default" <?php if($is_bm){?>onclick="javascript:location.href='<?php echo getUrl('web_credit_v2', 'editMemberCCPage', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>'"<?php }?>>
                            <i class="fa fa-edit"></i>
                        </button>
                    <?php }?>
                </p>
            </div>
            <div class="panel-body">
                <?php $member_cc_list=$output['member_cc_list']?>
                <?php if($member_cc_list){ ?>
                    <?php foreach ($member_cc_list as $v) { ?>
                        <div class="item">
                            <span class="col-first"><?php echo $v['user_code'];?></span>&nbsp;&nbsp;
                            <span class="col-first"><?php echo $v['officer_name'];?></span>&nbsp;&nbsp;
                            <span class="col-second"><?php echo $v['mobile_phone'];?></span>
                        </div>
                    <?php  } ?>
                <?php }else{?>
                    No Record
                <?php }?>
            </div>
        </div>

        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Risk-Controller
                    <?php if($is_bm){?>
                        <button type="button" class="btn btn-default" <?php if($is_bm){?>onclick="javascript:location.href='<?php echo getUrl('web_credit_v2', 'editMemberRCPage', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>'"<?php }?>>
                            <i class="fa fa-edit"></i>
                        </button>
                    <?php }?>
                </p>
            </div>
            <div class="panel-body">
                <?php $member_rc_list=$output['member_rc_list']?>
                <?php if($member_rc_list){ ?>
                    <?php foreach ($member_rc_list as $v) { ?>
                        <div class="item">
                            <span class="col-first"><?php echo $v['user_code'];?></span>&nbsp;&nbsp;
                            <span class="col-first"><?php echo $v['officer_name'];?></span>&nbsp;&nbsp;
                            <span class="col-second"><?php echo $v['mobile_phone'];?></span>
                        </div>
                    <?php  } ?>
                <?php }else{?>
                    No Record
                <?php }?>
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
                            <td><?php echo $val['name']?></td>
                            <td>
                            <?php
                                $str = '';
                                $label = '';
                                if(empty($val['detail'])){
                                    $str = 'None';
                                    $label = 'label-default';
                                }else{
                                    switch($val['detail']['verify_state']){
                                        case certStateEnum::LOCK:
                                            $str = 'LOCK';
                                            $label = 'label-default';
                                            break;
                                        case certStateEnum::CREATE:
                                            $str = 'CREATE';
                                            $label = 'label-primary';
                                            break;
                                        case certStateEnum::PASS:
                                            $str = 'PASS';
                                            $label = 'label-success';
                                            break;
                                        case certStateEnum::EXPIRED:
                                            $str = 'EXPIRED';
                                            $label = 'label-warning';
                                            break;
                                        case certStateEnum::NOT_PASS:
                                            $str = 'NOT PASS';
                                            $label = 'label-danger';
                                            break;
                                        default:
                                            break;
                                }
                            }?>
                                <span class="label <?php echo $label;?>"><?php echo $str;?></span>

                            </td>
                            <td>
                                <?php if($val['detail']){?>
                                    <?php if($val['detail']['verify_state'] != certStateEnum::LOCK){?>
                                        <a href="<?php echo getUrl("web_credit", "editUploadClientIdentity", array("member_id" => $client_info['uid'],"cert_id" => $val['detail']['uid'], "identity_type" => $key), false, BACK_OFFICE_SITE_URL) ?>" class="btn btn-default">Edit</a>
                                    <?php }?>
                                <?php }else{ ?>
                                    <a href="<?php echo getUrl("web_credit", "uploadClientIdentity", array("member_id" => $client_info['uid'], "identity_type" => $key), false, BACK_OFFICE_SITE_URL) ?>" class="btn btn-default">Add</a>
                                <?php } ?>
                                <?php if($val['detail']){?>
                                    <a href="<?php echo getUrl("client", "showCertificationDetail", array("uid" => $val['detail']['uid'], 'source_mark' => 'op_suggest'), false, BACK_OFFICE_SITE_URL) ?>" class="btn btn-default">Detail</a>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($val['is_new']) { ?>
                                    <span style="color: #c7254e;padding-right: 15px">New upload</span>
                                <?php } ?>
                                <?php if ($val['expired_time']) { ?>
                                    <span style="color: #c7254e;">Expired at <?php echo $val['expired_time']?></span>
                                <?php } ?>
                                <?php if ($val['will_be_expired']) { ?>
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
                    CBC-Record
                    <a type="button" class="btn btn-default" href="<?php echo getUrl('web_credit', 'addMemberCbcPage', array('member_id' => $client_info['uid'], 'client_id' => $client_info['uid'], "client_type" => 0), false, BACK_OFFICE_SITE_URL);?>">
                        <i class="fa fa-plus"></i>
                    </a>
                </p>
            </div>
            <div class="panel-body">
                <?php if($member_cbc){ ?>
                    <table class="table table-no-background">
                        <tr>
                            <td>Creator</td>
                            <td>PayToCBC</td>
                            <td>SRS Old Loan</td>
                            <td>Time</td>
                            <td>Function</td>
                        </tr>
                        <?php foreach ($member_cbc as $v) { ?>
                            <tr>
                                <td><?php echo $v['creator_name']?></td>
                                <td><?php echo $v['pay_to_cbc']?></td>
                                <td><?php echo $v['pay_to_srs']?></td>
                                <td><?php echo timeFormat($v['create_time']);?></td>
                                <td>
                                    <a class="btn btn-default" href="<?php echo getUrl('web_credit', 'showMemberCbcDetail', array('uid'=>$v['uid'],'member_id'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php }?>
                    </table>
                <?php }else{?>
                    None
                <?php }?>
            </div>
        </div>
        <div class="panel panel-default panel-item">
            <div class="panel-heading">
                <p class="panel-title">
                    Authority
                </p>
            </div>
            <div class="panel-body">
                <table class="table">
                    <?php foreach($output['author_list'] as $author_item){?>
                        <tr>
                            <td>
                                <?php echo $author_item['auth_text']?>
                            </td>
                            <td>
                                <i class="fa fa-check" style="<?php if(!$author_item['is_active']) echo 'display:None'?>">

                                </i>
                                <i class="fa fa-close" style="<?php if($author_item['is_active']) echo 'display:None'?>">

                                </i>
                            </td>
                            <td>
                                <button class="btn btn-default btn-xs btn-lock"
                                        style="<?php if(!$author_item['is_active']) echo 'display:None'?>"
                                        data-member-id="<?php echo $client_info['uid']?>"
                                        data-auth-key="<?php echo $author_item['auth_key']?>"
                                        onclick="btn_lock_authority_onclick(this)">
                                    Lock
                                </button>
                                <button class="btn btn-default btn-xs btn-unlock"
                                        style="<?php if($author_item['is_active']) echo 'display:None'?>"
                                        data-member-id="<?php echo $client_info['uid']?>"
                                        data-auth-key="<?php echo $author_item['auth_key']?>"
                                        onclick="btn_unlock_authority_onclick(this)">
                                    Un Lock
                                </button>
                            </td>
                        </tr>

                    <?php }?>
                </table>

            </div>
        </div>
    </div>
</div>
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Change Remark</h4>
            </div>
            <div class="modal-body">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="state_form">
                        <input type="hidden" name="member_id" value="<?php echo $client_info['uid']; ?>">
                        <input type="hidden" name="member_state" value="">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Remark</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="remark"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="submit_change()"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 1000px;height: 660px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Residence Location'?></h4>
            </div>
            <div class="modal-body">
                <div id="map-canvas">
                    <?php
                    $point = array('x' => $map_detail['coord_x'], 'y' => $map_detail['coord_y']);
                    include_once(template("widget/google.map.point"));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function showGoogleMap() {
        $('#mapModal').modal('show');
    }

    function lock_credit_officer(member_id) {
        if (!member_id) {
            return;
        }

        $.messager.confirm("Confirm","Are you sure to lock for co?",function(_r){
            if(!_r) return;
            yo.loadData({
                _c: "web_credit",
                _m: "lockClientForCreditOfficer",
                param: {member_id: member_id},
                callback: function (_o) {
                    if (_o.STS) {
                        alert(_o.MSG,1,function(){
                            window.location.reload();
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }

    function unlock_credit_officer(member_id) {
        if (!member_id) {
            return;
        }

        yo.confirm("Confirm","Are you sure to unlock for co?",function(_r){
            if(!_r) return;
            yo.loadData({
                _c: "web_credit",
                _m: "unlockClientForCreditOfficer",
                param: {member_id: member_id},
                callback: function (_o) {
                    if (_o.STS) {
                        alert(_o.MSG,1,function(){
                            window.location.reload();
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }

    function setPrimary(member_id) {
        var primary_id = $('input[name="primary_co"]:checked').val();
        if(!primary_id){
            alert('please choose co');
            return
        }
        yo.loadData({
            _c: "web_credit",
            _m: "setPrimaryCo",
            param: {
                    primary_id: primary_id,
                    member_id: member_id
            },
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

    function changeMemberState(memberState) {
        $('#myModal [name="member_state"]').val(memberState);
        $('#myModal').modal('show');
    }

    function submit_change() {
        var _values = $('#state_form').getValues();
        if(!_values.remark){
            alert('Please input remark.');
            return;
        }

        yo.loadData({
            _c: "web_credit",
            _m: "changeMemberState",
            param: _values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });

                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }
    function btn_handle_co_submit(_task_id,_sts){
        if(_sts==-1){
            //reject
            yo.dialog.prompt("Reject","Please Input Comment",function(_r){
                if(!_r) return;
                yo.loadData({
                    _c:"web_credit",
                    _m:"handleCOSubmit",
                    param:{task_id:_task_id,sts:0,msg:_r},
                    callback:function(_o){
                        if(_o.STS){
                            window.location.reload();
                        }else{
                            alert(_o.MSG,2);
                        }
                    }
                });

            });
        }else{
            //accept
            yo.confirm("Confirm","Are you sure lock co to edit?",function(_r){
                if(!_r) return;
                yo.loadData({
                    _c:"web_credit",
                    _m:"handleCOSubmit",
                    param:{task_id:_task_id,sts:1},
                    callback:function(_o){
                        if(_o.STS){
                            window.location.reload();
                        }else{
                            alert(_o.MSG);
                        }
                    }
                });
            });

        }

    }
    function btn_lock_authority_onclick(_e){
        $.messager.confirm("confirm","Are you sure lock this authority?",function(_r){
            if(!_r){
                return false;
            }
            $(document).waiting();
            var _member_id=$(_e).data("member-id");
            var _auth_type=$(_e).data("auth-key");
            yo.loadData({
                _c:"web_credit_v2",
                _m:"ajaxLockMemberAuthority",
                param:{member_id:_member_id,auth_type:_auth_type,state:0},
                callback:function(_o){
                    $(document).unmask();
                    if(_o.STS){
                        $(_e).closest("tr").find(".fa-check").hide();
                        $(_e).closest("tr").find(".fa-close").show();
                        $(_e).closest("tr").find(".btn-lock").hide();
                        $(_e).closest("tr").find(".btn-unlock").show();
                    }else{
                        alert(_o.MSG);
                    }

                }
            })
        });
    }
    function btn_unlock_authority_onclick(_e){
        $.messager.confirm("confirm","Are you sure unlock this authority?",function(_r){
            if(!_r){
                return false;
            }
            $(document).waiting();
            var _member_id=$(_e).data("member-id");
            var _auth_type=$(_e).data("auth-key");
            yo.loadData({
                _c:"web_credit_v2",
                _m:"ajaxLockMemberAuthority",
                param:{member_id:_member_id,auth_type:_auth_type,state:1},
                callback:function(_o){
                    $(document).unmask();
                    if(_o.STS){
                        $(_e).closest("tr").find(".fa-check").show();
                        $(_e).closest("tr").find(".fa-close").hide();
                        $(_e).closest("tr").find(".btn-lock").show();
                        $(_e).closest("tr").find(".btn-unlock").hide();
                    }else{
                        alert(_o.MSG);
                    }

                }
            })
        });
    }

</script>