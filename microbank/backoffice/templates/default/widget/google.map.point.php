<?php
 if(!$point) $point=$data['point'];//dynamic 模式
if (!$point || !$point['x'] || !$point['x']) { ?>
    <div id="map" style="width: 100%;height: 100%;text-align: center">
        <span style="font-size: 20px;color: slategrey;line-height: 100px;text-align: center;vertical-align:middle">NO SETTING</span>
    </div>
<?php } else {
    if (!$map_id) {
        $map_id = 'map';
    }
    ?>
    <div style="width: 100%;height: 100%">
        <div>
            <span>Coordinate</span><span style="padding-left: 20px">X:<?php echo $point['x'] ?>, Y:<?php echo $point['y'] ?></span>
        </div>
        <div id="<?php echo $map_id?>" style="width: 100%;height: 100%">
        </div>
        <script>
            function initGoogleMap() {
                var map_id = "<?php echo $map_id?>";
                var _point = {lat:<?php echo $point['y']?> , lng: <?php echo $point['x']?>};
                var _map = new google.maps.Map(
                    document.getElementById(map_id), {zoom: 15, center: _point});
                var marker = new google.maps.Marker({position: _point, map: _map});
            }
        </script>
        <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=<?php echo getConf("api_google_map")?>&callback=initGoogleMap">
        </script>

    </div>

<?php }?>
