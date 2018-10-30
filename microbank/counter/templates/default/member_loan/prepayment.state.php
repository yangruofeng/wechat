<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .width250 {
        width: 250px;
    }

    .content {
        padding: 5px
    }

    /*repayment少了一个点*/
    .back, .apply, .repayment {
        width: 90px;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }

</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div style="max-width: 1300px">
        <div class="col-sm-12 col-md-10 col-lg-7">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Prepayment State</h5>
            </div>
            <div class="content">
                <div>
                    <table class="table contract-table">
                        <tbody class="table-body">
                        <tr>
                            <td><label class="control-label">Contract Sn</label></td>
                            <td><?php echo $output['contract_info']['contract_sn']?></td>
                        </tr>
                        <?php if($output['prepayment_request']){ ?>
                        <tr>
                            <td><label class="control-label">Prepayment Method</label></td>
                            <td><?php echo $lang['prepayment_request_type_'.$output['prepayment_request']['prepayment_type']] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Currency</label></td>
                            <td><?php echo $output['prepayment_request']['currency']?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Total Prepayment</label></td>
                            <td><span class="money-style"><?php echo ncPriceFormat($output['prepayment_request']['total_payable_amount']) ?></span></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Submit Time</label></td>
                            <td><?php echo $output['prepayment_request']['apply_time'] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Deadline</label></td>
                            <td><?php echo $output['prepayment_request']['deadline_date']?></td>
                        </tr>
                        <?php }?>
                        <tr>
                            <td><label class="control-label">Contract State</label></td>
                            <td><label class="control-label"><?php echo $lang['loan_contract_state_'.$output['contract_state']]?></label></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Apply State</label></td>
                            <td><label class="control-label"><?php echo $lang['prepayment_request_state_'.$output['prepayment_request']['state']]?:'No apply' ?></label></td>
                        </tr>
                        <tr style="text-align: center">
                            <td colspan="2">
                                <?php
                                $state = $output['prepayment_request']['state'];
                                $is_contract = $output['is_contract'];
                                $is_apply = ((!$state || in_array($state, array(prepaymentApplyStateEnum::DISAPPROVE, prepaymentApplyStateEnum::SUCCESS, prepaymentApplyStateEnum::FAIL))) &&  $is_contract) ? true : false;
                                $is_prepayment = $state == prepaymentApplyStateEnum::APPROVED ? true : false;
                                ?>
                                <a class="btn btn-default apply" <?php echo $is_apply ? '' : 'disabled';?> href="<?php echo $is_apply ? getUrl('member_loan', 'prepaymentApply', array('contract_id' => $output['contract_info']['contract_id']), false, ENTRY_COUNTER_SITE_URL) : 'javascript:void(0)';?>">
                                    <i class="fa fa-check"></i><?php echo 'Apply' ?>
                                </a>
                                <a class="btn btn-default repayment" <?php echo $is_prepayment ? '' : 'disabled';?> href="<?php echo $is_prepayment ? getUrl('member_loan', 'submitPrepayment', array('apply_id' => $output['prepayment_request']['uid']), false, ENTRY_COUNTER_SITE_URL) : 'javascript:void(0)';?>">
                                    <i class="fa fa-check"></i><?php echo 'Payment' ?>
                                </a>
                                <a class="btn btn-default back" onclick="javascript:history.go(-1);">
                                    <i class="fa fa-reply"></i><?php echo 'Back' ?>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-10 col-lg-5">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5>History</h5>
                </div>
                <div class="business-content">
                    <div class="business-list">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        btn_search_onclick();
    });

    //  展示成功deposit列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "member_loan/prepayment.list",
            control:'counter_base',
            dynamic: {
                api: "member_loan",
                method: "getPrepaymentList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>

