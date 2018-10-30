<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=10">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=1">
<style>
.aui-tab-item.aui-active {
    z-index: 1;
}
</style>
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
  <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  <span class="right-search-btn" onclick="window.location.href='<?php echo getUrl('home', 'incomeResearchHistory', array('id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>'"><i class="aui-iconfont aui-icon-menu"></i></span>
</header>
<?php
$member_industry_info = $output['member_industry_info'];
$data = $output['last_research_info'];
?>
<div class="wrap assets-evalute-wrap inncome-research-wrap">
  <div class="evalute-amount">
    <p class="amount"><span id="span_total_income"><?php echo ncPriceFormat($output['total']);?> </span><em>USD</em></p>
    <p class="title"><?php echo $lang['label_total_imcome'];?></p>
  </div>
  <div class="assets-list custom-form">
    <form id="incomeForm" enctype="multipart/form-data" method="post">
      <input type="hidden" value="<?php echo cookie('token');?>" name="token">
      <input type="hidden" value="<?php echo cookie('member_id');?>" name="officer_id">
      <input type="hidden" value="<?php echo $_GET['id'];?>" name="member_id">
      <input type="hidden" value="<?php echo $member_industry_info['uid'];?>" name="industry_id" id="industry_id">
      <ul class="aui-list assets-ul aui-margin-b-10">
        <li class="aui-list-item assets-item business-title">
          <?php echo $lang['label_rental'];?>
        </li>
        <li class="aui-list-item assets-item">
          <div><?php echo 'Housing';?></div>
          <div><input type="text" onblur="calcTotalIncome()" class="mui_input survey-income-item" name="income_rental_housing" id="income_rental_housing" value="<?php echo $data['income_rental_housing']?:'';?>" /></div>
        </li>
        <li class="aui-list-item assets-item">
          <div><?php echo 'Land';?></div>
          <div><input type="text" class="mui_input survey-income-item" onblur="calcTotalIncome()" name="income_rental_land" id="income_rental_land" value="<?php echo $data['income_rental_land']?:'';?>" /></div>
        </li>
      </ul>
      <?php if(count($member_industry_info)){
          $min_i=min(array_keys($member_industry_info));
          ?>
          <div class="aui-tab" id="tab">
              <?php $i = 1;foreach($member_industry_info as $ids_i=>$ids_item){?>
                  <div class="aui-tab-item tab-<?php echo $i;?> <?php if($ids_i==$min_i) echo 'aui-active'?>" type="<?php echo $ids_item['uid']?>" onclick="changeTab(this)">
                    <?php echo $ids_item['industry_name']?>
                  </div>
              <?php $i++; }?>
          </div>
          <div class="aui-tab-content">
              <?php foreach($member_industry_info as $ids_i=>$ids_item){?>
                  <div class="aui-tab-content-item tab-item-<?php echo $ids_item['uid'];?>" style="<?php if($ids_i>$min_i) echo 'display:none'?>">
                      <?php
                        $child = my_json_decode($ids_item['industry_json']);
                        $child_kh=my_json_decode($ids_item['industry_json_kh']);
                        foreach($child as $chd_k=>$chk_v){
                            if($child_kh[$chd_k]) $child[$chd_k]=$child_kh[$chd_k];
                        }
                        $recent_value=my_json_decode($ids_item['research_json']);
                      ?>
                      <ul class="aui-list assets-ul aui-margin-b-10">
                          <li class="aui-list-item income-research-form industry-item"  style="padding-top: 8px" data-industry-name="【<?php echo $ids_item['industry_name']?>】" data-industry-uid="<?php echo $ids_item['uid']?>">
                              <div class="assets-list">
                                  <?php foreach ($child as $ck => $cv) { ?>
                                      <div class="item">
                                          <span class="name"><?php echo $cv;?></span>
                                          <span class="amount">
                                              <input type="text" style="width: 100%!important;" class="mui_input inline-input industry-item-survey" data-item-code="<?php echo $ck;?>" value="<?php echo $recent_value[$ck]?>"/>
                                          </span>
                                      </div>
                                  <?php } ?>
                                  <div class="item">
                                      <span class="name">Place</span>
                                      <span class="amount">
                                          <div class="mui_select_block" style="display: inline-block;width: 8rem;background-color: #ffffff">
                                              <select class="mui_select industry-item-survey" data-item-code="place">
                                                  <option value=""><?php echo $lang['label_select'];?></option>
                                                  <?php foreach($output['business_place'] as $place_item){?>
                                                      <option value="<?php echo $place_item['uid']?>" <?php if($recent_value['place']==$place_item['uid']) echo 'selected'?>><?php echo $place_item['place'];?></option>
                                                  <?php }?>
                                              </select>
                                              <i class="aui-iconfont aui-icon-down"></i>
                                          </div>
                                      </span>
                                  </div>
                                  <div class="item">
                                      <span class="name">Employees</span>
                                      <span class="amount" >
                                          <input  type="text" value="<?php echo $recent_value['employees']?:0;?>" style="width: 8rem;" class="mui_input inline-input industry-item-survey survey-employees" onblur="calcTotalEmployees()" data-item-code="employees">
                                      </span>
                                  </div>
                                  <div class="item">
                                      <span class="name">Profit</span>
                                      <span class="amount" >
                                          <input  type="text" value="<?php echo $recent_value['profit']?>" style="width: 8rem;" class="mui_input inline-input industry-item-survey survey-profit" onblur="calcTotalProfit()" data-item-code="profit">
                                      </span>
                                  </div>
                              </div>
                          </li>
                      </ul>
                  </div>
              <?php }?>

          </div>
          <ul class="aui-list assets-ul aui-margin-b-10">
              <li class="aui-list-item assets-item business-title">
                  <?php echo $lang['label_business'];?>
              </li>
              <li class="aui-list-item assets-item">
                  <div>Total <?php echo $lang['label_employees'];?></div>
                  <div><input type="text" class="mui_input " name="business_employees" id="total_survey_employees" readonly="true" style="background-color: #f3fafa" value="<?php echo $data['business_employees']?:'';?>" /></div>
              </li>
              <li class="aui-list-item assets-item">
                  <div>Total <?php echo $lang['label_profit'];?></div>
                  <div><input type="text" class="mui_input  survey-income-item" name="income_business" id="total_survey_profit" readonly="true" style="background-color: #f3fafa" value="<?php echo $data['income_business']?:'';?>" /></div>
              </li>

          </ul>
      <?php }?>


      <ul class="aui-list assets-ul aui-margin-b-10">
        <li class="aui-list-item assets-item business-title">
            <span>
              <?php echo $lang['label_salary'];?>
            </span>
            <span style="display: inline-block;float: right" data-sts="0" onclick="span_change_visible_onclick(this)">
                <i class="aui-iconfont aui-icon-down"></i>
            </span>
        </li>
          <li class="aui-list-item assets-item content-item" style="display: none">
              <ul class="aui-list assets-ul aui-margin-b-10">
                  <li class="aui-list-item assets-item">
                      <div><?php echo $lang['label_company_name'];?></div>
                      <div><input type="text" class="mui_input" name="company_name" id="company_name" value="<?php echo $data['company_name']?:'';?>" /></div>
                  </li>
                  <li class="aui-list-item assets-item">
                      <div><?php echo $lang['label_position'];?></div>
                      <div><input type="text" class="mui_input" name="work_position" id="work_position" value="<?php echo $data['work_position']?:'';?>" /></div>
                  </li>
                  <li class="aui-list-item assets-item">
                      <div><?php echo $lang['label_salary'];?></div>
                      <div><input type="text" class="mui_input  survey-income-item" name="income_salary" onblur="calcTotalIncome()" id="income_salary" value="<?php echo $data['income_salary']?:'';?>" /></div>
                  </li>
              </ul>
          </li>

      </ul>
        <ul class="aui-list assets-ul aui-margin-b-10">
            <li class="aui-list-item assets-item business-title">
            <span>
              <?php echo $lang['label_income_other'];?>
            </span>
            <span style="display: inline-block;float: right" data-sts="0" onclick="span_change_visible_onclick(this)">
                <i class="aui-iconfont aui-icon-down"></i>
            </span>
            </li>
            <li class="aui-list-item assets-item content-item" style="display: none">
                <ul class="aui-list assets-ul aui-margin-b-10">
                    <li class="aui-list-item assets-item">
                        <div><?php echo $lang['label_income_other'];?></div>
                        <div><input type="text" class="mui_input  survey-income-item" name="income_others" onblur="calcTotalIncome()" id="income_others" value="<?php echo $data['income_others']?:'';?>" /></div>
                    </li>
                    <li class="aui-list-item assets-item">
                        <div>
                            <?php echo $lang['label_other_remark'];?>
                            <textarea class="mui_textarea" name="others_remark" id="others_remark"><?php echo $data['others_remark']?:'';?></textarea>
                        </div>
                    </li>
                </ul>
            </li>

        </ul>
      <ul class="aui-list assets-ul aui-margin-b-10">
        <li class="aui-list-item assets-item business-title">
          <?php echo $lang['label_remark'];?>
        </li>
        <li class="aui-list-item assets-item">
          <div>
            <?php echo $lang['label_research_remark'];?>
            <textarea class="mui_textarea" name="research_remark" id="research_remark"><?php echo $data['research_remark']?:'';?></textarea>
          </div>
        </li>
      </ul>
      <div style="padding: 0 .8rem 1rem .8rem;">
        <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" onclick="submit();" id="submit"><?php echo $lang['act_submit'];?></div>
      </div>
    </form>
  </div>
