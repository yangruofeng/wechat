/**
 * 数组去重
 */
Array.prototype.distinct = function () {
    var unique = {};
    var distinct = [];
    for (var i = 0, len = this.length; i < len; i++) {
        if (typeof (unique[this[i].toString()]) == "undefined") {
            distinct.push(this[i]);
        }
        unique[this[i].toString()] = 0;
    }
    return distinct;
}
/**
 * 数组合计
 */
Array.prototype.sum = function () {
    var sum = 0;
    for (var i = 0, len = this.length; i < len; i++) {
        sum += this[i];
    }
    return sum;
}
/**
 * 返回最大值的索引
 */
Array.prototype.indexOfMax = function () {
    if (this.length === 0) {
        return -1;
    }

    var max = this[0], maxIndex = 0;
    for (var i = 1; i < this.length; i++) {
        if (this[i] > max) {
            maxIndex = i;
            max = this[i];
        }
    }
    return maxIndex;
}

/**
 * 返回最小值的索引
 */
Array.prototype.indexOfMin = function () {
    if (this.length === 0) {
        return -1;
    }

    var min = this[0], minIndex = 0;
    for (var i = 1; i < this.length; i++) {
        if (this[i] < min) {
            minIndex = i;
            min = this[i];
        }
    }
    return minIndex;
}
/**
 * 数组洗牌，原地修改，返回修改后的数组
 */
Array.prototype.shuffle = function () {
    for (var j, x, i = this.length; i; j = parseInt(Math.random() * i), x = this[--i], this[i] = this[j], this[j] = x);
    return this;
};

/**
 * 将一个数分成N份，各份值随机分布在平均值两侧，总和不变。
 * @param {number} number 
 * @param {number} part 
 */
function divideUniformlyRandomly(number, part) {
    if (part < 2) return [number];

    var alignedPart = part - part % 2, uniformRandoms = new Array(alignedPart), tail = number % alignedPart;
    number -= tail;
    var mean = number / alignedPart, sum = 0;

    for (var i = 0; i < alignedPart / 2; i++) {
        var delta = mean - Math.round(Math.random() * mean);
        if (delta == mean) delta = Math.round(mean / 2);

        uniformRandoms[i] = mean - delta;
        uniformRandoms[part - i - 1] = mean + delta;
        sum += uniformRandoms[i] + uniformRandoms[part - i - 1];
    }
    var idx = part % 2 == 1 ? Math.floor(part / 2) : uniformRandoms.indexOfMin();
    var diff = number + tail - sum;
    uniformRandoms[idx] = (uniformRandoms[idx] || 0) + diff;

    return uniformRandoms.shuffle();
}

var nextTick = 'undefined' !== typeof process
    ? process.nextTick
    : 'undefined' !== typeof setImmediate
        ? setImmediate
        : setTimeout

function series(arr, ready, safe) {
    var length = arr.length, orig

    if (!length && ready) return nextTick(ready, 1)

    function handleItem(idx) {
        arr[idx](function (err) {
            if (err && ready) return ready(err)
            if (idx < length - 1) return handleItem(idx + 1)
            return ready && ready()
        })
    }

    if (safe) {
        orig = handleItem
        handleItem = function (idx) {
            nextTick(function () {
                orig(idx)
            }, 1)
        }
    }

    handleItem(0)
}

function isFirefox() {
    return navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
}

function randomIntFromInterval(mn, mx) {
    return Math.floor(Math.random() * (mx - mn + 1) + mn);
}


function scrollPos() {
    return {
        x: self.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
        y: self.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop,
    }
}
function centerPos() {
    // Fixes dual-screen position   Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var scrollPos = scrollPos();
    var center = {
        x: width / 2 + dualScreenLeft + scrollPos.x,
        y: height / 2 + dualScreenTop + scrollPos.y,
    };
    return center;
}

function Overlay(opts) {
    this.id = opts.id;
    this.tag = opts.tag;

    this.show = function () {
        var sPos = scrollPos();
        var overlay = $('<' + this.tag + ' id="' + this.id + '">');
        var css = {
            "width": opts.width || "100%",
            "height": opts.height || "100%",
            "position": "absolute",
            "top": opts.y || sPos.y,
            "left": opts.x || sPos.x,
            "zIndex": 1000,
        }
        if (!opts.noBackground) {
            css["backgroundColor"] = "grey";
            css["opacity"] = 0.5;
            css["backgroundImage"] = "radial-gradient(farthest-side ellipse at right bottom , #900, black)";
        }
        overlay.css(css);
        $('body').append(overlay);
        // $('html, body').css({
        //     overflow: 'hidden',
        //     height: '100%'
        // });
        // var html = jQuery('html'); // it would make more sense to apply this to body, but IE7 won't have that
        // html.data('scroll-position', scrollPos());
        // html.data('previous-overflow', html.css('overflow'));
        // html.css('overflow', 'hidden');
        // window.scrollTo(scrollPos.x, scrollPos.y)
        overlay.on('scroll touchmove mousewheel', function (e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        })
    }
    this.remove = function () {
        // $("#" + this.id).off('scroll touchmove mousewheel');
        $("#" + this.id).remove();
        // $('html, body').css({
        //     overflow: 'auto',
        //     height: 'auto'
        // });

        // un-lock scroll position
        // var html = jQuery('html');
        // var scrollPos = html.data('scroll-position');
        // html.css('overflow', html.data('previous-overflow'));
        // window.scrollTo(scrollPos.x, scrollPos.y)
    }
}
// http://paulirish.com/2011/requestanimationframe-for-smart-animating/
// http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating

// requestAnimationFrame polyfill by Erik Möller. fixes from Paul Irish and Tino Zijdel

// MIT license

(function () {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame']
            || window[vendors[x] + 'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function (callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function () { callback(currTime + timeToCall); },
                timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function (id) {
            clearTimeout(id);
        };
}());
// ends requestAnimationFrame polyfill


Snap.plugin(function (Snap, Element, Paper, global) {
    Element.prototype.flip = function () {
        // var bbox = this.getBBox();
        // return [bbox.cx, bbox.cy]
        this.animate({ transform: 'r360,150,150' }, 1000, mina.bounce );
    };
});
