<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/product.css?v=5" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.config.js' ?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.all.js' ?>"></script>

<?php
    $category_info=$output['category_info'];
    $lang_list=enum_langClass::getLangType();
?>
<style>
    body{
        font-size: 14px;
    }
    .form-control {
        border-width: 0;
    }

    .text-right {
        padding-right: 20px !important;
    }

    .page-2 .content {
        padding: 5px 20px 10px;
    }
    .tab-top li{
        font-size: 14px;
    }
    .red{
        color:red;
    }
</style>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Category</h3>
            <ul class="tab-base">
                <li><a  href="<?php echo getUrl('loan', 'category', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <?php if($category_info['uid']){?>
                    <li><a class="current"><span>Edit</span></a></li>
                <?php }else{?>
                    <li><a class="current"><span>Add</span></a></li>
                <?php }?>

            </ul>
        </div>
    </div>
    <div class="container" style="width: 98%">
        <ul class="tab-top clearfix">
            <li class="active" data-page="page-1"><a>Base Info</a></li>
            <?php if($category_info['uid']){?>
                <li data-page="page-2"><a>Details</a></li>
                <li data-page="page-3" style="width: auto;padding-left: 6px;padding-right: 6px;"><a>Allowed InterestPackage</a></li>
                <li data-page="page-4" style="width: auto;padding-left: 6px;padding-right: 6px;"><a>Allowed Repayment Type</a></li>
            <?php }?>
        </ul>

        <div class="tab-content page-1" style="padding-top: 20px">
            <form class="form-horizontal" id="frm_editor" method="post">
                <input type="hidden" name="form_submit" value="ok">
                <input type="hidden" name="act" value="loan">
                <input type="hidden" name="op" value="submitCategoryEditor">

                <input type="hidden" name="uid" value="<?php echo $category_info['uid']?>">
                <table class="table table-bordered table-hover" style="width: 500px;">
                    <tr>
                        <td class="text-right">
                            <span class="red">*</span>Category Name
                        </td>
                        <td class="text-left">
                            <input type="text" class="form-control" value="<?php echo $category_info['category_name']?>" name="category_name">
                            <div class="error_msg"></div>
                        </td>
                    </tr>
                    <?php foreach($lang_list as $k=>$v){?>
                        <tr>
                            <td class="text-right">
                                Name-<?php echo $v?>
                            </td>
                            <td class="text-left">
                                <input type="text" class="form-control" value="<?php echo $category_info['category_lang'][$k]?>" name="category_lang[]">
                                <input type="hidden" name="lang_key[]" value="<?php echo $k;?>">
                            </td>
                        </tr>
                    <?php }?>

                    <tr>
                        <td class="text-right"><span class="red">*</span>Product Code(USD)</td>
                        <td class="text-left">
                            <input type="text" name="product_code_usd" class="form-control" value="<?php echo $category_info['product_code_usd']; ?>">
                            <div class="error_msg"></div>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-right"><span class="red">*</span>Product Code(KHR)</td>
                        <td class="text-left">
                            <input type="text" name="product_code_khr" class="form-control" value="<?php echo $category_info['product_code_khr']; ?>">
                            <div class="error_msg"></div>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-right">Icon Code</td>
                        <td class="text-left">
                            <select class="form-control" name="category_code">
                                <?php foreach($output['code_list'] as $k=>$v){?>
                                    <option value="<?php echo $k?>" <?php if($category_info['category_code']==$k) echo 'selected'?>><?php echo $v;?></option>
                                <?php }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">Default Repayment</td>
                        <td class="text-left">
                            <select class="form-control" name="default_product_id">
                                <?php foreach($output['sub_list'] as $item){?>
                                    <option value="<?php echo $item['sub_product_id']?>" <?php if($category_info['default_product_id']==$item['sub_product_id']) echo 'selected'?>><?php echo $item['sub_product_name'];?></option>
                                <?php }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">Is Special</td>
                        <td class="text-left">
                            <?php $key_lang = enum_langClass::getSpecialLoanCateLang(); ?>
                            <select name="special_cate_key" id="" class="form-control">
                                <option value="0">Not Special</option>
                                <?php foreach( (new specialLoanCateKeyEnum())->toArray() as $v ){ ?>
                                    <option value="<?php echo $v; ?>" <?php if( $category_info['is_special'] && $category_info['special_key'] == $v ){ echo 'selected';} ?> ><?php echo $key_lang[$v]; ?></option>
                                <?php } ?>
                            </select>

                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">
                            <span>One Time</span>
                        </td>
                        <td class="text-left">
                            <span class="form-control">
                                <input type="checkbox" value="1" name="is_one_time" <?php if($category_info['is_one_time']) echo 'checked'?>>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">Interest Package</td>
                        <td class="text-left">
                            <select class="form-control" name="interest_package_id">
                                <option value="0">Default</option>
                                <?php foreach($output['package_list'] as $item){?>
                                    <option value="<?php echo $item['uid']?>" <?php if($category_info['interest_package_id']==$item['uid']) echo 'selected'?>><?php echo $item['package'];?></option>
                                <?php }?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-right">
                            <span>Max Contracts Per Client</span>
                        </td>
                        <td class="text-left">
                            <input type="number"  name="max_contracts_per_client" class="form-control" value="<?php echo $category_info['max_contracts_per_client'];?>" >
                        </td>
                    </tr>

                    <tr>
                        <td class="text-right">
                            <span>Is only loan at APP</span>
                        </td>
                        <td class="text-left">
                            <span class="form-control">
                                <input type="checkbox" value="1" name="is_only_loan_by_app" <?php if($category_info['is_only_loan_by_app']) echo 'checked'?>>
                            </span>
                        </td>
                    </tr>


                    <tr>
                        <td class="text-right">
                            <span>Close</span>
                        </td>
                        <td class="text-left">
                            <span class="form-control">
                                <input type="checkbox" value="1" name="is_close" <?php if($category_info['is_close']) echo 'checked'?>>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="text-center" style="padding: 20px">
                            <button class="btn btn-primary" style="width: 150px" onclick="formSubmit();">Submit</button>
                            <button type="button" class="btn btn-default" style="width: 150px;margin-left: 20px" onclick="javascript:history.back(-1)">Cancel</button>
                        </td>
                    </tr>

                </table>
            </form>
        </div>
        <?php if($category_info['uid']){?>
            <div class="tab-content page-2 no_clear_ul_style"  style="display: none">
                <div class="base-info clearfix">
                    <div class="product_feature feature">
                        <div class="ibox-title">
                            <div class="col-sm-8"><h5>Summary</h5></div>
                            <div class="col-sm-4">
                                <i class="fa fa-edit <?php echo !$category_info['uid']?'not-allowed':''?> allow-state" title="<?php echo $category_info['uid'] ? 'Edit' : 'Please save the product info first.'?>" onclick="edit_text('product_feature')"></i>
                                <i class="fa fa-mail-reply" onclick="cancel_text('product_feature')"></i>
                                <i class="fa fa-floppy-o" onclick="save_text('product_feature')"></i>
                            </div>
                        </div>
                        <div class="content clearfix">
                            <div><?php echo $category_info['product_feature']?></div>
                            <textarea name="product_feature" id="product_feature" style="display: none;"><?php echo $category_info['product_feature']?></textarea>
                        </div>
                    </div>
                    <div class="product_description description">
                        <div class="ibox-title">
                            <div class="col-sm-8"><h5>Description</h5></div>
                            <div class="col-sm-4">
                                <i class="fa fa-edit <?php echo !$category_info['uid']?'not-allowed':''?> allow-state" title="<?php echo $category_info['uid'] ? 'Edit' : 'Please save the product info first.'?>" onclick="edit_text('product_description')"></i>
                                <i class="fa fa-mail-reply" onclick="cancel_text('product_description')"></i>
                                <i class="fa fa-floppy-o" onclick="save_text('product_description')"></i>
                            </div>
                        </div>
                        <div class="content clearfix">
                            <div><?php echo $category_info['product_description']?></div>
                            <textarea name="product_description" id="product_description" style="display: none;"><?php echo $category_info['product_description']?></textarea>
                        </div>
                    </div>
                </div>
                <div class="base-info clearfix">
                    <div class="product_qualification qualification">
                        <div class="ibox-title">
                            <div class="col-sm-8"><h5>Client qualification</h5></div>
                            <div class="col-sm-4">
                                <i class="fa fa-edit <?php echo !$category_info['uid']?'not-allowed':''?> allow-state" title="<?php echo $category_info['uid'] ? 'Edit' : 'Please save the product info first.'?>" onclick="edit_text('product_qualification')"></i>
                                <i class="fa fa-mail-reply" onclick="cancel_text('product_qualification')"></i>
                                <i class="fa fa-floppy-o" onclick="save_text('product_qualification')"></i>
                            </div>

                        </div>
                        <div class="content clearfix">
                            <div><?php echo $category_info['product_qualification']?></div>
                            <textarea name="product_qualification" id="product_qualification" style="display: none;"><?php echo $category_info['product_qualification']?></textarea>
                        </div>
                    </div>
                    <div class="product_required required">
                        <div class="ibox-title">
                            <div class="col-sm-8"><h5>Documents required</h5></div>
                            <div class="col-sm-4">
                                <i class="fa fa-edit <?php echo !$category_info['uid']?'not-allowed':''?> allow-state" title="<?php echo $category_info['uid'] ? 'Edit' : 'Please save the product info first.'?>" onclick="edit_text('product_required')"></i>
                                <i class="fa fa-mail-reply" onclick="cancel_text('product_required')"></i>
                                <i class="fa fa-floppy-o" onclick="save_text('product_required')"></i>
                            </div>
                        </div>
                        <div class="content clearfix">
                            <div><?php echo $category_info['product_required']?></div>
                            <textarea name="product_required" id="product_required" style="display: none;"><?php echo $category_info['product_required']?></textarea>
                        </div>
                    </div>
                </div>
                <div class="base-info clearfix">
                    <div class="product_notice notice">
                        <div class="ibox-title">
                            <div class="col-sm-8"><h5>Notice</h5></div>
                            <div class="col-sm-4">
                                <i class="fa fa-edit <?php echo !$category_info['uid']?'not-allowed':''?> allow-state" title="<?php echo $category_info['uid'] ? 'Edit' : 'Please save the product info first.'?>" onclick="edit_text('product_notice')"></i>
                                <i class="fa fa-mail-reply" onclick="cancel_text('product_notice')"></i>
                                <i class="fa fa-floppy-o" onclick="save_text('product_notice')"></i>
                            </div>
                        </div>
                        <div class="content clearfix">
                            <div><?php echo $category_info['product_notice']?></div>
                            <textarea name="product_notice" id="product_notice" style="display: none;"><?php echo $category_info['product_notice']?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content page-3 no_clear_ul_style"  style="display: none">
                <form id="frm_interest_package">
                    <ul class="list-group" id="ul_interest_package" style="margin-top: 15px;max-width: 400px">
                        <?php foreach($output['package_list'] as $item){?>
                            <li class="list-group-item">
                                <p>
                                    <label>
                                        <input name="chk_interest_package[]" type="checkbox" <?php if(in_array($item['uid'],$output['category_info']['allowed_interest_package_id'])) echo 'checked'?>
                                               value="<?php echo $item['uid'] ?>" >
                                    <span style="padding-left: 20px">
                                        <?php echo $item['package']?>
                                    </span>
                                    </label>
                                </p>
                            </li>
                        <?php }?>
                        <li class="list-group-item">
                            <button type="button" class="btn btn-primary btn-block" onclick="btn_save_package_onclick()">Save</button>
                        </li>
                    </ul>
                </form>

            </div>
            <div class="tab-content page-4 no_clear_ul_style" style="display: none">
                <form id="frm_sub_product">
                    <ul class="list-group" id="ul_sub_product" style="margin-top: 15px;max-width: 400px">
                        <?php foreach($output['sub_list'] as $item){?>
                            <li class="list-group-item">
                                <p>
                                    <label>
                                        <input name="chk_sub_product[]" type="checkbox" <?php if(in_array($item['sub_product_id'],$output['category_info']['allowed_sub_product_id'])) echo 'checked'?>
                                               value="<?php echo $item['sub_product_id'] ?>" >
                                    <span style="padding-left: 20px">
                                        <?php echo $item['sub_product_name']?>
                                    </span>
                                    </label>
                                </p>
                            </li>
                        <?php }?>
                        <li class="list-group-item">
                            <button type="button" class="btn btn-primary btn-block" onclick="btn_save_sub_product_onclick()">Save</button>
                        </li>
                    </ul>
                </form>

            </div>
        <?php }?>
    </div>
