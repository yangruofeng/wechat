<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .currency span {
        padding-right: 10px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Daily Data</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Navigator</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="padding-top: 20px ">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
                        <td>
                            <div class="input-group">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                                                onclick="btn_search_onclick();">
                                            <i class="fa fa-search"></i>
                                            <?php echo 'Search'; ?>
                                            <tton>
                                    </span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="data-content"></div>
    </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });
    function btn_search_onclick() {
        var _values = $('#frm_search_condition').getValues();
        $(".data-content").waiting();
        yo.dynamicTpl({
            tpl: "data_center_daily/index.data",
            dynamic: {
                api: "data_center_daily",
                method: "getInfo",
                param: _values
            },
            callback: function (_tpl) {
                $(".data-content").unmask();
                $(".data-content").html(_tpl);
            }
        });
    }

</script>


