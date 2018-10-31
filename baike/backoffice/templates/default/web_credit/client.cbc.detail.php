<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.css?v=1" rel="stylesheet" />
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
</style>
<?php
$client_cbc=$output['cbc_detail'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if($output['is_readonly']){?>
                <h3>CBC Research</h3>
                <ul class="tab-base">
                    <li><a onclick="javascript:history.back(-1)"><span> Back </span></a></li>

                    <li><a class="current"><span>CBC Detail</span></a></li>
                </ul>
            <?php }else{?>
                <?php if ($output['is_bm']) { ?>
                    <h3>Client</h3>
                    <ul class="tab-base">
                        <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                        <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                        <li><a class="current"><span>CBC Detail</span></a></li>
                    </ul>
                <?php } else { ?>
                    <h3>My Client</h3>
                    <ul class="tab-base">
                        <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                        <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                        <li><a class="current"><span>CBC Detail</span></a></li>
                    </ul>
                <?php }?>
            <?php }?>

        </div>
    </div>
    <div class="basic-info container" style="margin-top: 70px;max-width: 800px">
        <div class="business-content">
            <div>
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>CBC</h5>
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
                                    <td>Pay To Other Bank</td>
                                    <td style="font-size: 16px;font-weight: bold"><?php echo $client_cbc['pay_to_cbc']?></td>
                                </tr>
                                <tr>
                                    <td>SRS Old Loan</td>
                                    <td style="font-size: 16px;font-weight: bold"><?php echo $client_cbc['pay_to_srs']?></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <label class="control-label">General</label>
                                    </td>
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
                                    <td><span class="pl-25">Total Limits(USD)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['total_limits']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Liabilities(USD)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['total_liabilities']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Limits(KHR)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['total_limits_khr']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Liabilities(KHR)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['total_liabilities_khr']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Limits(THB)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['total_limits_thb']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Liabilities(THB)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['total_liabilities_thb']); ?></em></td>
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
                                    <td><span class="pl-25">Total Limits(USD)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['guaranteed_total_limits']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Liabilities(USD)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['guaranteed_total_liabilities']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Limits(KHR)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['guaranteed_total_limits_khr']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Liabilities(KHR)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['guaranteed_total_liabilities_khr']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Limits(THB)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['guaranteed_total_limits_thb']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Liabilities(THB)</span></td>
                                    <td><em><?php echo ncPriceFormat($client_cbc['guaranteed_total_liabilities_thb']); ?></em></td>
                                </tr>
                                <?php if( $client_cbc['cbc_file'] ){ ?>
                                    <tr>
                                        <td><label class="control-label">CBC File</label></td>
                                        <td><a target="_blank" href="<?php echo getBackOfficeUrl('file','pdfView',array(
                                                'file_path' => urlencode(getCBCFileUrl($client_cbc['cbc_file']) )
                                            )); ?>"><?php echo $client_cbc['cbc_file']; ?></a></td>
                                    </tr>
                                <?php } ?>

                                    <tr>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-default" onclick="javascript:history.back(-1);" > Back </button>
                                        </td>
                                        <?php if(!$output['is_readonly']){?>
                                            <td><a class="btn btn-warning" style="padding: 5px 15px" onclick="deleteCBC();"><i class="fa fa-remove"></i>Delete</a></td>
                                        <?php }else{?>
                                            <td></td>
                                        <?php }?>
                                    </tr>


                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.min.js?v=1"></script>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }
    function deleteCBC(){
        yo.confirm('Confirm','Are you sure to delete the CBC record ?', function (_r) {
            if(!_r) return false;
            yo.loadData({
                _c: 'web_credit',
                _m: 'deleteMemberCBC',
                param: {uid: '<?php echo $output['cbc_detail']['uid'];?>'},
                callback: function (_o) {
                    if (_o.STS) {
                        alert('Deleted success!', 1, function(){
                            window.location.href = '<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>';
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }
</script>






