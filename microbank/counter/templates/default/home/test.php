<div style="padding: 20px">
    <h1>Test Extra Func</h1>
    <div class="container">
        <ul class="list-group">
            <li class="list-group-item list-group-item-info">
                <button id="btn_set_fullscreen" onclick="callWin_set_fullscreen()">启动全屏</button>
            </li>
            <li class="list-group-item list-group-item-info">
                <button id="btn_unset_fullscrren" onclick="callWin_unset_fullscreen()">退出全屏</button>
            </li>
            <li class="list-group-item list-group-item-info">
                <button onclick="callWin_lock_screen();">锁定屏幕</button>
            </li>
            <li class="list-group-item list-group-item-info">
                <button onclick="callWin_registerFinger();">注册指纹</button>
                <img id="img_finger" style="width: 200px;height: 200px;border: solid 1px #000000">
            </li>
            <li class="list-group-item list-group-item-info">
                <button onclick="callWin_verifyFinger();">验证指纹</button>
            </li>
            <li class="list-group-item list-group-item-info">
                <button onclick="callWin_snapshot_master();">高拍仪主摄像头拍照（扫描）</button>
                <img id="img_master" style="width: 200px;height: 200px;border: solid 1px #000000">
            </li>
            <li class="list-group-item list-group-item-info">
                <button onclick="callWin_snapshot_slave();">高拍仪辅助摄像头拍照</button>
                <img id="img_slave" style="width: 200px;height: 200px;border: solid 1px #000000;">
            </li>
        </ul>

    </div>
</div>
<script>
    $(document).ready(function(){
        alert("test1");

    });
    function callWin_set_fullscreen(){
        if(window.external){
            try{
                window.external.setFullScreen();
            }catch (ex){
                alert(ex.Message);

            }
        }
    }
    function callWin_unset_fullscreen(){
        if(window.external){
            try{
                window.external.unsetFullScreen();
            }catch (ex){

            }
        }
    }
    function callWin_lock_screen(){
        if(window.external){
            try{
                window.external.lockScreen();
            }catch (ex){
                alert(ex.Message);

            }
        }
    }
    /// <summary>
    /// 注册指纹
    /// </summary>
    /// <param name="member_id">传入的member_id,用户标识id</param>
    /// <param name="finger_index">0表示右手，1表示左手</param>
    /// <returns>返回的是已经上传到upyun的指纹图片</returns>
    function callWin_registerFinger(){
        if(window.external){
            try{
               var _img_path= window.external.registerFingerPrint("10000001","0");
                if(_img_path!="" && _img_path!=null){
                    $("#img_finger").attr("src",getUPyunImgUrl(_img_path));
                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }
    }
    /// <summary>
    /// 验证某个用户的指纹是否有效。
    /// </summary>
    /// <param name="member_id">用户的id标识</param>
    /// <returns>0表示无效，1表示有效</returns>
    function callWin_verifyFinger(){
        if(window.external){
            try{
                var _ret= window.external.verifyFingerPrint("10000001");
                if(_ret=="1"){
                    alert("verify success!");
                }else{
                    alert("verify failed!");
                }

            }catch (ex){
                alert(ex.Message);

            }
        }
    }
    /// <summary>
    /// 获取高拍仪截图
    /// </summary>
    /// <param name="flag_master">0表示主设备（扫描），1表示辅助摄像头</param>
    /// <returns>返回已经保存到upyun的图片路径</returns>
    function callWin_snapshot_master(){
        if(window.external){
            try{
                var _img_path= window.external.getSnapshot("0");
                if(_img_path!="" && _img_path!=null){
                    $("#img_master").attr("src",getUPyunImgUrl(_img_path));
                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }
    }
    /// <summary>
    /// 获取高拍仪截图
    /// </summary>
    /// <param name="flag_master">0表示主设备（扫描），1表示辅助摄像头</param>
    /// <returns>返回已经保存到upyun的图片路径</returns>
    function callWin_snapshot_slave(){
        if(window.external){
            try{
                var _img_path= window.external.getSnapshot("1");
                if(_img_path!="" && _img_path!=null){
                    $("#img_slave").attr("src",getUPyunImgUrl(_img_path));
                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }
    }
    function getUPyunImgUrl(_img_path){
        return "http://bank-demo.test.upcdn.net/"+_img_path;
    }


</script>