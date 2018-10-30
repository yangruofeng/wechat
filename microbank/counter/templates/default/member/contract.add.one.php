<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
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
</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for request-id.">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-success" id="btn_search_list" onclick="btn_search_onclick();">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <a type="button" class="btn btn-default" id="btn_new" href="<?php echo getUrl('member', 'loan', array(), false, ENTRY_COUNTER_SITE_URL)?>">
                                <i class="fa fa-address-card-o"></i>Contract
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="info-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Request Information</h5>
                </div>
                <div class="content">
                    <div style="min-height: 200px;padding: 5px 20px"></div>
                </div>
            </div>
        </div>
        <div class="operation">
            <button class="btn btn-primary" onclick="create_contract()">Create Contract</button>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script>
    function btn_search_onclick() {
        var search_text = $.trim($('#search_text').val());
        if (!search_text) {
            return;
        }
        yo.dynamicTpl({
            tpl: "member/request.info",
            control: "counter_base",
            dynamic: {
                api: "member",
                method: "getRequestInfo",
                param: {search_text: search_text}
            },
            callback: function (_tpl) {
                $(".info-div .content").html(_tpl);
            }
        });
    }

    function create_contract() {
        var uid = $('#uid').val();
        if (!uid) {
            return;
        }

        yo.loadData({
            _c: "member",
            _m: "createContract",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    window.location.href = data.url;
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>