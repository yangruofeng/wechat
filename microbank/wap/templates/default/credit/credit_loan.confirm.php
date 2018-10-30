<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<?php $detail = $output['detail'];
  $contract_info = $detail['contract_info'];
  $loan_installment_scheme = $detail['loan_installment_scheme'];
  $loan_disbursement_scheme = $detail['loan_disbursement_scheme'];
?>
<input type="hidden" id="contract_id" value="<?php echo $detail['contract_id'];?>">
<div class="wrap withdraw-detail-wrap">
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list base-info-ul">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label title">
            <?php echo $lang['label_loan_amount'];?>
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $detail['loan_amount'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label title">
            <?php echo $lang['label_repayment_method'];?>
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $detail['repayment_type'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label title">
            <?php echo $lang['label_repayment_frequency'];?>
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $detail['repayment_period']?:'&nbsp;';?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label title">
            <?php echo $lang['label_loan_period'];?>
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $detail['loan_period_value'];?> (<?php echo $detail['loan_period_unit'];?>)
          </div>
        </div>
      </li>
    </ul>
    <div class="base-info-more">
      <span id="more"><?php echo $lang['label_more'];?> >></span>
    </div>
  </div>
  <div class="aui-tab contract-tab" id="tab" style="z-index: 0;">
    <div class="aui-tab-item aui-active"><?php echo $lang['label_disbursement_scheme'];?></div>
    <div class="aui-tab-item"><div></div><?php echo $lang['label_installment_scheme'];?></div>
  </div>
  <div class="limit-calculation tab-panel" id="tab-1" type="1">
    <div class="aui-content aui-margin-b-15">
      <?php if(count($loan_disbursement_scheme)){ ?>
        <ul class="aui-list aui-media-list installment-list">
          <li class="aui-list-item installment-item">
            <div class="aui-list-item-inner inner">
              <div class="aui-list-item-input">
                <?php echo $lang['label_date'];?>
                <span class="r-amount"><?php echo $lang['label_amount'];?></span>
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
  <div class="limit-calculation tab-panel" id="tab-2" type="2" style="display: none;">
    <div class="aui-content aui-margin-b-15">
      <?php if(count($loan_installment_scheme)){ ?>
        <ul class="aui-list aui-media-list installment-list">
          <li class="aui-list-item installment-item">
            <div class="aui-list-item-inner inner">
              <div class="aui-list-item-input">
                <?php echo $lang['label_date'];?>
                <span class="r-amount"><?php echo $lang['label_amount'];?></span>
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

  <div class="confirm-oprt">
    <div class="agree-tip">
      <span class="item-ck" data-uid="<?php echo $value['uid'];?>" data-name="<?php echo $value['item_name'];?>"></span>
      <?php echo $lang['tip_your_hereby_agree_to'];?> <em><?php echo $lang['tip_samrithisak_register_protocol'];?></em>
    </div>
    <div class="oprt-btn clearfix">
      <div class="btn btn-cancel" id="cancel">
        <?php echo $lang['act_cancel'];?>
      </div>
      <div class="btn btn-confirm disabled" id="confirm">
        <?php echo $lang['act_confirm'];?>
      </div>
    </div>
  </div>

  <div class="withdrawal-detail-wrap" style="display: none;">
    <div class="content">
      <div class="t">
        <span class="close-detail"><i class="aui-iconfont aui-icon-close"></i></span>
        <?php echo $lang['label_withdrawal_detail'];?>
      </div>
      <div class="c">
        <p><?php echo $lang['label_loan_amount'];?><em>$<?php echo $detail['loan_amount'];?></em></p>
        <p><?php echo $lang['label_total_interest'];?><em>$<?php echo $detail['total_interest'];?></em></p>
        <p><?php echo $lang['label_admin_fee'];?><em>$<?php echo $detail['total_admin_fee'];?></em></p>
        <p><?php echo $lang['label_loan_fee'];?><em>$<?php echo $detail['total_loan_fee'];?></em></p>
        <p><?php echo $lang['label_operate_fee'];?><em>$<?php echo $detail['total_operation_fee'];?></em></p>
        <p><?php echo $lang['label_insurance_expense'];?><em>$<?php echo $detail['total_insurance_fee'];?></em></p>
        <p><?php echo $lang['label_actual_amount'];?><em>$<?php echo $detail['actual_receive_amount'];?></em></p>
        <p><?php echo $lang['label_total_repayment'];?><em>$<?php echo $detail['total_repayment'];?></em></p>
        <p class="rule">
          <?php echo $lang['label_actual_amount'];?> = <?php echo $lang['label_loan_amount'];?> - <?php echo $lang['label_admin_fee'];?> - <?php echo $lang['label_loan_fee'];?> - <?php echo $lang['label_insurance_expense'];?>
        </p>
        <p class="rule">
          <?php echo $lang['label_total_repayment'];?> = <?php echo $lang['label_loan_amount'];?> + <?php echo $lang['label_total_interest'];?> + <?php echo $lang['label_operate_fee'];?>
        </p>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-tab.js"></script>
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
  $('#more').on('click', function(){
    $('.withdrawal-detail-wrap').show();
  });
  $('.close-detail').on('click', function(){
    $('.withdrawal-detail-wrap').hide();
  });
  $('.agree-tip').on('click', function(e){
    $(this).find('.item-ck').hasClass('active') ? $(this).find('.item-ck').removeClass('active') : $(this).find('.item-ck').addClass('active');
    $(this).find('.item-ck').hasClass('active') ? $('.btn-confirm').removeClass('disabled') : $('.btn-confirm').addClass('disabled');
  });
  $('#cancel').on('click', function(){
    $('.back').click();
  });
  $('#confirm').on('click', function(){
    if($(this).hasClass('disabled')){
      return;
    }
    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=ajaxSubmitConfirmWithdraw',
      data: {contract_id: $.trim($('#contract_id').val())},
      dataType: 'json',
      success: function(data){
        toast.hide();
        if(data.STS){
          window.location.href = '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=withdrawSuccess';
        }else{
          verifyFail(data.MSG);
        }
      },
      error: function(xhr, type){
        toast.hide();
        verifyFail('<?php echo $lang['tip_get_data_error'];?>');
      }
    });
  });

</script>
