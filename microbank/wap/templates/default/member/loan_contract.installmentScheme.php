<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/member.css?v=7">
<?php include_once(template('widget/inc_header'));?>
<?php $detail = $output['detail'];?>
<div class="wrap loan-contract-wrap">
  <div class="contract-base-info tab-panel" id="tab-1" type="2">
    <div class="aui-content aui-margin-b-15">
      <ul class="aui-list base-info-ul">
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Name
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $detail['scheme_name'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Date
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $detail['receivable_date'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Payable Principal
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $detail['receivable_principal'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Pabable Interest
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $detail['receivable_interest'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Operating Charges
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $detail['receivable_operation_fee'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Payable Total
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $detail['amount'];?>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
