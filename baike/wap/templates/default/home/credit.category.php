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
  <?php $data = $output['data'];?>
    <ul class="aui-list info-list aui-margin-b-10">
      <li class="aui-list-item info-item">
        <div class="aui-list-item-inner content fontweight700">
          <?php echo 'Loan Category';?>
        </div>
      </li>
      <?php foreach ($output['category'] as $k => $v) {?>
        <li class="aui-list-item info-item limit-product-item">
          <div class="aui-list-item-inner content">
            <?php echo $v['category_name'];?>
            <div>
                <div class="ios-checkbox" data-category="<?php echo $v['category_id'];?>"  data-state="<?php echo $v['is_close']?0:1;?>">
                    <label for="ios-checkbox" class="emulate-ios-button <?php if(!$v['is_close']){?>active<?php }?>"></label>
                    <input type="hidden" class="state" value="<?php echo $v['is_close']?0:1;?>">
                </div>
                <span class="edit-product edit-product-<?php echo $v['uid'];?>"
                      data-category-id="<?php echo $v['category_id'];?>" data-product-id="<?php echo $v['sub_product_id'];?>" style="<?php if($v['is_close']){?>display: none;<?php }?>">
                    Repayment
                </span>
                <span class="edit-product edit-package-<?php echo $v['uid'];?>"
                      data-category-id="<?php echo $v['category_id'];?>" data-product-id="<?php echo $v['sub_product_id'];?>" style="<?php if($v['is_close']){?>display: none;<?php }?>">
                    Interest Package
                </span>

            </div>
          </div>
        </li>
      <?php }?>
    </ul>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/mobileSelect/mobileSelect.js"></script>
<script type="text/javascript">
    var CATEGORY_ID;//
    /*
    var member_id = '<?php echo $_GET['id'];?>';
    var sub_arr = '<?php echo $output['sub_arr'];?>',
        sub_ids = '<?php echo $output['sub_ids'];?>',
        sub_names = '<?php echo $output['sub_names'];?>';
    sub_ids = eval('('+sub_ids+')');
    sub_names = eval('('+sub_names+')');
    sub_arr = eval('('+sub_arr+')');

    var cate_ids = '<?php echo $output['cate_ids'];?>';
    cate_ids = eval('('+cate_ids+')');

    for(var i = 0;i<cate_ids.length;i++){
        var category_id = cate_ids[i];

        var periodSelect = new MobileSelect({
            trigger: '.edit-product-'+category_id,
            title: 'Repayment',
            wheels: [
                {data: sub_names}
            ],
            triggerDisplayData: false,
            position:[0],
            transitionEnd:function(indexArr, data){
                //console.log(data);
            },
            callback:function(indexArr, data){
                var product_id = sub_arr[data[0]];
                yo.loadData({
                    _c: 'home',
                    _m: 'submitLoanCategoryProduct',
                    param: {member_id: member_id, category_id: CATEGORY_ID, product_id: product_id},
                    callback: function (_o) {
                        if(_o.STS){
                            hint('Success');
                            //$('.principal-period .tip').html(newPeriod);
                        }

                    }
                });
            }
        });
    }
   */

  $('.ios-checkbox').click(function(){
    var self_btn = $(this),
        label = self_btn.find('label'),
        edit = self_btn.parent().find('.edit-product'),
        cls = label.hasClass('active'),
        category_id = self_btn.attr('data-category'),
        state = self_btn.find('.state').val();
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
                    edit.hide()
                }else{
                    label.addClass('active');
                    self_btn.find('.state').val(1);
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
</script>