</div>
<script>
    var _WIDTH;
    $(function(){
        $('.tab-top li').click(function () {
            var _page = $(this).data('page');
            $('.tab-top li').removeClass('active');
            $(this).addClass('active');

            $(".tab-content").hide();
            $('.' + _page).show();

            if(_page == 'page-2'){
                _WIDTH = $('.content').width();
            }
        })
    });

    function formSubmit()
    {
        if( !$('#frm_editor').valid() ){
            return false;
        }
        $('#frm_editor').submit();

    }

    $('#frm_editor').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            category_name: {
                required: true
            },
            product_code_usd:{
                required: true
            },
            product_code_khr:{
                required: true
            }
        },
        messages: {
            category_name: {
                required: '<?php echo 'Required!'?>'
            },
            product_code_usd:{
                required: 'Please input USD code'
            },
            product_code_khr:{
                required: 'Please input KHR code'
            }
        }
    });

    function edit_text(_name) {
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
        var _val = ueArr[_name].getContent();
        yo.loadData({
            _c: "loan",
            _m: "updateCategoryDescription",
            param: {category_id: '<?php echo $category_info['uid']?>', name: _name, val: _val},
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
                'simpleupload', 'background', '|',
                'horizontal', 'date', 'time', 'spechars','inserttable',
            ]],
            initialFrameHeight: 300,
            enableAutoSave: false,
            autoHeightEnabled: false,
            lang: 'en'
        });
    }
</script>
<script>
    function btn_save_package_onclick(){
        var _values=$("#frm_interest_package").getValues();
        _values.category_id='<?php echo $category_info['uid']?>';
        console.log(_values);
        showMask();
        yo.loadData({
           _c:"loanV2",
            _m:"ajaxSaveLoanCategoryAllowedInterestPackage",
            param:_values,
            callback:function(_o){
                hideMask();
               if(_o.STS){
                   alert("Saved Successfully");
               } else{
                   alert(_o.MSG);
               }
            }
        });
    }
    function btn_save_sub_product_onclick(){
        var _values=$("#frm_sub_product").getValues();
        _values.category_id='<?php echo $category_info['uid']?>';
        console.log(_values);
        showMask();
        yo.loadData({
            _c:"loanV2",
            _m:"ajaxSaveLoanCategoryAllowedSubProduct",
            param:_values,
            callback:function(_o){
                hideMask();
                if(_o.STS){
                    alert("Saved Successfully");
                } else{
                    alert(_o.MSG);
                }
            }
        });
    }
</script>
