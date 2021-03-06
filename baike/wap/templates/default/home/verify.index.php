<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap credit-wrap">
  <!--<div class="cert-tip">
    <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/hint.png" alt="">
    <ul class="cert-tip-ul">
      <li>• We promise to keep your information safety and never compromise.</li>
      <li>• Only need to take pictures and upload relevant information,you will get line of credit.</li>
      <li>• The more certificatioon,the higher the credit line.</li>
    </ul>
  </div>-->
  <?php $cert_list = $output['credit'];?>
  <div class="cert-wrap member-info">
    <p class="t"><?php echo $lang['label_personal_information'];?></p>
    <div class="cert-list clearfix">
      <div class="item" onclick="<?php if($cert_list[certificationTypeEnum::ID] == -10){ ?>getCertedResult(<?php echo certificationTypeEnum::ID;?>);<?php }else{ ?>showCertCheckInfo(<?php echo certificationTypeEnum::ID;?>);<?php } ?>">
        <?php if($cert_list[certificationTypeEnum::ID] == certStateEnum::CREATE){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-29.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::ID] == certStateEnum::LOCK){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-28.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::ID] == certStateEnum::PASS){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-26.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::ID] == certStateEnum::NOT_PASS){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-27.png" alt="" class="icon-state">
        <?php } ?>
        <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-17.png?v=1" alt="">
        <p><?php echo $lang['label_identity_athentication'];?></p>
      </div>
      <div class="item" onclick="<?php if($cert_list[certificationTypeEnum::FAIMILYBOOK] == -10){ ?>getCertedResult(<?php echo certificationTypeEnum::FAIMILYBOOK;?>);<?php }else{ ?>showCertCheckInfo(<?php echo certificationTypeEnum::FAIMILYBOOK;?>);<?php } ?>">
        <?php if($cert_list[certificationTypeEnum::FAIMILYBOOK] == certStateEnum::CREATE){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-29.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::FAIMILYBOOK] == certStateEnum::LOCK){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-28.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::FAIMILYBOOK] == certStateEnum::PASS){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-26.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::FAIMILYBOOK] == certStateEnum::NOT_PASS){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-27.png" alt="" class="icon-state">
        <?php } ?>
        <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-18.png?v=1" alt="">
        <p><?php echo $lang['label_family_book'];?></p>
      </div>
      <div class="item" onclick="<?php if($cert_list[certificationTypeEnum::WORK_CERTIFICATION] == -10){ ?>getCertedResult(<?php echo certificationTypeEnum::WORK_CERTIFICATION;?>);<?php }else{ ?>showCertCheckInfo(<?php echo certificationTypeEnum::WORK_CERTIFICATION;?>);<?php } ?>">
        <?php if($cert_list[certificationTypeEnum::WORK_CERTIFICATION] == certStateEnum::CREATE){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-29.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::WORK_CERTIFICATION] == certStateEnum::LOCK){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-28.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::WORK_CERTIFICATION] == certStateEnum::PASS){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-26.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::WORK_CERTIFICATION] == certStateEnum::NOT_PASS){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-27.png" alt="" class="icon-state">
        <?php } ?>
        <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-19.png?v=1" alt="">
        <p><?php echo $lang['label_working_certificate'];?></p>
      </div>
      <div class="item" onclick="<?php if($cert_list[certificationTypeEnum::RESIDENT_BOOK] == -10){ ?>getCertedResult(<?php echo certificationTypeEnum::RESIDENT_BOOK;?>);<?php }else{ ?>showCertCheckInfo(<?php echo certificationTypeEnum::RESIDENT_BOOK;?>);<?php } ?>">
        <?php if($cert_list[certificationTypeEnum::RESIDENT_BOOK] == certStateEnum::CREATE){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-29.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::RESIDENT_BOOK] == certStateEnum::LOCK){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-28.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::RESIDENT_BOOK] == certStateEnum::PASS){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-26.png" alt="" class="icon-state">
        <?php } ?>
        <?php if($cert_list[certificationTypeEnum::RESIDENT_BOOK] == certStateEnum::NOT_PASS){ ?>
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-27.png" alt="" class="icon-state">
        <?php } ?>
        <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-30.png?v=1" alt="">
        <p><?php echo $lang['label_resident_book'];?></p>
      </div>
    </div>
  </div>
  <div class="cert-wrap member-info">
    <p class="t"><?php echo $lang['label_assets_certification'];?></p>
    <div class="cert-list clearfix">
      <div class="item" onclick="cetTypeList(<?php echo certificationTypeEnum::CAR;?>);">
        <span class="icon-number"><?php echo $cert_list[certificationTypeEnum::CAR]?:0;?></span>
        <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-20.png?v=1" alt="">
        <p><?php echo $lang['label_vehicle_property'];?></p>
      </div>
      <div class="item" onclick="cetTypeList(<?php echo certificationTypeEnum::LAND;?>);">
        <span class="icon-number"><?php echo $cert_list[certificationTypeEnum::LAND]?:0;?></span>
        <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-21.png?v=1" alt="">
        <p><?php echo $lang['label_landg_property'];?></p>
      </div>
      <div class="item" onclick="cetTypeList(<?php echo certificationTypeEnum::HOUSE;?>);">
        <span class="icon-number"><?php echo $cert_list[certificationTypeEnum::HOUSE]?:0;?></span>
        <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-22.png?v=1" alt="">
        <p><?php echo $lang['label_housing_property'];?></p>
      </div>
      <div class="item" onclick="cetTypeList(<?php echo certificationTypeEnum::MOTORBIKE;?>);">
        <span class="icon-number"><?php echo $cert_list[certificationTypeEnum::MOTORBIKE]?:0;?></span>
        <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-31.png?v=1" alt="">
        <p><?php echo $lang['label_motorcycle_asset_certificate'];?></p>
      </div>
    </div>
  </div>
  <div class="cert-wrap member-info">
    <p class="t"><?php echo $lang['label_relationships'];?></p>
    <div class="cert-list clearfix">
      <div class="item" onclick="cetTypeList(<?php echo certificationTypeEnum::GUARANTEE_RELATIONSHIP;?>);">
        <span class="icon-number"><?php echo $cert_list[certificationTypeEnum::GUARANTEE_RELATIONSHIP]?:0;?></span>
        <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/loan-23.png" alt="">
        <p><?php echo $lang['label_family_relation_athentication'];?></p>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
