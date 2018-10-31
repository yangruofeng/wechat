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
                <?php if ($item['uid']) { ?>
                    <li><a href="<?php echo getUrl('insurance', 'addProduct', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
                    <li><a class="current"><span>Edit</span></a></li>
                <?php }else{ ?>
                    <li><a class="current"><span>Add</span></a></li>
                <?php }?>
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
                  <div class="custom-btn-group approval-btn-group">
                    <!--<button type="button" class="btn btn-success" onclick=""><i class="fa  fa-edit"></i>Add</button>-->
                    <i class="fa fa-edit" onclick="edit_info();"></i>
                  </div>
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
            <div class="custom-btn-group approval-btn-group">
              <!--<button type="button" class="btn btn-success" onclick=""><i class="fa  fa-edit"></i>Edit</button>-->
              <i class="fa fa-edit <?php if(!$item['uid']){?>not-allowed<?php }?>" onclick="edit_text('product_description')"></i>
              <i class="fa fa-mail-reply" onclick="cancel_text('product_description')"></i>
              <i class="fa fa-floppy-o" onclick="save_text('product_description')"></i>
            </div>
          </div>
          <div class="content">
            <div><?php echo $item['product_description'];?></div>
            <textarea name="product_description" id="product_description" style="display: none;"><?php echo $item['product_description'];?></textarea>
          </div>
        </div>
        <div class="product_feature">
          <div class="ibox-title">
            <h5>Feature</h5>
            <div class="custom-btn-group approval-btn-group">
              <!--<button type="button" class="btn btn-success" onclick=""><i class="fa  fa-edit"></i>Edit</button>-->
              <i class="fa fa-edit <?php if(!$item['uid']){?>not-allowed<?php }?>" onclick="edit_text('product_feature')"></i>
              <i class="fa fa-mail-reply" onclick="cancel_text('product_feature')"></i>
              <i class="fa fa-floppy-o" onclick="save_text('product_feature')"></i>
            </div>
          </div>
          <div class="content">
            <div><?php echo $item['product_feature'];?></div>
            <textarea name="product_feature" id="product_feature" style="display: none;"><?php echo $item['product_feature'];?></textarea>
          </div>
        </div>
        <div class="product_required">
          <div class="ibox-title">
            <h5>Required</h5>
            <div class="custom-btn-group approval-btn-group">
              <!--<button type="button" class="btn btn-success" onclick=""><i class="fa  fa-edit"></i>Edit</button>-->
              <i class="fa fa-edit <?php if(!$item['uid']){?>not-allowed<?php }?>" onclick="edit_text('product_required');"></i>
              <i class="fa fa-mail-reply" onclick="cancel_text('product_required')"></i>
              <i class="fa fa-floppy-o" onclick="save_text('product_required')"></i>
            </div>
          </div>
          <div class="content">
            <div><?php echo $item['product_required'];?></div>
            <textarea name="product_required" id="product_required" style="display: none;"><?php echo $item['product_required'];?></textarea>
          </div>
        </div>
        <div class="product_notice">
          <div class="ibox-title">
            <h5>Notice</h5>
            <div class="custom-btn-group approval-btn-group">
              <!--<button type="button" class="btn btn-success" onclick=""><i class="fa  fa-edit"></i>Edit</button>-->
              <i class="fa fa-edit <?php if(!$item['uid']){?>not-allowed<?php }?>" onclick="edit_text('product_notice');"></i>
              <i class="fa fa-mail-reply" onclick="cancel_text('product_notice')"></i>
              <i class="fa fa-floppy-o" onclick="save_text('product_notice')"></i>
            </div>
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
            <div class="custom-btn-group approval-btn-group">
              <!--<button type="button" class="btn btn-success" onclick=""><i class="fa  fa-edit"></i>Edit</button>-->
              <i class="fa fa-edit <?php if(!$item['uid']){?>not-allowed<?php }?>" onclick="edit_item();"></i>
            </div>
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
                    <td><?php echo 'Function';?></td>
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
                    <td>
                      <div class="custom-btn-group">
                        <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" onclick="edit_item(<?php echo $value['uid'];?>);">
                          <span><i class="fa  fa-vcard-o"></i>Edit</span>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php }?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
</div>

