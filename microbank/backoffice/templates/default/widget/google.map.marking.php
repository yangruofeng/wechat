<?php
if(!$point || !$point['x'] || !$point['x']){
 $point=array(
     'x'=>'104.89746106250004',
     'y'=>'11.5446167591788',
 );
}
?>
<div id="map" style="width: 100%;height: 100%"></div>
<input type="hidden" id="coord_x" name="coord_x" value="<?php echo $point['x']?>">
<input type="hidden" id="coord_y" name="coord_y" value="<?php echo $point['y']?>">
<script>
    //Google map start
    var geocoder;
    var google_map;
    var google_marker;
    function initGoogleMap() {
        //地图初始化
        geocoder = new google.maps.Geocoder();
        var _point = {lat:<?php echo $point['y']?> , lng: <?php echo $point['x']?>};
        google_map = new google.maps.Map(
            document.getElementById('map'), {zoom: 15, center: _point});
        google_marker = new google.maps.Marker({position: _point, map: google_map, draggable:true,
            title:"Drag me!"});
        // 获取坐标
        google.maps.event.addListener(google_marker, "dragend", function () {
            $('#coord_x').val(google_marker.getPosition().lng());
            $('#coord_y').val(google_marker.getPosition().lat());
        });
    }
    //根据地址获取经纬度
    function codeAddress(address) {
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                google_map.setCenter(results[0].geometry.location);
                google_map.setZoom(15);
                google_marker.setPosition(results[0].geometry.location);
                $('#coord_x').val(google_marker.getPosition().lng());
                $('#coord_y').val(google_marker.getPosition().lat());
            }
        });
    }

</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo getConf("api_google_map")?>&callback=initGoogleMap">
</script>
