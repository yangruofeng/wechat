<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .page .container {
        margin-top: 80px;
    }

    .survey_info {
        margin-bottom: 10px;
    }

    .survey_info .col-sm-11 {
        padding: 0!important;
    }

    .survey_info .col-sm-11 .col-sm-4:first-child {
        padding-left: 0px!important;
        padding-right: 7px!important;
    }

    .survey_info .col-sm-11 .col-sm-4:last-child {
        padding-left: 7px!important;
        padding-right: 0px!important;
    }

    .survey_info .col-sm-1 .fa {
        margin-top: 10px;
        cursor: pointer;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Industry</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('setting', 'industry', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 650px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Industry Code'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="industry_code" placeholder="" value="<?php echo $_GET['industry_code']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Industry Category'?></label>
                <div class="col-sm-9">
                    <select class="form-control" name="industry_category">
                        <option value="">Please Select</option>
                        <?php foreach ($output['industry_category']['item_list'] as $key => $val) { ?>
                            <option value="<?php echo $key?>"><?php echo $val?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Industry Name'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="industry_name" placeholder="" value="<?php echo $_GET['industry_name']?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <?php $lang_lst=enum_langClass::getLangType();foreach($lang_lst as $lang_key=>$lang_value){?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Name('.$lang_value.")"?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="industry_name_json_<?php echo $lang_key?>" placeholder="" value="<?php echo $_GET['industry_name']?>">
                        <div class="error_msg"></div>
                    </div>
                </div>
            <?php }?>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Credit Of Profit(%)'?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="credit_rate" placeholder="" value="50">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'Survey Info'?></label>
                <div class="col-sm-9">
                    <div style="height: 34px;line-height: 34px;width: 30px;float: left">
                        <i class="fa fa-plus add-survey" style="margin-top: 10px;margin-left: 5px;cursor: pointer;" title="Add"></i>
                    </div>
                    <div class="error_msg survey_error" style="width: 330px;float: left;line-height: 30px"></div>
                </div>
                <!--
                <?php foreach ($_GET['survey_name'] as $key => $val) { ?>
                    <div class="col-sm-offset-3 col-sm-9 survey_info">
                        <div class="col-sm-11">
                            <div class="col-sm-6">
                                <input type="hidden" name="survery_code" value="<?php echo $_GET['survey_code'][$key]?>">
                                <input type="text" class="form-control" name="survey_name[]" placeholder="Name" value="<?php echo $val?>">
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="survey_name_kh[]" placeholder="Khmer Name" value="<?php echo $_GET['survey_code'][$key]?>">
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <i class="fa fa-minus" onclick="remove_survey(this)"></i>
                        </div>
                    </div>
                <?php } ?>
                -->
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'State'?></label>
                <div class="col-sm-9">
                    <label class="radio-inline"><input type="radio" value="1" name="state" checked>Valid</label>
                    <label class="radio-inline"><input type="radio" value="0" name="state">Invalid</label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" id="btn_add_industry" class="btn btn-danger" style="min-width: 80px;"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 10px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/html" id="survey-info">
    <div class="col-sm-offset-3 col-sm-9 survey_info">
        <div class="col-sm-11">
            <div class="col-sm-4">
                <input type="hidden" item_key="survey_code" name="survey_code[]">
                <input type="text" class="form-control" name="survey_name[]" placeholder="Name">
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="survey_name_kh[]" placeholder="Khmer Name">
            </div>
            <div class="col-sm-4">
                <select class="form-control" name="survey_type[]">
                    <?php foreach ($output['survey_type'] as $key => $val) { ?>
                        <option value="<?php echo $key?>"><?php echo ucwords(strtolower($val))?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-sm-1">
            <i class="fa fa-minus" onclick="remove_survey(this)"></i>
        </div>
    </div>
</script>

<script>
    $(function () {
        $('#btn_add_industry').click(function () {
            var valid = true;
            if (!$(".form-horizontal").valid()) {
                valid = false;
            }

            var survey_error = '';
            $('.survey_info input').each(function () {
                var val = $.trim($(this).val());
                if (!val) {
                    survey_error = 'Name and code required!';
                    valid = false;
                }
                $('.survey_error').html(survey_error);
            })

            if(!valid){
                return;
            }

            $('.form-horizontal').submit();
        })

        $('.add-survey').click(function () {
            var tpl = $('#survey-info').html();
            $(this).closest('.form-group').append(tpl);
            //产生随机数code，低频操作，重复概率很小
            var _new_svr_code=Math.floor(Math.random()*100000);
            var _mix_up=(new Date()).getSeconds();
            _new_svr_code=_new_svr_code.toString()+_mix_up.toString();
            $(this).closest('.form-group').find("input[item_key=survey_code]").last().val(_new_svr_code);
        })
    })

    function remove_survey(_e) {
        $(_e).closest('.survey_info').remove();
    }

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            industry_name: {
                required: true
            },
            industry_code: {
                required: true
            }
        },
        messages: {
            industry_name: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>