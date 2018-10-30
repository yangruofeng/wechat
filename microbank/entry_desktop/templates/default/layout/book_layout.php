<!DOCTYPE html>
<html lang="en">
<?php require_once template('widget/app.header'); ?>
<?php require_once template('widget/app.common.js'); ?>
<?php require_once template('widget/app.monitor.js'); ?>

<style>
    a:hover, a:focus {
        text-decoration: none;
        color: #28b779;
    }
    .app-header{
        margin-bottom: 0;
        border: none;
    }
    .app-header .btn{
        height: 28px
    }
    .app-header .navbar-brand{
        padding: 2px;
        font-weight: 500;
        line-height: 50px;
        display: block;
        width: 100%;
        text-align: center;
        background-color: #475270;
        color: yellow;
        border-bottom: solid 1px gray;
    }
    .app-body .sidebar{
        position: fixed;
        width: 220px;flex: 0 0 220px;
        background-color: #475270;
        height: calc(100vh - 55px)
    }
    .app-body .main-stage{
        /*margin-left: 220px;*/
        flex: 1;
        display: block;
        background-color: #e9ecf3;
        height: calc(100vh - 55px)
    }
    .app-body .main-stage .main-frame{
        width: 100%;
        height: calc(100vh - 55px);
        border: none;
        border-top: solid 1px dimgray;
    }
    /* Sidebar Navigation */
    #sidebar {
        display: block;
        float: left;
        position: relative;
        width: 220px;
        z-index: 16;
    }

    /*滚动条美化css 适合WebKit引擎的浏览器如chrome、oprea、Safari等*/
    ::-webkit-scrollbar {
        width: 16px;
    }

    ::-webkit-scrollbar-track,
    ::-webkit-scrollbar-thumb {
        border-radius: 999px;
        border: 5px solid transparent;
    }

    ::-webkit-scrollbar-track {
        box-shadow: 1px 1px 5px rgba(0, 0, 0, .2) inset;
    }

    ::-webkit-scrollbar-thumb {
        min-height: 20px;
        background-clip: content-box;
        box-shadow: 0 0 0 5px rgba(0, 0, 0, .2) inset;
    }

    ::-webkit-scrollbar-corner {
        background: transparent;
    }

    #sidebar > ul {
        list-style: none;
        margin: 0px 0 0;
        padding: 0;
        position: absolute;
        width: 220px;
    }

    #sidebar > ul > li {
        display: block;
        position: relative;
        font-size: 13px;
    }

    #sidebar > ul > li > a {
        padding: 8px 0 8px 15px;
        display: block;
        color: #e6ebef;
        cursor: pointer;
    }

    #sidebar > ul > li > a > i {
        margin-right: 10px;
    }

    #sidebar > ul > li > a > i:first-child {
        width: 12px;
        text-align: center
    }

    #sidebar li .icon {
        position: absolute;
        right: 15px;
        transition: all .25s ease;
        -webkit-transition: all .25s ease;
        -moz-transition: all .25s ease;
        -ms-transition: all .25s ease;
        -o-transition: all .25s ease;
    }

    #sidebar li.open .icon {
        transform: rotate(90deg);
        -webkit-transform: rotate(90deg);
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        transition: all .25s ease;
        -webkit-transition: all .25s ease;
        -moz-transition: all .25s ease;
        -ms-transition: all .25s ease;
        -o-transition: all .25s ease;
    }

    #sidebar > ul > li.active > a {
        text-decoration: none;
    }

    #sidebar > ul li ul li.active a {
        background: url("../img/menu-active.png") no-repeat scroll right center transparent !important;
        text-decoration: none;
    }

    #sidebar > ul > li > a > .label {
        margin: 0 22px 0 0;
        float: right;
        padding: 2px 8px 3px;
    }

    #sidebar > ul li ul {
        display: none;
        margin: 0;
        padding: 0;
    }

    #sidebar > ul li.open ul {
        display: block;
    }

    #sidebar > ul li ul li a {
        padding: 8px 0 8px 32px;
        display: block;
        color: #777777;
        cursor: pointer;
    }

    #sidebar > ul li ul li a i {
        margin-right: 10px;
    }

    #sidebar > ul li ul li:first-child a {
        border-top: 0;
    }

    #sidebar > ul li ul li:last-child a {
        border-bottom: 0;
    }

    /* Content */
    #content {
        background: none repeat scroll 0 0 #eeeeee;
        margin-left: 220px;
        margin-right: 0;
        position: relative;
        min-height: 100%;
        width: auto;
    }

    #content-header {
        margin-top: 0px;
        z-index: 20;
    }

    #content-header h1 {
        color: #454545;
        font-size: 18px;
        font-weight: bold;
        float: none;
        text-shadow: 0 1px 0 #ffffff;
        margin-left: 20px;
        padding-bottom: 10px;
        margin-right: 20px;
        position: relative;
        border-bottom: 2px solid #EE7600;
    }

    #content-header .btn-group {
        float: right;
        right: 20px;
        position: absolute;
    }

    #content-header h1, #content-header .btn-group {
        margin-top: 10px;
    }

    #content-header .btn-group .btn {
        padding: 11px 14px 9px;
    }

    #content-header .btn-group .btn .label {
        position: absolute;
        top: -7px;
    }

    .container-fluid .row-fluid:first-child {
        margin-top: 0px;
    }

    /* Breadcrumb */
    #breadcrumb {
        height: 38px;
        line-height: 17px;
    }

    #breadcrumb a {
        padding: 10px 20px 10px 10px;
        display: inline-block;
        background-image: url('../img/breadcrumb.png');
        background-position: center right;
        background-repeat: no-repeat;
        font-size: 13px;
        color: #666666;
    }

    #breadcrumb a:hover {
        color: #333333;
    }

    #breadcrumb a:last-child {
        background-image: none;
    }

    #breadcrumb a.current {
        font-weight: bold;
        color: #444444;
    }

    #breadcrumb a i {
        margin-right: 5px;
        opacity: .6;
    }

    #breadcrumb a:hover i {
        margin-right: 5px;
        opacity: .8;
    }
    #sidebar >a:hover{
        text-decoration: none;
    }
    #sidebar > ul{
        /* border-bottom: 1px solid #37414b*/
    }
    #sidebar > ul > li {
        /*border-top: 1px solid #37414b;*/
        border-bottom: inset 1px #5a6674;
    }
    #sidebar > ul > li.active {
        background-color: #27a9e3; border-bottom: 1px solid #27a9e3;  border-top: 1px solid #27a9e3;
    }

    #sidebar > ul li ul li.active {
        background-color: #28b779;
        color: #fff;
    }

    #sidebar > ul > li.active a{ color:#fff; text-decoration:none;}

    #sidebar > ul > li > a > .label {
        background-color:#2E8B57;
    }
    #sidebar > ul > li > a:hover {
        background-color: #27a9e3; color:#fff;
    }
    #sidebar > ul li ul {
        background-color: #343c53;
    }
    #sidebar > ul li ul li a{
        color:#fff3f3
    }
    #sidebar > ul li ul li a:hover, #sidebar > ul li ul li.active a {
        background-color: #28b779;
        color: #fff;
    }


    #search input[type=text] {
        background-color: #47515b; color: #fff;
    }
    #search input[type=text]:focus {
        color: #242424; color: #fff; box-shadow:none;
    }
    .dropdown-menu li a:hover, .dropdown-menu .active a, .dropdown-menu .active a:hover {
        color: #eeeeee;
        background:#666;

    }
    .top_message{ float:right; padding:20px 12px; position:relative; top:-13px; border-left:1px solid #3D3A37; font-size:14px; line-height:20px; *line-height:20px; color:#333; text-align:center;  vertical-align:middle; cursor:pointer; }
    .top_message:hover{ background:#000}
    .rightzero{ right:0px; display:none;}
    @media (max-width: 480px) {
        #sidebar > a {
            background:#27a9e3;

        }
        .quick-actions_homepage .quick-actions li{ min-width:100%;}
    }
    @media (min-width: 768px) and (max-width: 970px) {.quick-actions_homepage .quick-actions li{ min-width:20.5%;}}
    @media (min-width: 481px) and (max-width: 767px) {
        #sidebar > ul ul:before {
        }
        #sidebar > ul ul:after {
            border-right: 6px solid #222222;
        }
        .quick-actions_homepage .quick-actions li{ min-width:47%;}
    }


    .app-body .sidebar{
        OVERFLOW-Y: auto; OVERFLOW-X:hidden;
    }
    .app-body .sidebar li .icon {
        transition: all .25s ease;
        -webkit-transition: all .25s ease;
        -moz-transition: all .25s ease;
        -ms-transition: all .25s ease;
        -o-transition: all .25s ease;
    }

    .app-body .sidebar li.open .icon {
        transform: rotate(90deg);
        -webkit-transform: rotate(90deg);
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        transition: all .25s ease;
        -webkit-transition: all .25s ease;
        -moz-transition: all .25s ease;
        -ms-transition: all .25s ease;
        -o-transition: all .25s ease;
    }

    .app-body .sidebar .submenu img {
        width: 12px;
        margin-top: -4px;
    }

