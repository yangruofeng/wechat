<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .audit-table tr td:first-child {
        width: 200px;
    }

    .audit-table textarea {
        width: 300px;
        height: 80px;
        float: left;
    }

    .custom-btn-group {
        float: inherit;
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    #coModal .modal-dialog {
        margin-top: 10px !important;
    }

    #coModal .easyui-panel {
        border: 1px solid #DDD;
    }
</style>
<?php
$loanApplySourceLang = enum_langClass::getLoanApplySourceLang();
$unit_lang = enum_langClass::getLoanTimeUnitLang();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Loan Consult</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('branch_manager', 'loanConsult', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Handle</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <form class="form-horizontal">
            <table class="table audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Name</label></td>
                    <td><?php echo $output['apply_info']['applicant_name'] ?></td>
                    <td><label class="control-label">Apply Amount</label></td>
                    <td><?php echo ncPriceFormat($output['apply_info']['apply_amount']).$output['apply_info']['currency']; ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan Time</label></td>
                    <td><?php echo $output['apply_info']['loan_time'].' '.$unit_lang[$output['apply_info']['loan_time_unit']]; ?></td>
                    <td><label class="control-label">Loan Purpose</label></td>
                    <td><?php echo $output['apply_info']['loan_purpose'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan Mortgage</label></td>
                    <td><?php echo $output['apply_info']['mortgage'] ?></td>
                    <td><label class="control-label">Contact Phone</label></td>
                    <td><?php echo $output['apply_info']['contact_phone'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Address</label></td>
                    <td><?php echo $output['apply_info']['applicant_address'] ?></td>
                    <td><label class="control-label">Memo</label></td>
                    <td>
                        <?php echo $output['apply_info']['memo'] ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Apply Time</label></td>
                    <td><?php echo timeFormat($output['apply_info']['create_time']) ?></td>
                    <td><label class="control-label">Apply Source</label></td>
                    <td><?php echo ucwords(str_replace('_', '', $output['apply_info']['request_source'])); ?></td>
                </tr>
                <?php if ($output['apply_info']['operator_id']) { ?>
                <tr>
                    <td><label class="control-label">Operator</label></td>
                    <td><?php echo $output['apply_info']['operator_name'] ?></td>
                    <td><label class="control-label">Operator Remark</label></td>
                    <td><?php echo $output['apply_info']['operator_remark']; ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td><label class="control-label">State</label></td>
                    <td>
                        <?php echo $lang['loan_contract_state_' . $output['apply_info']['state']]; ?>
                    </td>
                    <td><label class="control-label">Credit Officer</label></td>
                    <td class="co">
                        <span class="co_name"><?php echo $output['apply_info']['co_name']; ?></span>
                        <input type="hidden" name="co_id" value=""/>
                        <?php if($output['apply_info']['state'] < loanConsultStateEnum::CO_CANCEL) {?>
                            <a href="#" onclick="showOc();"><i class="fa fa-check-square-o"></i><?php echo 'Select' ?></a>
                            <div class="error_msg"></div>
                        <?php }?>
                    </td>
                </tr>
<!--                --><?php //if(in_array($output['apply_info']['state'],array(loanConsultStateEnum::CREATE,loanConsultStateEnum::ALLOT_BRANCH,loanConsultStateEnum::OPERATOR_APPROVED))) {?>
                <tr>
                    <td><label class="control-label">Audit Remark</label></td>
                    <td colspan="3">
                        <textarea class="form-control" name="remark" style="width: 400px;height: 100px"></textarea>
                        <div class="error_msg"></div>
                    </td>
                </tr>
<!--                --><?php //}?>
                <?php if($output['apply_info']['state'] >= loanConsultStateEnum::CO_CANCEL) {?>
                    <tr>
                        <td><label class="control-label">CO Remark</label></td>
                        <td colspan="3"><?php echo $output['apply_info']['co_remark'] ?></td>
                    </tr>
                <?php }?>
                <tr>
                    <td colspan="4" style="text-align: center">
                        <button type="button" class="btn btn-primary" onclick="allotSubmit();"><i class="fa fa-check"></i><?php echo 'Submit' ?></button>
                        <button type="button" class="btn btn-warning" onclick="reject();"><i class="fa fa-close"></i><?php echo 'Reject' ?></button>
                        <button type="button" class="btn btn-default" onclick="javascript:window.location.href = '<?php echo getUrl('branch_manager', 'loanConsult', array(), false, BACK_OFFICE_SITE_URL) ?>';"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="uid" value="<?php echo $output['apply_info']['uid']; ?>">
        </form>
    </div>
</div>

<div class="modal" id="coModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Co List'?></h4>
            </div>
            <div class="modal-body" style="margin-bottom: 20px">
                <div class="business-condition">
                    <form class="form-inline" id="frm_search_condition">
                        <table class="search-table">
                            <tr>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for name" style="min-width: 150px">
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
                                <td>Co Name</td>
                                <td>Phone</td>
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
    function showOc() {
        btn_search_onclick();
        $('#coModal').modal('show');
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
            tpl: "branch_manager/co.select.list",
            dynamic: {
                api: "branch_manager",
                method: "getCoList",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    search_text: _search_text
                }
            },
            callback: function (_tpl) {
                $("#coModal .modal-table").html(_tpl);
            }
        });
    }

    function selectCo(user_id, user_name) {
        $('.co .co_name').text(user_name);
        $('.co input[name="co_id"]').val(user_id);
        $('#coModal').modal('hide');
    }

    function allotSubmit(){
        if (!$(".form-horizontal").valid()) {
            return;
        }
        var values = $(".form-horizontal").getValues();
        yo.loadData({
            _c: 'branch_manager',
            _m: 'allotConsultToCo',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.href = '<?php echo getUrl('branch_manager', 'loanConsult', array(), false, BACK_OFFICE_SITE_URL) ?>';
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

    function reject() {
        var values = $(".form-horizontal").getValues();
        if(!$.trim(values.remark)){
            alert('Please entry remark!')
            return;
        }
        yo.loadData({
            _c: 'branch_manager',
            _m: 'rejectConsult',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.href = '<?php echo getUrl('branch_manager', 'loanConsult', array(), false, BACK_OFFICE_SITE_URL) ?>';
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
            co_id: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            co_id: {
                required: '<?php echo 'Please select or change co.'?>'
            },
            remark: {
                required: '<?php echo 'Required'?>'
            }
        }
    });

</script>
