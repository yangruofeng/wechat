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
<?php
$asset_type = $output['asset_type'];
$asset_survey = $output['asset_survey'];
$certification_type = enum_langClass::getCertificationTypeEnumLang();
$lang_lst = enum_langClass::getLangType();
$survey_json = my_json_decode($asset_survey['survey_json']) ?: array();
$survey_json_kh = my_json_decode($asset_survey['survey_json_kh']) ?: array();
$survey_json_type = my_json_decode($asset_survey['survey_json_type']) ?: array();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Asset Survey</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('setting', 'assetSurvey', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 650px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="asset_type" value="<?php echo $asset_type?>">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Asset Name'?></label>
                <div class="col-sm-9">
                    <label class="control-label"><?php echo $certification_type[$asset_type]?></label>
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
                <?php foreach ($survey_json as $key => $val) { ?>
                    <div class="col-sm-offset-3 col-sm-9 survey_info">
                        <div class="col-sm-11">
                            <div class="col-sm-4">
                                <input type="hidden" name="survey_code[]" value="<?php echo $key?>">
                                <input type="text" class="form-control" name="survey_name[]" placeholder="Name" value="<?php echo $val?>">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="survey_name_kh[]" placeholder="Khmer Name" value="<?php echo $survey_json_kh[$key]?>">
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control" name="survey_type[]">
                                    <?php foreach ($output['survey_type'] as $k => $v) { ?>
                                        <option value="<?php echo $k?>" <?php echo $survey_json_type[$key] == $k ? 'selected' : ''?>><?php echo ucwords(strtolower($v))?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <i class="fa fa-minus" onclick="remove_survey(this)"></i>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <a type="button" class="btn btn-danger" id="edit_submit" style="min-width: 80px;"><i class="fa fa-check"></i><?php echo 'Save' ?></a>
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
        $('#edit_submit').click(function () {
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
</script>