<div class="modal" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Base Info'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="info_form">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Product Name'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="product_name" value="<?php echo $item['product_name'];?>" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Product Code'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="product_code" value="<?php echo $item['product_code'];?>" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'State'?></label>
                            <div class="col-sm-9">
                                <select class="form-control" name="product_state">
                                    <option value="10" <?php if($item['state'] == 10){echo 'selected';}?>>temp</option>
                                    <option value="20" <?php if($item['state'] == 20){echo 'selected';}?>>active</option>
                                    <option value="30" <?php if($item['state'] == 30){echo 'selected';}?>>inactive</option>
                                    <option value="40" <?php if($item['state'] == 40){echo 'selected';}?>>history</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_info();"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Item Info'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                  <form class="form-horizontal" id="item_form">
                    <input type="hidden" name="itemid" id="itemid" value="">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Item Code'?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="item_code" id="item_code" value="" placeholder="">
                            <div class="error-tip"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Item Name'?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="item_name" id="item_name" value="" placeholder="">
                            <div class="error-tip"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Fixed Amount'?></label>
                        <div class="col-sm-3 radio-label">
                          <label class="radio-inline">
                            <input type="radio" name="is_fixed_amount" id="inlineRadio1" value="1"> Yes
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="is_fixed_amount" id="inlineRadio2" value="0" checked> No
                          </label>
                        </div>
                        <div class="col-sm-6 radio-input">
                            <input type="text" class="form-control" name="fixed_amount" id="fixed_amount" value="" placeholder="" style="display: none;">
                            <div class="error-tip"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Fixed Price'?></label>
                        <div class="col-sm-3 radio-label">
                          <label class="radio-inline">
                            <input type="radio" name="is_fixed_price" id="inlineRadio1" value="1"> Yes
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="is_fixed_price" id="inlineRadio2" value="0" checked> No
                          </label>
                        </div>
                        <div class="col-sm-6 radio-input">
                            <input type="text" class="form-control" name="fixed_price" id="fixed_price" value="" placeholder="" style="display: none;">
                            <div class="error-tip"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Fixed Valid Days'?></label>
                        <div class="col-sm-3 radio-label">
                          <label class="radio-inline">
                            <input type="radio" name="is_fixed_valid_days" id="inlineRadio1" value="1"> Yes
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="is_fixed_valid_days" id="inlineRadio2" value="0" checked> No
                          </label>
                        </div>
                        <div class="col-sm-6 radio-input">
                            <input type="text" class="form-control" name="fixed_valid_days" id="fixed_valid_days" value="" placeholder="" style="display: none;">
                            <div class="error-tip"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Price Rate'?></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="number" class="form-control" name="price_rate" id="price_rate" value="" placeholder="">
                                <span class="input-group-addon" style="min-width: 60px;border-left: 0">%</span>
                                <div class="error-tip"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Bind Loan Product'?></label>
                        <div class="col-sm-9 loan-product-line">
                          <?php foreach ($output['loan_product'] as $key => $value) { ?>
                            <label><input type="checkbox" name="ck_loan_product" value="<?php echo $value['uid'];?>"> <?php echo $value['product_name'];?></label>
                          <?php } ?>
                            <!--<select class="form-control" name="bind_type">
                              <option value="0">None</option>
                              <?php foreach ($output['loan_product'] as $key => $value) { ?>
                                <option value="<?php echo $value['uid'];?>"><?php echo $value['product_name'];?></option>
                              <?php } ?>
                            </select>-->
                        </div>
                    </div>
                  </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_item()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="tpl_product_item">
  <tr id="trItem{{=it.uid}}">
    <td>{{=it.item_code}}</td>
    <td>{{=it.item_name}}</td>
    <td value="{{=it.is_fixed_amount}}">
      {{? it.is_fixed_amount }}
       {{=it.fixed_amount}}
      {{??}}
       None
      {{? }}
    </td>
    <td value="{{=it.is_fixed_price}}">
    {{? it.is_fixed_price }}
     {{=it.fixed_price}}
    {{??}}
     None
    {{? }}
    </td>
    <td value="{{=it.is_fixed_valid_days}}">
      {{? it.is_fixed_valid_days }}
       {{=it.fixed_valid_days}}
      {{??}}
       None
      {{? }}
    </td>
    <td value="{{=it.bind_type}}" data="{{=it.loan_product_ids}}">
      {{? it.bind_type == 1 }}
       loan_contract
      {{??}}
       None
      {{? }}
    </td>
    <td>{{=it.price_rate}}</td>
    <td>
      <div class="custom-btn-group">
        <a title="<?php echo $lang['common_edit'] ;?>" class="custom-btn custom-btn-secondary" onclick="edit_item('{{=it.uid}}');">
          <span><i class="fa  fa-vcard-o"></i>Edit</span>
        </a>
      </div>
    </td>
  </tr>
