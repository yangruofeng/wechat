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
    //    app.waiting = function () {
    //        $("body").waiting();
    //    };
    //    app.unmask = function () {
    //        $("body").unmask();
    //    };
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

    $(function () {
        <?php if($output['is_console']) {?>
            $('#frm_search_condition').keydown(function (e) {
                if (e.keyCode == 13) {
                    btn_search_onclick();
                    return false;
                }
            })
        <?php }?>
        <?php if($_GET['show_menu']){?>
        var _show_menu = '<?php echo $_GET['show_menu']?>';
        $('#sidebar .submenu', window.parent.document).removeClass('open');
        $('#sidebar .submenu ul', window.parent.document).hide();
        $('#sidebar .submenu ul li', window.parent.document).removeClass('active');
        $('#sidebar .' + _show_menu, window.parent.document).parent().addClass('active').parent().show().parent().addClass('open');
        <?php }?>
        <?php if($_GET['show_menu_a']){?>
        var _show_menu_a = '<?php echo $_GET['show_menu_a']?>';
        var _show_menu_b = '<?php echo $_GET['show_menu_b']?>';
        $('#sidebar .submenu', window.parent.document).removeClass('open active');
        $('#sidebar .submenu ul', window.parent.document).hide();
        $('#sidebar .submenu ul li', window.parent.document).removeClass('active');
        if (_show_menu_b) {
            $('#sidebar .submenu.' + _show_menu_a, window.parent.document).addClass('open');
            $('#sidebar .submenu .type_' + _show_menu_b, window.parent.document).addClass('active').parent().show();
        } else {
            $('#sidebar .submenu.' + _show_menu_a, window.parent.document).addClass('active');
        }
        <?php }?>
        <?php if ($output['processing_task']) { ?>
        var processing_task_title = '<?php echo $output['processing_task']['title']?>';
        var processing_task_url = '<?php echo $output['processing_task']['url']?>';
        var cancel_task_url = '<?php echo $output['processing_task']['cancel_url']?>';
        $('#breadcrumb .processing_task', window.parent.document).attr('link', processing_task_url);
        $('#breadcrumb .cancel_processing', window.parent.document).attr('link', cancel_task_url);
        $('#breadcrumb .processing_task #task_name', window.parent.document).text(processing_task_title);
        if (cancel_task_url) {
            $('#breadcrumb .cancel_processing', window.parent.document).show();
        } else {
            $('#breadcrumb .cancel_processing', window.parent.document).hide();
        }

        <?php } ?>

        $('#breadcrumb .processing_task', window.parent.document).click(function () {
            var link = $(this).attr('link');
            if (!link) {
                return;
            }
            window.location.href = link;
        })

        $('#breadcrumb .cancel_processing', window.parent.document).click(function () {
            var link = $(this).attr('link');
            if (!link) {
                return;
            }
            $.messager.confirm("Cancel", "Are you sure to cancel the task?", function (_r) {
                if (!_r) return;
                window.location.href = link;
            });

        })

        

        $('.img-asset-item').load(function(){
            //console.log($(this))
        });
    })

    $('#search-btn', window.parent.document).click(function () {
        var search_text = $.trim($('#search-text', window.parent.document).val());
        if (!search_text) {
            return;
        }
        yo.loadData({
            _c: "home",
            _m: "searchText",
            param: {search_text: search_text},
            callback: function (_o) {
                if (_o.STS) {
                    var _data = _o.DATA;
                    if (_data.type == 'client') {
                        window.location.href = _data.url;
                    }
                    if (_data.type == 'contract') {
                        window.location.href = _data.url;
                    }
                } else {
                    alert(_o.MSG);
                }
            }
        });
    })

    $('.calculator', window.parent.document).click(function () {
        window.location.href = '<?php echo getUrl('tools', 'calculator', array('show_menu'=>'tools-calculator'), false, BACK_OFFICE_SITE_URL)?>';
    })

    $('.product_interest', window.parent.document).click(function () {
        window.location.href = '<?php echo getUrl('data_center_product', 'productInterest', array(), false, BACK_OFFICE_SITE_URL)?>';
    })

    $('#change_password', window.parent.document).click(function () {
        window.location.href = '<?php echo getUrl('user', 'changePassword', array(), false, BACK_OFFICE_SITE_URL)?>';
    })

    $('#set_trade_password', window.parent.document).click(function () {
        window.location.href = '<?php echo getUrl('user', 'setTradingPassword', array(), false, BACK_OFFICE_SITE_URL)?>';
    })

    $('#my_profile', window.parent.document).click(function () {
        window.location.href = '<?php echo getUrl('user', 'myProfile', array(), false, BACK_OFFICE_SITE_URL)?>';
    })

    

</script>