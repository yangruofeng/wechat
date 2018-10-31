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
$work_type_lang=enum_langClass::getWorkTypeEnumLang();
$member_industry_key=array_keys($output['member_industry']);
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>New Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'newClient', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Audit</span></a></li>
            </ul>
        </div>
    </div>
    <?php $client_info = $output['client_info']?>
    <div class="container">
        <form class="form-horizontal">
            <table class="table audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Member Icon</label></td>
                    <td colspan="3">
                        <a target="_blank" href="<?php echo getImageUrl($client_info['member_icon']) ?>">
                            <img style="max-height: 100px;max-width: 200px" src="<?php echo getImageUrl($client_info['member_icon'], imageThumbVersion::MAX_240) ?>">
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
                    <td><label class="control-label">Display Name</label></td>
                    <td><?php echo $client_info['display_name'];?></td>
                    <td><label class="control-label">Phone</label></td>
                    <td><?php echo $client_info['phone_id'];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Email</label></td>
                    <td><?php echo $client_info['email'];?></td>
                    <td><label class="control-label">Member State</label></td>
                    <td><?php echo $lang['client_member_state_' . $client_info['member_state']];?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Check State</label></td>
                    <td><?php echo $lang['operator_task_state_' .$client_info['operate_state']];?></td>
                    <td><label class="control-label">Register Time</label></td>
                    <td><?php echo timeFormat($client_info['create_time']);?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Work Type</label></td>
                    <td colspan="3">
                        <select class="form-control" name="work_type" style="width: 250px">
                            <option value="">Please Select</option>
                            <?php foreach ($output['work_type'] as $key => $type) {?>
                                <option value="<?php echo $key?>" <?php echo $key == $client_info['work_type'] ? 'selected' : ''?>><?php echo $work_type_lang[$key]?></option>
                            <?php } ?>
                        </select>

                    </td>
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
                        <button type="button" class="btn btn-default" onclick="javascript:window.location.href = '<?php echo getUrl('operator', 'newClient', array(), false, BACK_OFFICE_SITE_URL) ?>';"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="uid" value="<?php echo $client_info['uid']; ?>">
        </form>
    </div>
</div>

<div class="modal" id="branchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Branch List'?></h4>
            </div>
            <div class="modal-body" style="margin-bottom: 20px">
                <div class="business-condition">
                    <form class="form-inline" id="frm_search_condition">
                        <table class="search-table">
                            <tr>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for branch" style="min-width: 150px">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                                <i class="fa fa-search"></i>
                                                <?php echo 'Search'; ?>
                                            </button>
                                         </span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="modal-table">
                    <div>
                        <table class="table table-bordered">
                            <thead>
                            <tr class="table-header" style="background-color: #EEE">
                                <td>CID</td>
                                <td>Branch Name</td>
                                <td>Address</td>
                                <td>Function</td>
                            </tr>
                            </thead>
                            <tbody class="table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/validform/jquery.validate.min.js?v=2"></script>
<script>
    $(function () {
        $('input[name="verify_state"]').click(function () {
            var verify_state = $(this).val();
            if (verify_state == 'allot') {
                $('.branch').show();
                $('.branch_default').hide();
            } else {
                $('.branch').hide();
                $('.branch_default').show();
            }
        });
    });

    function showSelectOc() {
        btn_search_onclick();
        $('#branchModal').modal('show');
    }

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _search_text = $('#search_text').val();
        yo.dynamicTpl({
            tpl: "operator/branch.list",
            dynamic: {
                api: "operator",
                method: "getBranchList",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    search_text: _search_text
                }
            },
            callback: function (_tpl) {
                $("#branchModal .modal-table").html(_tpl);
            }
        });
    }

    function selectBranch(branch_id, branch_name) {
        $('.branch .branch_name').text(branch_name);
        $('.branch input[name="branch_id"]').val(branch_id);
        $('#branchModal').modal('hide');
    }

    function checkSubmit(){
        if (!$(".form-horizontal").valid()) {
            return;
        }
        var values = $(".form-horizontal").getValues();
        values.verify_state = '<?php echo newMemberCheckStateEnum::PASS;?>';
        submitCheck(values);
    }

    function checkAbandon() {
        var values = $(".form-horizontal").getValues();
        values.verify_state = '<?php echo newMemberCheckStateEnum::CLOSE;?>';
        $.messager.confirm("Reject", "Are you sure to reject?", function (_r) {
            if (!_r) return;
            submitCheck(values);
        });
    }

    function submitCheck(values){
        yo.loadData({
            _c: 'operator',
            _m: 'submitCheckClient',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.href = '<?php echo getUrl('operator', 'newClient', array(), false, BACK_OFFICE_SITE_URL);?>';
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            remark: {
                required: true
            }
        },
        messages: {
            remark: {
                required: '<?php echo 'Required'?>'
            }
        }
    });

    jQuery.validator.addMethod("checkRequired", function (value, element) {
        var verify_state = $('input[name="verify_state"]:checked').val();
        if (verify_state == 'allot' && !value) {
            return false;
        } else {
            return true;
        }
    });
</script>
