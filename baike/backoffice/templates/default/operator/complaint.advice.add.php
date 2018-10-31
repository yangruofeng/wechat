<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/ueditor/utf8-php/ueditor.config.js'?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/ueditor/utf8-php/ueditor.all.js'?>"></script>

<style>
    .btn {
        min-width: 80px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Complaint And Advice</h3>
            <ul class="tab-base">
                <li><a class="current" href="#"><span>Add</span></a></li>
                <li><a href="<?php echo getUrl('operator', 'complaintAdvice', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post" action="<?php echo getUrl('operator', 'saveComplaintAdvice', array(), false, BACK_OFFICE_SITE_URL)?>">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Type';?></label>
                <div class="col-sm-9">
                    <select class="form-control" name="type">
                        <option value="complaint">Complaint</option>
                        <option value="advice">Advice</option>
                    </select>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Title';?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="title" placeholder="" value="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Content' ?></label>
                <div class="col-sm-9">
                    <textarea name="content" style="width: 600px;height:300px;" id="content">
                    </textarea>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Client Name' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="contact_name" placeholder="" value="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Client Phone'?></label>
                <div class="col-sm-4">
                    <div class="input-group" style="width: 300px">
                        <span class="input-group-addon" style="padding: 0;border: 0;">
                            <select class="form-control" name="country_code" style="min-width: 80px;height: 34px">
                                <option  value="855">+855</option>
                                <option  value="66">+66</option>
                                <option  value="86">+86</option>
                            </select>
                        </span>
                        <input  type="number" class="form-control numinput" id="phone" name="phone_number" placeholder="" value="">
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                    <button type="button" class="btn btn-danger"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                    <button type="button" class="btn btn-default" style="margin-left: 10px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(function(){
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

            initialFrameHeight:350,
//        initialFrameWidth:800,
            enableAutoSave:false,
            autoHeightEnabled: false,
//        autoFloatEnabled: true,
//        imageAllowFiles:[".png", ".jpg", ".jpeg", ".bmp"],
            lang:'en'
        });
    })


    $('.btn-danger').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            type: {
                required: true
            },
            title: {
                required: true
            },
            content: {
                required: true
            },
            contact_name: {
                required: true
            },
            phone_number: {
                required: true
            }
        },
        messages: {
            type: {
                required: '<?php echo 'Required!'?>'
            },
            title: {
                required: '<?php echo 'Required!'?>'
            },
            content: {
                required: '<?php echo 'Required!'?>'
            },
            contact_name: {
                required: '<?php echo 'Required!'?>'
            },
            phone_number: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>