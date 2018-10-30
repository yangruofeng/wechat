<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/loan.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap calculator-view-wrap">
  <div class="aui-content custom-content aui-margin-b-10">
    <ul class="aui-list aui-media-list">
      <?php $list = $output['loan_level'];?>
      <?php foreach ($list as $key => $value) { ?>
        <li class="aui-list-item aui-list-item-middle" onclick="javascript:location.href='<?php echo getUrl('loan', 'loanLevelDetail', array('index'=>$value['uid']), false, WAP_SITE_URL)?>'">
          <div class="aui-media-list-item-inner">
            <div class="aui-list-item-media loan-period"><?php echo $key+1;?></div>
            <div class="aui-list-item-inner aui-list-item-arrow">
              <div class="aui-list-item-text">
                <div class="aui-list-item-title aui-font-size-14"><?php echo $value['min_amount'];?>-<?php echo $value['max_amount'];?></div>
                <div class="aui-list-item-right loan-date">
                  <?php
                    $time = $value['disburse_time'];
                    $disburse_time_unit = $value['disburse_time_unit'];
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
              <div class="aui-list-item-text loan-text">
                <?php $cert_list = $value['cert_list']; $i = 0; $count = count($cert_list); $str = '';?>
                <?php for ($i; $i<$count; $i++){
                        $label;
                        //1 身份证 2 户口本 3 护照 4 房产 5 汽车资产 6 工作证明 7 公务员（合在工作）8 家庭关系证明 9 土地
                        switch ($cert_list[$i]) {
                          case 1:
                            $label = $lang['label_id_card'];
                            break;
                          case 2:
                            $label = $lang['label_family_book'];
                            break;
                          case 3:
                            $label = $lang['label_passport'];
                            break;
                          case 4:
                            $label = $lang['label_house_certificate'];
                            break;
                          case 5:
                            $label = $lang['label_car_certificate'];
                            break;
                          case 6:
                            $label = $lang['label_working_certificate'];
                            break;
                          case 7:
                            $label = $lang['label_civil_servant'];
                            break;
                          case 8:
                            $label = $lang['label_family_relation_athentication'];
                            break;
                          case 9:
                            $label = $lang['label_land_certificate'];
                            break;
                          default:
                            $label = $lang['label_id_card'];
                            break;
                        }
                        if($i == $count-1){
                          $str .= $label;
                        }else{
                          $str .= $label.' | ';
                        }
                      }
                      echo $str;
                ?>
              </div>
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/loan-4.png?v=1" alt="" class="icon-credit">
            </div>
          </div>
        </li>
      <?php } ?>
    </ul>
  </div>
</div>
