<?php $client_info=$output['client_info']?>
<div class="content" id="div_client_box" data-client_id="0" style="padding-bottom: 0">
    <div class="col-sm-6">
        <div class="input-group" style="width: 300px">
                                <span class="input-group-addon" style="padding: 0;border: 0;">
                                    <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                        <option <?php if($client_info['phone_country'] == 855){ echo 'selected';}?> value="855">+855</option>
                                        <option <?php if($client_info['phone_country'] == 66){ echo 'selected';}?> value="66">+66</option>
                                        <option <?php if($client_info['phone_country'] == 86){ echo 'selected';}?> value="86">+86</option>
                                    </select>
                                </span>
            <input type="text" class="form-control" id="s_phone" name="s_phone" value="<?php echo $client_info['phone_number']; ?>" placeholder="">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="btn_search" style="height: 30px;line-height: 14px;border-radius: 0">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                </span>
        </div>
        <div class="search-other">
            <img src="resource/img/member/phone.png">
            <img src="resource/img/member/qr-code.png">
            <img src="resource/img/member/bank-card.png">
        </div>
    </div>
    <div class="col-sm-6">
        <dl class="account-basic clearfix" style="margin-bottom: 0">
            <dt class="pull-left">
            <p class="account-head">
                <img id="member-icon" src="resource/img/member/bg-member.png" class="avatar-lg">
            </p>
            </dt>
            <dd class="pull-left margin-large-left">
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">Login Account</span>:
                    <span class="marginleft10" id="span_login-account"></span>
                </p>
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">Khmer Name</span>:
                    <span class="marginleft10" id="span_khmer-name"></span>
                </p>
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">English Name</span>:
                    <span class="marginleft10" id="span_english-name"></span>
                </p>
                <p class="text-small">
                    <span class="show pull-left base-name marginright3">Member State</span>:
                    <span class="marginleft10" id="span_member-state"></span>
                </p>
            </dd>
        </dl>
    </div>
</div>
<script>
    $(window).ready(function(){
        var phone = "<?php echo $client_info['phone_number'];?>";
        if(phone){
            search_click();
        }
        $('#btn_search').click(function () {
            search_click();
        })
    });
    $('#s_phone').bind('keydown',function(event){
        if(event.keyCode == "13") {
            search_click();
        }
    });
    function search_click() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('#s_phone').val();
        if (!$.trim(phone)) {
            return;
        }

        yo.loadData({
            _c: 'member',
            _m: 'getClientInfo',
            param: {country_code: country_code, phone: phone},
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $("#div_client_box").data("client_id",data.uid);
                    $('#member-icon').attr('src', data.member_icon ? data.member_icon : 'resource/img/member/bg-member.png');
                    $('#span_login-account').html(data.login_code);
                    $('#span_khmer-name').html(data.kh_display_name);
                    $('#span_english-name').html(data.display_name);
                    $('#span_member-state').html(data.member_state_text);
                    if(callback_after_search){
                        callback_after_search();
                    }
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

</script>