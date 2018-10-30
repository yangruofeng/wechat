<?php
$category_info=$output['category_info'];
?>
<div class="page__bd">
    <h3 style="padding: 20px"><?php echo $category_info['alias']?></h3>
    <div class="weui-cells__title">
        Repayment Type
    </div>
    <div class="weui-cells">
        <div class="weui-cell weui-cell_select">
            <div class="weui-cell__bd">
                <select class="weui-select" id="sel_sub_product_id">
                    <?php foreach($output['sub_list'] as $item){?>
                        <option value="<?php echo $item['sub_product_id']?>" <?php if($category_info['sub_product_id']==$item['sub_product_id']) echo 'selected'?>><?php echo $item['sub_product_name'];?></option>
                    <?php }?>
                </select>
            </div>
        </div>
    </div>
    <div class="weui-cells__title">
        Interest Package
    </div>
    <div class="weui-cells">
        <div class="weui-cell weui-cell_select">
            <div class="weui-cell__bd">
                <select class="weui-select" id="sel_interest_package_id">
                    <?php foreach($output['package_list'] as $item){?>
                        <option value="<?php echo $item['uid']?>" <?php if($category_info['interest_package_id']==$item['uid']) echo 'selected'?>><?php echo $item['package'];?></option>
                    <?php }?>
                </select>
            </div>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <button class="weui-btn weui-btn_primary" onclick="btn_submit_category_onclick();">Submit</button>
            <button class="weui-btn weui-btn_default" onclick="javascript:history.back(-1);">Back</button>
        </div>
    </div>
</div>
<script>
    function btn_submit_category_onclick(){
        var _category_id="<?php echo $category_info['category_id']?>";
        var _sub_product_id=$("#sel_sub_product_id").val();
        var _member_id="<?php echo $category_info['member_id']?>";
        if(!_sub_product_id){
            alert("Required to choose repayment-type");
            return false;
        }
        var _interest_package_id=$("#sel_interest_package_id").val();
        if(!_interest_package_id){
            alert("Required to choose interest-package");
            return false;
        }
        showMask();
        yo.loadData({
            _c:"client_profile",
            _m:"ajaxSaveMemberCategoryItem",
            param:{member_id:_member_id,category_id:_category_id,sub_product_id:_sub_product_id,interest_package_id:_interest_package_id},
            callback:function(_o){
                if(_o.STS){
                    alert("Saved Successfully");
                    setTimeout(function(){
                        history.back(-1);
                    },2000)
                }else{
                    hideMask();
                    alert(_o.MSG);
                }
            }


        });
    }
</script>