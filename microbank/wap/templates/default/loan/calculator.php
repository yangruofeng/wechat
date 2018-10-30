<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/loan.css?v=5">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap calculator-vi-wrap">
  <div class="aui-tab calculator-tab aui-margin-b-10" id="tab">
    <div class="aui-tab-item aui-active"><?php echo $lang['label_limit_calculation'];?></div>
    <div class="aui-tab-item"><div></div><?php echo $lang['label_interest_calculation'];?></div>
  </div>
  <div class="limit-calculation tab-panel" id="tab-1">
    <div class="aui-content aui-margin-b-15">
      <ul class="aui-list calculator-item">
        <li class="aui-list-item">
          <div class="aui-list-item-label-icon calculator-icon">
            <span class="item-ck active" value="1"></span>
          </div>
          <div class="aui-list-item-inner calculator-text">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-32.png" alt="" class="icon"><?php echo $lang['label_id'];?>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-label-icon calculator-icon">
            <span class="item-ck" value="2"></span>
          </div>
          <div class="aui-list-item-inner calculator-text">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-33.png" alt="" class="icon"><?php echo $lang['label_resident_book'];?>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-label-icon calculator-icon">
            <span class="item-ck" value="2"></span>
          </div>
          <div class="aui-list-item-inner calculator-text">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-34.png" alt="" class="icon"><?php echo $lang['label_household_register'];?>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-label-icon calculator-icon">
            <span class="item-ck" value="4"></span>
          </div>
          <div class="aui-list-item-inner calculator-text">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-35.png" alt="" class="icon"><?php echo $lang['label_submit_family_information'];?>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-label-icon calculator-icon">
            <span class="item-ck" value="8"></span>
          </div>
          <div class="aui-list-item-inner calculator-text">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-36.png" alt="" class="icon"><?php echo $lang['label_submit_working_certificate'];?>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-label-icon calculator-icon">
            <span class="item-ck" value="16"></span>
          </div>
          <div class="aui-list-item-inner calculator-text">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-37.png" alt="" class="icon"><?php echo $lang['label_civil_servant'];?>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-label-icon calculator-icon">
            <span class="item-ck" value="32"></span>
          </div>
          <div class="aui-list-item-inner calculator-text">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-38.png" alt="" class="icon"><?php echo $lang['label_have_house_mortagage'];?>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-label-icon calculator-icon">
            <span class="item-ck" value="64"></span>
          </div>
          <div class="aui-list-item-inner calculator-text">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-39.png" alt="" class="icon"><?php echo $lang['label_have_antomobile_mortagage'];?>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-label-icon calculator-icon">
            <span class="item-ck" value="64"></span>
          </div>
          <div class="aui-list-item-inner calculator-text">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-40.png" alt="" class="icon"><?php echo $lang['label_motorcycle_asset_certificate'];?>
          </div>
        </li>
      </ul>
      <div class="calculator-button">
        <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" id="limitCalculator"><?php echo $lang['label_calculate'];?></div>
      </div>
    </div>
  </div>
  <div class="interest-calculation tab-panel" id="tab-2" style="display: none;">
    <div class="calculator-form">
      <ul class="aui-list aui-form-list calculator-item">
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">
              <?php echo $lang['label_loan_amount'];?>
            </div>
            <div class="aui-list-item-input">
              <input type="number" name="loan_amount" id="loan_amount" placeholder="<?php echo $lang['label_enter'];?>">
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">
              <?php echo $lang['label_loan_period'];?>
            </div>
            <div class="aui-list-item-input input-period">
              <input type="number" name="loan_period" id="loan_period" placeholder="<?php echo $lang['label_enter'];?>">
              <select class="" name="loan_period_unit" id="loan_period_unit">
                <option value="year"><?php echo $lang['label_year'];?></option>
                <option value="month" selected><?php echo $lang['label_month'];?></option>
                <option value="day"><?php echo $lang['label_day'];?></option>
              </select>
              <i class="aui-iconfont aui-icon-down"></i>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">
              <?php echo $lang['label_repayment_method'];?>
            </div>
            <div class="aui-list-item-input input-select">
              <select class="" name="repayment_type" id="repayment_type">
                <option value="0"><?php echo $lang['label_select'];?></option>
                <option value="single_repayment"><?php echo $lang['label_bullet_repayment'];?></option>
                <option value="fixed_principal"><?php echo $lang['label_average_capital'];?></option>
                <option value="annuity_scheme"><?php echo $lang['label_average_capital_plus_interest'];?></option>
                <option value="flat_interest"><?php echo $lang['label_fixed_interest'];?></option>
                <option value="balloon_interest"><?php echo $lang['label_pay_first_return_later'];?></option>
              </select>
              <i class="aui-iconfont aui-icon-down"></i>
            </div>
          </div>
        </li>
        <li class="aui-list-item" id="repaymentFrequency">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label label">
              <?php echo $lang['label_repayment_frequency'];?>
            </div>
            <div class="aui-list-item-input input-select">
              <select name="repayment_period" id="repayment_period">
                <option value="0"><?php echo $lang['label_select'];?></option>
                <option value="monthly"><?php echo $lang['label_once_a_month'];?></option>
                <option value="weekly"><?php echo $lang['label_once_a_week'];?></option>
              </select>
              <i class="aui-iconfont aui-icon-down"></i>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div class="calculator-button">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" id="interestCalculator"><?php echo $lang['label_calculator'];?></div>
    </div>
    <div id="loanList"></div>
  </div>
