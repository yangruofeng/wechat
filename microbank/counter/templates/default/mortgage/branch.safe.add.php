<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        padding: 5px 12px;
    }

</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container" style="width: 500px;">
        <form class="form-horizontal" method="post"
              action="<?php echo getUrl('mortgage', 'addBranchSafe', array(), false, ENTRY_COUNTER_SITE_URL)?>">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-5 control-label"><?php echo 'Safe Code' ?></label>

                <div class="col-sm-7">
                    <input type="text" class="form-control" name="safe_code" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-5 control-label"><?php echo 'Remark' ?></label>

                <div class="col-sm-7">
                    <textarea class="form-control" name="remark"></textarea>
                </div>
            </div>
        </form>
        <div class="form-group">
            <div class="col-sm-offset-5 col-col-sm-7" style="padding-left: 12px">
                <button type="button" class="btn btn-danger" id="submit-btn"><i
                        class="fa fa-check"></i><?php echo 'Save' ?></button>
                <button type="button" class="btn btn-default" onclick="goBack();"><i
                        class="fa fa-reply"></i><?php echo 'Back' ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#submit-btn').click(function () {
        var safe_code = $.trim($('input[name="safe_code"]').val());
        if (!safe_code) {
            alert('Safe code can\'t be empty.');
            return;
        }
        $('.form-horizontal').submit();
    })

    function goBack() {
        window.location.href = '<?php echo getUrl('mortgage', 'branchSafe', array(), false, ENTRY_COUNTER_SITE_URL)?>';
    }
</script>