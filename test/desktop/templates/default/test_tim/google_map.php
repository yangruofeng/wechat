<!DOCTYPE html>
<html>
<head>
    <style>
        /* Set the size of the div element that contains the map */
        #map {
            height: 400px;  /* The height is 400 pixels */
            width: 100%;  /* The width is the width of the web page */
        }
    </style>
</head>
<body>
<?php
$point=$point?:array(
    'y'=>'11.530619',
    'x'=>'104.926965'
);

?>
<!--The div element for the map -->
<div id="map" style="width: 100%;height: 100%"></div>
<script>
    function initGoogleMap() {

        var _point = {lat:<?php echo $point['y']?> , lng: <?php echo $point['x']?>};
        var _map = new google.maps.Map(
            document.getElementById('map'), {zoom: 15, center: _point});
        var marker = new google.maps.Marker({position: _point, map: _map});
    }

</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDpiK4nCWqTGpv2sVKxodYkDv8mUaXak7g&callback=initGoogleMap">
</script>
</body>
</html>

<!--AIzaSyDpiK4nCWqTGpv2sVKxodYkDv8mUaXak7g-->