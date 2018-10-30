/**
 * Created by sahara on 2016/8/20.
 */
function changeLang(lang, refresh_type) {
    var _domain = document.domain;
    jQuery.cookie((COOKIE_PRE || '') + 'lang', lang, {path: '/', domain: _domain, expires: 3652.1});
    var href = location.href.substr(0, location.href.length - location.hash.length);
    if (!refresh_type)
        location.href = href;
    if (refresh_type == 2) {
        if (href.indexOf('refresh=') < 0)
            href += (href.indexOf('?') > 0 ? '&refresh=1' : '?refresh=1')
        location.href = href;
    }
}

function browserRedirect() {
    var sUserAgent = navigator.userAgent.toLowerCase();
    var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
    var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
    var bIsMidp = sUserAgent.match(/midp/i) == "midp";
    var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
    var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";
    var bIsAndroid = sUserAgent.match(/android/i) == "android";
    var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";
    var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";
    if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {
        return 1;
    } else {
        return 0;
    }
}

$(document).ready(function () {
    $("input[type=number]").on("keydown", function (e) {
        if (e.keyCode == 69) return false;
        if (e.keyCode == 189) return false;
        if (e.keyCode == 187) return false;
        if (e.keyCode == 107) return false;
        if (e.keyCode == 109) return false;

    });
});

function message(title, msg, type, okFn, cancelFn) {
    switch (type) {
        case 'loading':
            _modalLoading(title, msg);
            break;
        case 'prompt':
            _modalPrompt(title, msg);
            break;
        case 'confirm':
            _modalConfirm(title, msg, okFn, cancelFn);
            break;
        case 'succ':
            _modalSuccess(title, msg, okFn,cancelFn);
            break;
        case 'error':
            _modalError(title, msg,okFn,cancelFn);
            break;
        case 'close':
            _modalClose();
            break;
        case 'closeAll':
            _modalCloseAll();
            break;
        default:
            _modalPrompt('Handle fail', '')
    }
}

function _modalLoading(title, msg) {
    zeroModal.loading(4);
}

function _modalPrompt(title, msg) {
    zeroModal.alert({
        content: title,
        contentDetail: msg,
        esc: true,
        top: 100,
        overlayClose: true,
        buttons: [{
            className: 'zeromodal-btn zeromodal-btn-primary',
            name: 'Ok',
            fn: function (opt) {
            }
        }]
    });
}

function _modalConfirm(title, msg, okFn, cancelFn) {
    var _s=document.body.scrollTop||document.documentElement.scrollTop;
    var _h=document.documentElement.offsetTop;
    var scrollTop=_s-_h;
    if(scrollTop<0) scrollTop=0;

    zeroModal.confirm({
        content: title,
        contentDetail: msg,
        esc: true,
        top: 0,
        buttons: [{
            className: 'zeromodal-btn zeromodal-btn-primary',
            name: 'Ok',
            fn: function (opt) {
                if (typeof okFn === 'function') {
                    okFn();
                }
            }
        }, {
            className: 'zeromodal-btn zeromodal-btn-default',
            name: 'Cancel',
            fn: function (opt) {
                if (typeof cancelFn === 'function') {
                    cancelFn();
                }
            }
        }]
    });
}

function _modalSuccess(title, msg, okFn) {
    zeroModal.success({
        content: title,
        contentDetail: msg,
        esc: true,
        top: 100,
        overlayClose: true,
        buttons: [{
            className: 'zeromodal-btn zeromodal-btn-primary',
            name: 'Ok',
            fn: function (opt) {
                if (typeof okFn === 'function') {
                    okFn();
                }
            }
        }]
    });
}

function _modalError(title, msg) {
    zeroModal.error({
        content: title,
        contentDetail: msg,
        esc: true,
        top: 100,
        overlayClose: true,
        buttons: [{
            className: 'zeromodal-btn zeromodal-btn-primary',
            name: 'Ok',
            fn: function (opt) {
            }
        }]
    });
}

function _modalClose() {
    zeroModal.close();
}

function _modalCloseAll() {
    zeroModal.closeAll();
}

