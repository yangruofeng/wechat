<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Category</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('loan', 'editCategoryPage', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <table class="table table-hover table-striped table-bordered">
                <tr class="table-header">
                    <td>ID</td>
                    <td>Icon Code</td>
                    <td>Category Name</td>
                    <td>Product Code</td>
                    <td>Default Repayment</td>
                    <td>One Time</td>
                    <td>Interest Package</td>
                    <td>Closed</td>
                    <td>Function</td>
                </tr>
                <?php foreach($output['list'] as $item){?>
                    <tr>
                       <td><?php echo $item['uid']?></td>
                        <td><?php echo $item['category_code']?></td>
                        <td>
                            <?php if($item['is_close']){?>
                                <?php echo $item['category_name']?>
                            <?php }else{?>
                                <strong><?php echo $item['category_name']?></strong>
                            <?php }?>
                        </td>
                        <td>
                            <div>
                                <label for="">KHR: <?php echo $item['product_code_khr']; ?></label>
                            </div>
                            <div>
                                <label for="">USD: <?php echo $item['product_code_usd']; ?></label>
                            </div>
                        </td>
                        <td><?php echo $item['default_product_name']?></td>
                        <td>
                            <?php if($item['is_one_time']){?>
                                <i class="fa fa-check"></i>
                            <?php }?>
                        </td>
                        <td>
                            <?php if($item['interest_package_name']){
                                echo $item['interest_package_name'];
                            }else{
                                echo "Default";
                            }?>
                        </td>
                        <td>
                            <?php if($item['is_close']){?>
                                <i class="fa fa-check"></i>
                            <?php }?>
                        </td>

                        <td>
                            <a class="btn btn-link btn-xs" href="<?php echo getUrl("loan","editCategoryPage",array('uid'=>$item['uid']),false,BACK_OFFICE_SITE_URL)?>">
                                <i class="fa fa-edit"></i>
                                Edit
                            </a>
                            <a class="btn btn-link btn-xs" data-uid="<?php echo $item['uid']?>" onclick="btn_remove_category_onclick(this)" >
                                <i class="fa fa-trash"></i>
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php }?>
            </table>

        </div>
</div>
<script>
    function btn_remove_category_onclick(_e){
        var _uid=$(_e).data("uid");
        $.messager.confirm("Confirm","are you sure to delete this record?",function(_r){
            if(!_r) return;
            $(document).waiting();
            yo.loadData({
               _c:"loan",
                _m:"removeCreditCategory",
                param:{uid:_uid},
                callback:function(_o){
                    $(document).unmask();
                    if(!_o.STS){
                        alert(_o.MSG);
                    }else{
                        window.location.reload();
                    }
                }
            });
        })
    }

</script>
