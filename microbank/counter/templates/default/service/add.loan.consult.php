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
        margin-bottom: 30px;
    }

    .mortgage_type .checkbox-inline {
        margin-left: 0px;
    }

</style>

<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>

<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="register-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Add Consult</h5>
                </div>
                <div class="content" style="padding-right: 50px">
                    <form class="form-horizontal" method="post">
                        <input type="hidden" name="form_submit" value="ok">

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Applicant Name'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="applicant_name" placeholder="" value="<?php echo $_GET['applicant_name']?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Apply Amount'?></label>
                            <div class="col-sm-9">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" name="apply_amount" value="<?php echo $_GET['apply_amount']?>">
                                    <span class="input-group-addon" style="border: 0;padding: 0">
                                        <select class="form-control" name="currency" style="min-width: 80px;">
                                            <?php foreach ($output['currency_list'] as $key => $currnecy) { ?>
                                                <option value="<?php echo $key?>"><?php echo $currnecy?></option>
                                            <?php } ?>
                                        </select>
                                    </span>
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
                                    <input type="text" class="form-control" name="phone_number" value="<?php echo $_GET['contact_phone'] ?>" placeholder="" style="width: 80%;">
                                    <div class="error_msg"></div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Loan Time'?></label>
                            <div class="col-sm-9">
                                <div class="input-group" style="width: 100%;">
                                    <input type="number" class="form-control" name="loan_time" value="<?php echo $_GET['loan_time'] ?>" placeholder="" style="width: 70%;">

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
                                <input type="text" class="form-control" name="loan_purpose" placeholder="" value="<?php echo $_GET['loan_purpose']?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'Mortgage'?></label>
                            <div class="col-sm-9 mortgage_type">
                                <?php foreach ($output['mortgage_type']['item_list'] as $key => $item) { ?>
                                    <label class="col-sm-5 checkbox-inline">
                                        <input type="checkbox" name="mortgage[]" value="<?php echo $item; ?>">
                                        <?php echo $item; ?>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'Location' ?></label>
                            <div class="col-sm-9" id="select_area">

                            </div>
                            <div class="col-sm-9 col-sm-offset-3">
                                <input type="text" class="form-control" name="address_detail" placeholder="Detailed Address" value="">
                                <input type="hidden" name="address" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Content' ?></label>
                            <div class="col-sm-9">
                                <textarea name="memo" style="width: 542px" id="content">
                                </textarea>
                                <div class="error_msg"></div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group button">
        <button type="button" class="btn btn-danger" id="submit_save" style="min-width: 80px;"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
        <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 10px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
    </div>
</div>

<script>
    $(function () {

        var ue = UE.getEditor('content',{
            toolbars: [[
                'fullscreen', 'source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
                'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
//            'directionalityltr', 'directionalityrtl',
                'indent',
//            '|',
                'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
                'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                'simpleupload',
//                'insertimage', 'emotion',
//            'scrawl', 'insertvideo', 'music', 'attachment', 'map', 'gmap', 'insertframe', 'insertcode', 'webapp', 'pagebreak', 'template',
                'background', '|',
                'horizontal', 'date', 'time', 'spechars', 'snapscreen',
//            'wordimage',
                '|',
                'inserttable', 'deletetable',
//            'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts'
//            , '|', 'print', 'preview', 'searchreplace', 'drafts', 'help'
            ]],

            initialFrameHeight:280,
//        initialFrameWidth:800,
            enableAutoSave:false,
            autoHeightEnabled: false,
//        autoFloatEnabled: true,
//        imageAllowFiles:[".png", ".jpg", ".jpeg", ".bmp"],
            lang:'en'
        });

        getArea(0);
        $('#select_area').delegate('select', 'change', function () {
            var _value = $(this).val();
            $('input[name="address_id"]').val(_value);
            $(this).closest('div').nextAll().remove();

            if (_value != 0 && $(this).find('option[value="' + _value + '"]').attr('is-leaf') != 1) {
                getArea(_value);
            }
        })

        $('#submit_save').click(function () {
            if (!$(".form-horizontal").valid()) {
                return;
            }

            var _address_region = [];
            $('#select_area select').each(function () {
                if ($(this).val() != 0) {
                    _address_region.push($(this).find('option:selected').text());
                }
            })
            var _address_detail = $.trim($('input[name="address_detail"]').val());
            if (_address_detail) {
                _address_region.push(_address_detail);
            }
            var _address = _address_region.reverse().join(', ');
            $('input[name="address"]').val(_address);

            $('.form-horizontal').submit();
        })
    })

//  贷款申请列表
    function getArea(uid) {
        yo.dynamicTpl({
            tpl: "service/area.list",
            control:"counter_base",
            dynamic: {
                api: "service",
                method: "getAreaList",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $("#select_area").append(_tpl);
            }
        })
    }

//   验证是否输入
    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            applicant_name: {
                required: true
            },
            apply_amount: {
                required: true
            },
            loan_purpose: {
                required: true
            },
            contact_phone: {
                required: true
            }
        },
        messages: {
            applicant_name: {
                required: '<?php echo 'Required!'?>'
            },
            apply_amount: {
                required: '<?php echo 'Required!'?>'
            },
            loan_purpose: {
                required: '<?php echo 'Required!'?>'
            },
            contact_phone: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>