<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        padding: 5px 12px;
    }

</style>
<?php $user = $output['user'] ?>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post"
              action="<?php echo getUrl('cash_in_vault', 'cashierCredit', array(), false, ENTRY_COUNTER_SITE_URL) ?>">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="cashier_id" value="<?php echo $user['uid'] ?>">

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-5 control-label"><?php echo 'Cashier Name' ?></label>

                <div class="col-sm-7">
                    <div style="line-height: 34px">
                        <?php echo $user['user_name']; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-5 control-label"><?php echo 'Credit' ?></label>

                <div class="col-sm-7">
                    <div class="input-group">
                        <input type="number" class="form-control" name="credit"
                               value="<?php echo $user['credit']; ?>">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0;border-radius: 0">$</span>
                    </div>
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
        var credit = Number($('input[name="credit"]').val());
        if (credit < 0) {
            alert('Credit can\'t be less than 0.');
            return;
        }
        $('.form-horizontal').submit();
    })

    function goBack() {
        window.location.href = '<?php echo getUrl('cash_in_vault', 'cashier', array(), false, ENTRY_COUNTER_SITE_URL)?>';
    }
</script>