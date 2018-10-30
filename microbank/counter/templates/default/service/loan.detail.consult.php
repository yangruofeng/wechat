<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/ueditor/utf8-php/ueditor.config.js'?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/ueditor/utf8-php/ueditor.all.js'?>"></script>
<style>
    .mortgage_type .col-sm-4 {
        margin-top: 7px;
        padding-left: 0px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    #select_area .col-sm-6:nth-child(2n+1) {
        padding-left: 0px;
        margin-bottom: 10px;
    }

    #select_area .col-sm-6:nth-child(2n) {
        padding-right: 0px;
        margin-bottom: 10px;
    }



    .container{
        margin-top: 30px;
        width: 800px !important;
        margin-left: 60px;
    }

    button{
        border-radius:0px !important;
    }

    .button{
        padding:25px 280px;
        margin-bottom: 50px;
    }


    #content-bottom{
        position:fixed;
        bottom: 0px;
        z-index: 99!important;
    }



</style>

<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>

<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="register-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Loan Consult Detail</h5>
                </div>
                <div class="content" style="padding-right: 50px">
                    <form class="form-horizontal" method="post">
                        <input type="hidden" name="form_submit" value="ok">

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Applicant Name'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="applicant_name" placeholder="" value="<?php echo $output['consult']['applicant_name']?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Apply Amount'?></label>
                            <div class="col-sm-9">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" name="apply_amount" value="<?php echo $output['consult']['apply_amount']?>">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0;border-radius: 0">$</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Contact Phone' ?></label>
                            <div class="col-sm-9">
                                <div class="input-group" style="width: 100%;">
                                    <select class="form-control" name="country_code" id="" style="width: 20%;">
                                        <?php echo tools::getCountryCodeOptions('855'); ?>
                                    </select>
                                    <input type="text" class="form-control" name="phone_number" value="<?php echo $output['consult']['contact_phone'] ?>" placeholder="" style="width: 80%;">
                                    <div class="error_msg"></div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Loan Time'?></label>
                            <div class="col-sm-9">
                                <div class="input-group" style="width: 100%;">
                                    <input type="number" class="form-control" name="loan_time" value="<?php echo $output['consult']['loan_time'] ?>" placeholder="" style="width: 70%;">

                                    <select class="form-control" name="loan_time_unit" id="" style="width: 30%;">
                                        <?php $unit = (new loanPeriodUnitEnum())->toArray();$time_lang = enum_langClass::getLoanTimeUnitLang(); foreach( $unit as $key=>$value ){ ?>
                                            <option value="<?php echo $value; ?>"><?php echo $time_lang[$value]; ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Loan Purpose'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="loan_purpose" placeholder="" value="<?php echo $output['consult']['loan_purpose']?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'Mortgage'?></label>
                            <div class="col-sm-9 mortgage_type">
                                <input type="text" class="form-control" name="loan_purpose" placeholder="" value="<?php echo str_replace(",", "/", $output['consult']['mortgage']) ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'Location' ?></label>
                            <div class="col-sm-9">
                                <div class="col-sm-9" style="border: 1px solid lightgrey;width:542px;height: 50px;">
                                    <?php echo $output['consult']['address']?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Content' ?></label>
                            <div class="col-sm-9" style="border: 1px solid lightgrey;width:542px;height: 200px;margin-left: 15px">
                                 <?php echo $output['consult']['memo']?>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group button">
        <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 10px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
    </div>
</div>

