<style>
    #sidebar li .icon {
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

    #sidebar .submenu img {
        width: 12px;
        margin-top: -4px;
    }

    <?php if($output['is_operator'] || $output['is_sub']){?>
    #sidebar > ul li.active a {
        background: url(resource/img/menu-active.png) no-repeat scroll right center transparent !important;
        text-decoration: none;
    }

    #sidebar .submenu img.tab-active {
        display: none;
    }

    #sidebar .submenu.active img.tab-active, #sidebar .submenu:hover img.tab-active {
        display: inline-block;
    }

    #sidebar .submenu.active img.tab-default, #sidebar .submenu:hover img.tab-default {
        display: none;
    }

    #sidebar > ul li ul li a {
        padding-left: 25px;
    }

    #sidebar > ul li ul li {
        position: relative;
    }

    #sidebar > ul > li > a > .label-important {
        float: none!important;
        margin-right: 0px!important;
        position: absolute;
        right: 15px;
        top: 9px;
    }

    #sidebar > ul li ul li a .label-important {
        position: absolute;
        right: 25px;
        top: 10px;
    }
    <?php } ?>
</style>
<?php  ?>
<div>
    <h1>Test</h1>
</div>
<div id="sidebar" style="OVERFLOW-Y: auto; OVERFLOW-X:hidden;display: none;">
    <ul>
        <?php if ($output['is_operator'] || $output['is_sub']) { ?>
            <?php  foreach ($output['menu_items'] as $k_c => $s_item) {?>
                <?php $args = explode(',', $s_item['args']); ?>
                <li class="submenu <?php echo $k_c?>">
                    <a href="#" class="<?php echo $s_item['child'] ? 'menu_x' : 'menu_a'?>" data-task-code="<?php echo $s_item['task_type']?:'';?>" link="<?php echo getUrl($args[1], $args[2], array(), false, C('site_root') . DS . $args[0]); ?>">
                        <img class="tab-default" src="<?php echo ENTRY_DESKTOP_SITE_URL . '/resource/img/sub_icon/tab_' . $k_c . '.png' ?>">
                        <img class="tab-active" src="<?php echo ENTRY_DESKTOP_SITE_URL . '/resource/img/sub_icon/tab_' . $k_c . '_active.png' ?>">
                        <span><?php echo $s_item['title'] ?></span>
                        <?php if ($s_item['task_type']) { ?>
                            <img class="hint-switch" hint="open" src="resource/img/hint_open.png" style="width: 13px;">
                            <span class="label label-important" <?php if($s_item['task_is_msg']){?> style="background-color: #0000ff"<?php }?>>
                                <?php echo $output['task_num'][$s_item['task_type']]['count_pending'] ? : 0?>
                            </span>
                        <?php } elseif($s_item['child']) {?>
                            <i class="icon fa fa-angle-right"></i>
                        <?php }?>
                    </a>
                    <ul>
                        <?php foreach ($s_item['child'] as $key => $item) {
                            $c_args = explode(',', $item['args']); ?>
                            <?php if( 1==1 ||$k_c == 'certification_file' || $k_c == 'relative_cert_file' ){?>
                                <li class="<?php echo 'type_' . $key?>">
                                    <a class="menu_a" data-cert-code="<?php echo $key?>" link="<?php echo getUrl($args[1], $args[2],array_merge((array)$item['params'],array('type' => $key)) , false, C('site_root'). DS . $args[0]); ?>">
                                        <span><?php echo $item;?></span>
                                        <span class="label label-important sub-num"><?php echo $output['task_num'][$s_item['task_type']]['group_by'][$key]['count_pending']?></span>
                                    </a>
                                </li>
                            <?php }else{?>
                                <li class="<?php echo 'type_' . $key?>">
                                    <a class="menu_a" link="<?php echo getUrl($c_args[1], $c_args[2], array_merge((array)$item['params'],array('type' => $key)), false, C('site_root'). DS . $c_args[0]); ?>">
                                        <span><?php echo $item['title']?:$item;?></span>
                                    </a>
                                </li>
                            <?php }?>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        <?php } else { ?>
        <?php foreach ($output['menu_items'] as $k_c => $s_item) { ?>
            <li class="submenu">
                <a href="#">
                    <img src="<?php echo ENTRY_DESKTOP_SITE_URL . '/resource/img/icon-' . $k_c . '.png' ?>">
                    <span><?php echo $s_item['title']?></span>
                    <i class="icon fa fa-angle-right"></i>
                </a>
                <ul>
                    <?php foreach ($s_item['child'] as $item) { $args = explode(',', $item['args']);?>
                        <li>
                            <a class="menu_a <?php echo $args[1] . '-' . $args[2] ?>" link="<?php echo getUrl($args[1], $args[2], $s_item['params']?:array(), false, C('site_root'). DS . $args[0]); ?>">
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
<!--sidebar-menu-->