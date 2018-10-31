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

    .table > tbody > tr > td {
        background-color: #ffffff;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Search Asset Id</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Search</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 800px">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon" style="padding: 0;border: 0;display: inline;">
                                    <select class="form-control" name="asset_type" style="height: 34px;padding: 6px 12px">
                                        <?php foreach ($output['asset_type'] as $key => $name) { ?>
                                            <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                                <input type="text" class="form-control input-search" style="min-width: 200px" id="search_text" name="search_text" value="" placeholder="Search for asset-sn.">
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

        </div>
    </div>
</div>
<script>
    function btn_search_onclick() {
        var _search_text = $.trim($('#search_text').val());
        var _asset_type = $('select[name="asset_type"]').val();
        if (!_search_text) {
            return;
        }
        yo.dynamicTpl({
            tpl: "tools/asset.info",
            dynamic: {
                api: "tools",
                method: "getAssetInfoBySn",
                param: {search_text: _search_text, asset_type: _asset_type}
            },
            callback: function (_tpl) {
                $(".id-info").html(_tpl);
            }
        });
    }
</script>