<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=6">
<link rel="stylesheet" type="text/css" href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/mobileSelect/mobileSelect.css">
<style>
  .ios-checkbox {
    float:left;
  }
  .edit-product {
    font-size: 12px;
    background: #34bf49;
    color: #fff;
    padding: 1px 10px;
    border-radius: 2px;
    float: right;
    margin-left: 10px;
  }
</style>
<?php include_once(template('widget/inc_header'));?>
<div class="wrap information-wrap">
  <?php $data = $output['data'];$work_info = $output['work_info'];$due_date = $output['due_date'];$period = $output['period'];$semi_balloon = $output['semi_balloon'];?>
    <ul class="aui-list info-list aui-margin-b-10">
      <li class="aui-list-item info-item" onclick="javascript:location.href='<?php echo getUrl('home', 'occupationInfomation', array('cid'=>$_GET['cid'],'id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>'">
        <div class="aui-list-item-inner content aui-list-item-arrow">
          <?php echo 'Work Type';?>
          <?php if(!$work_info){?>
            <span class="tip">Not Verify</span>
          <?php }else{?>
              <span class="tip"><?php echo $work_info?></span>
          <?php }?>
        </div>
      </li>
      <li class="aui-list-item info-item" onclick="editAddress();">
        <div class="aui-list-item-inner content aui-list-item-arrow">
          <?php echo 'Residence';?>&nbsp;&nbsp;
          <span class="tip"><?php echo $output['residence']['full_text'];?></span>
        </div>
      </li>
      <li class="aui-list-item info-item due-date">
        <div class="aui-list-item-inner content fontweight700 aui-list-item-arrow">
          <?php echo 'Repayment Day';?>
          <input type="hidden" id="dueDate" value="<?php echo $due_date?:0;?>">
          <?php if(!$due_date){?>
            <span class="tip">Not Set</span>
          <?php }else{?>
              <span class="tip"><?php echo $due_date;?></span>
          <?php }?>
        </div>
      </li>
      <li class="aui-list-item info-item">
        <div class="aui-list-item-inner content fontweight700">
          <?php echo 'Loan Category';?>
        </div>
      </li>
      <?php foreach ($output['category'] as $k => $v) {?>
        <li class="aui-list-item info-item limit-product-item" style="min-height: 3rem">
          <div class="aui-list-item-inner content">
            <?php echo $v['alias'];?>
            <div>
                <div class="ios-checkbox" data-category="<?php echo $v['category_id'];?>"  data-state="<?php echo $v['is_close']?0:1;?>">
                    <label for="ios-checkbox" class="emulate-ios-button <?php if(!$v['is_close']){?>active<?php }?>"></label>
                    <input type="hidden" class="state" value="<?php echo $v['is_close']?0:1;?>">
                </div>
                <span class="edit-product edit-product-<?php echo $v['category_id'];?>"
                      data-category-id="<?php echo $v['category_id'];?>" data-product-id="<?php echo $v['sub_product_id'];?>" style="<?php if($v['is_close']){?>display: none;<?php }?>">
                    Repayment
                </span>
                <span class="edit-product edit-package-<?php echo $v['category_id'];?>"
                      data-category-id="<?php echo $v['category_id'];?>" data-package-id="<?php echo $v['interest_package_id'];?>" style="<?php if($v['is_close']){?>display: none;<?php }?>">
                    Interest
                </span>
            </div>
          </div>
          <div class="ext-info" style="display: block;position: absolute;clear: both;right: 10px;margin-top: 45px;font-size: 0.7em;<?php if($v['is_close']){?>display: none;<?php }?>">
              <div>
                  <span class="ext-interest-package"><?php echo $v['interest_package_name']?></span>

                  <span class="ext-sub-product" style="padding-left: 20px"><?php echo $v['sub_product_name']?></span>

              </div>
          </div>
        </li>
      <?php }?>

    </ul>
