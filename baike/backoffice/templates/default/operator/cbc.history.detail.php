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
                <li><a href="<?php echo getUrl('operator', 'addClientCbc', array('uid'=>$output['mid']), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
                <li><a href="<?php echo getUrl('operator', 'checkCbcClientHistory', array('uid'=>$output['mid']), false, BACK_OFFICE_SITE_URL) ?>"><span>History</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <?php $info = $output['member_info'];?>
    <div class="container">
        <div class="col-xs-12">
            <?php $client_cbc = $output['detail'];?>
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>CBC Detail</h5>
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




