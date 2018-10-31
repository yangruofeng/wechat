<div>
    <ul class="list-group">
        <?php foreach($all_images_list as $img_item){?>
            <?php
            $image_item=$img_item['url'];
            $viewer_width=200;
            ?>
            <li class="list-group-item">
                <ul class="list-inline">
                    <li>
                        <?php include(template(":widget/item.image.viewer.item"))?>
                    </li>
                    <li>
                        <button class="btn btn-default btn-xs" onclick="btn_remove_image_onclick(this,'<?php echo $img_item['uid']?>')">Remove</button>
                    </li>
                </ul>
            </li>
        <?php }?>
    </ul>
</div>
<script>
    function btn_remove_image_onclick(_e,_uid){
        $.messager.confirm("Confirm","Are You Sure To Remove This Picture",function(_r){
            if(!_r) return;
            $(document).waiting();
            yo.loadData({
                _c:"web_credit_v2",
                _m:"removeImageOfAssetItem",
                param:{uid:_uid},
                callback:function(_o){
                    $(document).unmask();
                    if(_o.STS){
                        alert("Remove Success");
                        $(_e).closest(".list-group-item").remove();
                    }else{
                        alert(_o.MSG);
                    }
                }
            })
        });
    }
</script>