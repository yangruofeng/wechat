<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .text-small {
        margin-bottom: 0;
    }

    .btn {
        min-width: 80px;
        border-radius: 0px;
    }

    .search-table input {
        height: 34px !important;
    }

    #search_text {
        width: 200px;
    }

    #btn_new {
        margin-left: 20px;
    }

    .info-div {
        margin-bottom: 60px;
    }

    .info-div .content {
        padding: 5px 0 0;
    }

    .info-div .content .table td {
        padding: 8px 20px;
    }

    .info-div .content .table.contract-table td:nth-child(1) {
        width: 20%;
    }

    .info-div .content .table.contract-table td:nth-child(2) {
        width: 30%;
    }

    .info-div .content .table.contract-table td:nth-child(3) {
        width: 20%;
    }

    .info-div .content .table.contract-table td:nth-child(4) {
        width: 30%;
    }

    .info-div .content .table td a {
        margin-left: 10px;
    }

    .info-div .content .table td label {
        margin-bottom: 0px;
    }

    .custom-btn-group {
        float: inherit;
    }

    .loan-exp-wrap {
        filter: alpha(Opacity=0);
        opacity: 0;
        z-index: 99;
        -moz-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -o-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -webkit-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        transition: top .2s ease-in-out, opacity .2s ease-in-out;
        visibility: hidden;
        position: absolute;
        top: 37px;
        right: 3px;
        padding: 7px 10px;
        border: 1px solid #ddd;
        background-color: #f6fcff;
        color: #5b9fe2;
        font-size: 12px;
        font-family: Arial, "Hiragino Sans GB", simsun;
    }

    .loan-exp-wrap .pos {
        position: relative;
    }

    .triangle-up {
        background-position: 0 -228px;
        height: 8px;
        width: 12px;
        display: block;
        position: absolute;
        top: -15px;
        right: 240px;
        bottom: auto;
    }

    .triangle-up {
        background-image: url(./resource/img/member/common-slice-s957d0c8766.png);
        background-repeat: no-repeat;
        overflow: hidden;
    }

    .loan-exp-table .t {
        color: #a5a5a5;
        font-size: 12px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a {
        color: #000;
        font-size: 14px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a .y {
        color: #ea544a;
    }

    tr.t td, tr.a td {
        padding: 4px 0px !important;
    }

    .contract-btn .btn {
        padding: 5px 7px;
    }

    #repaymentModal .modal-dialog {
        margin-top: 10px !important;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }

    .operation {
        margin-top: -30px;
        display: none;
    }

    .verify-state .btn.active {
        color: #fff;
        background-color: #5cb85c;
        border-color: #4cae4c;
    }
</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container" style="max-width: 1300px;margin-bottom: 40px">
        <div class="col-sm-12 col-md-10 col-lg-7" style="padding-left: 0px">
            <div class="basic-info">
                <?php include_once(template("widget/item.member.summary.v2"))?>
                <?php $client_info=$output['client_info'];$credit_balance=$output['credit_balance'];?>

                <input type="hidden" id="credit_state" value="<?php echo $client_info['credit_is_active']; ?>">

                <input type="hidden" id="member_id" value="<?php echo $output['member_id'] ?>">
                <?php foreach ($credit_balance as $key => $val) { ?>
                    <input type="hidden" id="credit_balance_<?php echo $key?>" value="<?php echo $val?>">
                <?php } ?>
            </div>

            <div class="basic-info product_list" style="margin-top: 20px;">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Product List</h5>
                </div>
                <div class="content">
                    <?php include(template('member_loan/member.one.time.product.list'))?>
                </div>
            </div>

            <div class="container">
                <div class="business-condition">
                    <form class="form-inline" id="frm_search_condition">
                        <table class="search-table">
                            <tbody>
                            <tr>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-btn verify-state">
                                           <button type="button" class="btn btn-default active" value="2">Pending Disburse</button>
                                            <button type="button" class="btn btn-default " value="1">Pending Approve</button>
                                           <button type="button" class="btn btn-default" value="3">Complete</button>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="business-content">
                    <div class="pending_business-list">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-10 col-lg-5" style="padding-left: 0px">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5>Add Contract History</h5>
                </div>
                <div class="business-content">
                    <div class="business-list">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script>
    $(function(){
        btn_search_onclick();
        btn_search_state();
        $('.verify-state .btn').on('click', function () {
            $('.verify-state .btn').removeClass('active');
            $(this).addClass('active');
            btn_search_state();
        });
    });


    function applyOneTimeLoan(member_id,category_id,_currency) {
        var member_id = member_id;
        var category_id = category_id;
        yo.loadData({
            _c: 'member_loan',
            _m: 'submitOneTimeLoanApply',
            param: {member_id:member_id,category_id:category_id,currency:_currency},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG);
                }
            }
        });

    }


    function btn_search_state() {
        var member_id = $('#member_id').val();
        var state = $('.verify-state .btn.active').attr('value');
        yo.dynamicTpl({
            tpl: "member_loan/pending.one.time.loan.list",
            control:'counter_base',
            dynamic: {
                api: "member_loan",
                method: "getOneTimeContractStateList",
                param: {member_id:member_id,state:state}
            },
            callback: function (_tpl) {
                $(".pending_business-list").html(_tpl);
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

        yo.dynamicTpl({
            tpl: "member_loan/one.time.contract.add.history.list",
            control:'counter_base',
            dynamic: {
                api: "member_loan",
                method: "getAddOneTimeContractList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }



    
</script>