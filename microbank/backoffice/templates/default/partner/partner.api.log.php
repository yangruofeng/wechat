<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
<style>
    .total{
        background-color: red !important;
    }
    .total td{
        font-size: 18px;
        color:#fff;
    }
</style>
<?php
 $partner_info = $output['partner_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3><?php echo "Partner - ".$partner_info['partner_name'];?></h3>
            <ul class="tab-base">
                <li><a class="current">
                        <span style="cursor: pointer" onclick="javascript:history.go(-1);"> BACK </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <input type="hidden" name="partner_id" value="<?php echo $partner_info['uid'];?>">
                <table class="search-table">
                    <tbody>
                        <tr>
                            <td>
                                <?php include(template("widget/inc_condition_datetime")); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="col-sm-12 user-info">
            <?php if($partner_info){?>
                <div class="col-sm-3">
                    Partner Code: <label for=""><?php echo $partner_info['partner_code'];?></label>
                </div>
                <div class="col-sm-3">
                    Partner Name: <label for=""><?php echo $partner_info['partner_name'];?></label>
                </div>
            <?php }else{?>
                <div class="tip">* The gl account does not exist or has been deleted</div>
            <?php }?>
        </div>
        <div class="business-content">
            <div class="business-list">

            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    var select = $('select[name=currency]').length;
    if(select){
        $("#choose_currency").change(function () {
            if (typeof(btn_search_onclick) != "undefined") {
                btn_search_onclick(1, 50);
            }
        });
    }


    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        yo.dynamicTpl({
            tpl: "partner/partner.api.log.list",
            dynamic: {
                api: "partner",
                method: "getApiLog",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
