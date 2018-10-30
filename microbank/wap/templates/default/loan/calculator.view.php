<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/loan.css?v=4">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap calculator-view-wrap">
  <?php $item = $output['item'];$product_info = $item['product_info'];$repayment_schema = $item['repayment_schema'];$total_repayment = $item['total_repayment'];?>
  <div class="base-info aui-margin-t-10">
    <div class="aui-content">
      <ul class="aui-list aui-form-list view-list">
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              <?php echo $lang['label_loan_amount'];?>
            </div>
            <div class="aui-list-item-input label-on">
              $<?php echo $item['arrival_amount'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              <?php echo $lang['label_repayment_method'];?>
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $_GET['repayment_type'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              <?php echo $lang['label_repayment_frequency'];?>
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $_GET['repayment_period'];?>
            </div>
          </div>
        </li>
        <li class="aui-list-item">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-label title">
              <?php echo $lang['label_loan_period'];?>
            </div>
            <div class="aui-list-item-input label-on">
              <?php echo $_GET['loan_period'];?>
            </div>
          </div>
        </li>
        <div class="secondary-info">
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label title">
                <?php echo $lang['label_Interest'];?>
              </div>
              <div class="aui-list-item-input label-off">
                <?php echo $item['interest_rate'];?>%
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label title">
                <?php echo $lang['label_loan_fee'];?>
              </div>
              <div class="aui-list-item-input label-off">
                <?php echo $item['loan_fee'];?>
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label title">
                <?php echo $lang['label_total_principal'];?>
              </div>
              <div class="aui-list-item-input label-off">
                <?php echo $item['arrival_amount'];?>
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label title">
                <?php echo $lang['label_total_interest'];?>
              </div>
              <div class="aui-list-item-input label-off">
                <?php echo $total_repayment['total_interest'];?>
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label title">
                <?php echo $lang['label_operation_fee'];?>
              </div>
              <div class="aui-list-item-input label-off">
                <?php echo $total_repayment['total_operator_fee'];?>
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label title">
                <?php echo $lang['label_total_repayment'];?>
              </div>
              <div class="aui-list-item-input label-off">
                <?php echo $total_repayment['total_period_pay'];?>
              </div>
            </div>
          </li>
        </div>
      </ul>
      <div class="open-secondary">
        <?php echo $lang['label_open'];?> <span><i class="aui-iconfont aui-icon-down i1"></i><i class="aui-iconfont aui-icon-down i2"></i></span>
      </div>
      <div class="fold-secondary">
        <?php echo $lang['label_fold'];?> <span><i class="aui-iconfont aui-icon-top i1"></i><i class="aui-iconfont aui-icon-top i2"></i></span>
      </div>
    </div>
  </div>
  <div class="schema-info aui-margin-t-10 aui-margin-b-15">
    <table class="schema-table" cellpadding="0" cellspacing="0">
      <thead>
        <tr>
          <td><?php echo $lang['label_index'];?></td>
          <td><?php echo $lang['label_amount'];?></td>
          <td><?php echo $lang['label_principal'];?></td>
          <td><?php echo $lang['label_Interest'];?></td>
          <td><?php echo $lang['label_operation_fee'];?></td>
        </tr>
      </thead>
      <tbody>
        <?php if(count($repayment_schema) > 0){ ?>
          <?php foreach($repayment_schema as $val){ ?>
            <tr>
              <td><?php echo $val['scheme_index'];?></td>
              <td><?php echo $val['amount'];?></td>
              <td><?php echo $val['receivable_principal'];?></td>
              <td><?php echo $val['receivable_interest'];?></td>
              <td><?php echo $val['receivable_operation_fee'];?></td>
            </tr>
          <?php } ?>
        <?php }else{ ?>
        <div class="no-record">
          <?php echo $lang['label_no_record'];?>
        </div>
      <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<script type="text/javascript">
$('.open-secondary').on('click', function(){
  $(this).hide();
  $('.secondary-info').show();
  $('.fold-secondary').show();
});
$('.fold-secondary').on('click', function(){
  $(this).hide();
  $('.secondary-info').hide();
  $('.open-secondary').show();
});
</script>
