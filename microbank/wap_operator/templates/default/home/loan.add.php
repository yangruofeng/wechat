<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=6">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap loan-wrap">
  Coming soon
  <?php $guarantee_relationship = $output['guarantee_relationship'];?>
  <!--<form id="" method="post">
    <input type="hidden" value="<?php echo $output['token'];?>" name="token">
    <input type="hidden" value="<?php echo $output['member_id'];?>" name="member_id">
    <?php if($output['cert_id'] > 0) { ?>
      <input type="hidden" value="<?php echo $output['cert_id'];?>" name="cert_id">
    <?php } ?>
    <div class="cerification-input aui-margin-b-10">
      <div class="loan-form">
        <ul class="aui-list aui-form-list loan-item">
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Client ID
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="member_id" id="member_id" value="<?php echo $output['client_id'];?>" readonly />
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Client Name
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="member_name" id="member_name" value="<?php echo $output['client_name'];?>" readonly />
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Loan Amount
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="amount" id="amount" value="" placeholder="Enter" />
                <span class="p-unit">USD</span>
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
              Loan Period
              </div>
              <div class="aui-list-item-input input-period">
                <input type="text" name="loan_time" id="loan_time" value="" placeholder="Enter" />
                <select class="" name="loan_time_unit" id="loan_time_unit">
                  <option value="year"><?php echo $lang['label_year'];?></option>
                  <option value="month" selected><?php echo $lang['label_month'];?></option>
                  <option value="day"><?php echo $lang['label_day'];?></option>
                </select>
                <i class="aui-iconfont aui-icon-down"></i>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div style="padding: .2rem .8rem;">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" id="submit">Submit</div>
    </div>
  </form>-->
</div>
<div class="upload-success">
  <div class="content">
    <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/gou.png" alt="">
    <p class="title"><?php echo 'Upload Successfully';?></p>
    <p class="tip"><?php echo str_replace('xxx','<em id="count">3</em>','It exits automatically xxx seconds later.');?></p>
  </div>
</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/common.js"></script>
<script type="text/javascript">
  $('#submit').on('click', function(){
    var client_id = '<?php echo $_GET['id'];?>';
        amount = $.trim($('#amount').val()),
        loan_time = $.trim($('#loan_time').val()),
        loan_time_unit = $.trim($('#loan_time_unit').val());
    if(!client_id){
      verifyFail('<?php echo 'Please reselect client.';?>');
      return;
    }
    if(!amount){
      verifyFail('<?php echo 'Please input loan amount.';?>');
      return;
    }
    if(!loan_time){
      verifyFail('<?php echo 'Please input loan time.';?>');
      return;
    }

    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=ajaxAddLoanRequest',
      data: {member_id: client_id,amount: amount, currency: 'USD',loan_time: loan_time,loan_time_unit: loan_time_unit},
      dataType: 'json',
      success: function(data){
        toast.hide();
        if(data.STS){
          $('.upload-success').show();
          var count = $('#count').text();
          var times = setInterval(function(){
            count--;
            $('#count').text(count);
            if(count <= 1){
              clearInterval(times);
              window.location.href = "<?php echo getUrl('home', 'search', array('guid'=>$_GET['cid'],'type'=>1,'back'=>'home'), false, WAP_OPERATOR_SITE_URL)?>";
            }
          },1000);
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
