<div id="sidebar" style="OVERFLOW-Y: auto; OVERFLOW-X:hidden;">
    <ul>
        <?php foreach ($output['menu_items'] as $k_c => $s_item) {
            /*
            foreach ($s_item['child'] as $first_child) {
                if (!$first_child['cross_domain']) break;
            }
            */
            if(!$s_item['args']){
                $first_child=current($s_item['child']);
            }else{
                $first_child=$s_item;
            }
            $args = explode(',', $first_child['args']);
            $cross_domain = $first_child['cross_domain'];
            $arr_cross = array();
            if ($cross_domain) {
                $arr_cross = array(
                    "cross_domain_uid" => $output['token_uid'],
                    "cross_domain_passport" => $output['token_passport']
                );
            }
            ?>
            <li class="submenu">
                <a href="#" class="menu_a"
                   link="<?php echo getUrl($args[1], $args[2], $arr_cross, false, C('site_root') . DS . $args[0]); ?>">
                <img class="tab-default"
                     src="<?php echo ENTRY_COUNTER_SITE_URL . '/resource/img/counter-icon/tab_' . ($s_item['icon']?:$k_c) . '.png' ?>">
                <img class="tab-active"
                     src="<?php echo ENTRY_COUNTER_SITE_URL . '/resource/img/counter-icon/tab_' . ($s_item['icon']?:$k_c) . '_active.png' ?>">
                <span><?php echo $s_item['title'] ?></span>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
<!--sidebar-menu-->
<script>
    // === Sidebar navigation === //

    $('.menu_a').click(function (e) {
        $("#iframe-main").attr("src", $(this).attr('link'));
        $(".menu_a").parent('li').removeClass('active');
        $(this).parent('li').addClass('active');
        var _title_3 = $(this).find('span').html();
        var _title_2 = $(this).closest('.submenu').find('a span').html();
        $('#content-header .title-2').html(_title_2);
        $('#content-header .title-3').html(_title_3);
    });

    $('.submenu > a').click(function (e) {
        e.preventDefault();
        var submenu = $(this).siblings('ul');
        var li = $(this).parents('li');
        var submenus = $('#sidebar li.submenu ul');
        var submenus_parents = $('#sidebar li.submenu');
        if (li.hasClass('open')) {
            if (($(window).width() > 768) || ($(window).width() < 479)) {
                submenu.slideUp()
            } else {
                submenu.fadeOut(250);
            }
            li.removeClass('open');
        } else {
            if (($(window).width() > 768) || ($(window).width() < 479)) {
                submenus.slideUp();
                submenu.slideDown();
            } else {
                submenus.fadeOut(250);
                submenu.fadeIn(250);
            }
            submenus_parents.removeClass('open');
            li.addClass('open');
        }
    });

    var ul = $('#sidebar > ul');

    $('#sidebar > a').click(function (e) {
        e.preventDefault();
        var sidebar = $('#sidebar');
        if (sidebar.hasClass('open')) {
            sidebar.removeClass('open');
            ul.slideUp(250);
        } else {
            sidebar.addClass('open');
            ul.slideDown(250);
        }
    });
    $(document).ready(function(){
        $(".menu_a").first().trigger("click");
    });
</script>