// JavaScript Document
$.fn.extend({
    getValues: function () {
        var me;
        if (!(this instanceof jQuery)) me = $(this); else me = this;
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
        me.find(":checkbox").each(function () {
            var _name = $(this).attr("name");
            if (_name) {
                o[_name] = $(this).is(":checked") ? 1 : 0;
            }
        });
        return o;
    },
    setValues: function (_data) {
        var me;
        if (!(this instanceof jQuery)) me = $(this); else me = this;
        //todo:完善处理checkbox和radio的情况
        for (var _k in _data) {
            me.find("input[name=" + _k + "]").val(_data[_k]);
            me.find("select[name=" + _k + "]").val(_data[_k]);
            if (parseInt(_data[_k]) > 0) {
                me.find("input[type='checkbox'][name=" + _k + "]").prop("checked", true);
            }
        }
        return me;

    },
    uploadFiles: function (url, callback) {
        var me;
        if (!(this instanceof jQuery)) me = $(this); else me = this;
        var elements = [];
        me.find(":file").each(function () {
            elements.push($(this));
        })
        async.map(elements, function (element, callback) {
            $.ajaxFileUpload({
                url: url,            //需要链接到服务器地址
                secureuri: false,
                fileElementId: $(element).attr("id"),                        //文件选择框的id属性
                dataType: 'json',
                success: function (data, status) {
                    callback(null, data);
                }, error: function (data, status, e) {
                    callback(e)
                }
            });
        }, function (err, results) {
            if (err) {
                callback(err);
            } else if (results) {
                var o = {};
                for (var i = 0; i < results.length; i++) {
                    if (!results[i]) continue;
                    for (var j = 0; j < results[i].length; j++) {
                        o[results[i][j]['name']] = results[i][j]['file_name'];
                    }
                }
                callback(null, o);
            } else {
                callback(null, {});
            }
        });
    },
    waiting: function () {
        this.mask("<img src='resource/img/waiting.gif'/>");
    },
    loadData: function (_data) {

    }
});

(function () {
    $.extend($.fn, {
        mask: function (msg, maskDivClass) {
            this.unmask();
            // 参数
            var op = {
                opacity: 0.8,
                z: 10000,
                bgcolor: '#ccc'
            };
            var original = $(document.body);
            var position = {top: 0, left: 0};
            var is_body = true;
            if (this[0] && this[0] !== window.document) {
                is_body = false;
                original = this;
                position = original.position(); //original.offset();
            }
            // 创建一个 Mask 层，追加到对象中
            var maskDiv = $('<div class="maskdivgen" style="text-align: center"> </div>');
            if (is_body) {
                maskDiv.appendTo(original);
            } else {
                maskDiv.appendTo(original.parent());
            }

            var maskWidth = original.outerWidth();
            if (!maskWidth) {
                maskWidth = original.width();
            }
            var maskHeight = original.outerHeight();
            if (!maskHeight) {
                maskHeight = original.height();
            }
            var _margin_t = parseInt(original.css("marginTop"));
            var _margin_l = parseInt(original.css("marginLeft"));
            var _margin_b = parseInt(original.css("marginBottom"));
            var _margin_r = parseInt(original.css("marginRight"));

            //maskWidth=maskWidth+_margin_l+_margin_r;
            //maskHeight=maskHeight+_margin_t+_margin_b;

            maskDiv.css({
                position: 'absolute',
                top: position.top,
                left: position.left,
                'z-index': op.z,
                width: maskWidth,
                height: maskHeight,
                'margin-left': _margin_l,
                'margin-right': _margin_r,
                'margin-top': _margin_t,
                'margin-bottom': _margin_b,

                //'margin-top':original.offset().top,
                //'margin-left':original.offset().left,
                'background-color': op.bgcolor,
                opacity: 0
            });
            if (maskDivClass) {
                maskDiv.addClass(maskDivClass);
            }
            if (msg) {
                var msgDiv = $('<div style="position:absolute; padding:2px;"><div style="line-height:24px;padding:2px 10px 2px 10px">' + msg + '</div></div>');
                //var msgDiv=$('<div style="position:absolute; padding:2px;border:#6593cf 1px solid;background:#ccca"><div style="line-height:24px;border:#a3bad9 1px solid;background:white;padding:2px 10px 2px 10px">'+msg+'</div></div>');
                msgDiv.appendTo(maskDiv);
                var widthspace = (maskDiv.width() - msgDiv.width());
                var heightspace = (maskDiv.height() - msgDiv.height());
                msgDiv.css({
                    cursor: 'wait',
                    top: (heightspace / 2 - 2),
                    left: (widthspace / 2 - 2)
                });
            }

            maskDiv.fadeIn('fast', function () {
                // 淡入淡出效果
                $(this).fadeTo('slow', op.opacity);
            });

            return maskDiv;
        },
        unmask: function () {
            var original = $(document.body);
//            var is_body=true;
            if (this[0] && this[0] !== window.document) {
                original = $(this[0]).parent();
            }
            original.find("> div.maskdivgen").fadeOut('slow', 0, function () {
                $(this).remove();
            });
        }
    });
})();

