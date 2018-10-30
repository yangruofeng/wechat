<style>
    .credit-amount{
        font-size: 16px;
        font-weight: 600;
        color: red;
    }
</style>
<?php

$category_contract = $data['data']['category_contract'];
$member_id = $data['member_id'];
?>
<div class="clearfix">

    <div class="alert alert-success">
        <i class="fa fa-check"></i> <label for="">Credit contract sign success!</label>
    </div>

    <form action="" role="form" class="form" id="frm_pending_disburse_contract">
        <div class="col-sm-8">

            <table class="table table-bordered table-hover">

                <?php foreach( $category_contract as $v ){ ?>
                    <tr>
                        <td class="text-right">
                            <kbd><?php echo $v['member_credit_category']['alias']; ?></kbd> /
                            <?php echo $v['member_credit_category']['sub_product_name']; ?>
                        </td>
                        <td>
                            <?php foreach( $v['contract_result'] as $va ){ ?>
                                <div>
                                    <label for=""><?php echo $va['contract_param']['currency']; ?></label>
                                    <span class="credit-amount"><?php echo ncPriceFormat($va['contract_param']['amount']); ?></span>
                                    <label for="" class="label label-info">
                                        <?php echo $va['contract_param']['loan_period'].' '.ucwords($va['contract_param']['loan_period_unit']); ?>
                                    </label>
                                    <?php if( $va['is_error'] ){ ?>
                                        <label for="" class="label label-danger"><?php echo $va['error_msg']; ?></label>
                                    <?php }else{ ?>
                                        <div class="clearfix">
                                            <label for="" class="pull-left">
                                                <?php echo $va['contract_data']['contract_sn']; ?>
                                            </label>
                                            <a href="<?php echo getUrl('member_loanV2','loanContractViewDetail',array(
                                                    'contract_id' => $va['contract_data']['contract_id']
                                            ),false,ENTRY_COUNTER_SITE_URL); ?>" class="btn btn-primary pull-right" target="_blank">
                                                Detail
                                            </a>

                                            <input type="hidden" name="biz_ids" value="<?php echo $va['biz_id']; ?>">
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>

            </table>

            <div class="text-center" style="margin: 20px 0;">
                <span class="btn btn-default" onclick="disburseCancel();">
                    Cancel
                </span>

                <span class="btn btn-primary" onclick="confirmDisburse();">
                    Next
                </span>
            </div>

        </div>
    </form>



</div>

<script>

    function disburseCancel()
    {
        var _values = getFormJson('#frm_pending_disburse_contract');
        $('.container').waiting();
        yo.loadData({
            _c: "member_credit",
            _m: "grantCreditOneTimeLoanCancel",
            param: _values,
            callback: function (_o) {
                $('.container').unmask();
                if (_o.STS) {
                    alert('Cancel success.',1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2,function(){
                        //window.location.reload();
                    });
                }
            }
        });

    }

    function confirmDisburse()
    {
        var _member_id = '<?php echo $member_id; ?>';
        var _values = getFormJson('#frm_pending_disburse_contract');
        //console.log(_values);
        $('.container').waiting();
        yo.loadData({
            _c: "member_credit",
            _m: "grantCreditOneTimeLoanConfirm",
            ajax:{
                timeout: 1000000
            },
            param: _values,
            callback: function (_o) {
                $('.container').unmask();
                if (_o.STS) {
                    alert(_o.MSG,1,function(){

                        $('.container').waiting();
                        yo.dynamicTpl({
                            tpl: "member_credit_v2/credit.one.time.loan.withdraw",
                            control:'counter_base',
                            dynamic: {
                                api: "member_credit",
                                method: "grantCreditOneTimeLoanWithdrawPage",
                                param: {member_id:_member_id}
                            },
                            callback: function (_tpl) {
                                $('.container').unmask();
                                $('#tab_authorize').html(_tpl);
                            }
                        });
                    });
                } else {
                    alert(_o.MSG);
                }
            }
        });

    }

</script>
