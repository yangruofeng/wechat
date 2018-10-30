<!--<link href="--><?php //echo ENTRY_COUNTER_SITE_URL; ?><!--/resource/css/member.css" rel="stylesheet" type="text/css"/>-->
<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=2" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.css?v=1" rel="stylesheet"/>
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

    #search_text {
        width: 200px;
    }

    .container {
        margin-bottom: 60px !important;
    }

    .basic-info.container {
        margin-top: 10px;
        margin-bottom: 10px!important;
    }

    .ibox-title {
        min-height: 40px;
        padding-top: 12px!important;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Search Client</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Search</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon" style="padding: 0;border: 0;display: inline">
                                    <select class="form-control" name="country_code" style="height: 34px;padding: 6px 12px">
                                        <?php echo tools::getCountryCodeOptions('855'); ?>
                                    </select>
                                </span>
                                <input type="text" class="form-control input-search" style="width: 200px" name="phone_number" value="" placeholder="">
                                <span class="input-group-btn">
                                    <a  class="btn btn-success btn-search" id="btn_search_list" onclick="search_onclick();">
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

        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js?v=1"></script>
<script>
    $(function(){
        search_onclick();
    })
    function search_onclick() {
        var _values = $('#frm_search_condition').getValues();
        if (!$.trim(_values.phone_number)) {
            return;
        }
        yo.dynamicTpl({
            tpl: "tools/member.info",
            dynamic: {
                api: "tools",
                method: "getMemberInfoByPhone",
                param: _values
            },
            callback: function (_tpl) {
                $(".id-info").html(_tpl);
            }
        });
    }

    function showCheckDetail(member_id, cert_type) {
        if(!member_id || !cert_type){
            return;
        }

        yo.loadData({
            _c: 'client',
            _m: 'getCheckDetailUrl',
            param: {member_id: member_id, cert_type: cert_type, source_mark: 'client_detail'},
            callback: function (_o) {
                if (_o.STS) {
                    var url = _o.DATA;
                    window.location.href = url;
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>