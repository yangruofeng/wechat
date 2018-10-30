<?php
function renderTree($tree, $path) {
    ?>
    <ul>
        <?php
        foreach ($tree as $k=>$v) {
            if (!is_array($v)) {
                $k = preg_replace('/\.php$/', '', $k);
                ?>
                <li id="<?php echo $path . "/" . $k ?>"><a href="?act=doc&op=test&api=<?php echo $path . "/" . $k ?>"><?php echo $k; ?></a></li>
                <?php
            } else {
                ?>
                <li>
                    <?php echo $k; renderTree($v, $path ? $path . "/" . $k : $k); ?>
                </li>
                <?php
            }
        }
        ?>
    </ul>
    <?php
}

renderTree($output['root_tree'], "");
?>
