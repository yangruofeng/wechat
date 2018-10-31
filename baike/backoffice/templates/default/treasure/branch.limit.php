<?php $branch_limit = $output['branch_limit'] ?>
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
    <div class="container" style="width: 800px;padding-left: 15px">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active" style="min-width: 70px;text-align: center">
                <a href="#limit_member" aria-controls="limit_member" role="tab" data-toggle="tab" style="border-left: 0;padding: 5px 10px">Limit Member</a>
            </li>
            <li role="presentation" style="min-width: 70px;text-align: center">
                <a href="#limit_approve" aria-controls="limit_approve" role="tab" data-toggle="tab" style="padding: 5px 10px">Limit Approve</a>
            </li>
        </ul>
        <div class="tab-content" style="padding-top: 20px">
            <div role="tabpanel" class="tab-pane active" id="limit_member" style="width: 600px">
                <form class="form-horizontal" id="form_limit_member" method="post"
                      action="<?php echo getUrl('treasure', 'branchLimit', array(), false, BACK_OFFICE_SITE_URL) ?>">
                    <input type="hidden" name="form_submit" value="ok">
                    <input type="hidden" name="branch_id" value="<?php echo $output['branch']['uid'] ?>">
                    <?php foreach ($output['biz_limit_name'] as $key => $value) { ?>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5"><?php echo $value ?></label>
                            <div class="col-sm-7">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="<?php echo $key ?>[max_per_day]"
                                           value="<?php echo $branch_limit[$key]['max_per_day'] >= 0 ? $branch_limit[$key]['max_per_day'] : '' ?>">
                                    <span class="input-group-addon" style="min-width: 80px;border-left: 0">$ Per Day</span>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-5 "><?php echo 'Approve Credit Limit' ?></label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <input type="number" class="form-control" name="approve_credit_limit"
                                       value="<?php echo $branch_limit['approve_credit_limit']['limit_value']; ?>">
                                <span class="input-group-addon" style="min-width: 80px;border-left: 0">$</span>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="form-group">
                    <div class="col-sm-offset-5 col-col-sm-7" style="padding-left: 12px">
                        <button type="button" class="btn btn-danger" id="submit_limit_member"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                        <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i
                                class="fa fa-reply"></i><?php echo 'Back' ?></button>
                    </div>
                </div>
            </div>

            <?php
            $list = $output['list'];
            $setting_value = $output['setting_value'];
            ?>
            <div role="tabpanel" class="tab-pane" id="limit_approve">
                <form class="form-horizontal" id="form_limit_approve" method="post"
                      action="<?php echo getUrl('treasure', 'saveLimitApprove', array(), false, BACK_OFFICE_SITE_URL) ?>">
                    <input type="hidden" name="form_submit" value="ok">
                    <input type="hidden" name="branch_id" value="<?php echo $output['branch']['uid'] ?>">
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
                            <button type="submit" class="btn btn-danger" id="submit_limit_approve"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                            <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i
                                    class="fa fa-reply"></i><?php echo 'Back' ?></button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#submit_limit_member').click(function () {
        $('#form_limit_member').submit();
    })

    $('#submit_limit_approve').click(function () {
        $('#form_limit_approve').submit();
    })
</script>