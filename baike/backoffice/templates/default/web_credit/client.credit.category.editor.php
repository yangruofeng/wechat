<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/product.css?v=5" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.config.js' ?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.all.js' ?>"></script>
<?php
$category_info=$output['category_info'];
$lang_list=enum_langClass::getLangType();
?>
<style>
    .form-control{
        border-width: 0;
    }
    .text-right{
        padding-right: 20px!important;
    }
</style>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Category</h3>
            <ul class="tab-base">
                <li><a  onclick="javascript:history.back(-1)"><span>BACK</span></a></li>
                <li><a class="current"><span>Edit</span></a></li>

            </ul>
        </div>
    </div>
    <div class="container">
        <form class="form-horizontal" id="frm_editor" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="act" value="web_credit">
            <input type="hidden" name="op" value="submitCreditCategoryEditor">

            <input type="hidden" name="uid" value="<?php echo $output['uid']?>">
            <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>">
            <input type="hidden" name="category_id" value="<?php echo $category_info['category_id']?>">

            <table class="table table-bordered table-hover" style="width: 500px;">
                <tr>
                    <td class="text-right">Repayment Product</td>
                    <td class="text-left">
                        <select class="form-control" name="sub_product_id">
                            <?php foreach($output['sub_list'] as $item){?>
                                <option value="<?php echo $item['sub_product_id']?>" <?php if($category_info['sub_product_id']==$item['sub_product_id']) echo 'selected'?>><?php echo $item['sub_product_name'];?></option>
                            <?php }?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">
                        <span>One Time</span>
                    </td>
                    <td class="text-left">
                        <input type="checkbox" value="1" name="is_one_time" <?php if($category_info['is_one_time']) echo 'checked'?>>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Interest Package</td>
                    <td class="text-left">
                        <select class="form-control" name="interest_package_id">
                            <?php foreach($output['package_list'] as $item){?>
                                <option value="<?php echo $item['uid']?>" <?php if($category_info['interest_package_id']==$item['uid']) echo 'selected'?>><?php echo $item['package'];?></option>
                            <?php }?>
                        </select>
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
    <script>
        $(function(){
            var _uid='<?php echo $category_info['uid']?>';
            if(!_uid){
                changeCategoryId();
            }
        });
        function changeCategoryId(){
            $('input[name="alias"]').val($("#sel_category_id").find("option:selected").text());
        }
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
                alias: {
                    required: true
                },
                category_id:{
                    required:true
                }
            },
            messages: {
                alias: {
                    required: '<?php echo 'Required!'?>'
                },
                category_id: {
                    required: '<?php echo 'Required!'?>'
                }
            }
        });

    </script>