</style>
<style>

    <?php if($output['is_operator'] || $output['is_sub']){?>
    .app-body .sidebar > ul li.active a {
        background: url(resource/img/menu-active.png) no-repeat scroll right center transparent !important;
        text-decoration: none;
    }

    .app-body .sidebar .submenu img.tab-active {
        display: none;
    }

    .app-body .sidebar .submenu.active img.tab-active, #sidebar .submenu:hover img.tab-active {
        display: inline-block;
    }

    .app-body .sidebar .submenu.active img.tab-default, #sidebar .submenu:hover img.tab-default {
        display: none;
    }

    .app-body .sidebar > ul li ul li a {
        padding-left: 25px;
    }

    .app-body .sidebar > ul li ul li {
        position: relative;
    }

    .app-body .sidebar > ul > li > a > .label-important {
        float: none!important;
        margin-right: 0px!important;
        position: absolute;
        right: 15px;
        top: 9px;
    }

    .app-body .sidebar > ul li ul li a .label-important {
        position: absolute;
        right: 25px;
        top: 10px;
    }
    <?php } ?>
</style>
<body style="min-height: 100vh">

<header class="app-header navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header" style="width: 220px">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">
                <?php echo $output['system_title'] ?: 'Console'?>
            </a>
        </div>
        <nav class="collapse navbar-collapse" id="app-navbar">
            <form id="content-header" class="nav navbar-form navbar-left">
                <button type="button" class="btn btn-default" onclick="switchSideBar()">
                    <i class="fa fa-align-justify"></i>
                </button>
                <video id="task-hint" src="resource/video/hint.mp3" style="display: none" preload="auto"></video>
                <div id="breadcrumb" style="display: inline">
                    <?php if ($output['is_operator']) { ?>
                        <a href="#" class="tip-bottom processing_task" link="<?php echo $output['processing_task']['url']?>" style="cursor: pointer;color: red;font-weight: 600">
                            <span>Processing Task: </span>
                            <span id="task_name"><?php echo $output['processing_task']['title'] ?></span>
                        </a>
                        <a class="tip-bottom cancel_processing" style="font-weight: 600;padding-left: 0;display: none" link=""><i class="fa fa-remove" style="margin-right: 1px"></i>Cancel</a>
                    <?php } else if ($output['is_sub']) { ?>
                        <a href="#" class="tip-bottom" style="cursor: default">
                            <span>HOME</span>
                            <i class="fa fa-angle-right"></i>
                            <span class="title-2"></span>
                        </a>
                    <?php } else { ?>
                        <a href="#" class="tip-bottom" style="cursor: default">
                            <span>HOME</span>
                            <i class="fa fa-angle-right"></i>
                            <span class="title-2"></span>
                            <i class="fa fa-angle-right"></i>
                            <span class="title-3"></span>
                        </a>

                    <?php } ?>
                </div>
            </form>


            <ul class="nav navbar-nav navbar-right" style="margin-top: 15px">
                <li  class="dropdown" id="profile-messages">
                    <a title="" href="#" style="padding: 2px" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle">
                        <!--<img alt="Avatar" style="width: 30px;height: 30px" src="<?php echo getUserIcon($output['user_info']['user_icon'])?>">-->
                        <i class="fa fa-user"></i>
                        <span class="text user_name"><?php echo $output['user_info']['user_name']?:$output['user_info']['user_code']?></span>&nbsp;
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" style="left:auto;right: 0;">
                        <li><a href="#" id="my_profile"><i class="fa fa-user"></i> My Profile</a></li>
                        <li class="divider"></li>
                        <li><a href="#" id="change_password"><i class="fa fa-tasks"></i> Change Login Password</a></li>
                        <li class="divider"></li>
                        <li><a href="#" id="set_trade_password"><i class="fa fa-tasks"></i> Setting Trading Password</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo getUrl('login','loginOut', array(), false, ENTRY_DESKTOP_SITE_URL)?>"><i class="fa fa-key"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
            <form class="nav navbar-form navbar-right">

                <div class="form-group">
                    <div class="input-group">
                        <input class="form-conrol" style="padding-left:10px;height: 28px;width: 270px;font-size: 10px" type="text" id="search-text"  placeholder="<?php echo $output['is_operator'] ? 'Search by client id.' : 'Search by client id or contract sn.'?>"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" title="Search" id="search-btn">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>

                <button type="button" class="btn btn-default calculator">
                    <i class="fa fa-calculator"></i>
                </button>

                <button type="button" class="btn btn-default product_interest" title="Product & Interest">
                    <i class="fa fa-list-alt"></i>
                </button>
            </form>
        </div>
