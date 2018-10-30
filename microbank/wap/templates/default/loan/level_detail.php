<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/loan.css?v=7">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap calculator-view-wrap">
  <?php $item = $output['item']; ?>
  <div class="calculator-form aui-margin-t-10">
    <ul class="aui-list aui-form-list calculator-item">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_loanable_amount'];?>
          </div>
          <div class="aui-list-item-input textalignright color949494">
            <?php echo $item['min_amount'].'-'.$item['max_amount'].' USD';?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_lend_time'];?>
          </div>
          <div class="aui-list-item-input textalignright color949494">
            <?php
              $time = $item['disburse_time'];
              $disburse_time_unit = $item['disburse_time_unit'];
              $str = '';
              if($disburse_time_unit == 1){
                $str = $time > 1 ? $time.$lang['label_minutes'] : $time.$lang['label_minute'];
              }
              if($disburse_time_unit == 2){
                $str = $time > 1 ? $time.$lang['label_hours'] : $time.$lang['label_hour'];
              }
              if($disburse_time_unit == 3){
                $str = $time > 1 ? $time.$lang['label_days'] : $time.$lang['label_day'];
              }
              echo $str;
            ?>
          </div>
        </div>
      </li>
      <li class="level-list-item">
        <div class="title">
          <?php echo $lang['label_required_conditions'];?>
        </div>
        <div class="list">
          <?php $cert_list = $item['cert_list']; $i = 0; $count = count($cert_list); $str = '';?>
          <ul class="aui-list level-detail-list">
            <?php for ($i; $i<$count; $i++){
              $label; $img;
              switch ($cert_list[$i]) {
                case 1:
                  $label = $lang['label_id_card'];
                  $img = 'loan-32';
                  break;
                case 2:
                  $label = $lang['label_family_book'];
                  $img = 'loan-34';
                  break;
                case 3:
                  $label = $lang['label_passport'];
                  $img = 'loan-33';
                  break;
                case 4:
                  $label = $lang['label_house_certificate'];
                  $img = 'loan-38';
                  break;
                case 5:
                  $label = $lang['label_car_certificate'];
                  $img = 'loan-39';
                  break;
                case 6:
                  $label = $lang['label_working_certificate'];
                  $img = 'loan-36';
                  break;
                case 7:
                  $label = $lang['label_civil_servant'];
                  $img = 'loan-37';
                  break;
                case 8:
                  $label = $lang['label_family_relation_athentication'];
                  $img = 'loan-35';
                  break;
                case 9:
                  $label = $lang['label_land_certificate'];
                  $img = 'loan-34';
                  break;
                default:
                  $label = $lang['label_id_card'];
                  $img = 'loan-32';
                  break;
              }
            ?>
              <li class="aui-list-item" onclick="getCertedResult(<?php echo $cert_list[$i];?>);">
                <div class="aui-list-item-inner aui-list-item-arrow">
                  <img src="<?php echo WAP_SITE_URL;?>/resource/image/<?php echo $img;?>.png" /><?php echo $label;?>
                </div>
              </li>
            <?php } ?>
          </ul>
          <div class="level-detail-tip">
            <?php echo $lang['label_tip'].$lang['label_colon'].$lang['tip_upload_confidential'];?>
          </div>
        </div>
      </li>
    </ul>
  </div>
</div>
<script type="text/javascript">
function getCertedResult(type){
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'POST',
    url: '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=getCertedResult',
    data: { type: type },
    dataType: 'json',
    success: function(data){
      console.log(data)
      toast.hide();
      var state = data.DATA.state, cert_id = data.DATA.cert_id;
      if(data.STS){
        if(state == -10 || state == null){
          window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=cerification&type="+type;
        }else{
          window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=showCertCheckInfo&type="+type+"&state="+state+"&cert_id="+cert_id;
        }
      }else{
        $('.error-tip').text(data.MSG);
      }
    },
    error: function(xhr, type){
      toast.hide();
      verifyFail('<?php echo $lang['tip_get_data_error'];?>');
    }
  });
}
</script>
