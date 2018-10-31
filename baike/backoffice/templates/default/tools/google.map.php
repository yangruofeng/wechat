<style>
    #map-canvas {
        width: 1000px;
        height: 500px;
        margin: 0px;
        padding: 0px
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Google</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Map</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <div id="map-canvas"></div>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA96UVKWM82_YHJx1h9j9-NhacFbGANf1k&v=3.exp">
</script>
<script>
    $(function () {
        var _coord_json = '<?php echo $output['coord_json']?>';
        if (_coord_json != "" && _coord_json != null) {
            var _coord_arr = JSON.parse(_coord_json);
            var neighborhoods = [];
            var _first_lat;
            var _first_lng;
            for (var i = 0; i < _coord_arr.length; i++) {
                var x = _coord_arr[i].x;
                var y = _coord_arr[i].y;
                neighborhoods[i] = new google.maps.LatLng(x, y);
                if (i == 1) {
                    _first_lat = _coord_arr[i].x;
                    _first_lng = _coord_arr[i].y;
                }
            }

            var poly;
            var map;
            var markers = [];
            var lastIndex = -1;
            var iterator = 0;

            //位置图标
            if (_coord_arr.length == 1) {
                var icon1 = 'resource/image/google_icon_2.png';
            } else {
                var icon1 = 'resource/image/google_icon_1.png';
            }

            //轨迹点图标
            var icon2 = 'resource/image/google_icon_2.png';

            function initialize() {

                var mapOptions = {
                    zoom: 15,
                    center: new google.maps.LatLng(parseFloat(_first_lat, _first_lng))
                };


                map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                var polyOptions = {
                    strokeColor: '#000000',
                    strokeOpacity: 1.0,
                    strokeWeight: 3
                };
                poly = new google.maps.Polyline(polyOptions);
                poly.setMap(map);
                drop();
            }

            //此处调用了setTimeout函数，i*2000是指距离第一次执行的时间
            function drop() {
                for (var i = 0; i < neighborhoods.length; i++) {
                    setTimeout(function () {
                        addMarker();
                    }, i * 2000);
                }
            }

            function addMarker() {
                var path = poly.getPath();

                // Because path is an MVCArray, we can simply append a new coordinate
                // and it will automatically appear.

                path.push(neighborhoods[iterator]);
                if (iterator > 0) {
                    markers[iterator - 1].setIcon(icon2);
                }
                neighborhoods[iterator]

                // Add a new marker at the new plotted point on the polyline.
                markers.push(new google.maps.Marker({
                    position: neighborhoods[iterator],
                    title: '#' + path.getLength(),
                    map: map,
                    icon: icon1
                }));
                map.panTo(neighborhoods[iterator]);
                map.setCenter
                iterator++;
            }

            google.maps.event.addDomListener(window, 'load', initialize);
        }
    })
</script>
