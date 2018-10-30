
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo getConf("api_google_map")?>&v=3.exp"></script>

<?php

$coor_data = $output['coordinates']?:array();
?>
<div>
    <div id="map-canvas" style="width: 800px;height: 500px;"></div>
</div>

<script>

    var line;

    var output_data = <?php echo json_encode($coor_data) ; ?>;

    console.log(output_data);

    var data = [];
    var center = {};

    var total_x = 0;
    var total_y = 0;
    // 处理坐标点
    var counter = 0;
    for( var x in output_data ){
        data.push({
            x:output_data[x]['x'],
            y:output_data[x]['y'],
            location:output_data[x]['location']
        });
        total_x += parseFloat(output_data[x]['x']);
        total_y += parseFloat(output_data[x]['y']);
        counter++;
    }
    center.x = total_x/counter;
    center.y = total_y/counter;

    console.log(data);
    console.log(center);

    function initialize() {
        var mapOptions = {
            center: new google.maps.LatLng(center.y, center.x),
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        //此处传入一个坐标的数组
        //图标在连接这两点的线上移动

        var lineCoordinates = [];
        var marker = [];

        for( var i in data ){
            lineCoordinates.push(new google.maps.LatLng(data[i].y,data[i].x));
            var _mark = new google.maps.Marker({
                position: new google.maps.LatLng(data[i].y,data[i].x),
                //icon:'resource/image/google_icon_2.png',
                title: data[i]['location']+'('+ data[i].x+','+data[i].y+')',
                map:map
            });
            marker.push(_mark);
        }

        var lineSymbol = {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 5,
            strokeColor: '#e40e0e'  // #393  #e40e0e
        };




        line = new google.maps.Polyline({
            path: lineCoordinates,
            icons: [{
                icon: lineSymbol,
                offset: '100%'
            }],
            strokeColor: '#0044cc',
            strokeOpacity: 1.0,
            strokeWeight: 3,
            map: map
        });

        animateCircle();
    }

    function animateCircle() {
        var count = 0;
        window.setInterval(function() {
            count = (count + 1) % 200;

            var icons = line.get('icons');
            icons[0].offset = (count / 2) + '%';
            line.set('icons', icons);
        }, 20);
    }

    google.maps.event.addDomListener(window, 'load', initialize);

</script>
