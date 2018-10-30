<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/ueditor/utf8-php/ueditor.config.js'?>"></script>
<!-- 编辑器源码文件 -->
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL.'/ueditor/utf8-php/ueditor.all.js'?>"></script>
<style>
    #select_area .col-sm-6:nth-child(2n+1) {
        padding-left: 0;
        padding-right: 3px;
        margin-bottom: 10px;
    }
    #select_area .col-sm-6:nth-child(2n) {
        padding-right: 0;
        padding-left: 3px;
        margin-bottom: 10px;
    }
    #show_company_icon {
        padding: 3px;
        border: 1px solid;
        max-height: 200px;
        max-width: 300px;
        margin-bottom: 5px;
        display: none;
    }
    .form-horizontal i {
        cursor: pointer;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Company Info</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('setting', 'companyInfo', array(), false, BACK_OFFICE_SITE_URL)?>"><span>View</span></a></li>
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <input type="hidden" name="address_id" value="<?php echo $output['company_config']['address_id']?>">
            <input type="hidden" name="address_region" value="<?php echo $output['company_config']['address_region']?>">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Company Name' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="company_name" value="<?php echo $output['company_config']['company_name'] ?>">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group hotline">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Hotline'; ?></label>
                <div class="col-sm-9">
                    <i class="fa fa-plus" style="margin-top: 11px" onclick="add_hotline()" title="Add Hotline"></i>
                </div>
            </div>
            <?php foreach ($output['company_config']['hotline'] as $hotline) { ?>
                <div class="form-group hotline">
                    <div class="col-sm-offset-3 col-sm-8" style="padding-right: 0">
                        <input type="text" class="form-control" name="hotline[]" value="<?php echo $hotline ?>">
                    </div>
                    <div class="col-sm-1">
                        <i class="fa fa-minus" style="margin-top: 11px" title="Remove Hotline" onclick="remove_hotline(this)"></i>
                    </div>
                </div>
            <?php } ?>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Email' ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="email" value="<?php echo $output['company_config']['email'] ?>" placeholder="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Icon'?></label>
                <div class="col-sm-9">
                    <div class="image-uploader-item">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <img id="show_company_icon" style="display: <?php echo $output['company_config']['company_icon']?'block':'none'?>;" src="<?php echo getImageUrl($output['company_config']['company_icon'],null,'company');?>">
                            </li>
                            <li class="list-group-item">
                                <button type="button" id="company_icon">Upload</button>
                                <input name="company_icon" type="hidden" value="<?php echo $output['company_config']['company_icon']?>">
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Location' ?></label>
                <div class="col-sm-9" id="select_area">
                    <?php if (!empty($output['region_list'])) { ?>
                        <?php foreach ($output['region_list'] as $area) { ?>
                            <div class="col-sm-6">
                                <select class="form-control">
                                    <option value="0">Please Select</option>
                                    <?php foreach ($area as $val) { ?>
                                        <option value="<?php echo $val['uid'] ?>" is-leaf="<?php echo $val['is_leaf'] ?>" <?php echo $val['selected']?'selected':''?>><?php echo $val['node_text'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="col-sm-9 col-sm-offset-3">
                    <input type="text" class="form-control" name="address_detail" placeholder="Detailed Address" value="<?php echo $output['company_config']['address_detail']?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Map</label>
                <div class="col-sm-9">
                    <div id="map" style="width: 600px;height: 350px;border: 1px solid #9e9e9e"></div>
                    <input type="hidden" id="coord_x" name="coord_x" value="<?php echo $output['company_config']['coord_x']?>">
                    <input type="hidden" id="coord_y" name="coord_y" value="<?php echo $output['company_config']['coord_y']?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Description' ?></label>
                <div class="col-sm-9">
                    <textarea name="description" style="width: 600px;height:300px;" id="description">
                        <?php echo $output['company_config']['description']?>
                    </textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="button" class="btn btn-danger save-info" style="margin-left: 0;min-width: 80px"><?php echo 'Save' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/html" id="hotline_tpl">
    <div class="form-group hotline">
        <div class="col-sm-offset-3 col-sm-8" style="padding-right: 0">
            <input type="text" class="form-control" name="hotline[]" value="" placeholder="">
        </div>
        <div class="col-sm-1">
            <i class="fa fa-minus" style="margin-top: 11px" title="Remove Hotline" onclick="remove_hotline(this)"></i>
        </div>
    </div>
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA96UVKWM82_YHJx1h9j9-NhacFbGANf1k&callback=initMap"></script>
<script type="text/javascript">

    //Google map start
    var geocoder;
    var map;
    var marker;
    function initMap() {
        if($('#map').length==0){
            return;
        }
        //地图初始化
        var coord_x = $('#coord_x').val() ? $('#coord_x').val() : '11.54461675917885';
        var coord_y = $('#coord_y').val() ? $('#coord_y').val() : '104.89746106250004';
        geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(coord_x, coord_y);
        var myOptions = {
            zoom: 14,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById("map"), myOptions);
        //引入marker
        marker = new google.maps.Marker({
            position: latlng,
            map: map,
            draggable:true,
            title:"Drag me!"
        });

        // 获取坐标
        google.maps.event.addListener(marker, "dragend", function () {
            $('#coord_x').val(marker.getPosition().lat());
            $('#coord_y').val(marker.getPosition().lng());
        });
    }

    //根据地址获取经纬度
    function codeAddress(address,zoom) {
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                map.setZoom(zoom);
                marker.setPosition(results[0].geometry.location);
                $('#coord_x').val(marker.getPosition().lat());
                $('#coord_y').val(marker.getPosition().lng());
            }
        });
    }
    //google map end
</script>

<script>
    var _address_region;
    $(function () {
        if(!'<?php echo $output['company_config']['address_id']?>'){
            getArea(0);
        }

        var ue = UE.getEditor('description',{
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

        $('#area_reset').click(function () {
            $('.area_reset').show();
        })

        $('#select_area').delegate('select', 'change', function () {
            var _value = $(this).val();
            $('input[name="address_id"]').val(_value);
            $(this).closest('div').nextAll().remove();
            _address_region = '';
            $('#select_area select').each(function () {
                if ($(this).val() != 0) {
                    _address_region += $(this).find('option:selected').text() + ' ';
                }
            })
            var _address = _address_region + ' ' + $('input[name="address_detail"]').val();
            codeAddress(_address, 14);
            if (_value != 0 && $(this).find('option[value="' + _value + '"]').attr('is-leaf') != 1) {
                getArea(_value);
            }
        })

        $('input[name="address_detail"]').change(function () {
            var _address = _address_region + ' ' + $('input[name="address_detail"]').val();
            codeAddress(_address, 14);
        })

        $('.save-info').click(function () {
            if (!$(".form-horizontal").valid()) {
                return;
            }

            var _address_region_1 = [];
            $('#select_area select').each(function () {
                if ($(this).val() != 0) {
                    _address_region_1.push($(this).find('option:selected').text());
                }
            })
            var _address_detail_1 = $.trim($('input[name="address_detail"]').val());
            if (_address_detail_1) {
                _address_region_1.push(_address_detail_1);
            }
            var _address = _address_region_1.reverse().join(', ');
            $('input[name="address_region"]').val(_address);

            $('.form-horizontal').submit();
        })
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.next());
        },
        rules: {
            email: {
                email: true
            }
        },
        messages: {
            email: {
                email: '<?php echo 'Please fill in the correct email!'?>'
            }
        }
    });

    function getArea(uid) {
        yo.dynamicTpl({
            tpl: "setting/area.list",
            dynamic: {
                api: "setting",
                method: "getAreaList",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $("#select_area").append(_tpl);
            }
        })
    }

    function add_hotline() {
        var hotline_tpl = $('#hotline_tpl').html();
        $('.hotline').last().after(hotline_tpl);
    }

    function remove_hotline(_e) {
       $(_e).closest('.hotline').remove();
    }
</script>
<!--图片上传 start-->
<?php require_once template(':widget/inc_upload_upyun');?>
<script type="text/javascript">
    webuploader2upyun('company_icon','company');
</script>
<!--图片上传 end-->