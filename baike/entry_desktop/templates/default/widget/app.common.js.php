<script type="text/javascript">
    var app = {};
    app.today = function () {
        return "<?php echo date("Y-m-d");?>";
    };
    app.now = function () {
        return "<?php echo date("Y-m-d H:i:s");?>";
    };
    app.yesterday = function () {
        return "<?php echo date("Y-m-d",time()-3600*24);?>";
    };
    app.before30days = function () {
        return "<?php echo date("Y-m-d",time()-3600*24*30);?>";
    };
    app.monday = function () {
        return "<?php echo date('Y-m-d',(time()-((date('w')==0?7:date('w'))-1)*86400));?>";
    };
    app.lastMonday = function () {
        return "<?php echo (date("l",time())=="Monday")?date("Y-m-d",strtotime("last monday")):date("Y-m-d",strtotime('-1 week last monday'));?>";
    };
    app.lastSunday = function () {
        return "<?php echo date('Y-m-d',strtotime('last sunday'));?>"
    };
    app.firstDayOfMonth = function () {
        return "<?php echo date('Y-m-d',strtotime(date('Y-m', time()).'-01 00:00:00'));?>"
    };
    app.firstDayOfLastMonth = function () {
        return "<?php echo date('Y-m-d',strtotime('-1 month', strtotime(date('Y-m', time()).'-01 00:00:00')));?>"
    };
    app.lastDayOfLastMonth = function () {
        return "<?php echo date('Y-m-d',strtotime(date('Y-m', time()).'-01 00:00:00')-86400);?>";
    };
    app.waiting = function () {
        $("body").waiting();
    };
    app.unmask = function () {
        $("body").unmask();
    };
    app.initWindow = function () {
        window.init_SubPage = function () {
        };
    };

    app.debug_state = "<?php echo C('debug');?>";
    app.sid = "<?php echo $_REQUEST['_s'];?>";
    app.logout = function () {
        yo.loadData({
            _c: "apiMain",
            _m: "logout",
            param: {},
            callback: function (_o) {
                window.location.reload();
            }
        });
    };
    app.loadMenu = function (_key, _dir, _e) {
        if (_e) {
            $("#span_menu_title").text($(_e).attr("my_title"));
            $("#span_menu_up_title").text($(_e).attr("up_title"));

            $(".right-menu-item").removeClass("list-group-item-white").addClass("list-group-item-orange");
            $(_e).removeClass("list-group-item-orange").addClass("list-group-item-white");
        }
        app.initWindow();
        var _mainStage = $("#divMainStage");
        _mainStage.waiting();
        _mainStage.data("page_key", _key);
        yo.loadTpl({
            tpl: _key,
            ext: {tpl_dir: _dir},
            callback: function (_tpl) {
                _mainStage.html(_tpl);
                _mainStage.unmask();
                //初始化page
                init_SubPage();//默认这些页面都有这个初始入口

            }
        })
    };

    //初始化相关元素高度
    function init() {
        $("body").height($(window).height() - 80);
        $("#iframe-main").height($(window).height() - 90);
        $("#sidebar").height($(window).height() - 50);
    }



    function goPage(newURL) {
        if (newURL != "") {
            if (newURL == "-") {
                resetMenu();
            } else {
                document.location.href = newURL;
            }
        }
    }

    function resetMenu() {
        document.gomenu.selector.selectedIndex = 2;
    }

</script>