var complexSelect = complexSelect || {};
complexSelect.create = function (_conf) {//
    var _title = _conf._title ? _conf._title : 'Title';
    var _width = _conf._width ? _conf._width : '800px';
    var _height = _conf._height ? _conf._height : '400px';

    if (_conf._url) {
        var _url = _conf._url;
    } else {
        alert('Url Required!');
        return;
    }

    complexSelect.callback = _conf._callback;

    $('#complexSelectModal').remove();
    var _html = '';
    _html += '<div class="modal" id="complexSelectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
    _html += '<style>#complexSelectModal .modal-dialog {margin-top: 10px!important;}</style>';
    _html += '<div class="modal-dialog" role="document" style="width: ' + _width + '">';
    _html += '<div class="modal-content">';
    _html += '<div class="modal-header">';
    _html += '<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="complexSelect.colse()"><span aria-hidden="true">&times;</span></button>';
    _html += '<h4 class="modal-title" id="myModalLabel">' + _title + '</h4></div>';
    _html += '<div class="modal-body">';
    _html += '<iframe id="frameStage" style="border: 0" src="' + _url + '" name="frameStage" width="100%" height="' + _height + '"></iframe>';
    _html += '</div><div class="modal-footer">';
    _html += '<button type="button" class="btn btn-default" data-dismiss="modal" onclick="complexSelect.colse()">Cancel</button>';
    _html += '<button type="button" class="btn btn-danger" onclick="complexSelect.confirm()">Confirm</button>';
    _html += '</div></div></div></div>';
    $('body').append(_html);
    $('#complexSelectModal').modal('show');
}
complexSelect.confirm = function () {
    var id, ext;
    $('#complexSelectModal #frameStage').contents().find('input[data-key="selector"]:checked').each(function (i) {
        var _id = $(this).data('value');
        var _ext = $(this).data('ext');
        if (i === 0) {
            id = _id;
            ext = _ext;
        } else {
            id += ',' + _id;
            ext += ',' + _ext;
        }
    });
    complexSelect.callback(id, ext);
    complexSelect.colse();
}
complexSelect.colse = function () {
    $('#complexSelectModal').next('.modal-backdrop').remove();
    $('#complexSelectModal').remove();
}

function validform(opt) {
    var i = 0, params = opt.params, len = params.length, rules = {}, messages = {}, str = JSON.stringify(params);
    $(opt.ele + ' input[name=validate]').val(str);
    for (i; i < len; i++) {
        var val = params[i], rule = val.rules, message = val.messages, temp = new Array();
        if (rule.regexp) {
            temp['reg'] = true;
            temp['regexp'] = rule['regexp'];
            temp['regexpFun'] = rule['regexpFun'];
            delete rule['regexpFun']
            delete rule['regexp'];
        }
        rules[val.field] = rule;

        if (message.regexp) {
            temp['regexpMsg'] = message['regexp'];
            delete message['regexp'];
        }
        messages[val.field] = message;
        if (temp['reg']) {
            var input = '<input type="hidden" name="' + val.field + 'Regexp" value="' + temp['regexp'] + '" />';
            $(opt.ele).append(input);
            addValidMethod(temp);
        }
    }
    $(opt.ele).validate({
        errorPlacement: function (error, element) {
            element.nextAll('.validate-checktip').first().html(error)
        },
        rules: rules,
        messages: messages
    });
}
function addValidMethod(exp) {
    var checkFun = exp['regexpFun'], checkReg = exp['regexp'], checkMsg = exp['regexpMsg'];
    $.validator.addMethod(checkFun, function (value, element, params) {
        return this.optional(element) || (checkReg.test(value));
    }, checkMsg);
}

function formatCurrency(num) {
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num))
        num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num * 100 + 0.50000000001);
    cents = num % 100;
    num = Math.floor(num / 100).toString();
    if (cents < 10)
        cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
        num = num.substring(0, num.length - (4 * i + 3)) + ',' +
        num.substring(num.length - (4 * i + 3));
    return (((sign) ? '' : '-') + num + '.' + cents);
}

