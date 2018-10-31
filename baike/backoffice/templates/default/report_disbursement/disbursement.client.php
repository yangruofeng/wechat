<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Disbursement</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Loan Disbursement</span></a></li>
                <li><a href="<?php echo getUrl('report_disbursement', 'loanOutstandingProvince', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Outstanding Province</span></a></li>
                <li><a href="<?php echo getUrl('report_disbursement', 'loanOutstandingGender', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Outstanding Gender</span></a></li>
                <li><a href="<?php echo getUrl('report_disbursement', 'loanOutstandingProduct', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Outstanding Product</span></a></li>
                <li><a href="<?php echo getUrl('report_disbursement', 'loanOutstandingPurpose', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Outstanding Purpose</span></a></li>
                <li><a href="<?php echo getUrl('report_disbursement', 'loanCollectionCategory', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Loan Collection</span></a></li>
                <li><a href="<?php echo getUrl('report_disbursement', 'paymentInArrears', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Payment In Arrears</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <select id="branch_id" class="form-control" name="branch_id" onchange="select_branch(this);">
                                <option value="0">Select Branch</option>
                                <?php foreach ($output['branch_list'] as $branch) { ?>
                                    <option value="<?php echo $branch['uid'];?>"><?php echo $branch['branch_name'];?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <select id="co_id" class="form-control" name="co_id" onchange="btn_search_onclick();" disabled>
                                <option value="0">Select Co</option>
                                <?php foreach ($output['branch_list'] as $branch) { ?>
                                    <option value="<?php echo $branch['uid'];?>"><?php echo $branch['branch_name'];?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
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
    function btn_search_onclick() {
        var _values = $('#frm_search_condition').getValues();
        if (!_values.co_id) {
            return;
        }
        yo.dynamicTpl({
            tpl: "report_disbursement/disbursement.client.list",
            dynamic: {
                api: "report_disbursement",
                method: "getDisbursementClientLoanList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function select_branch(_e) {
        var _branch_id = $(_e).val();
        if (_branch_id == 0) {
            $('#co_id').html('<option value="0">Select Co</option>');
            $('#co_id').attr('disabled', true);
        } else {
            yo.dynamicTpl({
                tpl: "report_disbursement/co.option",
                dynamic: {
                    api: "report_disbursement",
                    method: "getCoList",
                    param: {branch_id: _branch_id}
                },
                callback: function (_tpl) {
                    $('#co_id').html(_tpl);
                    $('#co_id').attr('disabled', false);
                }
            });
        }
    }
</script>
