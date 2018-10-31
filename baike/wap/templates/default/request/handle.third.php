<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/request.css?v=6">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap handle-wrap">
  <div class="handle-third">
    <div class="handle-nav">
      <ul class="nav-ul clearfix">
        <li class="nav-item">
          <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up1.png"></p>
          <p class="text">Comment</p>
        </li>
        <li class="nav-item">
          <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up2_press.png"></p>
          <p class="text">Product</p>
        </li>
        <li class="nav-item">
          <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up3_1_press.png"></p>
          <p class="text">Rate</p>
        </li>
      </ul>
    </div>  
    <?php $detail = $output['detail'];?>
    <ul class="aui-list request-detail-ul aui-margin-t-10">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            interest rate
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $detail['interest_rate'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            operatre fee
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $detail['operation_fee'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            loan fee
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $detail['loan_fee'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            admin fee
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $detail['admin_fee'];?>
          </div>
        </div>
      </li>
    </ul>
    <?php if($output['type'] == 1){ ?>
      <div style="padding: 0 .8rem;">
        <div class="aui-btn aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" id="confirm">Confirm</div>
      </div>
    <?php }else{ ?>
      <div class="rate-tip">所选择产品没有匹配到rate，请重新选择产品<span class="backSecond">Back</span></div>
    <?php } ?>
  </div>
  <div class="repayment-schema" style="display: none;">
    <div class="schema-info aui-margin-t-10 aui-margin-b-15">
      <p class="title">Repayment Schema</p>
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
        </tbody>
      </table>
      <div style="padding: 0 .8rem;">
        <div class="aui-btn aui-btn-block custom-btn custom-btn-purple schema-tip">Back Request</div>
      </div>
    </div>
  </div>
</div>
<script type="text/html" id="tpl_repayment_schema">
  {{ if( it && it.length > 0 ){ }}
    {{ for(var i = 0; i< it.length; i++) { }}
      <tr>
        <td>{{=it[i]['scheme_index']}}</td>
        <td>{{=it[i]['amount']}}</td>
        <td>{{=it[i]['receivable_principal']}}</td>
        <td>{{=it[i]['receivable_interest']}}</td>
        <td>{{=it[i]['receivable_operation_fee']}}</td>
    </tr>
    {{ } }}
  {{ } }}
</script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/doT.min.js"></script>
<script type="text/javascript">
  $('.rate-tip').on('click', function(){
    $('.back').click();
  });
  $('.schema-tip').on('click', function(){
    window.location.href = '<?php echo getUrl('request', 'checkApplication', array('source'=>'app'), false, WAP_OPERATOR_SITE_URL)?>';
  });
  $('#confirm').on('click', function(){
    if($(this).hasClass('disabled')){
      return;
    }
    $.ajax({
      url: '<?php echo getUrl('request', 'ajaxHandleApproved', array(), false, WAP_OPERATOR_SITE_URL)?>',
      type: 'post',
      data: {request_id: '<?php echo $_GET['request_id'];?>'},
      dataType: 'json',
      success: function(ret){
        console.log(ret)
        if(ret.STS){
          var preview_info = ret.DATA.preview_info, installment_schema = preview_info.installment_schema;
          console.log(installment_schema)
          var interText = doT.template($('#tpl_repayment_schema').text());
          $('tbody').html(interText(installment_schema));
          $('.handle-third').hide();
          $('.repayment-schema').show();
          $('#header .back').attr('onclick', "javascript:location.href='<?php echo getUrl('request', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>'");
        }else{
          verifyFail(ret.MSG);
        }
      }
    });
  });
</script>
