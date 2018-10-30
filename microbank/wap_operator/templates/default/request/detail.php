<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/request.css?v=3">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap request-wrap">
  <?php $data = $output['data']['request_detail']; ?>
  <ul class="aui-list request-detail-ul">
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Client ID
        </div>
        <div class="aui-list-item-input label-on">
          <?php echo $data['member_id'];?>
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Client Name
        </div>
        <div class="aui-list-item-input label-on">
          <?php echo $data['applicant_name'];?>
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Contact Phone
        </div>
        <div class="aui-list-item-input label-on">
          <?php echo $data['contact_phone'];?>
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Apply Amount
        </div>
        <div class="aui-list-item-input label-on">
        <?php echo $data['currency'];?> <?php echo $data['apply_amount'];?>
        </div>
      </div>
    </li>
    <?php if($data['state'] > 11){ ?>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Repayment Type
        </div>
        <div class="aui-list-item-input label-on">
        <?php echo $data['repayment_type'];?>
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
        principal
        </div>
        <div class="aui-list-item-input label-on">
        <?php echo $data['penalty_rate'];?>
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Interest
        </div>
        <div class="aui-list-item-input label-on">
        <?php echo $data['interest_rate'];?>%
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Operation Fee
        </div>
        <div class="aui-list-item-input label-on">
        <?php echo $data['operation_fee'];?>
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Loan Fee
        </div>
        <div class="aui-list-item-input label-on">
        <?php echo $data['loan_fee'];?>
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Admin Fee
        </div>
        <div class="aui-list-item-input label-on">
        <?php echo $data['admin_fee'];?>
        </div>
      </div>
    </li>
    <?php }?>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Request Time
        </div>
        <div class="aui-list-item-input label-on">
          <?php echo timeFormat($data['apply_time']);?>
        </div>
      </div>
    </li>
    <li class="aui-list-item">
      <div class="aui-list-item-inner">
        <div class="aui-list-item-label label">
          Operator Remark
        </div>
        <div class="aui-list-item-input label-on">
          <?php echo $data['remark'];?>
        </div>
      </div>
    </li>
  </ul>
  <?php if($data['state'] == 2 || $data['state'] == 10){ ?>
    <div style="padding: .2rem .8rem;">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" onclick="window.location.href='<?php echo getUrl('request', 'handleFirst', array('id'=>$data['uid']), false, WAP_OPERATOR_SITE_URL)?>'">Next</div>
    </div>
  <?php } ?>
</div>
<script type="text/javascript">
</script>
