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

    #search_text {
        width: 200px;
    }

    .container {
        margin-bottom: 60px!important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Search Id Number</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Search</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 1000px">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control input-search" style="min-width: 200px" id="search_text" name="search_text" value="" placeholder="Search for id-sn.">
                                <span class="input-group-btn">
                                    <a  class="btn btn-success btn-search" id="btn_search_list" onclick="btn_search_onclick();">
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
                    <h5 style="margin-top: 8px"><i class="fa fa-id-card-o"></i>Id Info</h5>
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
        var _search_text = $.trim($('#search_text').val());
        if (!_search_text) {
            return;
        }
        yo.dynamicTpl({
            tpl: "tools/id.info.v1",
            dynamic: {
                api: "tools",
                method: "getIdInfoBySn",
                param: {search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".id-info .content").html(_tpl);
            }
        });
    }
</script>