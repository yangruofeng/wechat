<?php $i; foreach($output['detail_type'] as $type => $title) { ++$i; ?>
    <?php if ($i % 2 == 1) { ?>
        <div class="base-info clearfix">
    <?php } ?>
    <div class="<?php echo $type ?>" style="width: 49%; float: <?php echo $i % 2 == 1 ? 'left' : 'right' ?>; background-color: #FFF">
        <div class="ibox-title">
            <div class="col-sm-8"><h5><?php echo $title ?></h5></div>
            <div class="col-sm-4">
                <i class="fa fa-edit <?php echo !$product_info['uid'] ? 'not-allowed' : '' ?> allow-state"
                   title="<?php echo $product_info['uid'] ? 'Edit' : 'Please save base info first.' ?>"
                   onclick="edit_text('<?php echo $type ?>')"></i>
                <i class="fa fa-mail-reply" onclick="cancel_text('<?php echo $type ?>')"></i>
                <i class="fa fa-floppy-o" onclick="save_text('<?php echo $type ?>')"></i>
            </div>
        </div>
        <div class="content clearfix">
            <div><?php echo $product_info[$type]?></div>
            <textarea name="product_feature" id="<?php echo $type ?>" style="display: none;"><?php echo $product_info[$type]?></textarea>
        </div>
    </div>
    <?php if ($i % 2 == 0) { ?>
        </div>
    <?php } ?>
<?php } ?>

<script>
    var _product_id = '<?php echo $product_info['uid']; ?>';
    function edit_text(_name) {
        if (!_product_id) {
            return;
        }
        $('.' + _name).find('.fa-edit').hide();
        $('.' + _name).find('.fa-mail-reply').show();
        $('.' + _name).find('.fa-floppy-o').show();
        $('.' + _name).find('.content div').first().hide();
        $('.' + _name).find('#' + _name).show();
        ue(_name);
    }

    function cancel_text(_name) {
        $('.' + _name).find('.fa-edit').show();
        $('.' + _name).find('.fa-mail-reply').hide();
        $('.' + _name).find('.fa-floppy-o').hide();
        $('.' + _name).find('.content div').first().show();
        $('.' + _name).find('#' + _name).hide();
    }

    function save_text(_name) {
        var _val = ueArr[_name].getContent();
        yo.loadData({
            _c: "savings",
            _m: "updateProductDescription",
            param: {uid: '<?php echo $product_info['uid']?>', name: _name, val: _val},
            callback: function (_o) {
                if (_o.STS) {
                    $('.' + _name).find('.fa-edit').show();
                    $('.' + _name).find('.fa-mail-reply').hide();
                    $('.' + _name).find('.fa-floppy-o').hide();
                    $('.' + _name).find('.content div').first().html(_val).show();
                    $('.' + _name).find('#' + _name).hide();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    var ueArr = [];
    function ue(_name) {
        ueArr[_name] = UE.getEditor(_name, {
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
    }
</script>