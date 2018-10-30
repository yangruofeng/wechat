<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<?php $list = $output['list'];print_r($list); ?>
<div class="wrap pending-repayment-wrap company-repayment">
    <ul class="aui-list aui-media-list repayment-base-ul">
      <li class="aui-list-item aui-list-item-middle">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title">Repayment Amount</div>
              <div class="aui-list-item-right total"><?php echo $_GET['amount'];?> <em><?php echo $_GET['currency'];?></em></div>
            </div>
          </div>
        </div>
      </li>
      <li class="aui-list-item aui-list-item-middle bank-select">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner inner">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title title">Destination Bank</div>
              <div class="aui-list-item-right input-select">
                <select class="" name="repayment_type" id="repayment_type">
                  <option value="0"><?php echo $lang['label_select'];?></option>
                  <?php foreach($list as $k => $v){ ?>
                    <option value="<?php echo $v['uid'];?>"><?php echo $v['bank_name'];?></option>
                  <?php } ?>
                </select>
                <i class="aui-iconfont aui-icon-down"></i>
              </div>
            </div>
          </div>
        </div>
      </li>
      <li class="aui-list-item aui-list-item-middle">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title">Account No.</div>
              <div class="aui-list-item-right"><span id="acc_no">123</span></div>
            </div>
          </div>
        </div>
      </li>
      <li class="aui-list-item aui-list-item-middle">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title">Account Name</div>
              <div class="aui-list-item-right"><span id="acc_name">123</span></div>
            </div>
          </div>
        </div>
      </li>
      <li class="aui-list-item aui-list-item-middle">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-inner">
            <div class="aui-list-item-text">
              <div class="aui-list-item-title">Account Phone</div>
              <div class="aui-list-item-right"><span id="acc_phone">123</span></div>
            </div>
          </div>
        </div>
      </li>
    </ul>
</div>
<div class="">
<?php foreach($list as $k => $v){ ?>
    <div class="">
        <p><?php echo $v['bank_account_no'];?></p>
        <p><?php echo $v['bank_account_name'];?></p>
        <p><?php echo $v['bank_account_phone'];?></p>
    </div>
<?php } ?>
</div>
<script type="text/javascript">

</script>
