// JavaScript Document
/*yo的基础类*/
var yo = yo || {};
(function () {
    var global = this,
        objectPrototype = Object.prototype,
        toString = objectPrototype.toString,
        enumerables = ['valueOf', 'toLocaleString', 'toString', 'constructor'],
        emptyFn = function () {
        },
        privateFn = function () {
        },
        identityFn = function (o) {
            return o;
        },
        callOverrideParent = function () {
            var method = callOverrideParent.caller.caller; // skip callParent (our caller)
            return method.owner.prototype[method.name].apply(this, arguments);
        },
        manifest = yo.manifest || {},
        i,
        iterableRe = /\[object\s*(?:Array|Arguments|\w*Collection|\w*List|HTML\s+document\.all\s+class)\]/,
        MSDateRe = /^\\?\/Date\(([-+])?(\d+)(?:[+-]\d{4})?\)\\?\//;

    yo.global = global;
    emptyFn.nullFn = identityFn.nullFn = emptyFn.emptyFn = identityFn.identityFn =
        privateFn.nullFn = true;
    privateFn.privacy = 'framework';
    yo['suspendLayouts'] = yo['resumeLayouts'] = emptyFn;
    for (i in {toString: 1}) {
        enumerables = null;
    }
    yo.enumerables = enumerables;
    yo.apply = function (object, config) {//
        return yo.override(object, config);
    };
    yo.override = function (object, config) {
        if (!object) object = {};
        object = $.extend(object, config);
        return object;
    };
    yo.extend = function (object, config) {
        return $.extend({}, object, config);
    }
    yo.apply(yo, {
        clone: function (item) {//copy from yo.clone(),能实现object\array的克隆
            if (item === null || item === undefined) {
                return item;
            }
            if (item.nodeType && item.cloneNode) {
                return item.cloneNode(true);
            }
            var type = toString.call(item),
                i, j, k, clone, key;
            // Date
            if (type === '[object Date]') {
                return new Date(item.getTime());
            }
            // Array
            if (type === '[object Array]') {
                i = item.length;
                clone = [];
                while (i--) {
                    clone[i] = yo.clone(item[i]);
                }
            } else if (type === '[object Object]' && item.constructor === Object) {
                clone = {};
                for (key in item) {
                    clone[key] = yo.clone(item[key]);
                }
                if (enumerables) {
                    for (j = enumerables.length; j--;) {
                        k = enumerables[j];
                        if (item.hasOwnProperty(k)) {
                            clone[k] = item[k];
                        }
                    }
                }
            }
            return clone || item;
        },
        emptyString: new String(),
        now: (global.performance && global.performance.now) ? function () {
            return performance.now();
        } : (Date.now || (Date.now = function () {
            return +new Date();
        })),
        isEmpty: function (value, allowEmptyString) {
            return (value == null) || (!allowEmptyString ? value === '' : false) || (yo.isArray(value) && value.length === 0);
        },
        isArray: ('isArray' in Array) ? Array.isArray : function (value) {
            return toString.call(value) === '[object Array]';
        },
        isDate: function (value) {
            return toString.call(value) === '[object Date]';
        },
        isMSDate: function (value) {
            if (!yo.isString(value)) {
                return false;
            }
            return MSDateRe.test(value);
        },
        isObject: ((toString.call(null) === '[object Object]') ?
            function (value) {
                // check ownerDocument here as well to exclude DOM nodes
                return value !== null && value !== undefined && toString.call(value) === '[object Object]' && value.ownerDocument === undefined;
            } :
            function (value) {
                return toString.call(value) === '[object Object]';
            }),
        isFunction: // Safari 3.x and 4.x returns 'function' for typeof <NodeList>, hence we need to fall back to using
        // Object.prototype.toString (slower)
            (typeof document !== 'undefined' && typeof document.getElementsByTagName('body') === 'function') ? function (value) {
                return !!value && toString.call(value) === '[object Function]';
            } : function (value) {
                return !!value && typeof value === 'function';
            },
        isNumber: function (value) {
            return typeof value === 'number' && isFinite(value);
        },
        isNumeric: function (value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        },
        isString: function (value) {
            return typeof value === 'string';
        },
        isBoolean: function (value) {
            return typeof value === 'boolean';
        },
        sizeOf: function (obj) {
            if (yo.isEmpty(obj)) return 0;
            var _len = 0;
            if (yo.isArray(obj) || yo.isObject(obj)) {
                for (var _i in obj) {
                    _len += 1;
                }
            }
            return _len;
        }

    });


    yo.cloneFn = function (method) {
        return function () {
            return method.apply(this, arguments);
        };
    };
    Function.prototype.defineMethod = function (methodName, methodBody) {
        this.prototype[methodName] = methodBody;
        methodBody.$name = methodName;
        //this.$owner = this;
    }
    Function.prototype.defineStaticMethod = function (methodName, methodBody) {
        this[methodName] = methodBody;
        methodBody.$name = methodName;
    }
    yo.base = function () {

    }
    yo.base.$baseType = null;
    yo.base.prototype.super = function () {
        return this.$baseType.prototype;
    }
    yo.base.prototype.callParent = function () {
        var method = arguments.callee.caller;

        if (!method.$name) return;
        if (!this.$baseType) return;
        var _x = this.$baseType.prototype[method.$name];
        if (!_x) {
            _x = this.$baseType[method.$name];
        }
        if (_x) {
            return _x.apply(this, arguments)
        }
    }
    // similar as namespace
    yo.define = function (className, args) {//可以让className有namespace的感觉
        if (!className) throw Error("[yo.define] Invalid classname, must be a string and must not be empty");
        args = args || {};
        var _getNP_ = function (namespace) {
            var names = namespace.split("."), po = window; //也可以考虑以后放到yo里,也就是自动增加yo前缀
            for (var i = 0; i < names.length - 1; i++) {
                var o = names[i];
                if (!yo.isObject(o)) {
                    if (yo.isEmpty(po[o])) {
                        po[o] = {};
                    }
                    po = po[o];
                }
            }
            return {p: po, fn_name: names[names.length - 1]};
        }
        var _rt = _getNP_(className);
        var po = _rt.p;
        var _fn_name = _rt.fn_name;

        var _target;
        if (!args.extend) args.extend = "yo.base";
        if (args.override) {
            var _rt_override = _getNP_(args.override);
            var _o_override = _rt_override.p[_rt_override.fn_name];
            if (!yo.isFunction(_o_override)) {
                throw Error("Invalid Override-Class");
            }
            _target = _o_override;
            if (!_target) throw Error("Invalid Override-Class");
            delete args.override;
        } else if (args.extend) {//todo:要提供callParent的方法
            var _rt_extend = _getNP_(args.extend);
            var _o_extend = _rt_extend.p[_rt_extend.fn_name];
            if (!yo.isFunction(_o_extend)) {
                throw  Error("Invalid Extend-Class");
            }


            _target = po[_fn_name] = function () {
                if (this.initFn) {
                    this.initFn.apply(this, arguments);
                    //this.initFn(arguments);
                }
            };//yo.clone(_o_extend);
            //yo.base.call(_target.prototype);

            //_o_extend.call(_target);

            //_target=po[_fn_name]=yo.clone(_o_extend);
            //console.log(_target.prototype.toString());
            //_target=po[_fn_name]=$.extend(function(){},_o_extend);
            _target = $.extend(_target, _o_extend);//clone statics
            _target.prototype = $.extend(_target.prototype, _o_extend.prototype);

            //_o_extend.call(_target.prototype);
            _target.prototype.$baseType = _o_extend;//window[args.extend]//_o_extend;

            delete args.extend;
        } else if (args.mix) {
            //todo:继承多个类
        } else {
            _target = po[_fn_name] = function () {
                yo.base.call(this);
                if (this.initFn) {
                    this.initFn.apply(this, arguments);
                }
                this.$baseType = yo.base;
            };

            //_target.prototype.$baseType=yo.base;
        }
        _target.prototype.className = className;//备用
        var _statics = args.statics;
        if (_statics) {
            delete args.statics;
            for (var name in _statics) {
                var _member = _statics[name];
                if (yo.isFunction(_member)) {
                    //_target[name]=yo.cloneFn(_member);
                    _target.defineStaticMethod(name, _member);
                } else {
                    _target[name] = _member;
                }
            }
        }
        for (var name in args) {
            if (args.hasOwnProperty(name)) {
                var _member = args[name];
                if (yo.isFunction(_member)) {
                    //_target.prototype[name]=yo.cloneFn(_member);
                    _target.defineMethod(name, _member);
                } else {
                    _target.prototype[name] = _member;
                }
            }
        }

        return _target;
    }


}());

