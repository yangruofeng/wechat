<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=1" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Disbursement</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('report_disbursement', 'disbursement', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Loan Disbursement</span></a></li>
                <li><a href="<?php echo getUrl('report_disbursement', 'loanCollectionCategory', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Loan Collection By Category</span></a></li>
                <li><a class="current"><span>Payment In Arrears</span></a></li>
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
                                <select id="branch_id" class="form-control" name="branch_id" onclick="btn_search_onclick();">
                                    <option value="">All Branch</option>
                                        <?php foreach ($output['branch_list'] as $branch) { ?>
                                            <option value="<?php echo $branch['uid'];?>"><?php echo $branch['branch_name'];?></option>
                                        <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select id="choose_currency" class="form-control" name="currency" onclick="btn_search_onclick();">
                                    <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                        <option value="<?php echo $key;?>"><?php echo $currency;?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="business-content">
            <div class="business-list"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        var _values = $('#frm_search_condition').getValues();
        yo.dynamicTpl({
            tpl: "report_disbursement/payment.arrear.list",
            dynamic: {
                api: "report_disbursement",
                method: "getPaymentInArrearsList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>