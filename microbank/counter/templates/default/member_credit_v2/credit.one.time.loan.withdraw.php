
<div class="row clearfix ">

    <form action="" role="form" class="form form-horizontal" id="frm_credit_contract_withdraw">

        <div class="col-sm-8">
            <table class="table">
                <tr>
                    <td class="text-right">
                        <label for="">Client Balance</label>

                    </td>
                    <td class="text-left">
                        <p>
                            <span >
                                <label for="" class="label label-info">USD</label>
                                <label for="">
                                    <?php echo ncPriceFormat($data['member_balance'][currencyEnum::USD]); ?>
                                </label>

                            </span>
                        </p>
                        <p>
                            <span >
                                <label for="" class="label label-info">KHR</label>
                                <label for="">
                                    <?php echo ncPriceFormat($data['member_balance'][currencyEnum::KHR]); ?>
                                </label>

                            </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="text-right">
                        <label for=""> Withdraw Amount - USD</label>

                    </td>
                    <td class="text-left">

                        <input type="number" name="currency_usd" class="form-control" value="<?php echo $data['member_balance'][currencyEnum::USD]; ?>">

                    </td>
                </tr>

                <tr>
                    <td class="text-right">
                        <label for="">Withdraw Amount - KHR</label>

                    </td>
                    <td class="text-left">

                        <input type="number" name="currency_khr" class="form-control" value="<?php echo $data['member_balance'][currencyEnum::KHR]; ?>">

                    </td>
                </tr>

            </table>

            <div class="text-center" style="margin: 20px 0;">
                <span class="btn btn-default" onclick="withdrawCancel();">
                    Cancel
                </span>

                <span class="btn btn-primary" onclick="withdrawConfirm();">
                    Submit
                </span>
            </div>
        </div>

    </form>

</div>

<script>

    var _member_id = '<?php echo $data['member_id']; ?>';
    var member_usd_balance = Number(<?php echo round($data['member_balance'][currencyEnum::USD],2); ?>);
    var member_khr_balance = Number(<?php echo round($data['member_balance'][currencyEnum::KHR],2); ?>);

    function withdrawCancel()
    {
        window.location.reload();
    }

    function withdrawConfirm()
    {
        var _param = getFormJson('#frm_credit_contract_withdraw');
        _param.member_id = _member_id;
        //console.log(_param);
        var _withdraw_usd = Number(_param.currency_usd);
        var _withdraw_khr = Number(_param.currency_khr);
        if( _withdraw_usd <=0 && _withdraw_khr <=0 ){
            alert('Please input withdraw amount.');
            return false;
        }
        if( _withdraw_usd > member_usd_balance ){
            alert('Client balance not enough.');
            return false;
        }
        if( _withdraw_khr > member_khr_balance ){
            alert('Client balance not enough.');
            return false;
        }

        $('.container').waiting();
        yo.loadData({
            _c: "member_credit",
            _m: "grantCreditOneTimeLoanWithdrawConfirm",
            param: _param,
            callback: function (_o) {
                $('.container').unmask();
                if (_o.STS) {
                    alert('Withdraw success!',1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG);
                }
            }
        });

    }
</script>