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
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container" style="max-width: 1500px">
        <div class="contract-info col-sm-5" style="padding-left: 0px">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Contract Information</h5>
                </div>
                <div class="content">
                    <?php include(template("member_loan/contract.summary"))?>
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
                        <button style="width: 300px!important;" class="btn btn-default"  onclick="billPayClick()">Billpay History</button>
                        <br/>
                        <button style="width: 300px!important;" class="btn btn-default"  onclick="repaymentHistoryClick()">Repayment History</button>
                        <br/>
                        <!--                        <button style="width: 300px!important;" class="btn btn-primary" disabled onclick="repaymentClick()">Repayment</button>-->
                        <!--                        <br/>-->
                        <a style="width: 300px!important;" class="btn btn-primary prepayment"
                            href="<?php echo getUrl('member_loan', 'getPrepayment', array('contract_id'=>$output['contract']['uid']), false, ENTRY_COUNTER_SITE_URL)?>"
                            >Prepayment</a>
                        <br/>
                        <a style="width: 300px!important;" class="btn btn-info" onclick="javascript:history.go(-1);">Back</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-sm-7 installment-scheme" style="padding-right: 0px;display: none">
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Installment Scheme</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showFunction()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"> <span style="font-size: 13px">Back</span></i>
                </div>
                <div class="content" style="padding: 0px">

                </div>
            </div>
        </div>

        <div class="col-sm-7 billpay-history" style="padding-right: 0px;display: none">
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Billpay History</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showFunction()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"> <span style="font-size: 13px">Back</span></i>
                </div>
                <div class="content" style="padding: 0px">

                </div>
            </div>
        </div>

        <div class="col-sm-7 repayment-history" style="padding-right: 0px;display: none">
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Repayment History</h5>
                    <i class="fa fa-angle-double-left fa-lg" onclick="showFunction()" style="position: absolute;right: 10px;top: 10px;cursor: pointer"> <span style="font-size: 13px">Back</span></i>
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
        var state = $('#state').val();
        if(state == <?php echo loanContractStateEnum::COMPLETE?>){
           $('.prepayment').attr({'disabled':true,'title':'This contract has been completed'});
        }
        $('.contract-function').show();
    });


    function showFunction(){
        $(".contract-function").show();
        $(".installment-scheme").hide();
        $(".repayment-history").hide();
        $(".billpay-history").hide();
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
                method: "getContractInstallmentScheme",
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
        var uid = '<?php echo $output['contract']['uid']?>';
        if (!uid) {
            return;
        }
        yo.dynamicTpl({
            tpl: "member_loan/contract.repayment.history",
            control: "counter_base",
            dynamic: {
                api: "member_loan",
                method: "getContractRepaymentHistory",
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
        var uid = '<?php echo $output['contract']['uid']?>';
        if (!uid) {
            return;
        }
        yo.dynamicTpl({
            tpl: "member_loan/contract.billpay.list",
            control: "counter_base",
            dynamic: {
                api: "member_loan",
                method: "getContractBillPayList",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $(".billpay-history .content").html(_tpl);
                $(".billpay-history").show();
                $(".contract-function").hide();
            }
        });
    }

    function print_installment_scheme() {
        var _uid = $('#uid').val();
//        window.location.href="<?php //echo getUrl('print_form', 'printInstallmentScheme', array(), false, ENTRY_COUNTER_SITE_URL)?>//&contract_id="+_uid
        window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printInstallmentScheme', array(), false, ENTRY_COUNTER_SITE_URL)?>&contract_id="+_uid);
    }
</script>