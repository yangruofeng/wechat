<!--
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/webuploader/webuploader.min.js"></script>
-->
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/webuploader/webuploader.css" type="text/css" rel="stylesheet"/>

<script type="text/javascript">
    var site_url = "<?php echo getUrl('base','getUploadParam',array(),false,PROJECT_SITE_URL)?>";
    var upload_url = "<?php echo C('upyun_param')['target_url']?>";
    var upyun_url = "<?php echo UPYUN_SITE_URL.DS?>";
    var swf_url = "<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/webupload/Uploader.swf";

    function webuploader2upyun(upload_id, default_dir) {
        var _btn=$("#"+upload_id);
        var _div=(_btn).closest(".image-uploader-item");

        _btn.addClass("btn btn-default");
        var _file_item=$('<input type="file" class="file-item" style="display: none">');
        _div.append(_file_item);
        _btn.on("click",function(){
            _file_item.click();
        });

        _file_item.on("change",function(){
            var _data = new FormData();
            _data.append("file_key","file_new_item");
            _data.append("file_dir",default_dir);
            _data.append('file_new_item', _file_item[0].files[0]);
            _div.waiting();
            yo.loadData({
                _c:"root",
                _m:"upload2UpYun",
                param:_data,
                is_formData:true,
                ajax:{cache: false,
                    processData: false,
                    contentType: false,
                    dataType:"JSON"
                },
                callback:function(_o){
                    _div.unmask();
                    if(!_o.STS) return;
                    var _img_path=_o.DATA.image_path;
                    var _full_path=_o.DATA.full_path;

                    _div.find('input[name=' + upload_id + ']').val(_img_path);
                    _div.find('input[id=txt_' + upload_id + ']').val(_img_path);
                    _div.find('#show_' + upload_id).attr('src', _full_path).show();
                    if (_div.find('.show_' + upload_id + '_div').length > 0) {
                        _div.find('.show_' + upload_id + '_div').css('display', 'block');
                    }
                }
            });

        });



        /*
        var uploader = WebUploader.create({
            // 选完文件后，是否自动上传。
            auto: false,

            // swf文件路径
            swf: swf_url,

            // 文件接收服务端。
            server: upload_url,

            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#' + upload_id,

            // 只允许选择图片文件。
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/jpg,image/jpeg,image/png'
            },
            method: 'POST'
        });

        uploader.on('beforeFileQueued', function (e) {
            $.get(site_url, {default_dir: default_dir}, function (response) {
                var obj = $.parseJSON(response);
                uploader.option('formData', {
                    'policy': obj.policy,
                    'authorization': obj.authorization
//                    'signature': obj.signature
                });
                uploader.upload();
            });
        })

        // 文件上传成功
        uploader.on('uploadSuccess', function (file, response) {
//            var img_name = response.url.split('/').pop();
            var img_name = response.url;
            $('input[name=' + upload_id + ']').val(img_name);
            $('input[id=txt_' + upload_id + ']').val(img_name);
            $('#show_' + upload_id).attr('src', upyun_url + response.url).show();
            if ($('.show_' + upload_id + '_div').length > 0) {
                $('.show_' + upload_id + '_div').css('display', 'block');
            }
        });

        // 文件上传失败
        uploader.on('uploadError', function (file) {
            alert('Upload Fail!');
        });
        */
    }
</script>
