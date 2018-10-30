
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA96UVKWM82_YHJx1h9j9-NhacFbGANf1k&v=3.exp"></script>

<div>
    <div id="map-canvas" style="width: 800px;height: 500px;"></div>
</div>
<script>

    var data = [
        {x:120,y:30},
        {x:100,y:32},
        {x:130,y:10},
        {x:90,y:40},
        {x:110,y:35}
    ];

    // lat 纬度
    var mapOptions = {
        center: new google.maps.LatLng(31, 110),
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);


    var coordinates = [];
    //var bounds = new google.maps.LatlngBounds();
    for( var i in data ){
        var point =  new google.maps.LatLng(data[i].y,data[i].x);
        //bounds.extend(point);
        coordinates.push(point);

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(data[i].y,data[i].x),
            icon:'resource/image/google_icon_2.png',
            map:map
        });
    }

    var line = new google.maps.Polyline({
        path:coordinates,
        strokeColor: '#0000FF',
        strokeOpacity: 0.5,
        strokeWeight: 4,
        map:map
    });

    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(10,130),
        icon:'resource/image/google_icon_2.png',
        map:map
    });

</script>