</script>
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
      $('input[name=is_fixed_amount]').change(function(){
        var _val = $(this).val();
        _val == 1 ? $('#fixed_amount').show() : $('#fixed_amount').hide();
      });
      $('input[name=is_fixed_price]').change(function(){
        var _val = $(this).val();
        _val == 1 ? $('#fixed_price').show() : $('#fixed_price').hide();
      });
      $('input[name=is_fixed_valid_days]').change(function(){
        var _val = $(this).val();
        _val == 1 ? $('#fixed_valid_days').show() : $('#fixed_valid_days').hide();
      });
    });

    var uid = $('#uid').val();
    function edit_info() {
      var _product_name = $('#product_name').html();
      var _product_code = $('#product_code').html();
      var _installment_type_val = $('#installment_type').attr('val');
      var _installment_frequencies_val = $('#installment_frequencies').attr('val');
      var _interest_payment_val = $('#interest_payment').attr('val');
      var  allow_multi_contract = $('#allow_multi_contract').attr('val');
      var  allow_balloon_payment = $('#allow_balloon_payment').attr('val');
      var  allow_advance_interest = $('#allow_advance_interest').attr('val');
      var  is_editable_interest = $('#is_editable_interest').attr('val');

      $('#infoModal input[name="product_name"]').val(_product_name);
      $('#infoModal input[name="product_code"]').val(_product_code);


      $('#infoModal').modal('show');
    }



    function save_info() {
      if (!$("#info_form").valid()) {
          return;
      }
      var _product_name = $('#infoModal input[name="product_name"]').val();
      var _product_code = $('#infoModal input[name="product_code"]').val();
      if (!_product_code || !_product_name) return;
      var _product_state_val = $('#infoModal select[name="product_state"]').val();
      var _product_state_text = $('#infoModal select[name="product_state"] option[value="' + _product_state_val + '"]').text();

      var values = $('#info_form').getValues();
      if(uid){
        values.uid = uid;
      }
      yo.loadData({
          _c: 'insurance',
          _m: 'insertProductMain',
          param: values,
          callback: function (_o) {
              if (_o.STS) {
                $('#product_name').html(_product_name);
                $('#product_code').html(_product_code);
                $('#state').html(_product_state_text);
                $('#creator_name').html(_o.DATA.creator_name);
                $('#create_time').html(_o.DATA.create_time);
                uid = _o.DATA.uid;
                $('#uid').val(uid);
                $('.not-allowed').removeClass('not-allowed');
                $('#infoModal').modal('hide');
              } else {
                alert(_o.MSG,2);
              }
          }
      });
    }

    function edit_item(itemid){
      if (!uid) return;
      if(!itemid){
        $('#itemid').val('');
        $('#item_code').val('');
        $('#item_name').val('');
        $('#price_rate').val('');
        $("input[name='is_fixed_amount'][value='0']").prop('checked', true);
        $('#fixed_amount').hide();
        $('#fixed_amount').val('');
        $("input[name='is_fixed_price'][value='0']").prop('checked', true);
        $('#fixed_price').hide();
        $('#fixed_price').val('');
        $("input[name='is_fixed_valid_days'][value='0']").prop('checked', true);
        $('#fixed_valid_days').hide();
        $('#fixed_valid_days').val('');
        $('#itemModal select[name="bind_type"]').val(0);
        $('#itemModal').modal('show');
        var item_name = $('#item_name').html();
        var item_code = $('#item_code').html();
      }else{
        var item = $('#trItem'+itemid+' td');
        $('#itemid').val(itemid);
        $('#item_code').val(item.eq(0).text());
        $('#item_name').val(item.eq(1).text());
        $('#price_rate').val(item.eq(6).text()*100);
        $("input[name='is_fixed_amount'][value='"+item.eq(2).attr('value')+"']").prop('checked', true);
        item.eq(2).attr('value') == 1 ? $('#fixed_amount').show() : $('#fixed_amount').hide();
        item.eq(2).attr('value') == 1 ? $('#fixed_amount').val($.trim(item.eq(2).text())) : $('#fixed_amount').val('');
        $("input[name='is_fixed_price'][value='"+item.eq(3).attr('value')+"']").prop('checked', true);
        item.eq(3).attr('value') == 1 ? $('#fixed_price').show() : $('#fixed_price').hide();
        item.eq(3).attr('value') == 1 ? $('#fixed_price').val($.trim(item.eq(3).text())) : $('#fixed_price').val('');
        $("input[name='is_fixed_valid_days'][value='"+item.eq(4).attr('value')+"']").prop('checked', true);
        item.eq(4).attr('value') == 1 ? $('#fixed_valid_days').show() : $('#fixed_valid_days').hide();
        item.eq(4).attr('value') == 1 ? $('#fixed_valid_days').val($.trim(item.eq(4).text())) : $('#fixed_valid_days').val('');
        //$('#itemModal select[name="bind_type"]').val(item.eq(5).attr('value'));
        var ck = item.eq(5).attr('data'), ck_arr = ck.split(',');

        $('input[name="ck_loan_product"]').each(function(){
            if($.inArray($(this).val(), ck_arr) > -1){
              $(this).prop('checked', true);
            }else{
              $(this).prop('checked', false);
            }
        });
        $('#itemModal').modal('show');
      }
    }

    function save_item(){
      var values = $('#item_form').getValues();
      var itemid = $('#itemid').val();
      values.product_id = uid;
      if(itemid){
        values.itemid = itemid;
      }
      var id_array = new Array();
      $('input[name="ck_loan_product"]:checked').each(function(){
          id_array.push($(this).val());//向数组中添加元素
      });
      var idstr = id_array.join(',');//将数组元素连接起来以构建一个字符串
      values.productids = idstr;
      yo.loadData({
          _c: 'insurance',
          _m: 'insertProductItem',
          param: values,
          callback: function (_o) {
              if (_o.STS) {
                var data = _o.DATA;
                if(itemid){
                  window.location.reload();
                  /*$('#trItem'+itemid+' td').eq(0).text(data.item_code);
                  $('#trItem'+itemid+' td').eq(1).text(data.item_name);
                  var fixed_amount = data.is_fixed_amount == 1 ? data.fixed_amount : 'None';
                  $('#trItem'+itemid+' td').eq(2).text(fixed_amount);
                  var fixed_price = data.is_fixed_price == 1 ? data.fixed_price : 'None';
                  $('#trItem'+itemid+' td').eq(3).text(fixed_price);
                  var fixed_valid_days = data.is_fixed_valid_days == 1 ? data.fixed_valid_days : 'None';
                  $('#trItem'+itemid+' td').eq(4).text(fixed_valid_days);
                  var bind_type = data.bind_type ? 'loan_contract' : 'None';
                  $('#trItem'+itemid+' td').eq(5).text(bind_type);
                  $('#trItem'+itemid+' td').eq(5).attr('data',data.loan_product_ids);
                  $('#trItem'+itemid+' td').eq(6).text(data.price_rate);*/
                }else{
                  var interText = doT.template($('#tpl_product_item').text());
                  $("#itemtBody").append(interText(data));
                }
                $('#itemModal').modal('hide');
              } else {
                alert(_o.MSG);
              }
          }
      });
    }

    function edit_text(_name) {
        if (!uid) return;
        $('.' + _name).find('.fa-edit').hide();
        $('.' + _name).find('.fa-mail-reply').show();
        $('.' + _name).find('.fa-floppy-o').show();
        $('.' + _name).find('.content div').first().hide();
        $('.' + _name).find('#' + _name).show();
        ue(_name);
    }

    function cancel_text(_name) {
        $('.' + _name).find('.fa-edit').show();
        $('.' + _name).find('.fa-mail-reply').hide();
        $('.' + _name).find('.fa-floppy-o').hide();
        $('.' + _name).find('.content div').first().show();
        $('.' + _name).find('#' + _name).hide();
    }

    function save_text(_name) {
      if (!uid) return;
      var _val = ueArr[_name].getPlainTxt();
      yo.loadData({
          _c: 'insurance',
          _m: 'editProduct',
          param: {uid: uid, filed: _name, val: _val},
          callback: function (_o) {
              if (_o.STS) {
                $('.' + _name).find('.fa-edit').show();
                $('.' + _name).find('.fa-mail-reply').hide();
                $('.' + _name).find('.fa-floppy-o').hide();
                $('.' + _name).find('.content div').first().html(_val).show();
                $('.' + _name).find('#' + _name).hide();
              } else {
                alert(_o.MSG);
              }
          }
      });

    }

    var ueArr = [];
    function ue(_name) {
        ueArr[_name] = UE.getEditor(_name, {
            toolbars: [[
                'source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|',
                'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                'indent', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',
                'link', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                'horizontal', 'date', 'time', 'spechars', '|',
                'inserttable', 'deletetable',
            ]],
            initialFrameHeight: 300,
            enableAutoSave: false,
            autoHeightEnabled: false,
            lang: 'en'
        });
    }
    $('#info_form').validate({
        errorPlacement: function (error, element) {

            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            product_name: {
                required: true
            },
            product_code: {
                required: true,
                checkNumAndStr: true
            }
        },
        messages: {
            product_name: {
                required: '<?php echo 'Required!'?>'
            },
            product_code: {
                required: '<?php echo 'Required!'?>',
                checkNumAndStr: '<?php echo 'It can only be Numbers or letters!'?>'
            }
        }
    });
    jQuery.validator.addMethod("checkNumAndStr", function (value, element) {
        value = $.trim(value);
        if (!/^[A-Za-z0-9]+$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });
</script>
