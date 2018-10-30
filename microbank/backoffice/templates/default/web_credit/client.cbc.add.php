<style>
    .btn {
        border-radius: 0;
    }

    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

     .ibox-title {
         padding-top: 12px!important;
         min-height: 40px;
     }



    .file {
        position: relative;
        display: inline-block;
        background: #fff;
        border: 1px solid #ccc;
        padding: 6px 12px;
        overflow: hidden;
        color: #000;
        text-decoration: none;
        text-indent: 0;
        line-height: 20px;
        cursor: pointer;
    }
    .file input {
        position: absolute;
        font-size: 100px;
        right: 0;
        top: 0;
        opacity: 0;
    }
    .file:hover {
        background: #AADFFD;
        border-color: #78C3F3;
        color: #004974;
        text-decoration: none;
    }

</style>
<?php
$client_info=$output['client_info'];
$last=$output['last_item'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>CBC</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>CBC</span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 1200px">
        <div class="business-condition">
             <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>CBC</h5>
                </div>
                <div class="content">
                    <form id="cbc_form" method="POST" enctype="multipart/form-data" action="<?php echo getUrl('web_credit', 'addMemberCbc', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>">
                        <input type="hidden" name="client_id" value="<?php echo $output['client_id']?>">
                        <input type="hidden" name="client_type" value="<?php echo $output['client_type']?>">

                        <div class="container" style="margin: 0">
                            <div class="col-sm-6">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">All Previous Enquiries</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="all_previous_enquiries" value="<?php echo $last['all_previous_enquiries']?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Enquiries For Previous 30 Days</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="enquiries_for_previous_30_days" value="<?php echo $last['enquiries_for_previous_30_days']?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Earliest Loan Issue Date</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="earliest_loan_issue_date" value="<?php echo $last['earliest_loan_issue_date']?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label" style="color: red">Pay To Other Bank</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="pay_to_cbc" value="<?php echo $last['pay_to_cbc']?:0?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label" style="color: red">SRS Old Loan</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="pay_to_srs" value="<?php echo $last['pay_to_srs']?:0?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="container" style="margin: 0">
                            <div class="col-sm-6">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label" style="text-align: left;font-size: 16px;margin-top: 20px">Primary</label>
                                    <div class="col-sm-6"></div>
                                </div>

                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Normal Accounts</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="normal_accounts" value="<?php echo $last['normal_accounts'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Delinquent Accounts</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="delinquent_accounts" value="<?php echo $last['delinquent_accounts'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Closed Accounts</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="closed_accounts" value="<?php echo $last['closed_accounts'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Reject Accounts</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="reject_accounts" value="<?php echo $last['reject_accounts'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Write Off Accounts</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="write_off_accounts" value="<?php echo $last['write_off_accounts'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Limits(USD)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="total_limits" value="<?php echo $last['total_limits'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Liabilities(USD)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="total_liabilities" value="<?php echo $last['total_liabilities'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Limits(KHR)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="total_limits_khr" value="<?php echo $last['total_limits_khr'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Liabilities(KHR)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="total_liabilities_khr" value="<?php echo $last['total_liabilities_khr'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Limits(THB)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="total_limits_thb" value="<?php echo $last['total_limits_thb'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Liabilities(THB)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="total_liabilities_thb" value="<?php echo $last['total_liabilities_thb'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>

                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">CBC File</label>
                                    <div class="col-sm-6">
                                        <?php if( $last['cbc_file'] ){ ?>
                                            <div style="padding: 8px 0;">
                                                <a style="margin-left: 10px;" target="_blank" href="<?php echo getBackOfficeUrl('file','pdfView',array(
                                                    'file_path' => urlencode(getCBCFileUrl($last['cbc_file']))
                                                )); ?>"><?php echo $last['cbc_file']; ?>
                                                </a>
                                            </div>
                                        <?php } ?>


                                        <div class="file-group">
                                            <a href="javascript:;" class="file">
                                               Upload
                                                <input class="file-input" type="file" name="cbc_file" id="cbc_file" >
                                            </a>
                                            <div class="file-name" ></div>
                                        </div>

                                        <span style="margin-top: 15px;font-style: italic;color:red;">Only support PDF file now!</span>
                                        <div class="error_msg"></div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-sm-6">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label" style="text-align: left;font-size: 16px;margin-top: 20px">Guarantee</label>
                                    <div class="col-sm-6"></div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Normal Accounts</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_normal_accounts" value="<?php echo $last['guaranteed_normal_accounts'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Delinquent Accounts</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_delinquent_accounts" value="<?php echo $last['guaranteed_delinquent_accounts'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Closed Accounts</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_closed_accounts" value="<?php echo $last['guaranteed_closed_accounts'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Reject Accounts</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_reject_accounts" value="<?php echo $last['guaranteed_reject_accounts'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Write Off Accounts</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_write_off_accounts" value="<?php echo $last['guaranteed_write_off_accounts'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Limits(USD)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_total_limits" value="<?php echo $last['guaranteed_total_limits'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Liabilities(USD)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_total_liabilities" value="<?php echo $last['guaranteed_total_liabilities'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Limits(KHR)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_total_limits_khr" value="<?php echo $last['guaranteed_total_limits_khr'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Liabilities(KHR)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_total_liabilities_khr" value="<?php echo $last['guaranteed_total_liabilities_khr'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Limits(THB)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_total_limits_thb" value="<?php echo $last['guaranteed_total_limits_thb'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Total Liabilities(THB)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" name="guaranteed_total_liabilities_thb" value="<?php echo $last['guaranteed_total_liabilities_thb'] ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="col-sm-12 form-group" style="text-align: center">
                            <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                            <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }

    $(function() {
        $('input[name="earliest_loan_issue_date"]').datepicker({
            format: 'yyyy-mm-dd'
        });

        $('.file-group .file-input').change(function(){
            //var file_path = $(this).val();
            var file_data =  $(this)[0].files[0];
            var file_name = file_data.name;
            $('.file-group .file-name').html(file_name);
        });
    });

    function btn_submit_onclick() {
        if (!$("#cbc_form").valid()) {
            return;
        }
        $("#cbc_form").submit();
    }

    $('#cbc_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {

//            earliest_loan_issue_date: {
//                required: true
//            },
            normal_accounts: {
                required: true
            }

        },
        messages: {

//            earliest_loan_issue_date: {
//                required: '<?php //echo 'Required'?>//'
//            },
            normal_accounts: {
                required: '<?php echo 'Required'?>'
            }

        }
    });

</script>






