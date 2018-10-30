<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=4">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/inc_header.css?v=4">
<header class="top-header" id="header">
  <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  <span class="right-btn" onclick="javascript:location.href='<?php echo getUrl('member', 'loanContract', array(), false, WAP_SITE_URL)?>'"><i class="aui-iconfont aui-icon-menu"></i></span>
</header>
<div class="wrap credit-loan-wrap">
  <?php $credit_info = $output['credit_info']; $months = $output['months'];?>
  <div class="credit-loan-balance">
    <div class="b">$ <?php echo $credit_info['balance'];?></div>
    <div class="l"><?php echo $lang['label_credit_balance'];?></div>
  </div>
  <div class="credit-loan-form">
    <ul class="aui-list aui-form-list credit-loan-item">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_amount'];?>
          </div>
          <div class="aui-list-item-input">
            <input type="number" name="amount" id="amount" placeholder="<?php echo $lang['label_enter'];?>">
          </div>
        </div>
      </li>
      <li class="aui-list-item select-insurance">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo 'Months';?>
          </div>
          <div class="aui-list-item-input input-select">
            <select name="loan_period" id="loan_period">
            <option value="0"><?php echo $lang['label_select'];?></option>
              <?php  if($months['min_month'] == $months['max_month']){ ?>
                <option value="<?php echo $months['min_month'];?>"><?php echo $months['min_month'];?></option>
              <?php }else{ ?>
                <?php $min = $months['min_month'];$max = $months['max_month']; if($months['min_month'] <= 0){$min = 1;}?>
                <?php for($min; $min <= $max; $min++){ ?>
                  <option value="<?php echo $min;?>"><?php echo $min;?></option>
                <?php } ?>
              <?php }?>
            </select>
            <i class="aui-iconfont aui-icon-down"></i>
          </div>
        </div>
      </li>
    </ul>
    <div style="padding: .8rem 0;">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" id="withdraw"><?php echo $lang['label_withdraw'];?></div>
    </div>
  </div>
</div>
<script type="text/javascript">
$('#withdraw').on('click', function(){
  //amount loan_period loan_period_unit propose repayment_type repayment_period insurance_item_id
  var total_amount = $.trim($('#total_amount').val()), amount = $.trim($('#amount').val()), loan_period = $.trim($('#loan_period').val()), param = {};
  if(!amount){
    verifyFail('<?php echo $lang['tip_please_input_withdrawal_amount'];?>');
    return;
  }
  param.amount = amount;
  if(!loan_period){
    verifyFail('<?php echo 'Please select months.';?>');
    return;
  }
  param.loan_period = loan_period;
  
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'POST',
    url: '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=submitWithdraw',
    data: param,
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        window.location.href = '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=withdrawConfirm&contract_id='+data.DATA.contract_id;
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