function getFormJson(frm) {
    var o = {};
    var a = $(frm).serializeArray();
    $.each(a, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}

//图片上传
function upload2upyun(upload_id,default_dir){
    setTimeout(function () {
        $('#'+upload_id).uploadify({
            'formData': {},
            'auto': false,
            'fileObjName': 'file',
            'swf': swf_url,
            'uploader': upload_url,
            'width': 80,//指定按钮的宽度
            'height': 30,//指定按钮的宽度
            'buttonText': button_text,  //按钮上的文本
            'debug': false,   //是否调试
            'removeTimeout': 1,
            'multi'    : false, //该插件是否支持多文件上传
            'fileTypeExts' : '*.gif; *.jpg;*.jpe; *.png; *.jpeg', //指定文件上传的类型
            'fileSizeLimit' : '2MB',  //限制上传的大小
            'onSelect': function (file) {
                $('.uploadify-queue').remove();
                $.get(site_url,{default_dir:default_dir}, function (response) {
                    var obj = $.parseJSON(response);
                    var up = $('#'+upload_id);
                    var formData = up.uploadify('settings', 'formData');
                    formData = {policy: obj.policy, signature: obj.signature};
                    up.uploadify('settings', 'formData', formData);
                    up.uploadify('upload', '*');
                });
            },
            'onUploadSuccess': function (file, data, response) {
                var dataObj = $.parseJSON(data);
                if (dataObj.code != 200) {
                    alert('Upload success!');
                } else {
                    var img_name= dataObj.url.split('/').pop();
                    $('input[name='+upload_id+']').val(img_name);
                    $('#show_'+upload_id).attr('src',upyun_url+dataObj.url);
                }
            }
        });
    }, 50);
}

//验证金钱格式
function checkMoney(money){
    var reg = /(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/;
    if (reg.test(money)) {
      return true;
    }else{
      return false;
    }
  }


// JS 浮点数处理对象类
var  NumberObj = NumberObj || {};
NumberObj.round = function(num,digits){
    return Number(Number(num).toFixed(digits));
};
NumberObj.isInteger = function (number){
    return Math.floor(number) === number;
};
/**
 * 将浮点数变成整数
 * @param floatNum
 * @returns {{times: number, num: number, digits: number}}
 * times 放大倍数  num 返回的整数  digits 原始数据的小数位数
 */
NumberObj.toInteger = function (floatNum){
    floatNum = Number(floatNum);
    if( isNaN(floatNum) ){
        floatNum = 0;
    }
    var ret = {times:1,num:0,digits:0};
    if( NumberObj.isInteger(floatNum) ){
        ret.num = floatNum;
        return ret;
    }
    var floatStr = floatNum+'';
    var dotPos = floatStr.indexOf('.');
    var length = floatStr.substr(dotPos+1).length;
    var times = Math.pow(10,length);
    var intNum = Number(floatNum.toString().replace('.',''));
    ret.times = times;
    ret.num = intNum;
    ret.digits = length;
    return ret;
};
NumberObj.operation = function operation(a, b, op,digits){
    var o1 = NumberObj.toInteger(a);
    var o2 = NumberObj.toInteger(b);
    var n1 = o1.num;
    var n2 = o2.num;
    var t1 = o1.times;
    var t2 = o2.times;
    var max = t1 > t2 ? t1 : t2;
    var result = null;
    switch (op) {
        case '+':
            if (t1 === t2) { // 两个小数位数相同
                result = n1 + n2;
            } else if (t1 > t2) { // o1 小数位 大于 o2
                result = n1 + n2 * (t1 / t2);
            } else { // o1 小数位 小于 o2
                result = n1 * (t2 / t1) + n2;
            }
            result =  result / max;
            break;
        case '-':
            if (t1 === t2) {
                result = n1 - n2;
            } else if (t1 > t2) {
                result = n1 - n2 * (t1 / t2);
            } else {
                result = n1 * (t2 / t1) - n2;
            }
            result = result / max;
            break;
        case '*':
            result = (n1 * n2) / (t1 * t2);
            break;
        case '/':
            result = (n1 / n2) * (t2 / t1);
            break;
    }

    if( digits === undefined ){
        return result;
    }else{
        return Number(Number(result).toFixed(digits));
    }

};

NumberObj.add = function(a,b,digits){
    return NumberObj.operation(a,b,'+',digits);
};

NumberObj.minus = function(a,b,digits){
    return NumberObj.operation(a,b,'-',digits);
};

NumberObj.multiply = function(a,b,digits){
    return NumberObj.operation(a,b,'*',digits);
};

NumberObj.divide = function(a,b,digits){
    return NumberObj.operation(a,b,'/',digits);
};

