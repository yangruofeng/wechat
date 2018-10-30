<script>
    var app={};
    app.today=function(){
        return "<?php echo date("Y-m-d");?>";
    };
    app.now=function(){
        return "<?php echo date("Y-m-d H:i:s");?>";
    };
    app.yesterday=function(){
        return "<?php echo date("Y-m-d",time()-3600*24);?>";
    };
    app.before30days=function(){
        return "<?php echo date("Y-m-d",time()-3600*24*30);?>";
    };
    app.monday=function(){
        return "<?php echo date('Y-m-d',(time()-((date('w')==0?7:date('w'))-1)*86400));?>";
    };
    app.lastMonday=function(){
        return "<?php echo (date("l",time())=="Monday")?date("Y-m-d",strtotime("last monday")):date("Y-m-d",strtotime('-1 week last monday'));?>";
    };
    app.lastSunday=function(){
        return "<?php echo date('Y-m-d',strtotime('last sunday'));?>"
    };
    app.firstDayOfMonth=function(){
        return "<?php echo date('Y-m-d',strtotime(date('Y-m', time()).'-01 00:00:00'));?>"
    };
    app.firstDayOfLastMonth=function(){
        return "<?php echo date('Y-m-d',strtotime('-1 month', strtotime(date('Y-m', time()).'-01 00:00:00')));?>"
    };
    app.lastDayOfLastMonth=function(){
        return "<?php echo date('Y-m-d',strtotime(date('Y-m', time()).'-01 00:00:00')-86400);?>";
    };
    app.waiting=function(){
        $("body").waiting();
    };
    app.unmask=function(){
        $("body").unmask();
    };
    app.initWindow=function(){
        window.init_SubPage=function(){};
    };

    app.debug_state="<?php echo C('debug');?>";
    app.sid="<?php echo $_REQUEST['_s'];?>";
    app.logout= function () {
        yo.loadData({
            _c:"apiMain",
            _m:"logout",
            param:{},
            callback:function(_o){
                window.location.reload();
            }
        });
    };
    app.loadMenu=function(_key,_dir,_e){
        if(_e){
            $("#span_menu_title").text($(_e).attr("my_title"));
            $("#span_menu_up_title").text($(_e).attr("up_title"));

            $(".right-menu-item").removeClass("list-group-item-white").addClass("list-group-item-orange");
            $(_e).removeClass("list-group-item-orange").addClass("list-group-item-white");
        }
        app.initWindow();
        var _mainStage=$("#divMainStage");
        _mainStage.waiting();
        _mainStage.data("page_key",_key);
        yo.loadTpl({
            tpl:_key,
            ext:{tpl_dir:_dir},
            callback:function(_tpl){
                _mainStage.html(_tpl);
                _mainStage.unmask();
                //初始化page
                init_SubPage();//默认这些页面都有这个初始入口

            }
        })
    };
</script>
<!--<script>
    setTimeout(function(){
        $.extend($.validator.messages, {
            required: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_required');?></span>",
            remote: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_remote');?></span>",
            email: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_email');?></span>",
            url: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_url');?></span>",
            date: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_date');?></span>",
            dateISO: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_dateISO');?></span>",
            number: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_number');?></span>",
            digits: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_digits');?></span>",
            creditcard: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_creditcard');?></span>",
            equalTo: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_equalTo');?></span>",
            extension: "<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_extension');?></span>",
            maxlength: $.validator.format("<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_maxlength');?></span>"),
            minlength: $.validator.format("<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_minlength');?></span>"),
            rangelength: $.validator.format("<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_rangelength');?></span>"),
            range: $.validator.format("<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_range');?></span>"),
            max: $.validator.format("<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_max');?></span>"),
            min: $.validator.format("<span style='color:red'><img src='resources/validate_error.gif'/> &nbsp;&nbsp;<?php echo L('jqinvalid_min');?></span>")
        });
    },1000);
</script>-->
<script>
    window.onerror=function(){
        if(arguments.length>0){
            alert(arguments[0]);
        }
    };
    function doMouseOverRow(_uid){
        $(".list-action-"+_uid).show();
    }
    function doMouseOutRow(_uid){
        $(".list-action-"+_uid).hide();
    }
</script>