</div>

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/mobileSelect/mobileSelect.js"></script>
<script type="text/javascript">
  var CATEGORY_ID;//
  var member_id = '<?php echo $_GET['id'];?>';
  var _cate_ids=eval('('+'<?php echo my_json_encode(array_keys($output["category"]))?>'+')');

  var sub_product_names = eval('('+'<?php echo my_json_encode($output['sub_product_list'])?>'+')');
  var sub_product_ids=eval('('+'<?php echo my_json_encode(array_keys(resetArrayKey($output["sub_product_list"],"id")))?>'+')');

  for(var _i in _cate_ids){
      var category_id=_cate_ids[_i];
      var _start_i=sub_product_ids.indexOf($('.edit-product-'+category_id).data("product-id"));
      var periodSelect = new MobileSelect({
          trigger: '.edit-product-'+category_id,
          title: 'Repayment',
          wheels: [
              {data: sub_product_names}
          ],
          triggerDisplayData: false,
          position:[_start_i],
          transitionEnd:function(indexArr, data){
              //console.log(data);
          },
          callback:function(indexArr, data){
              var product_id = data[0].id;
              var _product_name=data[0].value;
              yo.loadData({
                  _c: 'home',
                  _m: 'submitLoanCategoryProduct',
                  param: {member_id: member_id, category_id: CATEGORY_ID, product_id: product_id},
                  callback: function (_o) {
                      if(_o.STS){
                          hint('Success');
                          //$('.principal-period .tip').html(newPeriod);
                          $('.edit-product-'+CATEGORY_ID).closest(".aui-list-item").find(".ext-info").find(".ext-sub-product").text(_product_name);


                      }

                  }
              });
          }
      });
  }

  var sub_package_names = eval('('+'<?php echo my_json_encode($output['sub_package_list'])?>'+')');
  var sub_package_ids=eval('('+'<?php echo my_json_encode(array_keys(resetArrayKey($output["sub_package_list"],"id")))?>'+')');



  for(var _j in _cate_ids){
       category_id=_cate_ids[_j];
       var _start_j=sub_package_ids.indexOf($('.edit-package-'+category_id).data("package-id"));
       periodSelect = new MobileSelect({
          trigger: '.edit-package-'+category_id,
          title: 'Interest',
          wheels: [
              {data: sub_package_names}
          ],
          triggerDisplayData: false,
          position:[_start_j],
          transitionEnd:function(indexArr, data){
              //console.log(data);
          },
          callback:function(indexArr, data){
              var package_id = data[0].id;
              var package_name=data[0].value;
              yo.loadData({
                  _c: 'home',
                  _m: 'submitLoanCategoryInterest',
                  param: {member_id: member_id, category_id: CATEGORY_ID, package_id: package_id},
                  callback: function (_o) {
                      if(_o.STS){
                          hint('Success');
                          //$('.principal-period .tip').html(newPeriod);
                          $('.edit-product-'+CATEGORY_ID).closest(".aui-list-item").find(".ext-info").find(".ext-interest-package").text(package_name);

                      }

                  }
              });
          }
      });
  }



  $('.ios-checkbox').click(function(){
    var self_btn = $(this),
        label = self_btn.find('label'),
        edit = self_btn.parent().find('.edit-product'),
        cls = label.hasClass('active'),
        category_id = self_btn.attr('data-category'),
        state = self_btn.find('.state').val();
      showMask();
    yo.loadData({
      _c: 'home',
      _m: 'submitLoanCategory',
      param: {member_id: member_id, category_id: category_id, state: state},
      callback: function (_o) {
        if (_o.STS) {
          cls ? label.removeClass('active') : label.addClass('active');
          if(cls){
            label.removeClass('active');
            self_btn.find('.state').val(0);
              self_btn.closest(".aui-list-item").find(".ext-info").hide();
              edit.hide()
          }else{
            label.addClass('active');
            self_btn.find('.state').val(1);
              self_btn.closest(".aui-list-item").find(".ext-info").show();

            edit.show();
          }
        } else {
          hint(_o.MSG);
        }
      }
    });
  });

  $('.edit-product').click(function(){
    var self = $(this), category_id = self.data('category-id');
    CATEGORY_ID = category_id;
  });


if('<?php echo $_GET['back']?>'){
  $('.back').attr('onclick',"window.location.href = '<?php echo getUrl('home', 'search', array('guid'=>$_GET['cid'],'type'=>1,'back'=>true,'time'=>time()), false, WAP_OPERATOR_SITE_URL);?>';");
}
  function editAddress(){
    if(window.operator){
      window.operator.memberPlaceOfResidence('<?php echo $_GET['id'];?>');
      return;
    }
  }

  var dayArr = [], days = 28, i = 1, dueDate = parseInt($('#dueDate').val());
  for(i;i <= days;i++){
    dayArr.push(i);
  }
  var dueDateSelect = new MobileSelect({
    trigger: '.due-date',
    title: 'Repayment Day',
    wheels: [
      {data: dayArr}
    ],
    triggerDisplayData: false,
    position:[dueDate == 0 ? dueDate : dueDate - 1],
    transitionEnd:function(indexArr, data){
        //console.log(data);
    },
    callback:function(indexArr, data){
      var newDay = data[0];
        if(newDay == dueDate){
          return;
        }
        showMask();
        yo.loadData({
            _c: 'home',
            _m: 'submitClientRepaymentDay',
            param: {cid: '<?php echo $_GET['cid'];?>',day: newDay},
            callback: function (_o) {
              if(_o.STS){
                $('.due-date .tip').html(newDay);
              }
                
            }
        });
      }
  });
</script>
