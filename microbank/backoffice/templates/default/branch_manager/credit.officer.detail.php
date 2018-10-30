<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/client.css?v=3" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        height: 30px;
        min-width: 80px;
        padding: 5px 12px;
        border-radius: 0px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        background-color: #FFF;
        overflow: hidden;
    }

    .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    .activity-list .item {
        margin-top: 0;
        padding: 10px 20px 10px 15px;;
    }

    .activity-list .item div > span:first-child {
        font-weight: 500;
    }

    .activity-list .item span.check-state {
        float: right;
        font-size: 12px;
        margin-left: 5px;
    }

    .activity-list .item span.check-state .fa-check {
        font-size: 18px;
        color: green;
    }

    .activity-list .item span.check-state .fa-question {
        font-size: 18px;
        color: red;
        padding-right: 5px;
    }

    #cbcModal .modal-dialog {
        margin-top: 10px!important;
    }

    #cbcModal .modal-dialog input{
        height: 30px;
    }

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
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'creditOfficer', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="co-assets-wrap">
            <div class="content">
                <div class="panel-tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="<?php echo $output['show_tab'] == 'co_co_cash_on_hand' ? 'active' : ''?>">
                            <a href="#co_cash_on_hand" aria-controls="co_co_cash_on_hand" role="tab" data-toggle="tab"><?php echo 'Cash on Hand';?></a>
                        </li>
                        <li role="presentation" class="<?php echo $output['show_tab'] == 'co_client_list' ? 'active' : ''?>">
                            <a href="#co_client_list" aria-controls="co_client_list" role="tab" data-toggle="tab"><?php echo 'Client List';?></a>
                        </li>
                        <li role="presentation" class="<?php echo $output['show_tab'] == 'co_activities' ? 'active' : ''?>">
                            <a href="#co_activities" aria-controls="co_activities" role="tab" data-toggle="tab"><?php echo 'Activities';?></a>
                        </li>
                        <li role="presentation" class="<?php echo $output['show_tab'] == 'activities_track' ? 'active' : ''?>">
                            <a href="#activities_track" aria-controls="activities_track" role="tab" data-toggle="tab"><?php echo 'Activities Track';?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane <?php echo $output['show_tab'] == 'co_cash_on_hand' ? 'active' : ''?>" id="co_cash_on_hand">
                            <div class="contract-wrap">
                                <?php $cash = $output['cash']; ?>
                                <table class="table">
                                    <thead>
                                        <tr class="table-header">
                                            <td>Currency</td>
                                            <td>Balance</td>
                                        </tr>
                                    </thead>
                                    <tbody class="table-body">
                                    
                                        <?php foreach ($cash as $k => $v) {?>
                                            <tr>
                                                <td><?php echo $k;?></td>
                                                <td><?php echo $v;?></td>
                                            </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane <?php echo $output['show_tab'] == 'co_client_list' ? 'active' : ''?>" id="co_client_list">
                            <div class="contract-wrap">
                                <?php $client_list = $output['client_list']; ?>
                                <?php if(count($client_list) > 0){?>
                                    <table class="table">
                                        <thead>
                                            <tr class="table-header">
                                                <td>CID</td>
                                                <td>Login Code</td>
                                                <td>Display Nname</td>
                                                <td>Phone</td>
                                            </tr>
                                        </thead>
                                        <tbody class="table-body">
                                        
                                            <?php foreach ($client_list as $k => $v) {?>
                                                <tr>
                                                    <td><?php echo $v['obj_guid'];?></td>
                                                    <td><?php echo $v['login_code'];?></td>
                                                    <td><?php echo $v['display_name'];?></td>
                                                    <td><?php echo $v['phone_id'];?></td>
                                                </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                <?php }else{ ?>
                                    <div class="no-record">No Record.</div>
                                <?php } ?>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane <?php echo $output['show_tab'] == 'co_activities' ? 'active' : ''?>" id="co_activities">
                            <div class="contract-wrap">
                                <?php $track_list = $output['track_list'];?>
                                <?php if(count($track_list) > 0){?>
                                    <div class="track-wrap">
                                        <?php foreach ($track_list as $k => $v) {?>
                                            <div class="title"><?php echo dateFormat($k);?><a target="_blank" href="<?php echo getBackOfficeUrl('branch_manager','showUserDayTrace',array(
                                                    'user_id' => $output['uid'],
                                                    'date' => $k
                                                )); ?>">【Map】</a></div>
                                            <div class="track-content">
                                                <?php foreach ($v as $ck => $cv) {?>
                                                    <div class="track-item">
                                                        <i class="node-icon"></i>
                                                        <span class="time"><?php echo explode(' ', timeFormat($cv['sign_time']))[1];?></span>
                                                        <span class="coord_x"><em>Longitude: </em><?php echo $cv['coord_x'];?></span>
                                                        <span class="coord_y"><em>Latitude:</em><?php echo $cv['coord_y'];?></span>
                                                        <span class="location"><?php echo $cv['location'];?></span>
                                                    </div>
                                                <?php }?>
                                            </div>
                                        <?php }?>
                                    </div>
                                <?php }else{ ?>
                                    <div class="no-record">No Record.</div>
                                <?php } ?>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane <?php echo $output['show_tab'] == 'activities_track' ? 'active' : ''?>" id="activities_track">
                            <div class="contract-wrap">
                                <form class="form-inline" id="frm_search_condition">
                                    <table  class="search-table">
                                        <tr>
                                            <td>
                                                <?php include(template("widget/inc_condition_datetime")); ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default" onclick="search_onclick()">Search</button>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                                <div class="google-map">
                                    <div id="map-canvas"></div>
                                    <div id="no_record_div" style="width: 150px;height:500px;margin-top: 10px;display: none">
                                        <?php include(template(":widget/no_record")); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>       
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA96UVKWM82_YHJx1h9j9-NhacFbGANf1k&v=3.exp">
</script>
<script>
    var _user_id = '<?php echo $output['uid']?>';
    $(function () {
        var _coord_json = '<?php echo $output['coord_json']?>';
        var _coord_arr = JSON.parse(_coord_json);
        if (_coord_arr.length > 0) {
            $('#map-canvas').show();
            $('#no_record_div').hide();

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
                var icon1 = 'resource/image/google_icon_.png';
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
        } else {
            $('#map-canvas').hide();
            $('#no_record_div').show();
        }
    })

    function search_onclick() {
        var _values = $('#frm_search_condition').getValues();
        var _url = '<?php echo getUrl('branch_manager','showCreditOfficerDetail',array('uid'=>$output['uid']), false, BACK_OFFICE_SITE_URL)?>&start_date=' + _values.date_start + '&end_date=' + _values.date_end + '&show_tab=activities_track';
        window.location.href = _url;
    }
</script>