Date.prototype.DateAdd = function (strInterval, Number) {
    var dtTmp = this;
    switch (strInterval) {
        case 's' :
            return new Date(Date.parse(dtTmp) + (1000 * Number));
        case 'n' :
            return new Date(Date.parse(dtTmp) + (60000 * Number));
        case 'h' :
            return new Date(Date.parse(dtTmp) + (3600000 * Number));
        case 'd' :
            return new Date(Date.parse(dtTmp) + (86400000 * Number));
        case 'w' :
            return new Date(Date.parse(dtTmp) + ((86400000 * 7) * Number));
        case 'q' :
            return new Date(dtTmp.getFullYear(), (dtTmp.getMonth()) + Number * 3, dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());
        case 'm' :
            return new Date(dtTmp.getFullYear(), (dtTmp.getMonth()) + Number, dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());
        case 'y' :
            return new Date((dtTmp.getFullYear() + Number), dtTmp.getMonth(), dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());
    }
};

Date.prototype.DateFormat = function (formatStr) {
    var str = formatStr;
    var Week = ['日', '一', '二', '三', '四', '五', '六'];

    str = str.replace(/yyyy|YYYY/, this.getFullYear());
    str = str.replace(/yy|YY/, (this.getYear() % 100) > 9 ? (this.getYear() % 100).toString() : '0' + (this.getYear() % 100));

    var _month = this.getMonth() + 1;

    str = str.replace(/MM/, _month > 9 ? _month.toString() : '0' + _month);
    str = str.replace(/M/g, _month);

    str = str.replace(/w|W/g, Week[this.getDay()]);

    str = str.replace(/dd|DD/, this.getDate() > 9 ? this.getDate().toString() : '0' + this.getDate());
    str = str.replace(/d|D/g, this.getDate());

    str = str.replace(/hh|HH/, this.getHours() > 9 ? this.getHours().toString() : '0' + this.getHours());
    str = str.replace(/h|H/g, this.getHours());
    str = str.replace(/mm/, this.getMinutes() > 9 ? this.getMinutes().toString() : '0' + this.getMinutes());
    str = str.replace(/m/g, this.getMinutes());

    str = str.replace(/ss|SS/, this.getSeconds() > 9 ? this.getSeconds().toString() : '0' + this.getSeconds());
    str = str.replace(/s|S/g, this.getSeconds());

    return str;
};
Date.prototype.DateDiff = function (strInterval, dtEnd) {
    var dtStart = this;
    if (typeof dtEnd == 'string')//如果是字符串转换为日期型
    {
        dtEnd = StringToDate(dtEnd);
    }
    switch (strInterval) {
        case 's' :
            return parseInt((dtEnd - dtStart) / 1000);
        case 'n' :
            return parseInt((dtEnd - dtStart) / 60000);
        case 'h' :
            return parseInt((dtEnd - dtStart) / 3600000);
        case 'd' :
            return parseInt((dtEnd - dtStart) / 86400000);
        case 'w' :
            return parseInt((dtEnd - dtStart) / (86400000 * 7));
        case 'm' :
            return (dtEnd.getMonth() + 1) + ((dtEnd.getFullYear() - dtStart.getFullYear()) * 12) - (dtStart.getMonth() + 1);
        case 'y' :
            return dtEnd.getFullYear() - dtStart.getFullYear();
    }
};


