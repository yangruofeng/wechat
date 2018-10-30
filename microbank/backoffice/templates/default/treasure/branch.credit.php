<?php $branch = $output['branch'] ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Branch Management</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl("treasure", "branchList", array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Branch - <?php echo $branch['branch_code'] ?></span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post"
              action="<?php echo getUrl('treasure', 'branchCredit', array(), false, BACK_OFFICE_SITE_URL) ?>">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="branch_id" value="<?php echo $output['branch']['uid'] ?>">

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-5 control-label"><?php echo 'Credit' ?></label>

                <div class="col-sm-7">
                    <div class="input-group">
                        <input type="number" class="form-control" name="credit"
                               value="<?php echo $branch['credit']; ?>">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0">$</span>
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
                <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i
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

</script>