<style>
    .btn {
        height: 30px;
        min-width: 80px;
        padding: 5px 12px;
        border-radius: 0px;
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
        background-color: #FFF;
        overflow: hidden;
    }

    .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client CBC</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'checkCbc', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <?php $info = $output['info'];?>
    <div class="container">
        <div class="col-xs-7">
            <form class="form-horizontal" id="cbc_form">
                <input type="hidden" name="uid" value="<?php echo $output['mid']; ?>">
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label" style="text-align: left;font-size: 15px">Member Information</label>
                    <div class="col-sm-6"></div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label">Member Name</label>
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo $info['login_code'];?></label>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label">Phone</label>
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo $info['phone_id'];?></label>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label" style="text-align: left;font-size: 15px">General</label>
                    <div class="col-sm-6"></div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>All Previous Enquiries</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="all_previous_enquiries" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Enquiries For Previous 30 Days</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="enquiries_for_previous_30_days" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Earliest Loan Issue Date</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="earliest_loan_issue_date" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Normal Accounts</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="normal_accounts" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Delinquent Accounts</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="delinquent_accounts" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Closed Accounts</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="closed_accounts" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Reject Accounts</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="reject_accounts" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Write Off Accounts</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="write_off_accounts" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Total Limits</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="total_limits" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Total Liabilities</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="total_liabilities" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label" style="text-align: left;font-size: 15px">Guarantee</label>
                    <div class="col-sm-6"></div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Normal Accounts</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="guaranteed_normal_accounts" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Delinquent Accounts</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="guaranteed_delinquent_accounts" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Closed Accounts</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="guaranteed_closed_accounts" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Reject Accounts</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="guaranteed_reject_accounts" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Write Off Accounts</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="guaranteed_write_off_accounts" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Total Limits</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="guaranteed_total_limits" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Total Liabilities</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="guaranteed_total_liabilities" value="">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <a  class="btn btn-default" onclick="javascript:history.back(-1)"><i class="fa fa-reply"></i><?php echo 'Cancel'?></a>
                    <a  class="btn btn-danger" onclick="cbc_submit()"><i class="fa fa-check"></i><?php echo 'Submit'?></a>
                </div>
            </form>
        </div>
        <div class="col-xs-5">
            <?php $client_cbc = $output['history'];?>
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>CBC</h5>
                    <?php if($client_cbc){ ?>
                    <a href="<?php echo getUrl('operator', 'checkCbcClientHistory', array('uid'=> $info['uid']), false, BACK_OFFICE_SITE_URL) ?>" style="position:absolute;right: 10px;font-weight: 500;text-decoration: none">History</a>
                    <?php } ?>
                </div>
                <div class="content">
                    <table class="table audit-table">
                        <tbody class="table-body">
                            <?php if(!$client_cbc){?>
                                <tr>
                                    <td colspan="2"><div class="no-record">No Record</div></td>
                                </tr>
                            <?php } else {?>
                                <tr>
                                    <td><span class="pl-25">Check Time</span></td>
                                    <td><?php echo timeFormat($client_cbc['create_time']); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Creator Name</span></td>
                                    <td><?php echo $client_cbc['creator_name']; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <label class="control-label">General</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">All Previous Enquiries</span></td>
                                    <td><?php echo $client_cbc['all_previous_enquiries']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Enquiries For Previous 30 Days</span></td>
                                    <td><?php echo $client_cbc['enquiries_for_previous_30_days']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Earliest Loan Issue Date</span></td>
                                    <td><?php echo dateFormat($client_cbc['earliest_loan_issue_date']); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Normal Accounts</span></td>
                                    <td><?php echo $client_cbc['normal_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Delinquent Accounts</span></td>
                                    <td><?php echo $client_cbc['delinquent_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Closed Accounts</span></td>
                                    <td><?php echo $client_cbc['closed_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Reject Accounts</span></td>
                                    <td><?php echo $client_cbc['reject_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Write Off Accounts</span></td>
                                    <td><?php echo $client_cbc['write_off_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Limits</span></td>
                                    <td><em><?php echo ncAmountFormat($client_cbc['total_limits']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Liabilities</span></td>
                                    <td><em><?php echo ncAmountFormat($client_cbc['total_liabilities']); ?></em></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <label class="control-label">Guarantee</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Normal Accounts</span></td>
                                    <td><?php echo $client_cbc['guaranteed_normal_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Delinquent Accounts</span></td>
                                    <td><?php echo $client_cbc['guaranteed_delinquent_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Closed Accounts</span></td>
                                    <td><?php echo $client_cbc['guaranteed_closed_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Reject Accounts</span></td>
                                    <td><?php echo $client_cbc['guaranteed_reject_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Write Off Accounts</span></td>
                                    <td><?php echo $client_cbc['guaranteed_write_off_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Limits</span></td>
                                    <td><em><?php echo ncAmountFormat($client_cbc['guaranteed_total_limits']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Liabilities</span></td>
                                    <td><em><?php echo ncAmountFormat($client_cbc['guaranteed_total_liabilities']); ?></em></td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function() {
        $('input[name="earliest_loan_issue_date"]').datepicker({
            format: 'yyyy-mm-dd'
        });
    })
    function cbc_submit() {
        if (!$("#cbc_form").valid()) {
            return;
        }
        var values = getFormJson($('#cbc_form'));
        yo.loadData({
            _c: 'operator',
            _m: 'editMemberCbc',
            param: values,
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

    $('#cbc_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            all_previous_enquiries: {
                required: true
            },
            Enquiries_for_previous_30_days: {
                required: true
            },
//            earliest_loan_issue_date: {
//                required: true
//            },
            normal_accounts: {
                required: true
            },
            delinquent_accounts: {
                required: true
            },
            closed_accounts: {
                required: true
            },
            reject_accounts: {
                required: true
            },
            write_off_accounts: {
                required: true
            },
            total_limits: {
                required: true
            },
            total_liabilities: {
                required: true
            },
            guaranteed_normal_accounts: {
                required: true
            },
            guaranteed_delinquent_accounts: {
                required: true
            },
            guaranteed_closed_accounts: {
                required: true
            },
            guaranteed_reject_accounts: {
                required: true
            },
            guaranteed_write_off_accounts: {
                required: true
            },
            guaranteed_total_limits: {
                required: true
            },
            guaranteed_total_liabilities: {
                required: true
            }
        },
        messages: {
            all_previous_enquiries: {
                required: '<?php echo 'Required'?>'
            },
            Enquiries_for_previous_30_days: {
                required: '<?php echo 'Required'?>'
            },
//            earliest_loan_issue_date: {
//                required: '<?php //echo 'Required'?>//'
//            },
            normal_accounts: {
                required: '<?php echo 'Required'?>'
            },
            delinquent_accounts: {
                required: '<?php echo 'Required'?>'
            },
            closed_accounts: {
                required: '<?php echo 'Required'?>'
            },
            reject_accounts: {
                required: '<?php echo 'Required'?>'
            },
            write_off_accounts: {
                required: '<?php echo 'Required'?>'
            },
            total_limits: {
                required: '<?php echo 'Required'?>'
            },
            total_liabilities: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_normal_accounts: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_delinquent_accounts: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_closed_accounts: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_reject_accounts: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_write_off_accounts: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_total_limits: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_total_liabilities: {
                required: '<?php echo 'Required'?>'
            }
        }
    });
</script>




