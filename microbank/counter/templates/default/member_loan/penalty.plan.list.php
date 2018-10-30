<?php
$loanPenaltyHandlerStateLang = enum_langClass::getLoanPenaltyHandlerStateLang();
$penalty_plan_list=$output['penalty_plan_list'];
?>
<div>
    <form style="margin-bottom: 0">
        <table class="table table-bordered">
            <thead>
            <tr style="background-color: #DEDEDE">
                <td>Contract Sn</td>
                <td>Scheme Name</td>
                <td>Currency</td>
                <td>Penalty</td>
                <td>State</td>
                <td>Create Time</td>
            </tr>
            </thead>
            <tbody>
            <?php if ($penalty_plan_list) {  ?>
                <?php foreach ($penalty_plan_list as $key => $row) { ?>
                    <tr>
                        <input type="hidden" class="checkbox-amount" currency="<?php echo $row['currency']; ?>" amount="<?php echo $row['penalty_amount']; ?>" >
                        <td>
                            <?php echo $row['contract_sn'] ?>
                        </td>
                        <td>
                            <?php echo $row['scheme_name'] ?>
                        </td>
                        <td>
                            <?php echo $row['currency'] ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($row['penalty_amount']) ?>
                        </td>
                        <td>
                            <?php echo ucwords($loanPenaltyHandlerStateLang[$row['state']]); ?>
                        </td>
                        <td>
                            <?php echo $row['create_time'] ?>
                        </td>
                    </tr>
                <?php } ?>

                <table class="table" style="margin-top: 20px">
                    <tbody class="table-body">
                    <?php  $currency_list = (new currencyEnum())->Dictionary();
                    foreach ($currency_list as $key => $currency)
                    { ?>
                        <tr>
                            <td><label class="control-label">Total(<?php echo $currency;?>)</label></td>
                            <td><span class="currency_total money-style" currency="<?php echo $key?>"></span></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <tr>
                    <td colspan="7">No Record</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php if ($penalty_plan_list) { ?>
            <?php
            $m_dict = new core_dictionaryModel();
            $global_settings = $m_dict->getDictValue(dictionaryKeyEnum::GLOBAL_SETTINGS);
            $teller_reduce_penalty_maximum = round($global_settings['teller_reduce_penalty_maximum']);

            ?>
        <div style="text-align: center">
            <p>
                if apply to deduct amount >= <kbd><?php echo $teller_reduce_penalty_maximum;?></kbd>,need to approve by committee!
            </p>
            <a class="btn btn-primary" onclick="submit_apply_one()" style="width: 100%;margin-top: 15px"><?php echo 'Apply Receive Penalty' ?></a>

        </div>
        <?php } ?>
    </form>
</div>
<script>
    $(function () {
        penalty_select();
    })

    function penalty_select() {
        $('.currency_total').each(function () {
            var currency = $(this).attr('currency');
            var _total = 0;
            $('.checkbox-amount[currency="' + currency + '"]').each(function () {
                var _amount = $(this).attr('amount');
                _total += Number(_amount);
            })
            $(this).text(_total);
        })
    }
</script>