<?php $map_data = $output['map_param']; ?>
<div id="map-canvas" class="map-canvas" style="width: 800px;height: 600px;">
    <h4><?php echo $map_data['title']; ?></h4>
    <h5>Address Detail: <?php echo $map_data['address_detail']; ?></h5>
    <?php
    $point = array('x' => $map_data['x'], 'y' => $map_data['y']);
    include(template("widget/google.map.point"));
    ?>
</div>