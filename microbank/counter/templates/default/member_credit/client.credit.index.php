<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css?v=6" rel="stylesheet" type="text/css"/>
<style>
    .text-small {
        margin-bottom: 0;
    }
</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container">
        <div class="row" style="max-width: 1300px">
            <div class="col-sm-12 col-md-10 col-lg-7">
                <div class="basic-info">
                    <?php include_once(template("widget/item.member.summary.v2"))?>
                </div>
                <div class="scene-photo">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Client Credit Record</h5>
                    </div>
                    <div class="business-content">
                        <div class="credit-list">
                            <?php include(template("member_credit/client.credit.info"))?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-10 col-lg-5">
                <div class="authorizing-history">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>History</h5>
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
    //  分页展示贷款申请列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "member_credit/credit.history.list",
            control:'counter_base',
            dynamic: {
                api: "member_credit",
                method: "getCreditHistoryList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }


</script>