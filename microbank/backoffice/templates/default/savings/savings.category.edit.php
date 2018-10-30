<?php $info = $output['info'] ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Category</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('savings', 'category', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Category List</span></a>
                </li>
                <li><a class="current"><span><?php echo $output['current_title']?></span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content" style="width: 700px">
            <form class="form-horizontal" method="post" id="frm_item">
                <input type="hidden" name="form_submit" value="ok">
                <input type="hidden" name="uid" value="<?php echo $info['uid'] ?>">

                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Category Code' ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="category_code" value="<?php echo $info['category_code'] ?>">
                        <div class="error_msg"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Category Name' ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="category_name" value="<?php echo $info['category_name'] ?>">
                        <div class="error_msg"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Category Type' ?></label>
                    <div class="col-sm-9">
                        <select class="form-control" name="category_type">
                            <?php foreach ($output['category_type'] as $key => $type) { ?>
                                <option value="<?php echo $key; ?>" <?php echo $info['category_type'] == $key ? 'selected' : ''?>><?php echo ucwords(strtolower($type)); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Category Icon'?></label>
                    <div class="col-sm-9">
                        <div class="image-uploader-item">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <img id="show_category_icon" style="display: <?php echo $info['category_icon']?'block':'none'?>;" src="<?php echo getImageUrl($info['category_icon'],imageThumbVersion::SMALL_IMG);?>">
                                </li>
                                <li class="list-group-item">
                                    <button type="button" id="category_icon">Upload</button>
                                    <input name="category_icon" type="hidden" value="<?php echo $info['category_icon']?>">
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Term Style' ?></label>
                    <div class="col-sm-9">
                        <select class="form-control" name="category_term_style">
                            <?php foreach ((new savingsCategoryTermStyleEnum())->Dictionary() as $key => $text) { ?>
                                <option value="<?php echo $key; ?>" <?php echo $info['category_term_style'] == $key ? 'selected' : ''?>><?php echo ucwords(strtolower($text)); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3"></label>
                    <div class="col-sm-9">
                        <ul>
                            <li><b>Fixed:</b> Product term is a fixed number of days. Manually select product that need to be purchased by clients.</li>
                            <li><b>Range:</b> Product term is a range of days. The clients choose the purchase period, and the system automatically finds the right product for him.</li>
                            <li><b>Free:</b> Product has a minimum term only. The clients can withdraw at any time after the minimum term.</li>
                        </ul>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Description' ?></label>
                    <div class="col-sm-9">
                        <textarea name="category_description" id="category_description"><?php echo $info['category_description'] ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'State' ?></label>
                    <div class="col-sm-9">
                        <label class="radio-inline"><input type="radio" name="state" value="<?php echo savingsCategoryState::ACTIVE; ?>" <?php echo (!isset($info['state']) || $info['state'] == savingsCategoryState::ACTIVE) ? 'checked' : ''?>>Active</label>
                        <label class="radio-inline"><input type="radio" name="state" value="<?php echo savingsCategoryState::INACTIVE; ?>" <?php echo (isset($info['state']) && $info['state'] == savingsCategoryState::INACTIVE) ? 'checked' : ''?>>Inactive</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-col-sm-9" style="padding-left: 20px">
                        <button type="button" class="btn btn-danger" style="min-width: 80px;" id="btnSubmit">
                            <i class="fa fa-check"></i><?php echo 'Save' ?>
                        </button>
                        <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 10px" onclick="javascript:history.go(-1);">
                            <i class="fa fa-reply"></i><?php echo 'Back' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.config.js' ?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.all.js' ?>"></script>
<script>
    $(function () {
        $('#btnSubmit').click(function () {
            if (!$("#frm_item").valid()) {
                return;
            }

            $('#frm_item').submit();
        })
    });

    ue = UE.getEditor('category_description', {
        toolbars: [[
            'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'indent', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',
            'link', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'background', '|',
            'horizontal', 'date', 'time', 'spechars','inserttable',
        ]],
        initialFrameHeight: 300,
        enableAutoSave: false,
        autoHeightEnabled: false,
        lang: 'en'
    });

    $('#frm_item').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            category_code: {
                required: true
            },
            category_name: {
                required: true
            }
        },
        messages: {
            category_code: {
                required: '<?php echo 'Required.'?>'
            },
            category_name: {
                required: '<?php echo 'Required.'?>'
            }
        }
    });
</script>
<!--图片上传 start-->
<?php require_once template(':widget/inc_upload_upyun');?>
<script type="text/javascript">
    webuploader2upyun('category_icon','product');
</script>
<!--图片上传 end-->