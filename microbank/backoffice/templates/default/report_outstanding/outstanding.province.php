<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=1" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Disbursement</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Outstanding Province</span></a></li>
                <li><a href="<?php echo getUrl('report_outstanding', 'loanOutstandingGender', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Outstanding Gender</span></a></li>
                <li><a href="<?php echo getUrl('report_outstanding', 'loanOutstandingProduct', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Outstanding Product</span></a></li>
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
                                <select id="branch_id" class="form-control" name="branch_id" onclick="btn_search_onclick();">
                                    <option value="">All Branch</option>
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
            tpl: "report_outstanding/outstanding.province.list",
            dynamic: {
                api: "report_outstanding",
                method: "getLoanOutstandingProvinceList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
