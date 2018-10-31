<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $output['html_title'];?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
  <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/aui/aui.2.0.css?v=2">
  <link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/init.css?v=1">
  <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/zepto.min.js"></script>
  <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/zepto.cookie.min.js"></script>
  <script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/aui/aui-toast.js"></script>
  <script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/fastclick.js"></script>
  <script type="text/javascript">
  var toast = new auiToast();
  function verifyFail(msg){
    toast.fail({
      title: msg,
      duration: 2000
    });
  }
  function hint(_msg){
      toast.success({
          title:_msg,
          duration:2000
      });
  }
  function showMask(){
      toast.loading({
          title:"Please Waiting..",
          duration:1
      });
  }
  function hideMask(){
      toast.hide();
  }
  function reLogin(){
    toast.fail({
      title: 'Please Relogin',
      duration: 2000
    });
    setTimeout(function(){
      if(window.operator){
        window.operator.reLogin();
      }else{
        window.location.href = "<?php echo getUrl('login', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>";
      }
    },2000);
  }
  var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';var COOKIE_DOMAIN="<?php echo SUBDOMAIN_SUFFIX;?>";
  var CURRENT_LANGUAGE_CODE = "<?php echo Language::currentCode(); ?>";
  </script>
    <script>
        //add by tim
        $.fn.getValues=function(){
            var me;
            me = $(this);
            /*
             if(jQuery.type(this)==="form"){
             me=this;
             }else{
             me=this.find("form").first();
             }*/
            if (me.length == 0) return [];
            var _rt = me.serializeArray() || [];
            var o = {};
            if (_rt) {
                for (var _k in _rt) {
                    var _name = _rt[_k].name;
                    var _value = _rt[_k].value;
                    if (_name.indexOf("[]") >= 0) {
                        if (!o[_name.replace("[]", "")]) {
                            o[_name.replace("[]", "")] = [];
                        }
                        o[_name.replace("[]", "")].push(_value);
                    } else {
                        if (o[_name] !== undefined) {
                            if (!o[_name].push) {
                                o[_name] = [o[_name]];
                            }
                            o[_name].push(_value);
                        } else {
                            o[_name] = _value;
                        }
                    }
                }
            }
            me.find("input[type=checkbox]").each(function () {
                var _name = $(this).attr("name");
                if (_name) {
                    o[_name] = $(this).is(":checked") ? 1 : 0;
                }
            });
            return o;
        };
        var yo={};
        yo.loadData = function (_config) {
            var _url = '<?php echo WAP_OPERATOR_SITE_URL."/index.php";?>';
            var _args = {};
            var _args_data = {};
            if (_config._c) _args["act"] = _config._c;
            if (_config._m) _args.op = _config._m;
            if (_config._p) _args_data.param = _config._p;
            if (_config.cls) _args["act"] = _config.cls;
            if (_config.method) _args.op = _config.method;
            if (_config.param) _args_data.param = _config.param;
            if (_config.args) _args_data.param = _config.args;
            if (_config.act) _args.act = _config.act;
            if (_config.op) _args.op = _config.op;

            if (!_args.op) throw  Error(L.err_noargs + ":Method");
            if (!_config.dataType) _config.dataType = "json";


            //var _data = $.toJSON(_args_data);
            _url = _url + "?_c=" + _args.act + "&_m=" + _args.op
            var _ajax_default = {
                timeout: 10000,
                data: JSON.stringify(_args_data),
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    hideMask();
                    if (_config.onError) {
                        _config.onError(textStatus, errorThrown);
                        return;
                    }
                    if (textStatus) {
                        alert(errorThrown);return;
                    }
                    if (errorThrown) {
                        alert(errorThrown);return;
                    }
                    alert(textStatus);
                },
                success: function (obj, textStatus, XMLHttpRequest) {
                    if(obj.STS){

                    }else{
                        if(obj.CODE == '<?php echo errorCodesEnum::INVALID_TOKEN;?>' || obj.CODE == '<?php echo errorCodesEnum::NO_LOGIN;?>'){
                            reLogin();
                        }
                    }
                    if (_config.callback) {
                        _config.callback(obj);
                    }
                },
                dataType: _config.dataType,
                url: _url,
                type: "post"
            };
            $.ajax(_ajax_default);
        };
        //这是动态加载模版，动态获取数据的方式
        yo.dynamicTpl = function (_config) {
            if (!_config) _config = {};
            var _callback = _config.callback;
            if (!_callback) return;//已经没必要去load了
            var _tpl = _config.tpl;
            var _control = _config.control ? _config.control : 'wap_operator_base';
            var _ext = _config.ext ? _config.ext : {};
            var _tpl_arr = _tpl.split("/");
            if (_tpl_arr.length == 2) {
                _ext.tpl_dir = _tpl_arr[0];
                _tpl = _tpl_arr[1];
            }
            if (_config.dynamic) {
                _ext.dynamic = _config.dynamic;
            }

            var _param = {tpl: _tpl};
            if (_ext) _param = $.extend(_param, _ext);
            var _ajax = $.extend({}, _config.ajax || {}, {dataType: "html"});

            yo.loadData({
                _c: _control,
                _m: "getTpl",
                param: _param,
                ajax: _ajax,
                dataType:"html",
                callback: function (_o) {
                    _callback(_o);
                }
            });
        };
        function formatAmount(num){
            return (num.toFixed(2) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
        }
        function formatAmountNoFix(num){
            return (num + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
        }
    </script>
</head>
<body>
  <?php
  require_once($tpl_file);
  ?>
</body>
</html>
