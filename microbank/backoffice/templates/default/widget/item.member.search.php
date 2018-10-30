<div class="basic-info container">
    <div class="ibox-title" style="background-color: #DDD">
        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Member Information</h5>
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
                        <button type="button" class="btn btn-default btn-search" id="btn_search" onclick="btn_search_member_onclick()" style="height: 30px;line-height: 14px;border-radius: 0">
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
<script>

    function btn_search_member_onclick() {
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
                    $('#member-grade').html(data.grade_code);
                    $('#id_sn').html(data.id_sn);
                    $("#member_state").html(data.member_state)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>