</div>
<script type="text/html" id="tpl_loan_item">
  {{ if( it && it.length > 0 ){ }}
    <div class="aui-content aui-margin-b-10">
      <ul class="aui-list aui-media-list calculator-item">
        {{ for(var i = 0; i< it.length; i++) { }}
          <li class="aui-list-item aui-list-item-middle" onclick="showProductInfo('{{=it[i]['product_info']['product_id']}}', '{{=it[i]['arrival_amount']}}');">
            <div class="aui-media-list-item-inner">
              <div class="aui-list-item-inner aui-list-item-arrow">
                <div class="aui-list-item-text">
                  <div class="aui-list-item-title aui-font-size-14">{{=it[i]['product_info']['product_name']}}</div>
                  <div class="aui-list-item-right"><?php echo $lang['label_total_returned'].$lang['label_colon'];?> {{=it[i]['period_repayment_amount']}}</div>
                </div>
                <div class="aui-list-item-text">
                  <?php echo $lang['label_Interest'].$lang['label_colon'];?> {{=it[i]['interest_rate']}}%
                  <div class="aui-list-item-right"><?php echo $lang['label_total_interest'].$lang['label_colon'];?>  {{=it[i]['repayment_schema'][0]['receivable_interest']}}</div>
                </div>
              </div>
            </div>
          </li>
        {{ } }}
      </ul>
    </div>
  {{ } }}
</script>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-tab.js"></script>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-dialog.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/doT.min.js"></script>
<script type="text/javascript">
  var toast = new auiToast(), dialog = new auiDialog();
  var tab = new auiTab({
    element:document.getElementById('tab'),
    index: 1,
    repeatClick: false
  },function(ret){
    var i = ret.index;
    $('.tab-panel').hide();
    $('#tab-' + i).show();
  });

  $('.item-ck').on('click', function(e){
    $(this).hasClass('active') ? $(this).removeClass('active') : $(this).addClass('active');
  });

  $('#limitCalculator').on('click', function(){
    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    var val_array = new Array();
    $('.item-ck').each(function(index, item){
      if($(item).hasClass('active')){
        val_array.push($(item).attr('value'));
      }
    });
    var values = val_array.join(',');
    $.ajax({
      type: 'GET',
      url: '<?php echo ENTRY_API_SITE_URL;?>/credit_loan.credit.calculator.php',
      data: { values: values },
      dataType: 'json',
      success: function(data){
        toast.hide();
        if(data.STS){
          dialog.alert({
            title: '<?php echo $lang['tip_total_interest'];?>',
            msg: data.DATA,
            buttons:['<?php echo $lang['act_confirm'];?>']
          });
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
  $('#repayment_type').on('change', function(){
    var repayment_type = $.trim($('#repayment_type').val());
    if(repayment_type == 'single_repayment'){
      $('#repaymentFrequency').hide();
    }else{
      $('#repaymentFrequency').show();
    }
  });
  $('#interestCalculator').on('click', function(){
    var loan_amount = $.trim($('#loan_amount').val()), loan_period = $.trim($('#loan_period').val()),loan_period_unit =  $.trim($('#loan_period_unit').val()),
        repayment_type = $.trim($('#repayment_type').val()), repayment_period = $.trim($('#repayment_period').val()), param = {};

    if(!loan_amount) return;
    if(repayment_type == 0) return;
    if(repayment_type != 'single_repayment'){
      if(repayment_period == 0) return;
      param.repayment_period = repayment_period;
    }

    if(!loan_period) return;
    param.loan_amount = loan_amount;
    param.repayment_type = repayment_type;
    param.loan_period = loan_period;
    param.loan_period_unit = loan_period_unit;
    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    $.ajax({
      type: 'GET',
      url: '<?php echo ENTRY_API_SITE_URL;?>/loan.calculator.new.php',
      data: param,
      dataType: 'json',
      success: function(data){
        toast.hide();
        if(data.STS){
          var interText = doT.template($('#tpl_loan_item').text());
          $('#loanList').html(interText(data.DATA));
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
  function showProductInfo(product_id, amount){
    var loan_period = $.trim($('#loan_period').val()), repayment_type = $.trim($('#repayment_type').val()), repayment_period = $.trim($('#repayment_period').val());
    window.location.href = '<?php echo WAP_SITE_URL;?>/index.php?act=loan&op=productInfo&product_id='+product_id+'&amount='+amount+'&loan_period='+loan_period+'&repayment_type='+repayment_type+'&repayment_period='+repayment_period;
  }
  function verifyFail(msg){
    toast.fail({
      title: msg,
      duration: 2000
    });
  }
</script>
