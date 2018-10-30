<!--
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/webuploader/webuploader.min.js"></script>
-->
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/webuploader/webuploader.css" type="text/css" rel="stylesheet"/>

<script type="text/javascript">
    var site_url = "<?php echo getUrl('base','getUploadParam',array(),false,PROJECT_SITE_URL)?>";
    var upload_url = "<?php echo C('upyun_param')['target_url']?>";
    var upyun_url = "<?php echo UPYUN_SITE_URL.DS?>";
    var swf_url = "<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/webupload/Uploader.swf";
    /**
     *   @param upload_id 上传按钮ID
     *   @param default_dir 上传upyun文件夹
     *   @param file_name 上传图片input name
     *   @param prepend_el 图片列表外层DIV
     *   @param clone_el 图片列表样式DIV
     *   @param multiple 是否是多图 是true 否false
     */
    function webuploader2upyun(upload_id, default_dir, file_name, prepend_el, clone_el, multiple) {
        var _div=$("#"+upload_id);
        var _file_item=$('<input type="file" id="file_new_item" name="file_new_item" class="file-item" style="display: none">');
        _div.append(_file_item);
        _div.find("img").on("click",function(){
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

                    $(clone_el).find('.img-a').attr('href',_full_path);
                    $(clone_el).find('img').attr('src', _full_path);
                    $(clone_el).find('.del-item').attr('onclick', 'delImageItem(this,"'+_img_path+'","'+file_name+'")');  // 用_img_path是找不到的
                    $(prepend_el).before($(clone_el).html());
                    var jsonImgs = $('input[name='+file_name+']').val();
                    var arr = jsonImgs ? JSON.parse(jsonImgs.replace(/'/g, '"')) : [];
                    arr.push(_img_path);
                    $('input[name='+file_name+']').val(JSON.stringify(arr));
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
            var img_name = response.url.split('/').pop();
            if(multiple){
                $(clone_el).find('img').attr('src', upyun_url + response.url+'!120');
                $(clone_el).find('.del-item').attr('onclick', 'delImageItem(this,"'+response.url+'")');
                $(prepend_el).before($(clone_el).html());
                var jsonImgs = $('input[name='+file_name+']').val();
                var arr = jsonImgs ? JSON.parse(jsonImgs.replace(/'/g, '"')) : [];
                arr.push(response.url);
                $('input[name='+file_name+']').val(JSON.stringify(arr));
            }else{

            }
        });

        // 文件上传失败
        uploader.on('uploadError', function (file) {
            alert('Upload Fail!');
        });
        */
    }
</script>
