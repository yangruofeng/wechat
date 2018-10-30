
<?php
$platform = $output['platform'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Module Business Setting</h3>
        </div>
    </div>
    <div class="container" style="width: 800px;">
        <ul class="nav nav-tabs" role="tablist">
            <?php foreach($platform as $k=>$item){?>
                <li role="presentation" class="<?php if($item['default_tab']) echo 'active'?>">
                    <a href="#tab_<?php echo $k?>" aria-controls="tab_<?php echo $k;?>" role="tab" data-toggle="tab"><?php echo $item['title']?></a>
                </li>
            <?php }?>
        </ul>

        <div class="tab-content">
            <?php foreach($platform as $k=>$item){?>
                <div role="tabpanel" class="tab-pane <?php if($item['default_tab']) echo 'active'?>" id="tab_<?php echo $k?>">
                    <table class="table table-striped table-hover table-bordered">
                        <tr class="table-header">
                            <td>Module Code</td>
                            <td>Module Name</td>
                            <td>Close</td>
                            <td>Show</td>
                            <td>New</td>
                            <td>Function</td>
                        </tr>
                        <?php foreach($item['list'] as $row){?>
                            <tr>
                                <td>
                                    <?php echo $row['module_code']?>
                                </td>
                                <td>
                                    <?php echo $row['module_name']?>
                                </td>
                                <td>
                                    <?php if($row['is_close']){?>
                                        <i class="fa fa-check"></i>
                                    <?php }?>
                                </td>
                                <td>
                                    <?php if($row['is_show']){?>
                                        <i class="fa fa-check"></i>
                                    <?php }?>
                                </td>
                                <td>
                                    <?php if($row['is_new']){?>
                                        <i class="fa fa-check"></i>
                                    <?php }?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-link btn-xs"
                                            data-module_code="<?php echo $row['module_code'] ?>"
                                            data-platform="<?php echo $k ?>"
                                            data-is_new="<?php echo $row['is_new'] ?>"
                                            data-is_close="<?php echo $row['is_close'] ?>"
                                            data-is_show="<?php echo $row['is_show'] ?>"
                                            onclick="btn_edit_module_onclick(this)">Edit</button>
                                </td>
                            </tr>

                        <?php }?>
                    </table>
                </div>
            <?php }?>
        </div>

    </div>
</div>

<script>
    function btn_edit_module_onclick(_e){
        var _data={};
        _data.module_code=$(_e).data("module_code");
        _data.platform=$(_e).data("platform");
        _data.is_new=$(_e).data("is_new");
        _data.is_show=$(_e).data("is_show");
        _data.is_close=$(_e).data("is_close");


        yo.dynamicTpl({
            tpl: "dev/module.business.setting.editor",
            ext:{
                data:_data
            },
            callback:function(_tpl){
                yo.dialog.show({
                    title:"Edit",
                    content:_tpl,
                    hideFooter:true
                })
            }
        });
    }

</script>