<link rel="stylesheet" type="text/css" href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/main-style.css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Region</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('region', 'list', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
<!--                <li><a href="--><?php //echo getUrl('region', 'add', array(), false, BACK_OFFICE_SITE_URL)?><!--"><span>Add</span></a></li>-->
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 500px;">
        <form class="form-horizontal" method="post" action="<?php echo getUrl('region', 'edit', array(), false, BACK_OFFICE_SITE_URL) ?>">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['region_row']['uid']?>">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Region'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="region_text" placeholder="" value="<?php echo $output['region_row']['node_text']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <?php $text_alias = my_json_decode($output['region_row']['node_text_alias']);
                   foreach ($output['lang_list'] as $key => $lang){?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo 'Region(' . $lang . ')'?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="<?php echo $key?>" placeholder="" value="<?php echo $text_alias[$key]?>">
                        <div class="error_msg"></div>
                    </div>
                </div>
            <?php }?>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Parent';?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?php echo $output['parent']['node_text'] ?: 'Top-level' ?>" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Sort';?></label>
                <div class="col-sm-9">
                   <input type="number" class="form-control" min="0"  name="node_sort" value="<?php echo $output['region_row']['node_sort']?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9">
                    <button type="button" class="btn btn-danger" style="margin-left: 15px;margin-right: 10px"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back'?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.next());
        },
        rules : {
            region_text : {
                required : true
            }
        },
        messages : {
            region_text : {
                required : '<?php echo 'Required'?>'
            }
        }
    });


</script>