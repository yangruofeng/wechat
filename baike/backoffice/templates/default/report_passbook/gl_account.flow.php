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
<div class="page">

    <div class="fixed-bar">
        <div class="item-title">
            <h3><?php if( $output['report_type'] == 'income' ){ echo 'Income Statement';}else{ echo 'Balance Sheet';} ?></h3>
            <ul class="tab-base">
                <?php $op = 'balanceSheet'; if($output['main_url']){$op = $output['main_url'];}?>
                <li><a  href="<?php echo getUrl('report_passbook',$op,array(),false,BACK_OFFICE_SITE_URL); ?>"><span>Main</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <?php $gl_account = $output['gl_passbook'];?>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                        <tr>
                            <td>
                                <?php include(template("widget/inc_condition_datetime")); ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-info report-back" onclick="javascript:history.go(-1);">
                                    Back
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="col-sm-12 user-info">
            <?php if($gl_account){?>
                <div class="col-sm-3">
                    Book Code: <label for=""><?php echo $gl_account['book_code'];?></label>
                </div>
                <div class="col-sm-3">
                    Book Name: <label for=""><?php echo $gl_account['book_name'];?></label>
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

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        _values.book_id = '<?php echo $_GET['book_id'];?>';
        _values.currency = '<?php echo $_GET['currency'];?>';
        _values.type = '<?php echo $_GET['type'];?>';
        yo.dynamicTpl({
            tpl: "report_passbook/gl_account.flow.list",
            dynamic: {
                api: "report_passbook",
                method: "getPassbookFlow",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