/**********yo的applicaiton级别应用,在其他应用平台我们要拆成两个文件**********/

yo.getUrl = function () {
    var _tmp_url = window.location.href;
    _tmp_url = _tmp_url.split("?")[0];
    _tmp_url = _tmp_url.split("#")[0];
    var _url = _tmp_url;
    return _url;
}
yo.log = function (_key, _value) {
    if (!_key && !_value) return;
    var _k, _v;
    if (!_value) {
        _v = _key;
        _k = "yo-log";
    } else {
        _k = _key;
        _v = _value;
    }
    if (window.console) {
        console.log(_k, _v);
    }
}

yo.loadData = function (_config) {
    var _url = _config.url || yo.getUrl();
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
    if (!_config.dataType) _config.dataType = "text json";
    var _data = $.toJSON(_args_data);

    if(_config.is_formData==true){
        var _data = _args_data.param;
    }else{
        var _data = $.toJSON(_args_data);
    }

    _url = _url + "?_c=" + _args.act + "&_m=" + _args.op + "&yoajax=1";
    var _ajax_default = {
        timeout: 10000,
        data: _data,
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            //异常处理应该设计成接口的，由app去实现，不是由core来实现
            //if (app) app.unmask();
            //if (yo.dialog) yo.dialog.unmask();

            if (_config.onError) {
                _config.onError(textStatus, errorThrown);
                return;
            }
            if (textStatus) {
                switch (textStatus) {
                    case "timeout":
                        throw Error(L.timeout);
                        break;
                    case "parsererror":
                        throw Error(L.err_unknown);
                        break;
                    default:
                        throw Error(L.err_unknown + ":" + textStatus);
                        break;
                }
            }
            if (errorThrown) {
                throw Error(errorThrown);
            }
        },
        success: function (obj, textStatus, XMLHttpRequest) {
            if (yo.isEmpty(obj)) {
                throw Error(L.err_empty);
            }
            yo.traceLog(obj.logger);
            if (obj.errmsg && !_config.ignoreErr) {
                //异常处理应该设计成接口的，由app去实现，不是由core来实现
                //if (app) app.unmask();
                //if (yo.dialog) yo.dialog.unmask();
                throw Error(obj.errmsg);
            }

            //if (obj.STS == false) {//错误及异常处理
            //    alert(obj.MSG ? obj.MSG : 'Exception error!');
            //}

            if (_config.callback && yo.isFunction(_config.callback)) {
                _config.callback(obj);
            }
        },
        processData: false,
        dataType: "text json",
        url: _url,
        type: "POST"
    }
    var _ajax = $.extend({}, _ajax_default, _config.ajax || {});
    $.ajax(_ajax);
}
yo.print_r = function (_o) {
    //if(!_o) return "";
    //if(!yo.isArray(_o)) return "";
    var _arr = [];
    for (var _k in _o) {
        _arr.push(_k + "=>" + _o[_k]);
    }
    return _arr.join("");
};
yo.traceLog = function (_trace) {
    if (app.debug_state != true) return;
    if (!_trace) return false;
    if (!window['trace']) window['trace'] = [];
    window['trace'].push(_trace);
    $("#debug").append("<p>--------------------------------------------------------------------------------------------</p>");
    for (var _x in _trace) {
        $("#debug").append("<p>" + _trace[_x] + "</p>");
    }

};
//这是动态加载模版，动态获取数据的方式
yo.dynamicTpl = function (_config) {
    if (!_config) _config = {};
    var _callback = _config.callback;
    if (!yo.isFunction(_callback)) return;//已经没必要去load了
    var _tpl = _config.tpl;
    var _control = _config.control ? _config.control : 'base';
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
        callback: function (_o) {
            _callback(_o);
        }
    });
};
//这是加载模版并且render 外部数据的方式
yo.loadTpl = function (_config) {
    if (!_config) _config = {};
    var _callback = _config.callback;
    if (!yo.isFunction(_callback)) return;//已经没必要去load了
    var _data = _config.data || {};
    var _tpl = _config.tpl;
    var _ext = _config.ext ? _config.ext : {};
    if (!_ext.data) _ext.data = _data;
    var _tpl_arr = _tpl.split("/");
    if (_tpl_arr.length == 2) {
        _ext.tpl_dir = _tpl_arr[0];
        _tpl = _tpl_arr[1];
    }
    var _param = {tpl: _tpl};
    if (_ext) _param = $.extend(_param, _ext);
    var _tpl_name = "";
    if (yo.isString(_tpl)) {
        _tpl_name = _tpl;
    } else {
        _callback(yo.render(_tpl, _data, ""));
    }
    yo.loadData({
        _c: "control",
        _m: "getTpl",
        param: _param,
        ajax: {dataType: "html"},
        callback: function (_o) {
            if (_o.errmsg) throw Error(_o.errmsg);
            // window['tpl_cache'][_tpl_name]=_o;
            return _callback(_o);
            //_callback(yo.render(_o,_data,_tpl_name));
        }
    });
    //todo:1.也许要考虑特殊路径的调用,2.权限的统一过滤
    //判断是否有缓存,弃用
    /*
     *
     *
     *
     window['tpl_cache']=window['tpl_cache'] || {};
     var _cache=window['tpl_cache'][_tpl_name];
     if(app.debug_state!=true && _cache){
     yo.log("from cache");
     _callback(yo.render(_cache,_data,_tpl_name));
     }else{
     yo.loadData({
     _c:"apiMain",
     _m:"getTpl",
     param:_param,
     ajax:{dataType:"html"},
     callback:function(_o){
     if(_o.errmsg) throw Error(_o.errmsg);
     window['tpl_cache'][_tpl_name]=_o;
     return _callback(_o);
     //_callback(yo.render(_o,_data,_tpl_name));
     }
     });

     }
     */
}//end loadTpl
yo.render = function (tpl_s, json_obj, name_tpl) {
    if (yo.sizeOf(json_obj) == 0) return tpl_s;

    var _micro_templates_ = window['_micro_templates_'];
    if (!_micro_templates_) {
        _micro_templates_ = window['_micro_templates_'] = {};
    }
    //临时变量，用于compile出现问题时来得到临时返回...
    var _micro_templates_s_ = window['_micro_templates_s_'];
    if (!_micro_templates_s_) {
        _micro_templates_s_ = window['_micro_templates_s_'] = {};
    }

    var _func_tmp = function () {
        return arguments[0].replace(/'|\\/g, "\\$&").replace(/\n/g, "\\n");
    };//这个函数返回一个可以把单引号或者反斜杠全换成\$&，以及把真回车换成字符串\n

    if (!_micro_templates_[name_tpl] || app.debug_state != true) {
        var tpl = tpl_s;
        tpl = tpl
            .replace(/&lt;%/g, "<%")//因为有时把html拿出来的时候是会做了这样的转换
            .replace(/%&gt;/g, "%>")
            .replace(/\r|\*+="/g, ' ')//把换行或者连续的空格变成单一空格..
            .split('<%').join("\r") //把左注释换成换行
            .replace(/(?:^|%>)[^\r]*/g, _func_tmp) //这一个暂时还不是很明白，似乎是把 %>之后的空行给处理一下??
            .replace(/\r=(.*?)%>/g, "',$1,'")
            .split("\r").join("');");//join回来..

        tpl = tpl.split('%>').join("\n" + "_write.push('");
        _micro_templates_s_[name_tpl] = tpl;
        var obj_name = "mgrender_arg_obj";
        var _s = "";
        try {
            _s = "try{";
            _s += "var _write=[];with(" + obj_name + "){" + "\n" + "_write.push('" + tpl + "');};return _write.join('');";
            _s += "}catch(ex){try{yo.log('err in tpl " + name_tpl + "');yo.log(window['_micro_templates_s_']['" + name_tpl + "']);yo.log(''+ex);}catch(e){alert(e);}}";
            var nf = new Function(obj_name, _s);
        } catch (ex) {
            try {
                yo.log("tpl error");
                yo.log(_micro_templates_s_[name_tpl]);
            } catch (e) {
            }
            throw ex;
        }
        window['_micro_templates_'][name_tpl] = _micro_templates_[name_tpl] = nf;
    }

    var _nf = _micro_templates_[name_tpl];
    if (typeof(_nf) == "function") {
        var _rt = _nf(json_obj);
        _rt = $(_rt);
        if (yo.isIE) {

            //$.bootstrapIE6(_rt); //处理IE6的风格
        }
        return _rt;
    } else {
        throw Error("" + name_tpl + "tpl not found");
    }

    //$.bootstrapIE6($("#"+_div_id)); 处理IE6的风格

    //如果玩的是debug模式，把编译好的模板缓存给清掉...
    if (app.debug_state != true) {
        _micro_templates_[name_tpl] = null;//no cache for debug mode
    }
}
/**********************语言*******************************/
yo.lang = yo.lang || {};
yo.lang.Default = "en";
yo.lang.config = {en: 0, zh_cn: 1};
yo.define("L", {});

yo.lang.data = {
    login: ["login", "登陆了哈"],
    err_timeout: ['Oper Timeout', "操作超时"],
    err_noargs: ['params is not definded', '参数配置错误'],
    err_unknown: ['Unknown Eror', '未知错误'],
    err_parse: ['parser Error', '解析错误'],
    err_empty: ['return is Empty', '返回空数据'],
    noFound: ['not found', '没找到'],


    user_name: ['User-Name', "用户名"],
    login: ['Login', '登录']
}
yo.launch = function () {
//格式化语言包
    var _l = yo.lang.Default || "en";
    _l = yo.lang.config[_l];
    if (!_l) _l = 0;
    if (!yo.lang.data) yo.lang.data = {};
    for (var _x in yo.lang.data) {
        L[_x] = yo.lang.data[_x][_l];
    }

}
yo.launch();

function showMask(){
    $(document).waiting();
}
function hideMask(){
    $(document).unmask();
}


