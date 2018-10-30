<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=3">
<?php include_once(template('widget/inc_header'));?>
<?php $detail = $output['detail'];
  $info = $output['info'];
  $contract_info = $detail['contract_info'];
?>
<div class="wrap repayment-wrap">
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list aui-media-list repayment-base-ul">
      <li class="aui-list-item aui-list-item-middle">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title">Contract No.</div>
              <div class="aui-list-item-right"><?php echo $contract_info['contract_sn'];?></div>
            </div>
          </div>
        </div>
      </li>
      <li class="aui-list-item aui-list-item-middle">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-      ">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title">Remainder of this period</div>
              <div class="aui-list-item-right"><?php echo ncPriceFormat($info['next_repayment_amount']);?> <?php echo $contract_info['currency'];?></div>
            </div>
          </div>
        </div>
      </li>
      <li class="aui-list-item aui-list-item-middle">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title">Due Date</div>
              <div class="aui-list-item-right"><?php echo dateFormat($info['next_repayment_date']);?></div>
            </div>
          </div>
        </div>
      </li>
      <li class="aui-list-item aui-list-item-middle">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title t-total">Total</div>
              <div class="aui-list-item-right r-total"><?php echo ncPriceFormat($info['next_repayment_amount']);?> <em><?php echo $contract_info['currency'];?></em></div>
            </div>
          </div>
        </div>
      </li>
      <li class="aui-list-item aui-list-item-middle">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title">Remainder of this period</div>
              <div class="aui-list-item-right"><?php echo ncPriceFormat($info['total_payable_amount']);?> <?php echo $contract_info['currency'];?></div>
            </div>
          </div>
        </div>
      </li>
    </ul>

    <ul class="aui-list aui-form-list repayment-info-ul aui-margin-t-10">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo 'Repayment Memthod';?>
          </div>
          <div class="aui-list-item-input input-select">
            <select name="repayment_method" id="repayment_method">
              <option value="1"><?php echo 'Bank Transfer';?></option>
              <option value="2"><?php echo 'Bound Account';?></option>
            </select>
            <i class="aui-iconfont aui-icon-down"></i>
          </div>
        </div>
      </li>
      <li class="aui-list-item select-insurance">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo 'Repayment Amount';?>
          </div>
          <div class="aui-list-item-input input-select">
            <input type="text" name="amount" id="amount" value="<?php echo ncPriceFormat($info['next_repayment_amount']);?>">
            <select name="currency" class="currency" id="currency">
              <option value="<?php echo currencyEnum::USD;?>"><?php echo currencyEnum::USD;?></option>
              <option value="<?php echo currencyEnum::KHR;?>"><?php echo currencyEnum::KHR;?></option>
              <option value="<?php echo currencyEnum::CNY;?>"><?php echo currencyEnum::CNY;?></option>
              <option value="<?php echo currencyEnum::VND;?>"><?php echo currencyEnum::VND;?></option>
              <option value="<?php echo currencyEnum::THB;?>"><?php echo currencyEnum::THB;?></option>
            </select>
            <i class="aui-iconfont aui-icon-down"></i>
          </div>
        </div>
      </li>
    </ul>
  </div>
  <div class="repayment-oprt">
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" id="confirm"><?php echo 'Confirm Repayment';?></div>
    <p class="tip">Payment information or evidence needs to be provided.</p>
    <div class="aui-btn aui-btn-block custom-btn pending-btn" onclick="javascript:location.href='<?php echo getUrl('credit', 'repaymentPending', array(), false, WAP_SITE_URL)?>'"><?php echo 'Pending Repayment';?></div>
  </div>
</div>
<script type="text/javascript">
  $('#confirm').on('click', function(){
    var repayment_method = $('#repayment_method').val(), currency = $('#currency').val(), amount = $('#amount').val();
    if(repayment_method == 1){
      window.location.href = '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=repaymentCompanyReceiveAccount&currency='+currency+'&amount='+amount;
    }else{
      window.location.href = '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=repaymentBoundAccount';
    }
  });

</script>
