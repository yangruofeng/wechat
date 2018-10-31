
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
            <h3>Counter Biz Setting</h3>
        </div>
    </div>
    <div class="container" style="width: 800px;">
        <form class="form-horizontal" method="post" action="<?php echo getUrl('dev', 'counterBizSetting', array(), false, BACK_OFFICE_SITE_URL) ?>">

            <input type="hidden" name="form_submit" value="ok">
            <?php foreach( $list as $code=>$name ){ ?>

                <div>

                    <label for="input" class="col-sm-4 control-label"><?php echo $name; ?></label>
                    <div class="col-sm-8">
                        <div class="form-group" >
                            <span class="second-label col-sm-4"><?php echo 'Is Require CT Check:'; ?></span>
                            <div class="col-sm-8">

                                <input type="hidden" name="biz_code[<?php echo $code; ?>]" value="<?php echo $code; ?>">
                                <input style="width: 25px;margin-top: -5px;" type="checkbox" class="form-control"
                                       name="is_require_ct_check[<?php echo $code; ?>]"
                                       value="1" <?php echo $setting_value[$code]['is_require_ct_approve']?'checked':''; ?> >

                            </div>
                        </div>


                        <div class="form-group">
                            <span class=" second-label col-sm-4"><?php echo 'Min Check Amount:'; ?></span>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input style="width: 120px;" type="number" step="1"  name="min_check_amount[<?php echo $code; ?>]" value="<?php if($setting_value[$code]['min_approve_amount'] > 0){ echo $setting_value[$code]['min_approve_amount'];} ?>" class="form-control">
                                    <span class="input-group-addon" style="width: 80px;">USD</span>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>





            <?php } ?>

            <div class="form-group">
                <div class="col-sm-offset-4 col-col-sm-8" style="padding-left: 15px">
                    <input type="submit" class="btn btn-danger" id="save-limit" value="<?php echo 'Save' ?>">

                    <!--<button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php /*echo 'Back' */?></button>
                    -->
                </div>
            </div>

        </form>

    </div>
</div>

<script>

</script>