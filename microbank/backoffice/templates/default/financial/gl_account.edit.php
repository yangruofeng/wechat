<link rel="stylesheet" type="text/css" href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/main-style.css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    div.currency-code { display: none; }
    .checkbox-inline {
        margin-left: 0px!important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Region</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('gl_account', 'list', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <?php if ($output['editing_row']) {?>
                    <li><a class="current"><span>Edit</span></a></li>
                <?php } else { ?>
                    <li><a class="current"><span>Add</span></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 500px;">
        <form class="form-horizontal" method="post" action="<?php echo getUrl('gl_account', $output['editing_row'] ? 'edit' : 'add', array(), false, BACK_OFFICE_SITE_URL) ?>">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="uid" value="<?php echo $output['editing_row']['uid']?>">
            <?php if ($output['parent']) { ?>
                <input type="hidden" name="account_parent" value="<?php echo $output['parent']['uid']?>">
            <?php } ?>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Account Code'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="account_code" placeholder="" value="<?php echo $output['editing_row']['account_code']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'GL Code'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="gl_code" placeholder="" value="<?php echo $output['editing_row']['gl_code']?>" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Account Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="account_name" placeholder="" value="<?php echo $output['editing_row']['account_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Parent';?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?php echo $output['parent']['account_name'] ?: 'Top-level' ?>" readonly>
                </div>
            </div>

            <?php if ($output['parent']) { ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo 'Category';?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" value="<?php echo $output['parent']['category'] ?>" readonly>
                    </div>
                </div>
            <?php } else if ($output['editing_row']) { ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo 'Category';?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" value="<?php echo $output['editing_row']['category'] ?>" readonly>
                    </div>
                </div>
            <?php } else { ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Category'?></label>
                    <div class="col-sm-9">
                        <select class="form-control" name="category">
                            <option value="" selected>--Please Select--</option>
                            <?php foreach ((new passbookTypeEnum())->Dictionary() as $k=>$v): ?>
                                <option value="<?php echo $k;?>"><?php echo $v;?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error_msg"></div>
                    </div>
                </div>
            <?php } ?>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo '&nbsp;' ?></label>
                <div class="col-sm-9">
                    <label class="col-sm-4 checkbox-inline">
                        <input type="checkbox"
                               name="is_gl_leaf" id="is_leaf"
                            <?php echo $output['editing_row']['is_gl_leaf'] ? "checked" : "" ?>
                               >
                        <?php echo 'Is Leaf' ?>
                    </label>
                </div>
            </div>
            <?php foreach ((new currencyEnum())->Dictionary() as $k=>$v): ?>
            <div class="form-group currency-code">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Gl Code For '.$v; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="gl_code_<?php echo strtolower($k);?>" value="<?php echo $output['editing_row']['gl_code_' . strtolower($k)] ?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <?php endforeach; ?>
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
    });

    $('input[name="account_code"]').change(function(){
        var t = $(this).val();
        if (t) {
            <?php if ($output['parent']): ?>
            $('input[name="gl_code"]').val("<?php echo $output['parent']['gl_code'] ?>-" + t);
            <?php else: ?>
            $('input[name="gl_code"]').val(t);
            <?php endif; ?>
        }
    });

    $('input[name="is_gl_leaf"]').change(function(){
        if ($(this).is(":checked")) {
            $("div.currency-code").show();
        } else {
            $("div.currency-code").hide();
        }
    }).change();

    $('.form-horizontal').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.next());
        },
        rules : {
            account_code : {
                required : true
            },
            account_name : {
                required : true
            },
            category : {
                required : true
            }
            <?php foreach ((new currencyEnum())->Dictionary() as $k=>$v): ?>
            , "gl_code_<?php echo strtolower($k);?>": {
                required: "#is_leaf:checked"
            }
            <?php endforeach; ?>
        },
        messages : {
            required: '<?php echo 'Required'?>',
            account_code : {
                required : '<?php echo 'Required'?>'
            },
            account_name : {
                required : '<?php echo 'Required'?>'
            },
            category: {
                required : '<?php echo 'Required'?>'
            }
        }
    });


</script>