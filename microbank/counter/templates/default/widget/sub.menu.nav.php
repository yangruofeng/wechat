<div class="content-nav">
    <ul class="nav nav-tabs">
        <?php foreach($output['sub_menu'] as $menu){
            $args = explode(',', $menu['args']);
            $cross_domain = $menu['cross_domain'];
            $arr_cross = array();
            if ($cross_domain) {
                $arr_cross = array(
                    "cross_domain_uid" => $output['token_uid'],
                    "cross_domain_passport" => $output['token_passport']
                );
            }
            ?>
            <li role="presentation" class="<?php echo ($_GET['op'] == $args[2] || $output['show_menu'] == $args[2]) ? 'active' : ''; ?>">
                <a href="<?php
                echo ($_GET['op'] == $args[2] || $output['show_menu'] == $args[2]) ? 'javascript:void(0)' : getUrl($args[1], $args[2], $arr_cross, false, C('site_root') . DS . $args[0]);
                ?>">
                    <?php echo $menu['title']?>
                </a>
            </li>
        <?php }?>
    </ul>
</div>
