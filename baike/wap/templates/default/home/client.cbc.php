<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=3">
<?php include_once(template('widget/inc_header'));?>
  <?php $data = $output['data'];?>
<div class="wrap cbc-wrap">
  <?php if(!$data){?>
    <div class="no-record">No Record</div>
  <?php }else{?>
    <ul class="aui-list aui-form-list loan-item">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Check Time</div>
            <div class="aui-list-item-input label-on"><?php echo timeFormat($data['create_time']);?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Creator Name</div>
            <div class="aui-list-item-input label-on"><?php echo $data['creator_name'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">All Previous Enquiries</div>
            <div class="aui-list-item-input label-on"><?php echo ncPriceFormat($data['all_previous_enquiries']);?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Enquiries For Previous 30 Days</div>
            <div class="aui-list-item-input label-on"><?php echo $data['enquiries_for_previous_30_days'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Earliest Loan Issue Date</div>
            <div class="aui-list-item-input label-on"><?php echo dateFormat($data['earliest_loan_issue_date']);?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Pay To Other Bank</div>
            <div class="aui-list-item-input label-on"><em class="fontweight700"><?php echo $data['pay_to_cbc'];?></em></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label"><em class="fontweight700">General</em></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Normal Accounts</div>
            <div class="aui-list-item-input label-on"><?php echo $data['normal_accounts'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Delinquent Accounts</div>
            <div class="aui-list-item-input label-on"><?php echo $data['delinquent_accounts'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Closed Accounts</div>
            <div class="aui-list-item-input label-on"><?php echo $data['closed_accounts'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Reject Accounts</div>
            <div class="aui-list-item-input label-on"><?php echo $data['reject_accounts'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Write Off Accounts</div>
            <div class="aui-list-item-input label-on"><?php echo $data['write_off_accounts'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Total Limits</div>
            <div class="aui-list-item-input label-on"><?php echo '$'.ncPriceFormat($data['total_limits']);?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Total Liabilities</div>
            <div class="aui-list-item-input label-on"><?php echo '$'.ncPriceFormat($data['total_liabilities']);?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label"><em class="fontweight700">Guarantee</em></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Normal Accounts</div>
            <div class="aui-list-item-input label-on"><?php echo $data['guaranteed_normal_accounts'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Delinquent Accounts</div>
            <div class="aui-list-item-input label-on"><?php echo $data['guaranteed_delinquent_accounts'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Closed Accounts</div>
            <div class="aui-list-item-input label-on"><?php echo $data['guaranteed_closed_accounts'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Reject Accounts</div>
            <div class="aui-list-item-input label-on"><?php echo $data['guaranteed_reject_accounts'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Write Off Accounts</div>
            <div class="aui-list-item-input label-on"><?php echo $data['guaranteed_write_off_accounts'];?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Total Limits</div>
            <div class="aui-list-item-input label-on"><?php echo '$'.ncPriceFormat($data['guaranteed_total_limits']);?></div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">Total Liabilities</div>
            <div class="aui-list-item-input label-on"><?php echo '$'.ncPriceFormat($data['guaranteed_total_liabilities']);?></div>
        </div>
      </li>
    </ul>
  <?php }?> 
</div>
<script>
  
</script>