var back = '<?php echo $_GET['back'];?>', refresh = '<?php echo $_GET['refresh'];?>';
if(refresh == 1){
  //window.location.href = "<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=verify&cid=<?php echo $_GET['cid'];?>&id=<?php echo $_GET['id'];?>&name=<?php echo $_GET['name'];?>"
}
if(back){
  $('#header .back').attr('onclick', "javascript:location.href='<?php echo getUrl('home', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>'");
}

function cetTypeList(type){
  window.location.href = "<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=certTypeList&type="+type+"&id="+"<?php echo $_GET['id'];?>";
}
function getCertedResult(type){
  if(window.operator){
    window.operator.uploadAssets('<?php echo $_GET['id'];?>', 0 , type);
    return;
  }
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'get',
    url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=getCertedResult',
    data: { type: type, id: '<?php echo $_GET['id'];?>' },
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        window.location.href = "<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=cerification&type="+type+"&id="+"<?php echo $_GET['id'];?>";
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

function showCertCheckInfo(type){
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'get',
    url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=getCertedResult',
    data: { type: type, id: '<?php echo $_GET['id'];?>' },
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        window.location.href = "<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=showCertCheckInfo&id="+'<?php echo $_GET['id'];?>'+"&type="+type+"&state="+data.DATA.state+"&cert_id="+data.DATA.cert_id;
      }else{
        if(data.CODE == '<?php echo errorCodesEnum::INVALID_TOKEN;?>' || data.CODE == '<?php echo errorCodesEnum::NO_LOGIN;?>'){
          if(window.operator){
            window.operator.reLogin();
            return;
          }
        }
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
