<?php $info_uid = $info_uid?:0;?>
<?php $btn = $btn?:0;?>
<button type="button" class="btn btn-danger" id="btn_expire" onclick="expire(<?php echo $info_uid;?>,'<?php echo $btn;?>');"><i
        class="fa fa-vcard-o"></i>Set Expired
</button>
<script>
    function expire(info_uid, btn){
        $.messager.confirm("Set Certification To Expired", "Are you sure to set this certification to expired?", function (_r) {
            if (!_r) return;
            $("div.page").waiting();
            yo.loadData({
                _c: "client",
                _m: "setCertificationExpired",
                param: {uid: info_uid},
                callback: function (_obj) {
                    $("div.page").unmask();
                    if (!_obj.STS) {
                        alert(_obj.MSG);
                    } else {
                        if(btn != 0){
                            $(btn+' .current').click();
                        }else{
                            //window.history.back();
                            location.href = $("#anchor_back").attr("href");
                        }

                        //location.href = $("#anchor_back").attr("href");
                    }
                }
            })
        });
        //重新計算彈出框位置，以當前點擊的按鈕為準
        var style = $('.messager-window').attr('style'),  shadow_style = $('.window-shadow').attr('style');
        var top = ($( ".messager-window" ).offset().top - $('.messager-window').outerHeight()) + "px";
        style = style+'top: '+top+';';
        shadow_style = shadow_style+'top: '+top+';';
        $("div.messager-window").css("cssText", style);
        $("div.window-shadow").css("cssText", shadow_style);
    }
</script>
