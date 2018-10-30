

<ul class="tab-base">
    <?php foreach( $output['sub_menu_list'] as $menu ){ ?>
        <li>
            <?php if( $menu['is_active']){ ?>
                <a href="<?php echo $menu['url']; ?>" class="current">
                    <span><?php echo $menu['title']; ?></span>
                </a>
            <?php }else{ ?>
                <a href="<?php echo $menu['url']; ?>">
                    <span><?php echo $menu['title']; ?></span>
                </a>
            <?php } ?>
        </li>
    <?php } ?>
</ul>