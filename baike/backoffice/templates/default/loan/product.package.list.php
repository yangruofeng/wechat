<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Interest Package</h3>
            <ul class="tab-base">
                <li><a class="current" href="<?php echo getUrl('loan', 'productPackagePage', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Package List</span></a></li>
                <li><a  href="<?php echo getUrl('loan', 'addProductPackagePage', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Add Package</span></a></li>

            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <table class="table table-hover table-bordered">
                <tr class="table-header">
                    <td>ID</td>
                    <td>Package Name</td>
                    <td>Remark</td>
                    <td>Update Time</td>
                    <td>Creator</td>
                    <td>Function</td>
                </tr>

                <?php if(count($output['list'])){
                    foreach($output['list'] as $item){
                ?>
                        <tr>
                            <td><?php echo $item['uid']?></td>
                            <td><?php echo $item['package']?></td>
                            <td><?php echo $item['remark']?></td>
                            <td><?php echo $item['update_time']?></td>
                            <td><?php echo $item['creator_name']?></td>
                            <td>
                                <a class="btn btn-default" style="border: none"
                                   href="<?php echo getUrl("loan", "editProductPackagePage", array("package_id" => $item['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                    Edit
                                </a>
                                <a class="btn btn-primary"  style="border: none"
                                   href="<?php echo getUrl("loan", "editPackageSizeRate", array("package_id" => $item['uid'], "package_name" => $item['package']), false, BACK_OFFICE_SITE_URL)?>">
                                    Interest
                                </a>
                                <a class="btn  btn-default"  style="border: none"
                                   href="<?php echo getUrl("loan", "showPackageSizeRate", array("package_id" => $item['uid'], "package_name" => $item['package']), false, BACK_OFFICE_SITE_URL)?>">
                                    View
                                </a>
                                <button class="btn btn-default"  style="border: none"
                                        onclick="btn_delete_onclick('<?php echo $item['uid']?>');">Delete
                                </button>

                            </td>
                        </tr>
                <?php }}else{?>
                    <tr>
                        <td colspan="10">
                            <?php include template(":widget/no_record")?>
                        </td>
                    </tr>
                <?php }?>
            </table>

        </div>
    </div>
</div>
<script>
    function btn_delete_onclick(_uid){
        $.messager.confirm("Delete","Are you sure you want to delete this item?",function(_r){
            if(!_r) return;
            yo.loadData({
                _c:"loan",
                _m:"deleteProductPackage",
                param:{uid:_uid},
                callback: function (_obj) {
                    if(!_obj.STS){
                        alert(_obj.MSG);
                    }else{
                        window.location.reload();
                    }
                }
                    
            });

        });

    }
</script>
