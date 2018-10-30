<div id="yo_dialog">

</div>
<style>
    .weui-mask {
        position: fixed;
        z-index: 1000;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        margin: 0;
        padding: 0;
        display: block;
    }

    .weui-dialog {
        position: fixed;
        z-index: 5000;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        border-radius: 3px;
        overflow: hidden;
    }

</style>
<div class="js_dialog"  id="iosDialog" style="opacity: 1;margin: 0;padding: 0;display: none">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="text-align: left;">Notices</h4>
            </div>
            <div class="modal-body" style="min-width: 300px;min-height: 100px;font-size: 15px">
                Save Successful
            </div>
            <div class="modal-footer default-footer">
                <a class="btn btn-got-it" style="font-size: 15px;font-weight: bold;color: #09BB07;text-decoration: none" onclick="javascript:$('#iosDialog').hide();">Got It</a>
                <button type="button" class="btn btn-primary btn-confirm">Confirm</button>
                <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal" onclick="javascript:$('#iosDialog').hide();">Cancel</button>
            </div>
            <div class="modal-footer new-footer" style="display: none">

            </div>
        </div><!-- /.modal-content -->
    </div>
</div>
<script>
    if(!yo){
        yo={};
    }
    yo.confirm=function(_title,_desc,_fn){
        //后面要改写成用bootstrap的
        if(_title){
            $("#iosDialog").find(".modal-title").text(_title);
        }else{
            $("#iosDialog").find(".modal-title").text('Notice');
        }
        _desc='<i class="fa fa-question-circle" style="font-size: 28px;color: indigo;font-weight: bold"></i> '+_desc;
        $("#iosDialog").find(".modal-body").html(_desc);
        $("#iosDialog").find(".btn-got-it").hide();
        $("#iosDialog").find(".btn-cancel").show().on("click",function(){
            $("#iosDialog").hide();
            if($.isFunction(_fn)){
                _fn(false);
            }
        });
        $("#iosDialog").find(".btn-confirm").show().on("click",function(){
            $("#iosDialog").hide();
            if($.isFunction(_fn)){
                _fn(true);
            }
        });
        $("#iosDialog").find(".default-footer").show();
        $("#iosDialog").find(".new-footer").hide();

        $("#iosDialog").show();
    };
    function alert(_msg,_msg_type,_fn){

        if(_msg_type==1){
            _msg='<p><i style="font-size: 30px;font-weight: bold;color: #008000" class="fa fa-check-circle"></i></p><div>'+_msg+'</div>';
        }else if(_msg_type==2){
            _msg='<p><i style="font-size: 30px;font-weight: bold;color: red" class="fa fa-warning"></i></p><div>'+_msg+'</div>';
        }else{
            _msg='<p><i style="font-size: 30px;font-weight: bold;color: blue" class="fa fa-info-circle"></i></p><div>'+_msg+'</div>';
        }
        $("#iosDialog").find(".modal-title").text('Notice');
        $("#iosDialog").find(".modal-body").html(_msg);
        $("#iosDialog").find(".btn-got-it").show();
        $("#iosDialog").find(".btn-cancel").hide();
        $("#iosDialog").find(".btn-confirm").hide();

        $("#iosDialog").find(".default-footer").show();
        $("#iosDialog").find(".new-footer").hide();

        $("#iosDialog").find(".btn-got-it").unbind("click");
        if($.isFunction(_fn)){
            $("#iosDialog").find(".btn-got-it").bind("click",_fn);
        }
        $("#iosDialog").show();
    }
    $.messager={};
    $.messager.confirm=function(_title,_desc,_fn){
        yo.confirm(_title,_desc,_fn);
    };
    yo.dialog={};
    yo.dialog.div=function(){
        return $("#iosDialog");
    };
    yo.dialog.body=function(){
        return  yo.dialog.div().find(".modal-body");
    };
    yo.dialog.close=function(){
        var _dialog=$("#iosDialog");
        _dialog.find(".modal-body").html("");
        _dialog.hide();
    };
    yo.dialog.show=function(_args){
        var _dialog=$("#iosDialog");
        if(_args.title){
            _dialog.find(".modal-title").text(_args.title);
        }else{
            _dialog.find(".modal-title").text('Notice');
        }
        if(_args.content){
            _dialog.find(".modal-body").html(_args.content);
        }
        _dialog.find(".default-footer").hide();

        if(!_args.hideFooter){
            _dialog.find(".new-footer").show();
            if(_args.buttons){
                _dialog.find(".new-footer").html("");
                for(var _btn_id in _args.buttons){
                    var _btn=_args.buttons[_btn_id];
                    var _btn_cls='btn btn-default';
                    if(_btn.cls){
                        _btn_cls=_btn.cls;
                    }
                    var _btn_str='<button type="button" class="'+_btn_cls+'">'+_btn.text+'</button>';
                    var _btn_obj=$(_btn_str);
                    var _handler_fn=_btn.handler;
                    _dialog.find(".new-footer").append(_btn_obj);
                    _btn_obj.on('click',_btn.handler);
                }
            }
        }else{
            _dialog.find(".new-footer").hide();
        }
        _dialog.show();
    };


    yo.dialog.prompt=function(_title,_desc,_fn,_default_value){
        if(!_default_value) _default_value="";
        var _frm='<div><textarea class="txt_prompt form-control"  placeholder="'+_desc+'" style="width: 100%" cols="30" rows="3">'+_default_value+'</textarea></div>';
        _frm=$(_frm);
        yo.dialog.show({
            content:_frm,
            title:_title,
            buttons:[{text:"Submit",cls:"btn btn-primary",handler:function(){
                var _value=yo.dialog.body().find(".txt_prompt").val();
                yo.dialog.close();
                if(_fn){
                    _fn(_value);
                }
            }},{text:"Cancel",handler:function(){
                yo.dialog.close();
                if(_fn){
                    _fn(null);
                }
            }}]
        });
        yo.dialog.body().find(".txt_prompt").focus();
    };


</script>