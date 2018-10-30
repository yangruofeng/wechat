<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/member.css?v=6">
<?php include_once(template('widget/inc_header'));?>
<?php $detail = $output['detail'];
  $contract_info = $detail['contract_info'];
  $loan_product_info = $detail['loan_product_info'];
  $interest_info = $detail['interest_info'];
  $loan_installment_scheme = $detail['loan_installment_scheme'];
  $loan_disbursement_scheme = $detail['loan_disbursement_scheme'];
?>
<div class="wrap loan-contract-wrap">
  <div class="aui-tab contract-tab aui-margin-b-10" id="tab">
    <div class="aui-tab-item aui-active"><?php echo $lang['label_base_info'];?></div>
    <div class="aui-tab-item"><div></div><?php echo $lang['label_installment'];?></div>
    <div class="aui-tab-item"><div></div><?php echo $lang['label_disbursement'];?></div>
  </div>
  <div class="contract-base-info tab-panel" id="tab-1" type="2">
    <div class="aui-content aui-margin-b-15">
      <ul class="aui-list base-info-ul">
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Contract No.
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $contract_info['contract_sn'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Product
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $loan_product_info['product_name'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Contract Amount
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $contract_info['apply_amount'];?>&nbsp;<?php echo $contract_info['currency'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Period
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $contract_info['loan_period_value'];?>
              <?php echo $contract_info['loan_period_unit'];?>(s)
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Repayment Type
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $contract_info['repayment_type'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Interest Rate
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo sprintf('%.1f',$contract_info['interest_rate']);?>%
              <?php echo $contract_info['interest_rate_unit'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Operation Fee
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo sprintf('%.1f',$interest_info['operation_fee']);?>%
              <?php echo $interest_info['operation_fee_unit'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Admin Fee
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $interest_info['admin_fee'];?>
              <?php echo $interest_info['currency'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Insurance Fee
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $detail['total_insurance_fee'];?>
              <?php echo $detail['currency'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Due Date
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $detail['due_date'];?>
              <?php switch ($detail['due_date_type']) {
                case 'once':
                  # code...
                  break;
                case 'yearly':
                  # code...
                  break;
                case 'monthly':
                  # code...
                  break;
                case 'weekly':
                  # code...
                  break;
                case 'daily':
                  # code...
                  break;
                default:
                  # code...
                  break;
              }?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Purpose
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $contract_info['propose'];?>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
  <div class="limit-calculation tab-panel" id="tab-2" type="3" style="display: none;">
    <div class="aui-content aui-margin-b-15">
      <?php if(count($loan_installment_scheme)){ ?>
        <ul class="aui-list aui-media-list installment-list">
          <li class="aui-list-item installment-item">
            <div class="aui-list-item-inner inner">
              <div class="aui-list-item-input">
                Date
                <span class="r-amount">Amount</span>
              </div>
            </div>
          </li>
          <?php foreach ($loan_installment_scheme as $key => $value) { ?>
            <li class="aui-list-item installment-item" onclick="javascript:location.href='<?php echo getUrl('member', 'installmentScheme', array('contract_id'=>$detail['contract_id'], 'key'=>$key), false, WAP_SITE_URL)?>'">
              <div class="aui-list-item-inner aui-list-item-arrow inner">
                <div class="aui-list-item-input">
                  <?php echo $value['receivable_date'];?>
                  <span class="r-amount"><?php echo $value['amount'];?></span>
                </div>
              </div>
            </li>
          <?php } ?>
        </ul>
      <?php }else{ ?>
        <div class="no-record"><?php echo $lang['label_no_data'];?></div>
      <?php } ?>
    </div>
  </div>
  <div class="limit-calculation tab-panel" id="tab-3" type="1" style="display: none;">
    <div class="aui-content aui-margin-b-15">
      <?php if(count($loan_disbursement_scheme)){ ?>
        <ul class="aui-list aui-media-list installment-list">
          <li class="aui-list-item installment-item">
            <div class="aui-list-item-inner inner">
              <div class="aui-list-item-input">
                Date
                <span class="r-amount">Amount</span>
              </div>
            </div>
          </li>
          <?php foreach ($loan_disbursement_scheme as $key => $value) { ?>
            <li class="aui-list-item installment-item" onclick="javascript:location.href='<?php echo getUrl('member', 'disbursementScheme', array('contract_id'=>$detail['contract_id'], 'key'=>$key), false, WAP_SITE_URL)?>'">
              <div class="aui-list-item-inner aui-list-item-arrow inner">
                <div class="aui-list-item-input">
                  <?php echo $value['disbursable_date'];?>
                  <span class="r-amount"><?php echo $value['amount'];?></span>
                </div>
              </div>
            </li>
          <?php } ?>
        </ul>
      <?php }else{ ?>
        <div class="no-record"><?php echo $lang['label_no_data'];?></div>
      <?php } ?>
    </div>
  </div>
</div>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-tab.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/doT.min.js"></script>
<script type="text/javascript">
  var tab = new auiTab({
    element: document.getElementById('tab'),
    index: 1,
    repeatClick: false
  },function(ret){
    var i = ret.index;
    $('.tab-panel').hide();
    $('#tab-' + i).show();
  });

</script>
