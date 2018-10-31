<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=7">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=1">
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
  <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  <span class="right-search-btn" onclick="window.location.href='<?php echo getUrl('home', 'suggestHistory', array('id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>'"><i class="aui-iconfont aui-icon-menu"></i></span>
</header>
<style>
    .table-rate{
        margin: 5px;
    }
    .table-rate>tbody>tr>td{
        font-size: 0.5rem;
    }
    .table-rate>tbody>tr>td>input{
        font-size: 0.5rem;
        border: 1px solid #fdb58f;
        border-radius: 4px!importan;
        min-height: 0.1rem;
    }
</style>
<div class="wrap loan-wrap">
  <?php $data = $output['data'];
        $asset_list = $data['asset_list'];
        $credit_system_rate = $data['credit_system_rate'];
        $last_submit_suggest = $data['last_submit_suggest'];
        $suggest_detail_list = $last_submit_suggest['suggest_detail_list'];$new_suggest_detail_list = array();
        foreach ($suggest_detail_list as $key => $value) {
          $new_suggest_detail_list[$value['member_asset_id']] = $value;
        }
  ?>
  <form class="custom-form" id="frm_credit" method="post">
    <div class="cerification-input aui-margin-b-10">
      <div class="loan-form request-credit-form">
        <ul class="aui-list aui-form-list loan-item">
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Evalution of assets
              </div>
              <div class="aui-list-item-input">
                <?php echo ncPriceFormat($data['total_assets_evaluation']);?>
              </div>
            </div>
          </li>

          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Evalution of income(Monthly)
              </div>
              <div class="aui-list-item-input">
                <?php echo ncPriceFormat($data['total_income']);?>
              </div>
            </div>
          </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label label">
                        Client Request Credit
                    </div>
                    <div class="aui-list-item-input">
                        <input type="text" class="mui_input" name="client_request_credit" id="client_request_credit" value="<?php echo $last_submit_suggest['client_request_credit']?:'';?>" />
                    </div>
                </div>
            </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Monthly Repayment Ability
              </div>
              <div class="aui-list-item-input">
                <input type="text" class="mui_input" name="monthly_repayment_ability" id="monthly_repayment_ability" value="<?php echo $last_submit_suggest['monthly_repayment_ability']?:'';?>" />
              </div>
            </div>
          </li>
          <li class="aui-list-item liner2">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Invalid Terms
              </div>
              <div class="aui-list-item-input">
                <input type="text" class="mui_input" name="invalid_terms" id="invalid_terms" value="<?php echo $last_submit_suggest['credit_terms']?:'';?>" /> <span class="u">(Months)</span>
              </div>
            </div>
          </li>
          <li class="aui-list-item liner2">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Default Credit 
              </div>
              <div class="aui-list-item-input">
                <input type="text" class="mui_input credit-amount-item" onblur="maxCreditcalculation();" name="default_credit" id="default_credit" value="<?php echo $last_submit_suggest['default_credit']?:'';?>" />
                  <div class="t">
                      <?php
                      $income_list=$data['income_list'];
                      $industry_list=$data['industry_list'];
                      $income_1=$income_list['income_rental_land']+$income_list['income_rental_housing']+$income_list['income_salary']+$income_list['income_others'];
                      $income_1=$income_1?:0;
                      $credit_rate_1=$credit_system_rate['default_credit_rate'];
                      $credit_1=intval($income_1*$credit_rate_1);


                      $total_sug=$credit_1;

                      foreach($industry_list as $ids_item){
                          $credit_ids=intval($ids_item['profit']*$ids_item['credit_rate']/100);
                          $total_sug+=$credit_ids;
                          echo "<p style='font-size: 0.5rem;color: #b3b3b3'>".$ids_item['industry_name']." : ".$ids_item['profit']."*".$ids_item['credit_rate']."%=".$credit_ids."</p>";
                      }
                      echo "<p  style='font-size: 0.5rem;color: #b3b3b3'>"."Others : ".$income_1."*".($credit_rate_1*100)."%=".$credit_1."</p>";
                      if($total_sug>$credit_1){
                          echo "<p  style='font-size: 0.5rem;color: #b3b3b3'>Total Suggest : ".$total_sug."</p>";
                      }

                      ?>
                  </div>
              </div>
            </div>
          </li>
          <?php if(count($asset_list) > 0){ ?>
            <li class="aui-list-item border-b-none">
              <div class="aui-list-item-inner">
                <div class="aui-list-item-label label label-all">
                  Increase Credit By Mortgage Asset
                </div>
              </div>
            </li>
            <li class="aui-list-item">
              <div class="assets-list" id="assetsList">
                <?php foreach ($asset_list as $k => $v) { ?>
                    <?php
                    $str = '';$rate = 0;
                    switch ($v['asset_type']) {
                        case certificationTypeEnum::CAR :
                            $str = 'Car';
                            $rate = $credit_system_rate['car_credit_rate'];
                            break;
                        case certificationTypeEnum::HOUSE :
                            $str = 'Housing & Store';
                            $rate = $credit_system_rate['house_credit_rate'];
                            break;
                        case certificationTypeEnum::LAND :
                            $str = 'Land';
                            $rate = $credit_system_rate['land_credit_rate'];
                            break;
                        case certificationTypeEnum::MOTORBIKE :
                            $str = 'Motorbike';
                            $rate = $credit_system_rate['motorbike_credit_rate'];
                            break;
                        default:
                            $str = 'Car';
                            break;
                    }
                    ?>
                  <div class="item">

                      <span class="name">
                          <?php echo $v['asset_name']?>
                      </span>

                    <span class="amount">
                        <input type="text" class="mui_input inline-input suggest-item-asset  credit-amount-item" name="increase_credit" onblur="maxCreditcalculation();" data-asset-id="<?php echo $v['uid'];?>" value="<?php echo $new_suggest_detail_list[$v['uid']]['credit'];?>" />
                        <span class="t"><?php echo $str."&nbsp;&nbsp;&nbsp;";?>(<?php echo $v['evaluation']?:0;?>*<?php echo $rate*100;?>%=<?php echo $v['evaluation']*$rate;?>)</span>
                  </div>
                <?php } ?>
              </div>
            </li>
          <?php } ?>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Max Credit
              </div>
              <div class="aui-list-item-input">
               <input type="text" class="mui_input" name="max_credit" id="max_credit" value="<?php echo $last_submit_suggest['max_credit']?:'';?>" readonly />
              </div>
            </div>
          </li>
            <li class="aui-list-item">
                <?php
                    $prod_list=$output['product_list'];
                ?>
                <table class="table table-rate">
                    <tr>
                        <td>Product</td>
                        <td>NoMortgage</td>
                        <td>MortgageSoft</td>
                        <td>MortgageHard</td>
                    </tr>
                    <?php foreach($prod_list as $item){?>
                        <tr class="tr-credit-item" data-product-id="<?php echo $item['uid']?>" data-product-name="<?php echo $item['sub_product_name']?>">
                            <td><?php echo $item['sub_product_name']?></td>
                            <td>
                                <input class="input-rate" type="text" data-product-id="<?php echo $item['uid']?>" data-item-key="rate_no_mortgage" value="<?php echo $item['last_rate_no_mortgage']?:$item['max_rate_mortgage'];?>">
                            </td>
                            <td>
                                <input class="input-rate" type="text" data-product-id="<?php echo $item['uid']?>" data-item-key="rate_mortgage1" value="<?php echo $item['last_rate_mortgage1']?:$item['max_rate_mortgage'];?>">
                            </td>
                            <td>
                                <input class="input-rate" type="text" data-product-id="<?php echo $item['uid']?>" data-item-key="rate_mortgage2" value="<?php echo $item['last_rate_mortgage2']?:$item['max_rate_mortgage'];?>">
                            </td>
                        </tr>
                    <?php }?>
                </table>
            </li>

          <li class="aui-list-item last-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label label-all">
                Remark
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner paddingright075">
              <textarea class="mui_textarea" name="remark" id="remark"><?php echo $last_submit_suggest['remark']?:'';?></textarea>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div style="padding: 0 .8rem 1rem .8rem;">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" onclick="submit()" id="submit">Submit</div>
    </div>
  </form>
