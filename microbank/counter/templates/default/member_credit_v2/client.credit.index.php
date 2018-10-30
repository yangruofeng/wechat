<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css?v=6" rel="stylesheet" type="text/css"/>
<style>
    .record-base-table>tbody>tr>td{
        padding: 2px;
    }
    .image-item{
        position: relative;
    }
    .image-item img{
        position: relative;
    }
    .image-item .a-delete{
        right: -10px;top: -10px;position: absolute;width: 20px;height: 20px;border-radius: 60%;background-color: red;color: #ffffff
    }
    .image-item .a-delete .fa-close{
        top:3px;position: absolute;left: 6px
    }


    #notCheckPassword,.notCheckPassword{
        width: 20px;
        position: absolute;
        top: 6px;
        right: 18px;
    }

    #checkPasswordFailure,.checkPasswordFailure{
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 18px;
    }

    #checkPasswordDone,.checkPasswordDone{
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 18px;
    }

    .authorize_input{
        position: relative;
    }

</style>
<?php
if(!$data) $data=$output['data'];
$detail = $data['detail'];$client_authorized_history = $data['client_authorized_history'];
$asset_enum=(new certificationTypeEnum())->Dictionary();
?>

<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container"  style="max-width: 1300px">
        <div class="row">
            <div class="col-sm-12">
                <div class="basic-info">
                    <?php include_once(template("widget/item.member.summary.v2"))?>
                </div>

            </div>
        </div>
        <div class="row" style="padding-top: 10px">
            <div class="col-sm-6">
                <?php include(template("member_credit_v2/client.credit.part.summary"))?>
            </div>
            <div class="col-sm-6">
                <?php include(template("member_credit_v2/client.credit.part.relative"))?>
            </div>
        </div>
        <div class="row" style="margin: 0;padding-top: 10px">
            <div class="panel-tab custom-panel-tab">
                <ul class="nav nav-tabs record-tabs" role="tablist">
                    <li role="presentation" class="authorize-li active">
                        <a href="#tab_authorize" aria-controls="tab_authorize" role="tab" data-toggle="tab"><?php echo 'Authorize';?></a>
                    </li>

                    <li role="presentation" class="draft-li">
                        <a href="#tab_interest" aria-controls="tab_interest" role="tab" data-toggle="tab"><?php echo 'Interest & Currency';?></a>
                    </li>
                    <li role="presentation" class="history-li">
                        <a href="#tab_history" aria-controls="tab_history" role="tab" data-toggle="tab"><?php echo 'Contract List';?></a>
                    </li>
                    <li role="presentation" class="tab-detail-li" style="display: none;">
                        <a href="#tab_detail" aria-controls="tab_detail" role="tab" data-toggle="tab"><?php echo 'Authorized Contract Detail';?></a>
                    </li>
                    <li role="presentation" class="teller-history-li">
                        <a href="#tab_teller_history" aria-controls="tab_teller_history" role="tab" data-toggle="tab"><?php echo 'Teller History';?></a>
                    </li>
                </ul>
                <div class="tab-content" style="background-color: #FFFFFF">
                    <?php include(template("member_credit_v2/client.credit.part.authorize"))?>
                    <?php include(template("member_credit_v2/client.credit.part.currency.interest"))?>
                    <?php include(template("member_credit_v2/client.credit.part.history"))?>
                    <div role="tabpanel" class="tab-pane tab-detail-pane" id="tab_detail" style="display: none;">
                        <div class="contract-detail"></div>
                    </div>
                    <div role="tabpanel" class="tab-pane tab-teller-history-pane authorizing-history" id="tab_teller_history">
                        <div class="teller-history-list">

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script>

    $(document).ready(function () {
        btn_search_onclick(1,20);
    });
    //  分页展示贷款申请列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".authorizing-history").data('pageNumber');
        if (!_pageSize) _pageSize = $(".authorizing-history").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".authorizing-history").data("pageNumber", _pageNumber);
        $(".authorizing-history").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "member_credit_v2/credit.history.list",
            control:'counter_base',
            dynamic: {
                api: "member_credit",
                method: "getCreditHistoryList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".teller-history-list").html(_tpl);
            }
        });
    }


</script>