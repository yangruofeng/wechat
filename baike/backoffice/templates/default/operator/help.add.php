<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/ueditor/utf8-php/ueditor.config.js'?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/ueditor/utf8-php/ueditor.all.js'?>"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Help</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('operator', 'help', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Add System Help</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px;">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Category';?></label>
                <div class="col-sm-9">
                    <select class="form-control" name="category">
                        <?php foreach($output['help_category'] as $key => $category){?>
                            <option value="<?php echo $key?>"><?php echo ucwords(strtolower($category));?></option>
                        <?php }?>
                    </select>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Title';?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="help_title" placeholder="" value="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Content' ?></label>
                <div class="col-sm-9">
                    <textarea name="help_content" style="width: 600px;height:300px;" id="help_content">
                    </textarea>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Sort' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="sort" placeholder="" value="0">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo 'Is Show';?></label>
                <div class="col-sm-9" style="margin-top: 7px">
                    <label><input type="radio" value="1" name="is_show" checked><?php echo 'Show'?></label>
                    <label style="margin-left: 10px"><input type="radio" value="0" name="status"><?php echo 'Not Show'?></label>
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
        var ue = UE.getEditor('help_content',{
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
            category: {
                required: true
            },
            help_title: {
                required: true
            },
            help_content: {
                required: true
            },
            sort: {
                required: true
            }
        },
        messages: {
            category: {
                required: '<?php echo 'Required!'?>'
            },
            help_title: {
                required: '<?php echo 'Required!'?>'
            },
            help_content: {
                required: '<?php echo 'Required!'?>'
            },
            sort: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>