<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/insurance.css?v=7" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/doT.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.config.js' ?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.all.js' ?>"></script>
<style>
.loan-product-line label {
  margin-right: 5px;
}
</style>
<?php $item = $output['item'];$items = $output['items'];?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('insurance', 'product', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a href="<?php echo getUrl('insurance', 'addProduct', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
                <li><a class="current"><span>Info</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container insurance-product-edit">
      <ul class="tab-top clearfix">
          <li class="active" page="page-1"><a>Product Info</a></li>
          <li page="page-2"><a>Product Item</a></li>
      </ul>
      <div class="page-1">
        <div class="edit-wrap clearfix">
          <div class="edit-info">
              <div class="ibox-title">
                  <h5>Insurance Product Info</h5>
              </div>
              <div class="content clearfix">
                <input type="hidden" id="uid" value="<?php echo $item['uid'];?>">
                <table class="table">
                    <tr><td>Product Name：</td><td id="product_name"><?php echo $item['product_name'];?></td><td>Product Code：</td><td id="product_code"><?php echo $item['product_code'];?></td></tr>
                    <tr><td>State：</td><td id="state"><?php if($item['state'] == 10){echo 'temp';}elseif($item['state'] == 20){echo 'active';}elseif($item['state'] == 30){echo 'inactive';}elseif($item['state'] == 40){echo 'history';};?></td><td>Creator Name</td><td id="creator_name"><?php echo $item['creator_name'];?></td></tr>
                    <tr><td>Create Time：</td><td id="create_time" colspan="3"><?php echo timeFormat($item['create_time']);?></td></tr>
                </table>
              </div>
          </div>
        </div>
        <div class="product_description">
          <div class="ibox-title">
            <h5>Description</h5>
          </div>
          <div class="content">
            <div><?php echo $item['product_description'];?></div>
            <textarea name="product_description" id="product_description" style="display: none;"><?php echo $item['product_description'];?></textarea>
          </div>
        </div>
        <div class="product_feature">
          <div class="ibox-title">
            <h5>Feature</h5>
          </div>
          <div class="content">
            <div><?php echo $item['product_feature'];?></div>
            <textarea name="product_feature" id="product_feature" style="display: none;"><?php echo $item['product_feature'];?></textarea>
          </div>
        </div>
        <div class="product_required">
          <div class="ibox-title">
            <h5>Required</h5>
          </div>
          <div class="content">
            <div><?php echo $item['product_required'];?></div>
            <textarea name="product_required" id="product_required" style="display: none;"><?php echo $item['product_required'];?></textarea>
          </div>
        </div>
        <div class="product_notice">
          <div class="ibox-title">
            <h5>Notice</h5>
          </div>
          <div class="content">
            <div><?php echo $item['product_notice'];?></div>
            <textarea name="product_notice" id="product_notice" style="display: none;"><?php echo $item['product_notice'];?></textarea>
          </div>
        </div>
      </div>
      <div class="page-2">
        <div class="edit-info">
          <div class="ibox-title">
            <h5>Insurance Product Item(<?php echo count($items);?>)</h5>
          </div>
          <div class="content">
            <table class="table">
              <thead>
                <tr class="table-header">
                    <td><?php echo 'Item Code';?></td>
                    <td><?php echo 'Item Name';?></td>
                    <td><?php echo 'Fixed Amount';?></td>
                    <td><?php echo 'Fixed Price';?></td>
                    <td><?php echo 'Fixed Valid Days';?></td>
                    <td><?php echo 'Bind Type';?></td>
                    <td><?php echo 'Price Rate';?></td>
                </tr>
              </thead>
              <tbody class="table-body" id="itemtBody">
                <?php foreach ($items as $key => $value) { ?>
                  <tr id="trItem<?php echo $value['uid'];?>">
                    <td><?php echo $value['item_code'];?></td>
                    <td><?php echo $value['item_name'];?></td>
                    <td value="<?php echo $value['is_fixed_amount'];?>"><?php if($value['is_fixed_amount']){echo $value['fixed_amount'];}else{echo 'None';}?></td>
                    <td value="<?php echo $value['is_fixed_price'];?>"><?php if($value['is_fixed_price']){echo $value['fixed_price'];}else{echo 'None';}?></td>
                    <td value="<?php echo $value['is_fixed_valid_days'];?>"><?php if($value['is_fixed_valid_days']){echo $value['fixed_valid_days'];}else{echo 'None';}?></td>
                    <td value="<?php echo $value['bind_type'];?>" data="<?php echo $value['loan_product_ids'];?>"><?php if($value['bind_type'] == 1){echo 'loan_contract';}else{echo 'None';}?></td>
                    <td><?php echo $value['price_rate'];?></td>

                  </tr>
                <?php }?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
</div>
<script>
    $(function () {
      var height = $('.product-info .content').height();
      $('.penalty-info .content').height(height);
      $('.tab-top li').click(function () {
        var _page = $(this).attr('page');
        $('.tab-top li').removeClass('active');
        $(this).addClass('active');
        $('.page-1,.page-2').hide();
        $('.' + _page).show();
      });
    });
</script>