(function (jQuery) {

    if (jQuery.browser) return;

    jQuery.browser = {};
    jQuery.browser.mozilla = false;
    jQuery.browser.webkit = false;
    jQuery.browser.opera = false;
    jQuery.browser.msie = false;

    var nAgt = navigator.userAgent;
    jQuery.browser.name = navigator.appName;
    jQuery.browser.fullVersion = '' + parseFloat(navigator.appVersion);
    jQuery.browser.majorVersion = parseInt(navigator.appVersion, 10);
    var nameOffset, verOffset, ix;

// In Opera, the true version is after "Opera" or after "Version"
    if ((verOffset = nAgt.indexOf("Opera")) != -1) {
        jQuery.browser.opera = true;
        jQuery.browser.name = "Opera";
        jQuery.browser.fullVersion = nAgt.substring(verOffset + 6);
        if ((verOffset = nAgt.indexOf("Version")) != -1)
            jQuery.browser.fullVersion = nAgt.substring(verOffset + 8);
    }
// In MSIE, the true version is after "MSIE" in userAgent
    else if ((verOffset = nAgt.indexOf("MSIE")) != -1) {
        jQuery.browser.msie = true;
        jQuery.browser.name = "Microsoft Internet Explorer";
        jQuery.browser.fullVersion = nAgt.substring(verOffset + 5);
    }
// In Chrome, the true version is after "Chrome"
    else if ((verOffset = nAgt.indexOf("Chrome")) != -1) {
        jQuery.browser.webkit = true;
        jQuery.browser.name = "Chrome";
        jQuery.browser.fullVersion = nAgt.substring(verOffset + 7);
    }
// In Safari, the true version is after "Safari" or after "Version"
    else if ((verOffset = nAgt.indexOf("Safari")) != -1) {
        jQuery.browser.webkit = true;
        jQuery.browser.name = "Safari";
        jQuery.browser.fullVersion = nAgt.substring(verOffset + 7);
        if ((verOffset = nAgt.indexOf("Version")) != -1)
            jQuery.browser.fullVersion = nAgt.substring(verOffset + 8);
    }
// In Firefox, the true version is after "Firefox"
    else if ((verOffset = nAgt.indexOf("Firefox")) != -1) {
        jQuery.browser.mozilla = true;
        jQuery.browser.name = "Firefox";
        jQuery.browser.fullVersion = nAgt.substring(verOffset + 8);
    }
// In most other browsers, "name/version" is at the end of userAgent
    else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) <
        (verOffset = nAgt.lastIndexOf('/'))) {
        jQuery.browser.name = nAgt.substring(nameOffset, verOffset);
        jQuery.browser.fullVersion = nAgt.substring(verOffset + 1);
        if (jQuery.browser.name.toLowerCase() == jQuery.browser.name.toUpperCase()) {
            jQuery.browser.name = navigator.appName;
        }
    }
// trim the fullVersion string at semicolon/space if present
    if ((ix = jQuery.browser.fullVersion.indexOf(";")) != -1)
        jQuery.browser.fullVersion = jQuery.browser.fullVersion.substring(0, ix);
    if ((ix = jQuery.browser.fullVersion.indexOf(" ")) != -1)
        jQuery.browser.fullVersion = jQuery.browser.fullVersion.substring(0, ix);

    jQuery.browser.majorVersion = parseInt('' + jQuery.browser.fullVersion, 10);
    if (isNaN(jQuery.browser.majorVersion)) {
        jQuery.browser.fullVersion = '' + parseFloat(navigator.appVersion);
        jQuery.browser.majorVersion = parseInt(navigator.appVersion, 10);
    }
    jQuery.browser.version = jQuery.browser.majorVersion;
    jQuery.handleError = function (s, xhr, status, e) {

        // If a local callback was specified, fire it
        if (s.error)
            s.error(xhr, status, e);
        // If we have some XML response text (e.g. from an AJAX call) then log it in the console
        else if (xhr.responseText) {
            if (console) {
                console.log(xhr.responseText);
            }
        }
    };
})(jQuery);

