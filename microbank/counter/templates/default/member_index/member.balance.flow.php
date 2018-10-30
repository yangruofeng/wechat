<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        padding: 5px 12px;
    }
</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>"/>
                <input type="hidden" name="currency" value="<?php echo $output['currency']?>"/>
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

        <div class="business-content" style="margin-bottom: 40px">
            <div style="font-size: 14px;font-weight: 600;margin-bottom: 10px">
                <span>Member Code : <?php echo $output['member_info']['login_code']?></span>
                <span style="margin-left: 30px">Currency : <?php echo $output['currency']?></span>
            </div>
            <div class="business-list">

            </div>
        </div>
        <div style="margin-bottom: 40px;text-align: center">
            <a class="btn btn-default" style="min-width: 80px;margin-left: -40px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
            <a class="btn btn-danger" style="min-width: 80px;margin-left: 10px" onclick="print_member_flow()"><i class="fa fa-check"></i><?php echo 'Print' ?></a>
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
        yo.dynamicTpl({
            tpl: "member_index/member.balance.list",
            control:'counter_base',
            dynamic: {
                api: "member_index",
                method: "getMemberFlow",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
    function print_member_flow() {
        var member_id =  $("input[name='member_id']").val();
        var currency =  $("input[name='currency']").val();
        var date_start =  $("input[name='date_start']").val();
        var date_end =  $("input[name='date_end']").val();
//        window.location.href = "<?php //echo getUrl('print_form', 'printMemberFlow', array(), false, ENTRY_COUNTER_SITE_URL)?>//&member_id="+ member_id + "&currency="+ currency+ "&date_start="+ date_start+ "&date_end="+ date_end
        window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printMemberFlow', array(), false, ENTRY_COUNTER_SITE_URL)?>&member_id="+ member_id + "&currency="+ currency+ "&date_start="+ date_start+ "&date_end="+ date_end);
    }
</script>