</div>
<div class="upload-success">
  <div class="content">
    <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/gou.png" alt="">
    <p class="title"><?php echo 'Upload Successfully';?></p>
    <p class="tip"><?php echo str_replace('xxx','<em id="count">3</em>','It exits automatically xxx seconds later.');?></p>
  </div>
</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/aui/aui-tab.js"></script>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/common.js?v=1"></script>
<script type="text/javascript">
  if(window.operator){
    window.operator.showTitle('<?php echo $output['header_title'];?>');
  }

  function changeTab(e){
    var tab_type = $(e).attr('type');
    $('.aui-tab-item').removeClass('aui-active');
    $(e).addClass('aui-active');
    $('.aui-tab-content .aui-tab-content-item').hide();
    $('.aui-tab-content .tab-item-' + tab_type).show();
  }

  function span_change_visible_onclick(e){
      var _sts=$(e).data('sts');//怪事，用.data('sts')取不出来，可能不能jquery
      var _content=$(e).closest("ul").find(".content-item");
      if(_sts=='1'){
          $(e).find(".aui-iconfont").removeClass("aui-icon-top").addClass("aui-icon-down");
          $(e).data('sts',0);
          _content.hide();
      }else{
          $(e).find(".aui-iconfont").removeClass("aui-icon-down").addClass("aui-icon-top");
          $(e).data('sts',1);
          _content.show();
      }
  }
  function calcTotalEmployees(){
      var _total=0;
      $("#incomeForm").find(".survey-employees").each(function(){
          var _iv=$(this).val();
          if(_iv){
              _total=parseInt(_total)+parseInt(_iv);
          }
      });
      $("#total_survey_employees").val(_total);
      calcTotalIncome();
  }
  function calcTotalProfit(){
      var _total=0;
      $("#incomeForm").find(".survey-profit").each(function(){
          var _iv=$(this).val();
          if(_iv){
              _total=parseInt(_total)+parseInt(_iv);
          }
      });
      $("#total_survey_profit").val(_total);
      calcTotalIncome();
  }
  function calcTotalIncome(){
      var _total=0;
      $("#incomeForm").find(".survey-income-item").each(function(){
          var _iv=$(this).val();
          if(_iv){
              _total=parseInt(_total)+parseInt(_iv);
          }
      });
      $("#span_total_income").html(_total.toString());
  }

  function submit(){
    var params = {}, member_id = '<?php echo $_GET['id'];?>', 
        officer_id = '<?php echo cookie('member_id');?>', token = '<?php echo cookie('token');?>', 
        income_rental_land = $.trim($('#income_rental_land').val()), 
        income_rental_housing = $.trim($('#income_rental_housing').val()), 

        company_name = $('#company_name').val(),
        work_position = $('#work_position').val(),
        income_salary = $('#income_salary').val(),
        income_others = $('#income_others').val(),
        others_remark = $('#others_remark').val(),
        research_remark = $('#research_remark').val(),
        business_employees = $('#total_survey_employees').val(),
        income_business = $('#total_survey_profit').val(),
        business_des = $('#business_des').val();
    
    if(!member_id){
      verifyFail('<?php echo 'Please reselect client.';?>');
      return;
    }
    if(income_rental_housing && !checkMoney(income_rental_housing)){
      verifyFail('<?php echo 'Housing rental must be monetary.';?>');
      return;
    }

    if(income_rental_land && !checkMoney(income_rental_land)){
      verifyFail('<?php echo 'Land rental must be monetary.';?>');
      return;
    }
    if(business_employees && !checkInteger(business_employees)){
      verifyFail('<?php echo 'Employees must be positive integer.';?>');
      return;
    }
    if(income_business && !checkMoney(income_business)){
      verifyFail('<?php echo 'Profit must be monetary.';?>');
      return;
    }
    if(income_salary && !checkMoney(income_salary)){
      verifyFail('<?php echo 'Salary must be monetary.';?>');
      return;
    }
    if(income_others && !checkMoney(income_others)){
      verifyFail('<?php echo 'Income others must be monetary.';?>');
      return;
    }
      var _arr_survey=[];
      var _chk_survey=true;
      
      $("#incomeForm").find(".industry-item").each(function(){
          var _survey_item={};
          var _new_item={};
          var _survey_item_uid=$(this).data("industry-uid");
          var _survey_item_name=$(this).data('industry-name');
          _new_item.industry_id=_survey_item_uid;
          $(this).find(".industry-item-survey").each(function(){
              var _item_key=$(this).data("item-code");
              var _item_val=$(this).val();
              _survey_item[_item_key]=_item_val;
              if(_item_key=='profit'){
                  if(!_item_val){
                      verifyFail('required to input profit for '+_survey_item_name);
                      _chk_survey=false;
                  }
                  if(parseInt(_item_val)<=0){
                      verifyFail(_survey_item_name+' profit must be more than 0');
                      _chk_survey=false;
                  }
              }
          });
          _new_item.industry_name=_survey_item_name;
          _new_item.business_place=_survey_item.place;
          _new_item.business_employees=_survey_item.employees;
          _new_item.income_business=_survey_item.profit;
          _new_item.industry_research_json=encodeURI(JSON.stringify(_survey_item));


          _arr_survey.push(_new_item);
      });
      if(_chk_survey==false){
          return;
      }
    var _research_json = encodeURI(JSON.stringify(_arr_survey));
    params.business_research = _research_json;
    params.member_id = member_id;
    params.officer_id = officer_id;
    params.token = token;
    params.income_rental_land = income_rental_land;
    params.income_rental_housing = income_rental_housing;
    params.business_employees = business_employees;
    params.income_business = income_business;
    params.business_des = business_des;
    params.company_name = company_name;
    params.work_position = work_position;
    params.income_salary = income_salary;
    params.income_others = income_others;
    params.others_remark = others_remark;
    params.research_remark = research_remark;

    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    
    $.ajax({
      type: 'POST',
      url: '<?php echo ENTRY_API_SITE_URL;?>/officer.submit.member.income.research.php',
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
  
  
  function cancelCheck(el){
    $('#'+el+'_check_img').attr('src', '');
    $('#'+el+'_uncheck').show();
    $('#'+el+'_check').hide();
    formData.delete(el);
  }
</script>