</header>
<div class="app-body container-fluid" style="display: flex;flex-direction:row;padding: 0">
    <div id="sidebar" class="sidebar">
        <ul>
            <?php if ($output['is_operator'] || $output['is_sub']) { ?>
                <?php foreach ($output['menu_items'] as $k_c => $s_item) {?>
                    <?php $args = explode(',', $s_item['args']); ?>
                    <li class="submenu <?php echo $k_c?> " data-task-type="<?php echo $s_item['task_type']; ?>">
                        <a href="#" class="<?php echo $s_item['child'] ? 'menu_x' : 'menu_a'?>" data-task-code="<?php echo $s_item['task_type']?:'';?>" link="<?php echo getUrl($args[1], $args[2], array(), false, C('site_root') . DS . $args[0]); ?>">
                            <img class="tab-default" src="<?php echo ENTRY_DESKTOP_SITE_URL . '/resource/img/sub_icon/tab_' . ($s_item['icon_key']?:$k_c) . '.png' ?>">
                            <img class="tab-active" src="<?php echo ENTRY_DESKTOP_SITE_URL . '/resource/img/sub_icon/tab_' . ($s_item['icon_key']?:$k_c) . '_active.png' ?>">
                            <span><?php echo $s_item['title'] ?></span>
                            <?php if ($s_item['task_type']) { ?>
                                <img class="hint-switch" hint="open" src="resource/img/hint_open.png" style="width: 13px;">
                                <span class="label label-important" <?php if($s_item['task_is_msg']){?> style="background-color: #0000ff"<?php }?>>
                                <?php echo $output['task_num'][$s_item['task_type']]['count_pending'] ? : 0?>
                            </span>
                            <?php } elseif($s_item['child']) {?>
                                <?php
                                $with_task_type=false;
                                $with_task_num=0;
                                foreach($s_item['child'] as $ss_item){
                                    if($ss_item['task_type']){
                                        $with_task_type=true;
                                        $with_task_num+=$output['task_num'][$ss_item['task_type']]['count_pending'] ? : 0;
                                    }
                                }
                                if(!$with_task_type){
                                ?>
                                    <i class="icon fa fa-angle-right"></i>
                                <?php }else{?>
                                    <img class="hint-switch" hint="open" src="resource/img/hint_open.png" style="width: 13px;">
                                    <span class="label label-important">
                                        <?php echo $with_task_num?>
                                    </span>
                                <?php }?>

                            <?php }?>
                        </a>
                        <ul>
                            <?php foreach ($s_item['child'] as $key => $item) {
                                $c_args = explode(',', $item['args']); ?>
                                <?php if($k_c == 'certification_file' || $k_c == 'relative_cert_file'){?>
                                    <li class="<?php echo 'type_' . $key?>">
                                        <a class="menu_a" data-cert-code="<?php echo $key?>" link="<?php echo getUrl($args[1], $args[2], array('type' => $key), false, C('site_root'). DS . $args[0]); ?>">
                                            <span><?php echo $item;?></span>
                                            <span class="label label-important sub-num"><?php echo $output['task_num'][$s_item['task_type']]['group_by'][$key]['count_pending']?></span>
                                        </a>
                                    </li>
                                <?php }else{?>
                                    <li class="<?php echo 'type_' . $key?>">
                                        <a class="menu_a" link="<?php echo getUrl($c_args[1], $c_args[2], array('type' => $key), false, C('site_root'). DS . $c_args[0]); ?>">
                                            <span><?php echo $item['title']?:$item;?></span>
                                            <?php if($item['task_type']){?>
                                                <span class="label label-important sub-num" <?php if($item['task_is_msg']){?> style="background-color: #0000ff"<?php }?>>
                                                 <?php echo $output['task_num'][$item['task_type']]['count_pending'] ? : 0?>
                                                </span>
                                            <?php }?>
                                        </a>
                                    </li>
                                <?php }?>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
            <?php } else {?>
                <?php foreach ($output['menu_items'] as $k_c => $s_item) { ?>

                    <li class="submenu" data-task-type="<?php echo $s_item['task_type']; ?>">
                        <a href="#">
                            <img src="<?php echo ENTRY_DESKTOP_SITE_URL . '/resource/img/icon-' . ($s_item['icon_key']?:$k_c) . '.png' ?>">
                            <span><?php echo $s_item['title']?></span>
                            <i class="icon fa fa-angle-right"></i>
                        </a>
                        <ul>
                            <?php foreach ($s_item['child'] as $item) { $args = explode(',', $item['args']);?>
                                <li>
                                    <a class="menu_a <?php echo $args[1] . '-' . $args[2] ?>" link="<?php echo getUrl($args[1], $args[2], array(), false, C('site_root'). DS . $args[0]); ?>">
                                        <span><?php echo $item['title']?></span>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
    <div class="main-stage">
        <iframe class="main-frame" id="iframe-main" frameborder="0" src="#">

        </iframe>
    </div>
</div>

<script>
    function switchSideBar(){
        if($("#sidebar").is(":hidden")){
            $("#sidebar").show();
            //$("#content").css('margin-left','220px');
        }else{
            $("#sidebar").hide();
            //$("#content").css('margin-left','0px');
        }
    }
    $(function () {
        $('#sidebar ul li').first().find('a').click();
        <?php if(!$output['is_operator']){?>
            $('#sidebar ul li').first().find('ul li').first().find('a').click();
        <?php }?>

        /*
        if (window != top) {
            top.location.href = location.href;
        }
        */
    });
</script>
</body>
</html>