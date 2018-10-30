<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/jcrop/jquery.Jcrop.css" rel="stylesheet" type="text/css">
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/jcrop/jquery.Jcrop.js"></script>
<style>
    .part01-jscrop {
        min-height: auto;!important;
    }
</style>
<?php
$row = $output['user_info'];
$profile = my_json_decode($row['profile']);
$cords = $profile['cords'] ?: array();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Setting Avatar</h3>
        </div>
    </div>
    <div class="container">
        <form id="frm_icon">
            <input type="hidden" id="txt_cords_x1" name="cords_x1" value="<?php echo $cords['x']?:0?>" />
            <input type="hidden" id="txt_cords_y1" name="cords_y1" value="<?php echo $cords['y']?:0?>" />
            <input type="hidden" id="txt_cords_x2" name="cords_x2" value="<?php echo $cords['x2']?:120?>" />
            <input type="hidden" id="txt_cords_y2" name="cords_y2" value="<?php echo $cords['y2']?:120?>" />
            <input type="hidden" id="txt_cords_w" name="cords_w" value="<?php echo $cords['w']?:120?>" />
            <input type="hidden" id="txt_cords_h" name="cords_h" value="<?php echo $cords['h']?:120?>" />
            <input type="hidden" id="txt_source_img" name="src_img" value="<?php echo $row['user_image']?>">
        </form>
        <div id="zoom01" class="part01-jscrop">
            <div id="div_cutuserimg" style="margin-top: 15px;">
                <img style="max-width:400px;" id="img_user_img_jcrop" src="<?php echo getUserIcon($row['user_image'])?>"/>
            </div>
            <span id="preview_box_jcrop" class="crop_preview" style="left:500px;">
                <img id="img_user_icon_jcrop" src="<?php echo getUserIcon($row['user_image'])?>"/>
            </span>
        </div>
        <div style="margin-top: 20px;margin-left: 300px">
            <ul class="list-inline">
                <li><?php include(template("widget/inc_upload_handler"));?></li>
                <li>
                    <button class="btn btn-danger" onclick="btn_submit_avatar();">
                        <i class="fa fa-check"></i>
                        <?php echo 'Save Avatar'?>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var _cords='<?php echo json_encode($cords,true);?>';
        _cords=eval('('+_cords+')');
        if(_cords.w>0){
        }else{
            _cords={x:0,y:0,x2:120,y2:120}
        }
        $("#img_user_img_jcrop").Jcrop({
            onChange:showPreview,
            onSelect:showPreview,
            aspectRatio:1,
            setSelect:([_cords.x,_cords.y,_cords.x2,_cords.y2])
        });
        $("#frm_icon").data("is_change",false);
    });

    function after_upload_callback(_o){
        if(_o.STS){
            $("#div_cutuserimg").html('');
            var _str="<img style='max-width:400px; ' id='img_user_img_jcrop'/>";
            $("#div_cutuserimg").append($(_str));
            $("#img_user_img_jcrop").attr("src",_o.DATA.full_path_1);
            $("#img_user_icon_jcrop").attr("src",_o.DATA.full_path_1);
            $("#txt_source_img").val(_o.DATA.file_name);
            $("#frm_icon").data("is_change",1);
            setTimeout(function(){
                $("#img_user_img_jcrop").Jcrop({
                    onChange:showPreview,
                    onSelect:showPreview,
                    aspectRatio:1,
                    setSelect:([0,0,120,120])
                });
            },2000);
        }else{
            alert(_o.MSG);
        }
    }

    function showPreview(cords){
        if(parseInt(cords.w) > 0){
            $("#txt_cords_x1").val(cords.x);
            $("#txt_cords_y1").val(cords.y);
            $("#txt_cords_x2").val(cords.x2);
            $("#txt_cords_y2").val(cords.y2);
            $("#txt_cords_w").val(cords.w);
            $("#txt_cords_h").val(cords.h);
            $("#frm_icon").data("is_change",1);

            //计算预览区域图片缩放的比例，通过计算显示区域的宽度(与高度)与剪裁的宽度(与高度)之比得到
            var rx = $("#preview_box_jcrop").width() / cords.w;
            var ry = $("#preview_box_jcrop").height() / cords.h;
            //通过比例值控制图片的样式与显示
            $("#img_user_icon_jcrop").css({
                width:Math.round(rx * $("#img_user_img_jcrop").width()) + "px",	//预览图片宽度为计算比例值与原图片宽度的乘积
                height:Math.round(rx * $("#img_user_img_jcrop").height()) + "px",	//预览图片高度为计算比例值与原图片高度的乘积
                marginLeft:"-" + Math.round(rx * cords.x) + "px",
                marginTop:"-" + Math.round(ry * cords.y) + "px"
            });
        }
    }

    function btn_submit_avatar(){
        var _frm=$("#frm_icon");
        if(!_frm.data("is_change")){
            alert("No Changes");
            return false;
        }
        var _values=_frm.getValues();
        yo.loadData({
            _c:"user",
            _m:"updateUserIcon",
            param:_values,
            callback:function(_o){
                if(_o.STS){
                    _frm.data("is_change",false);
                    var icon_src = _o.DATA.icon;
                    $('#profile-messages .img-circle', window.parent.document).attr('src',icon_src);
                    setTimeout(function(){
                        window.location.href="<?php echo getUrl('user', 'myProfile', array(), false, ENTRY_COUNTER_SITE_URL)?>";
                    },500);
                }else{
                    alert(_o.MSG);
                }
            }
        });
    }
</script>
