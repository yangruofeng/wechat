<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/member.css?v=8">
<?php include_once(template('widget/inc_header'));?>
<?php $detail = $output['detail'];
  $insurance_contract = $detail['insurance_contract'];
  $insurance_product = $detail['insurance_product'];
  $beneficiary = $detail['beneficiary'];
?>
<div class="wrap loan-contract-wrap">
  <div class="aui-tab contract-tab aui-margin-b-10" id="tab">
    <div class="aui-tab-item aui-active"><?php echo $lang['label_base_info'];?></div>
    <div class="aui-tab-item"><div></div>Beneficiaries</div>
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
              <?php echo $insurance_contract['contract_sn'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Product
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $insurance_product['product_name'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Insured Amount
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $insurance_contract['start_insured_amount'];?>
              <?php echo $insurance_contract['currency'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Floating Amount
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $insurance_contract['floating_amount'];?>
              <?php echo $insurance_contract['currency'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Price
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $insurance_contract['price'];?>
              <?php echo $insurance_contract['currency'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              Start Date
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $insurance_contract['start_date'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              End Date
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $insurance_contract['end_date']?:'-';?>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
  <div class="limit-calculation tab-panel" id="tab-2" type="3" style="display: none;">
    <div class="aui-content aui-margin-b-15">
      <?php if(count($beneficiary)){ ?>
        <ul class="aui-list aui-media-list beneficiaries-list">
          <li class="aui-list-item beneficiaries-item clearfix">
            <div class="aui-list-item-input">Sequence</div>
            <div class="aui-list-item-input">Name</div>
            <div class="aui-list-item-input">Phone</div>
          </li>
          <?php foreach ($beneficiary as $key => $value) { ?>
            <li class="aui-list-item beneficiaries-item">
              <div class="aui-list-item-input"><?php echo $value['benefit_index'];?></div>
              <div class="aui-list-item-input"><?php echo $value['benefit_name'];?></div>
              <div class="aui-list-item-input"><?php echo $value['benefit_phone'];?></div>
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
