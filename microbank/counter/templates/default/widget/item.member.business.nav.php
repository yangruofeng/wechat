<div class="content-nav">
    <ul class="nav nav-tabs">
        <?php foreach($output['sub_menu'] as $menu){
            $args = explode(',', $menu['args']);
            ?>
            <li role="presentation" class="<?php echo ($_GET['op'] == $args[2] || $output['show_menu'] == $args[2]) ? 'active' : ''; ?>">
                <a href="<?php
                echo getUrl($args[1], $args[2], array("member_id"=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL);
                ?>">
                    <?php echo $menu['title']?>
                </a>
            </li>
        <?php }?>
    </ul>
</div>
