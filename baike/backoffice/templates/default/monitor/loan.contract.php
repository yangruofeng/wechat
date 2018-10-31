<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .currency span {
        padding-right: 10px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Loan Contract</h3>
            <ul class="tab-base">
                <li><a  class="current"><span>List</span></a></li>
                <li><a onclick="javascript:history.back(-1);">Back</a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <input type="hidden" name="uid" value="<?php echo $output['uid'];?>">
                <?php if(!$output['uid']){?>
                    <div class="form-group">
                        <label>Client</label>
                        <input type="text" class="form-control input-search" name="client_text" placeholder="CID/Name/Phone">
                    </div>
                    <div class="form-group">
                        <label>Contract Sn</label>
                        <input type="text" class="form-control input-search" name="contract_sn">
                    </div>
                    <div class="form-group">
                            <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                                    onclick="btn_search_onclick();">
                                <i class="fa fa-search"></i>
                                <?php echo 'Search'; ?>
                            </button>
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <select class="form-control" name="state" id="state">
                            <option value="0">All</option>
                            <option value="<?php echo loanContractStateEnum::CREATE?>">Create</option>
                            <option value="<?php echo loanContractStateEnum::PENDING_APPROVAL?>">Pending Approval</option>
                            <option value="<?php echo loanContractStateEnum::REFUSED?>">Refused</option>
                            <option value="<?php echo loanContractStateEnum::PENDING_DISBURSE?>">Pending Disburse</option>
                            <option value="<?php echo loanContractStateEnum::PROCESSING?>">Processing</option>
                            <option value="<?php echo loanContractStateEnum::PAUSE?>">Pause</option>
                            <option value="<?php echo loanContractStateEnum::ONLY_PENALTY?>">Only Penalty</option>
                            <option value="<?php echo loanContractStateEnum::COMPLETE?>">Complete</option>
                            <option value="<?php echo loanContractStateEnum::WRITE_OFF?>">Write Off</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <?php include(template("widget/inc_condition_datetime")); ?>
                    </div>
                <?php }else{?>
                    Filters: <?php echo $output['uid'];?>
                <?php }?>
            </form>
        </div>
        <div class="data-content"></div>
    </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });
    $('#state').change(function(){
        btn_search_onclick();
    });
    function btn_search_onclick() {
        var _values = $('#frm_search_condition').getValues();
        $(".data-content").waiting();
        yo.dynamicTpl({
            tpl: "monitor/loan.contract.list",
            dynamic: {
                api: "monitor",
                method: "ajaxLoanContractList",
                param: _values
            },
            callback: function (_tpl) {
                $(".data-content").unmask();
                $(".data-content").html(_tpl);
            }
        });
    }

</script>


