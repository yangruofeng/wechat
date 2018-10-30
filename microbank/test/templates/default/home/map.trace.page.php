
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA96UVKWM82_YHJx1h9j9-NhacFbGANf1k&v=3.exp"></script>

<div>
    <div id="map-canvas" style="width: 800px;height: 500px;"></div>
</div>
<script>





    var neighborhoods = [new google.maps.LatLng(52.511467, 13.447179), new google.maps.LatLng(52.549061, 13.422975), new google.maps.LatLng(52.497622, 13.396110), new google.maps.LatLng(52.517683, 13.394393)];
    var poly;
    var map;
    var markers = [];
    var lastIndex=-1;
    var iterator = 0;
    //小车图标
    var icon1 = 'resource/image/google_icon_2.png';
    //轨迹点图标
    var icon2 = 'resource/image/google_icon_2.png';

    function initialize() {

        var mapOptions = {
            zoom : 15,
            // Center the map on Chicago, USA.
            center : new google.maps.LatLng(parseFloat(52.511467, 13.447179))
        };


        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

        var polyOptions = {
            strokeColor : '#000000',
            strokeOpacity : 1.0,
            strokeWeight : 3
        };
        poly = new google.maps.Polyline(polyOptions);
        poly.setMap(map);
        drop();
    }

    //此处调用了setTimeout函数，i*2000是指距离第一次执行的时间
    function drop() {
        for (var i = 0; i < neighborhoods.length; i++) {
            setTimeout(function() {
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
        //neighborhoods[iterator]
        // Add a new marker at the new plotted point on the polyline.
        markers.push(new google.maps.Marker({
            position : neighborhoods[iterator],
            title : '#' + path.getLength(),
            map : map,
            icon : icon1
        }));
        map.panTo(neighborhoods[iterator]);
        //map.setCenter
        iterator++;
    }

    google.maps.event.addDomListener(window, 'load', initialize);

</script>
