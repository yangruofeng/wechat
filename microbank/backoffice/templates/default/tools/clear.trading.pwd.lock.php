<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        border-radius: 0px;
        margin-bottom: 10px;
        height: 34px;
    }

    .search-table input {
        height: 34px !important;
    }

    .container {
        margin-bottom: 60px!important;
    }

    #member-icon {
        max-width: 100px;
        max-height: 100px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Clear Password Lock</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Search</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 700px">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon" style="padding: 0;border: 0;vertical-align: top">
                                    <select class="form-control" name="country_code" style="min-width: 100px;height: 34px">
                                        <?php echo tools::getCountryCodeOptions(855);?>
                                    </select>
                                </span>
                                <input type="text" class="form-control" id="phone" name="phone" value="" placeholder="">
                                <span class="input-group-btn">
                                    <a  class="btn btn-success" id="btn_search_list" onclick="btn_search_onclick();">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </a>
                                </span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="id-info">
            <div class="basic-info">
                <div class="ibox-title" style="min-height: 36px;">
                    <h5 style="margin-top: 8px"><i class="fa fa-id-card-o"></i>Information</h5>
                </div>
                <div class="content" style="padding: 0;min-height: 40px">
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script>

    function btn_search_onclick() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('#phone').val();
        if (!$.trim(phone)) {
            return;
        }

        yo.dynamicTpl({
            tpl: "tools/member.input.pwd.info",
            dynamic: {
                api: "tools",
                method: "getMemberTradingPwdInfo",
                param: {country_code: country_code, phone: phone}
            },
            callback: function (_tpl) {
                $(".id-info .content").html(_tpl);
            }
        });
    }

    function clear_times(member_id) {
        if (!member_id) {
            return;
        }
        yo.loadData({
            _c: "tools",
            _m: "clearMemberErrorTradingPwdTimes",
            param: {member_id: member_id},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>