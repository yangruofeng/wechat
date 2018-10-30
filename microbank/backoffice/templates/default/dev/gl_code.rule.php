
<?php
$list = $output['list'];
$setting_value = $output['setting_value'];
?>
<style>
    .second-label{
        font-weight: 500;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>GL Code Rules</h3>
        </div>
    </div>
    <div class="container" style="width: 800px;">
        <form class="form-horizontal" method="post" action="<?php echo getUrl('dev', 'gl_code_rule', array(), false, BACK_OFFICE_SITE_URL) ?>">
            <input type="hidden" name="form_submit" value="ok">
            <?php foreach( $list as $code=>$name ){ ?>
                <input type="hidden" name="biz_code[<?php echo $code; ?>]" value="<?php echo $code; ?>">
                <div>
                    <label for="input" class="col-sm-4 control-label"><?php echo $name; ?></label>
                    <div class="col-sm-8">
                        <?php foreach ((new currencyEnum())->Dictionary() as $k => $v): ?>
                        <div class="form-group" >
                            <span class="second-label col-sm-4"><?php echo 'Prefix of ' . $v . ':'; ?></span>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="prefix_<?php echo $k; ?>[<?php echo $code; ?>]" value="<?php echo $setting_value[$code]['prefix'][$k]; ?>">
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <div class="form-group">
                            <span class=" second-label col-sm-4"><?php echo 'ID Width:'; ?></span>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" step="1"  name="length[<?php echo $code; ?>]" value="<?php if($setting_value[$code]['length'] > 0){ echo $setting_value[$code]['length'];} ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>

            <div class="form-group">
                <div class="col-sm-offset-4 col-col-sm-8" style="padding-left: 15px">
                    <input type="submit" class="btn btn-danger" id="save-limit" value="<?php echo 'Save' ?>">
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </div>
            </div>

        </form>

    </div>
</div>

<script>

</script>