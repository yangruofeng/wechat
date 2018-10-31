
<?php $map_detail = $output['map_detail'];  ?>

<div class="page" >
    <h4 ><?php echo $output['map_title'];?></h4>

    <div style="width: 800px;height: 600px;">
        <div id="map-canvas">
            <?php
            $point = array('x' => $map_detail['coord_x'], 'y' => $map_detail['coord_y']);
            include_once(template("widget/google.map.point"));
            ?>
        </div>
    </div>
</div>
