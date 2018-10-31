<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=1" rel="stylesheet" type="text/css"/>
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
$nbsp = '&nbsp;';
$nbsp_len = strlen($nbsp);
$data = $output['data'];
$asset_data = $data['asset'];
$liabilities_data = $data['liabilities'];
$currency = (new currencyEnum())->toArray();
?>

<div class="page">

    <div class="fixed-bar">
        <div class="item-title">
            <h3>Balance Sheet</h3>
            <ul class="tab-base">
                <li><a  class="current"><span>Main</span></a></li>
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
                            <?php include(template("widget/inc_condition_datetime_simple")); ?>
                        </td>
                        <td>
                            <div class="input-group">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                                                onclick="btn_search_onclick();">
                                            <i class="fa fa-search"></i>
                                            <?php echo 'Search'; ?>
                                        </button>
                                    </span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
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

    function btn_search_onclick() {
        var _values = $('#frm_search_condition').getValues();
        yo.dynamicTpl({
            tpl: "report_passbook/balance_sheet.list",
            dynamic: {
                api: "report_passbook",
                method: "getBalanceSheetData",
                param: _values
            },
            ajax: {
                timeout: 60000
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
                adjustTrLength();
            }
        });
    }
</script>
