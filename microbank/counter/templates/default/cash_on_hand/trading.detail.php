<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>


<div class="page" style="width: 700px">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="register-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Trading Detail</h5>
                </div>
                <div class="content" style="padding-right: 50px">
                    <form class="form-horizontal" method="post">
                        <?php if($output['detail']['display_name'] || $output['detail']['user_name'] ){?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Partner Name'?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="" value="<?php echo $output['detail']['display_name']?:$output['detail']['user_name']?>" readonly>
                                </div>
                            </div>
                        <?php }?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Trading Type'?></label>
                            <div class="col-sm-9">
                                <div class="input-group" style="width: 100%">
                                    <input type="text" class="form-control"  placeholder="" value="<?php echo ucwords(str_replace('_', ' ', $output['detail']['trading_type'])) ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Trading Direction' ?></label>
                            <div class="col-sm-9">
                                <div class="input-group" style="width: 100%">
                                    <input type="text" class="form-control" name="apply_amount" value="<?php echo $output['detail']['credit']>0 ? "Cash in": "Cash out"; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Trading Amount' ?></label>
                            <div class="col-sm-9">
                                <div class="input-group" style="width: 100%">
                                    <input type="text" class="form-control" name="apply_amount" value="<?php echo $output['detail']['credit']>0 ? ncPriceFormat($output['detail']['credit']): ncPriceFormat($output['detail']['debit']); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Trading Time' ?></label>
                            <div class="col-sm-9">
                                <div class="input-group" style="width: 100%">
                                    <input type="text" class="form-control" placeholder="" value="<?php echo $output['detail']['update_time'] ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group button">
        <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 310px;margin-top: 30px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
    </div>
</div>




