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
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Income Statement</h3>
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
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-money"></i>&nbsp;Incomes &amp; Expenses &amp; Commons &amp; Costs</h5>
                    </div>
                    <div class="business-content">
                        <div class="business-list">

                        </div>
                    </div>
                </div>
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
            tpl: "report_passbook/profit.index.list",
            dynamic: {
                api: "report_passbook",
                method: "getIncomeStatementData",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
