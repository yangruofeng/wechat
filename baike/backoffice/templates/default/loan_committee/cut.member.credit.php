<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .input-h30 {
        height: 30px !important;
    }

    em {
        font-weight: 500;
        font-size: 15px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        /*background-color: #FFF;*/
        overflow: hidden;
    }

    .col-sm-6 .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    .tab-content label {
        margin-bottom: 0px!important;
    }

    .form-horizontal {
        margin-bottom: 0px;
    }

    .form-horizontal .control-label {
        text-align: left;
    }

    .tr_1 {
        background-color: #FFF!important;
    }

    .tr_2 {
        background-color: #F3F4F6!important;
    }

</style>
<?php $client_info = $output['client_info'];?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Fast Grant Credit</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan_committee', 'fastGrantCredit', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Client List</span></a></li>
                <li><a class="current"><span>Grant Credit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 1200px">
        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Cut Credit</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" method="post" action="<?php echo getUrl('loan_committee', 'submitCutCredit', array(), false, BACK_OFFICE_SITE_URL)?>">
                        <input type="hidden" name="form_submit" value="ok">
                        <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Member Credit</label></td>
                                <td>
                                    <em><?php echo ncAmountFormat($client_info['credit_info']['credit']); ?></em>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Credit Balance</label></td>
                                <td>
                                    <em><?php echo ncAmountFormat($client_info['credit_info']['balance']); ?></em>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Loan Balance</label></td>
                                <td>
                                    <em><?php echo ncAmountFormat(memberClass::getLoanBalance($credit_info['uid'])->DATA); ?></em>
                                </td>
                            </tr>

                            <tr>
                                <td><label class="control-label">Cut Amount</label></td>
                                <td>
                                    <input type="hidden" id="member_credit" value="<?php echo $client_info['credit_info']['credit']; ?>">
                                    <input type="number" class="form-control input-h30" name="amount" value="">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Remark</label></td>
                                <td>
                                    <textarea class="form-control" name="remark" style="width: 100%;height: 50px"></textarea>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <button type="button" class="btn btn-info" id="cut-submit" style="width: 50%"><i class="fa fa-check"></i><?php echo 'Commit' ?></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Client Info</h5>
                </div>
                <div class="content">
                    <table class="table">
                        <tbody class="table-body">
                        <tr>
                            <td><label class="control-label">Icon</label></td>
                            <td colspan="3">
                                <a target="_blank" href="<?php echo getImageUrl($client_info['member_icon']); ?>">
                                    <img src="<?php echo getImageUrl($client_info['member_icon'], imageThumbVersion::MAX_120); ?>" style="max-height: 100px">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Login Account</label></td>
                            <td><?php echo $client_info['login_code']; ?></td>
                            <td><label class="control-label">Name</label></td>
                            <td><?php echo $client_info['display_name']; ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Phone</label></td>
                            <td><?php echo $client_info['phone_id']; ?></td>
                            <td><label class="control-label">Grade</label></td>
                            <td><?php echo $client_info['grade_code']; ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Work Type</label></td>
                            <td><?php echo ucwords(str_replace('_', '', $client_info['work_type'])); ?></td>
                            <td><label class="control-label">Status</label></td>
                            <td><?php echo $lang['client_member_state_' . $client_info['member_state']]; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Credit History</h5>
                </div>
                <div class="content credit-history">

                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);
        var member_id = $('input[name="member_id"]').val();
        yo.dynamicTpl({
            tpl: "branch_manager/hq.credit.list",
            dynamic: {
                api: "branch_manager",
                method: "getCreditGrantList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, member_id: member_id}
            },
            callback: function (_tpl) {
                $(".credit-history").html(_tpl);
            }
        });
    }

    $('#cut-submit').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            amount: {
                required: true,
                chkAmount: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            amount: {
                required: '<?php echo 'Required!'?>',
                chkAmount: 'Cannot exceed member credit limit.'
            },
            remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

    jQuery.validator.addMethod("chkAmount", function (value, element) {
        value = Number(value);
        var _amount = value.toFixed(2);
        var _member_credit = $('#member_credit').val();
        _member_credit = Number(_member_credit);
        if (_member_credit < _amount) {
            return false;
        } else {
            return true;
        }
    });
</script>