</div>
<div class="upload-success">
  <div class="content">
    <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/gou.png" alt="">
    <p class="title"><?php echo 'Upload Successfully';?></p>
    <p class="tip"><?php echo str_replace('xxx','<em id="count">3</em>','It exits automatically xxx seconds later.');?></p>
  </div>
</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/common.js?v=1"></script>
<script type="text/javascript">
  if(window.operator){
    window.operator.showTitle('<?php echo $output['header_title'];?>');
  }

function maxCreditcalculation(){
    var _total=0;
    $("#frm_credit").find(".credit-amount-item").each(function(){
        _total=parseInt(_total)+parseInt($(this).val());
    });
    $('#max_credit').val(_total);

}
function submit(){
  var params = {}, member_id = '<?php echo $_GET['id'];?>', 
      officer_id = '<?php echo cookie('member_id');?>', token = '<?php echo cookie('token');?>', 
      monthly_repayment_ability = $.trim($('#monthly_repayment_ability').val()),
      invalid_terms = $.trim($('#invalid_terms').val()),
      default_credit = $.trim($('#default_credit').val()),
      max_credit = $.trim($('#max_credit').val()),
      client_request_credit=  $.trim($('#client_request_credit').val()),
      remark = $.trim($('#remark').val());
    
  if(!member_id){
    verifyFail('<?php echo 'Please reselect client.';?>');
    return;
  }
  if(!monthly_repayment_ability){
    verifyFail('<?php echo 'Please input monthly repayment ability.';?>');
    return;
  }
  if(!checkMoney(monthly_repayment_ability)){
    verifyFail('<?php echo 'Monthly Repayment Ability must be monetary.';?>');
    return;
  }
  if(!invalid_terms){
    verifyFail('<?php echo 'Please input invalid terms.';?>');
    return;
  }
  if(!checkInteger(invalid_terms)){
    verifyFail('<?php echo 'Invalid terms must be positive integer.';?>');
    return;
  }
  if(!default_credit){
    verifyFail('<?php echo 'Please input default credit.';?>');
    return;
  }
  if(!checkMoney(default_credit)){
    verifyFail('<?php echo 'Default credit must be monetary.';?>');
    return;
  }

  if(!max_credit){
    verifyFail('<?php echo 'Please input max credit.';?>');
    return;
  }
    //取资产信用
    var _arr_assets=[];
    $("#frm_credit").find(".suggest-item-asset").each(function(){
        _arr_assets.push({
            "asset_id":$(this).data('asset-id'),
            "credit":$(this).val()?parseInt($(this).val()):0
        });
    });
   var _arr_assets_json=encodeURI(JSON.stringify(_arr_assets));

    //取利率建议
    var _arr_rate=[];
    $("#frm_credit").find(".tr-credit-item").each(function(){
        var _new_item={};
        _new_item.product_id=$(this).data("product-id");
        _new_item.product_name=$(this).data("product-name");
        $(this).find(".input-rate").each(function(){
            _new_item[$(this).data('item-key')]=$(this).val();
        });
        _arr_rate.push(_new_item);
    });
    var _arr_rate_json=encodeURI(JSON.stringify(_arr_rate));


    
  params.member_id = member_id;
  params.officer_id = officer_id;
  params.token = token;
  params.monthly_repayment_ability = monthly_repayment_ability;
  params.invalid_terms = invalid_terms;
  params.default_credit = default_credit;
  params.client_request_credit=client_request_credit;
   params.asset_credit = _arr_assets_json;
  params.rate_credit=_arr_rate_json;
  params.max_credit = max_credit;
  params.remark = remark;
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'POST',
    url: '<?php echo ENTRY_API_SITE_URL;?>/officer.submit.suggest.member.credit.php',
    data: params,
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
            if(window.operator){
                window.operator.memberInfo();
                return;
              }
              $('.back').click();
          }
        },1000);
      }else{
        if(data.CODE == '<?php echo errorCodesEnum::INVALID_TOKEN;?>' || data.CODE == '<?php echo errorCodesEnum::NO_LOGIN;?>'){
          reLogin();
        }
        verifyFail(data.MSG);
      }

    },
    error: function(xhr, type){
      toast.hide();
      verifyFail('<?php echo $lang['tip_get_data_error'];?>');
    }
  });
}
 /**  
    * 将二维数组转为 json 字符串  
    */  
    function encodeArray2D(obj) {  
        var array = [];  
        for (var i = 0; i < obj.length; i++) {  
            array[i] = '[' + obj[i].join(',') + ']';  
        }  
        return '[' + array.join(',') + ']';  
    }  
</script>
