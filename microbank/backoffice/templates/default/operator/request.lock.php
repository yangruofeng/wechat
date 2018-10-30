<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group{
        margin-bottom: 20px;
    }

    .container{
        width: 800px!important;
    }

    .mincontent{
        padding:15px
    }

    .idnum{
        margin-top: 5px;
    }

    .redstar{
        color: red;
        font-size: 14px;
        padding-right: 1px;
    }

    .ibox-title{
        padding-top: 10px;
        height: 40px!important;
        min-height: 0px!important;
    }

    .btn {
        border-radius: 0;
        min-width: 80px;
    }
</style>
<?php $client_info = $output['client_info'];?>

<?php
$memberStateLang = enum_langClass::getMemberStateLang();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Lock </h3>
            <ul class="tab-base">
                <li><a class="current"><span>Request</span></a></li>
                <li><a href="<?php echo getUrl('operator', 'lockList', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Lock List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="collection-div">
        <div class="basic-info container">
            <div class="ibox-title" style="background-color: #DDD">
                <h5 style="color: black"><i class="fa fa-id-card-o"></i>Check The Basic Information</h5>
            </div>
            <div class="content">
                <div class="col-sm-6 mincontent">
                    <form class="form-inline input-search-box">
                        <div class="input-group" style="width: 300px">
                            <span class="input-group-addon" style="padding: 0;border: 0;">
                                <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                    <option value="855" <?php echo $client_info['phone_country'] == 855 ? 'selected' : ''?>>+855</option>
                                    <option value="66" <?php echo $client_info['phone_country'] == 66 ? 'selected' : ''?>>+66</option>
                                    <option value="86" <?php echo $client_info['phone_country'] == 86 ? 'selected' : ''?>>+86</option>
                                </select>
                            </span>
                            <input type="text" class="form-control input-search" id="phone" name="phone" value="">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-search" id="btn_search" style="height: 30px;line-height: 14px;border-radius: 0">
                                    <i class="fa fa-search"></i>
                                    Search
                                </button>
                            </span>
                        </div>
                    </form>

                    <p class="idnum" style="margin-top: 15px">
                        <span class="show pull-left base-name marginright3  redstar">*  Identity Number</span>:
                        <span class="marginleft10  redstar" id="id_sn"></span>
                    </p>

                    <p class="idnum">
                        <span class="show pull-left base-name marginright3"><span class="redstar">*</span> Member States</span>:
                        <span class="marginleft10 redstar" id="member_state"></span>
                    </p>

                </div>
                <div class="col-sm-6 mincontent">
                    <dl class="account-basic clearfix">
                        <dt class="pull-left">
                        <p class="account-head">
                            <img id="member-icon" src="resource/image/bg-member.png" class="avatar-lg">
                        </p>
                        </dt>
                        <dd class="pull-left margin-large-left">
                            <input type="hidden" id="client_id" name="client_id" value="">
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Login Account</span>:
                                <span class="marginleft10" id="login-account"></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Khmer Name</span>:
                                <span class="marginleft10" id="khmer-name"></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">English Name</span>:
                                <span class="marginleft10" id="english-name"></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Member Grade</span>:
                                <span class="marginleft10" id="member-grade"></span>
                            </p>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-sm-12 form-group" style="text-align: center;margin-top: 20px">
            <a class="btn btn-default" href="<?php echo getUrl('operator', 'requestLock', array(), false, BACK_OFFICE_SITE_URL); ?>">
                <i class="fa fa-refresh"></i>
                <?php echo 'Reset' ?>
            </a>
            <button type="button" class="btn btn-danger" id="btn-lock">
                <i class="fa fa-lock"></i>
                <?php echo 'Lock' ?>
            </button>
        </div>
    </div>
</div>


<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        $('#btn_search').click(function () {
            search_click()
        })
    })

    function search_click() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('#phone').val();
        if (!$.trim(phone)) {
            return;
        }

        yo.loadData({
            _c: 'operator',
            _m: 'getClientInfo',
            param: {country_code: country_code, phone: phone},
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $('#member-icon').attr('src', data.member_icon ? data.member_icon : 'resource/image/bg-member.png');
                    $('#client_id').val(data.uid);
                    $('#login-account').html(data.login_code);
                    $('#khmer-name').html(data.kh_display_name);
                    $('#english-name').html(data.display_name);
                    $('#id_sn').html(data.id_sn);
                    $("#member_state").html(data.member_state);
                    $("#member-grade").html(data.grade_code);
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('.btn-danger').click(function () {
        var uid = $("#client_id").val();
        var state = $("#member_state").html()
        if (state == 'Locking' || state == 'Cancel') {
            alert("State Inappropriate")
            return;
        }
        if (uid > 0) {
            yo.loadData({
                _c: 'operator',
                _m: 'lockMember',
                param: {uid: uid},
                callback: function (_o) {
                    if (_o.STS) {
                        alert(_o.MSG,1,function(){
                            window.location.href = "<?php echo getUrl('operator', 'requestLock', array(), false, BACK_OFFICE_SITE_URL);?>"
                        });
                    } else {
                        alert(_o.MSG);
                    }
                }
            });
        } else {
            alert("please Input Phone Number");
        }
    })

</script>



