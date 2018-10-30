<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        border-radius: 0px;
        margin-bottom: 10px;
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


    .contract-info .content {
        min-height: 40px;
        padding: 0px;
    }

    .container {
        margin-bottom: 60px!important;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }
</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container" style="max-width: 1500px">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text" onkeydown="if(event.keyCode==13){return false;}" value="<?php echo $output['sn']?>" placeholder="Search for contract sn.">
                                <span class="input-group-btn">
                                    <a  class="btn btn-success" id="btn_search_list" onclick="btn_search_onclick();">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td>
                            <a type="button" class="btn btn-default" id="btn_new" href="<?php echo getUrl('member', 'addContract', array(), false, ENTRY_COUNTER_SITE_URL)?>">
                                <i class="fa fa-plus"></i>Create Contract
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="contract-info col-sm-5" style="padding-left: 0px">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Contract Information</h5>
                </div>
                <div class="content">
                </div>
            </div>
        </div>

        <div class="col-sm-7 contract-function" style="padding-right: 0px;display: none">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Function</h5>
                </div>
                <div class="content">
                    <div>
                        <button style="width: 300px!important;" class="btn btn-default"  onclick="installmentSchemeClick()">Installment Scheme</button>
                        <br/>
                        <button style="width: 300px!important;" class="btn btn-default"  onclick="billPayClick()">Billpay Method</button>
                        <br/>
                        <button style="width: 300px!important;" class="btn btn-default"  onclick="repaymentHistoryClick()">Repayment History</button>
                        <br/>
<!--                        <button style="width: 300px!important;" class="btn btn-primary" disabled onclick="repaymentClick()">Repayment</button>-->
<!--                        <br/>-->
                        <a style="width: 300px!important;" class="btn btn-primary prepayment">Prepayment</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-7 installment-scheme" style="padding-right: 0px;display: none">
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Installment Scheme</h5>
                    <i class="fa fa-file-powerpoint-o" onclick="print_installment_scheme()" style="position: absolute;right: 30px;top: 13px;cursor:pointer" title="Print"></i>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showFunction()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"></i>
                </div>
                <div class="content" style="padding: 0px">

                </div>
            </div>
        </div>

        <div class="col-sm-7 billpay_method" style="padding-right: 0px;display: none">
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Billpay Method</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showFunction()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"></i>
                </div>
                <div class="content" style="padding: 0px">

                </div>
            </div>
        </div>

        <div class="col-sm-7 repayment-history" style="padding-right: 0px;display: none">
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Repayment History</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showFunction()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"></i>
                </div>
                <div class="content" style="padding: 0px">

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
    })

    function btn_search_onclick() {
        var search_text = $.trim($('#search_text').val());
        if (!search_text) {
            return;
        }
        $('.contract-function').hide();
        yo.dynamicTpl({
            tpl: "member/contract.info",
            control: "counter_base",
            dynamic: {
                api: "member",
                method: "getContractInfoBySn",
                param: {search_text: search_text}
            },
            callback: function (_tpl) {
                $(".contract-info .content").html(_tpl);
                var uid = $('#uid').val();
                $('.prepayment').attr('href','<?php echo getUrl('member', 'getPrepayment', array(), false, ENTRY_COUNTER_SITE_URL)?>&contract_id='+uid);
                if(uid>0){
                    $('.contract-function').show();
                }
            }
        });
    }

    function showFunction(){
        $(".contract-function").show();
        $(".installment-scheme").hide();
        $(".repayment-history").hide();
        $(".billpay_method").hide();
    }

    function installmentSchemeClick() {
        var uid = '<?php echo $output['contract']['uid']?>';
        if (!uid) {
            return;
        }
        yo.dynamicTpl({
            tpl: "member_loan/contract.installment.scheme",
            control: "counter_base",
            dynamic: {
                api: "member_loan",
                method: "getInstallmentScheme",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $(".installment-scheme .content").html(_tpl);
                $(".installment-scheme").show();
                $(".contract-function").hide();
            }
        });
    }

    function repaymentHistoryClick() {
        var uid = $('#uid').val();
        if (!uid) {
            return;
        }
        yo.dynamicTpl({
            tpl: "member/repayment.history",
            control: "counter_base",
            dynamic: {
                api: "member",
                method: "getRepaymentHistory",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $(".repayment-history .content").html(_tpl);
                $(".repayment-history").show();
                $(".contract-function").hide();
            }
        });
    }

    function billPayClick() {
        var uid = $('#uid').val();
        if (!uid) {
            return;
        }
        yo.dynamicTpl({
            tpl: "member/billpay.list",
            control: "counter_base",
            dynamic: {
                api: "member",
                method: "getBillPayList",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $(".repayment-history .content").html(_tpl);
                $(".repayment-history").show();
                $(".contract-function").hide();
            }
        });
    }


    $('#search_text').bind('keydown',function(event){
        if(event.keyCode == "13") {
            btn_search_onclick();
        }
    });

    function print_installment_scheme() {
        var _uid = $('#uid').val();
        window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printInstallmentScheme', array(), false, ENTRY_COUNTER_SITE_URL)?>&contract_id="+_uid);
    }
</script>