if (!$lopts) $lopts = window.lopts || {};
if (!$lopts.statIdName || !$lopts.url) throw new Error("Please set 'lopts' before include this script");
window.Logger = new (function(){
    /**
     * vlstat 浏览器统计脚本
     */
    var statIdName = $lopts.statIdName;
    var xmlHttp;
    /**
     * 设置cookieId
     */
    function setCookie(c_name, value, expiredays) {
        var exdate = new Date();
        exdate.setDate(exdate.getDate() + expiredays);
        document.cookie = c_name + "=" + encodeURIComponent(value) + ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString()) + ";path=/;domain=" + location.host;
    }
    /**
     * 获取cookieId
     */
    function getCookie(c_name) {
        if (document.cookie.length > 0) {
            var c_start = document.cookie.indexOf(c_name + "=");
            if (c_start != -1) {
                c_start = c_start + c_name.length + 1;
                var c_end = document.cookie.indexOf(";", c_start);
                if (c_end == -1) {
                    c_end = document.cookie.length;
                }
                return decodeURIComponent(document.cookie.substring(c_start, c_end));
            }
        }
        return "";
    }
    /**
     * 获取当前时间戳
     */
    function getTimestamp() {
        var timestamp = (new Date()).getTime();
        return timestamp;
    }
    /**
     * 生成statId
     */
    function genStatId() {
        var cookieId = getTimestamp().toString() + "-" + Math.round(Math.random() * 3000000000);
        return cookieId;
    }
    /**
     * 设置StatId
     */
    function setStatId() {
        var cookieId = genStatId();
        setCookie(statIdName, cookieId, 365);
    }
    /**
     * 获取StatId
     */
    function getStatId() {
        var statId = getCookie(statIdName);
        if (statId != null && statId.length > 0) {
            return statId;
        } else {
            setStatId();
            return getStatId();
        }
    }
    /**
     * 获取UA
     */
    function getUA() {
        var ua = navigator.userAgent;
        if (ua.length > 250) {
            ua = ua.substring(0, 250);
        }
        return ua;
    }
    /**
     * 获取浏览器类型
     */
    function getBrower() {
        var ua = getUA();
        if (ua.indexOf("Maxthon") != -1) {
            return "Maxthon";
        } else if (ua.indexOf("MSIE") != -1) {
            return "MSIE";
        } else if (ua.indexOf("Firefox") != -1) {
            return "Firefox";
        } else if (ua.indexOf("Chrome") != -1) {
            return "Chrome";
        } else if (ua.indexOf("Opera") != -1) {
            return "Opera";
        } else if (ua.indexOf("Safari") != -1) {
            return "Safari";
        } else {
            return "ot";
        }
    }
    /**
     * 获取浏览器语言
     */
    function getBrowerLanguage() {
        var lang = navigator.language;
        return lang != null && lang.length > 0 ? lang : "";
    }
    /**
     * 获取操作系统
     */
    function getPlatform() {
        return navigator.platform;
    }
    /**
     * 获取页面title
     */
    function getPageTitle() {
        return document.title;
    }

    /**
     * 构造XMLHttpRequest对象
     *
     * @return
     */
    function createXMLHttpRequest() {
        if (window.ActiveXObject) {
            xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
        } else if (window.XMLHttpRequest) {
            xmlHttp = new XMLHttpRequest();
        }
    }

    this.log = function(vlch, vlch1, vlch2, vlch3) {
        var p;
        var vlstatCH = vlch != null && vlch.length > 0 ? vlch : "";
        var vlstatCH1 = vlch1 != null && vlch1.length > 0 ? vlch1 : "";
        var vlstatCH2 = vlch2 != null && vlch2.length > 0 ? vlch2 : "";
        var vlstatCH3 = vlch3 != null && vlch3.length > 0 ? vlch3 : "";
        var vlstatCookieId = getStatId();
        var vlstatUA = encodeURIComponent(getUA());
        var vlstatREFURL = encodeURIComponent(document.referrer);
        var vlstatURL = encodeURIComponent(document.URL);
        var vlstatScreenX = screen.width;
        var vlstatScreenY = screen.height;
        var vlstatOS = getPlatform();
        var vlstatBrower = getBrower();
        var vlstatBrowerLanguage = getBrowerLanguage();
        var vlstatPageTitle = encodeURIComponent(getPageTitle());
        var vlstatAction = $lopts.url;
        p = "cookieId=" + vlstatCookieId + "&ua=" + vlstatUA + "&refurl="
            + vlstatREFURL + "&url=" + vlstatURL + "&screenX=" + vlstatScreenX + "&screenY=" + vlstatScreenY
            + "&os=" + vlstatOS + "&browser=" + vlstatBrower + "&browserLang=" + vlstatBrowerLanguage
            + "&title=" + vlstatPageTitle + "&ch=" + vlstatCH + "&ch1=" + vlstatCH1 + "&ch2=" + vlstatCH2
            + "&ch3=" + vlstatCH3;
        var urlGo = vlstatAction.indexOf('?') != -1 ? vlstatAction + "&" + p : vlstatAction + "?" + p;
        createXMLHttpRequest();
        xmlHttp.open('GET', urlGo);
        xmlHttp.send(null);
    };

    this.log("page", "init");

    this.record = function(element, eventName, opts, key) {
        if (!key) key = element.id;
        element.addEventListener(eventName, function(){
            if (opts) {
                window.Logger.log(key, eventName, opts[0], opts[1]);
            } else {
                window.Logger.log(key, eventName);
            }
        });
    }
});
