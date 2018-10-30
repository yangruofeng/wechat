<style>
    .td-analysis-remark{
        text-align: right;font-size: 11px;color: #695f86
    }
</style>
<table class="table">
    <tbody class="table-body">
    <tr>
        <td><label class="control-label">Client Request</label></td>
        <td></td>
    </tr>
    <tr>
        <td><span class="pl-25">Credit</span></td>
        <td><em><?php echo $member_request ? ncAmountFormat($member_request['credit']) : '' ?></em></td>
    </tr>
    <tr>
        <td><span class="pl-25">Terms</span></td>
        <td><?php echo $member_request ? $member_request['terms'] . 'Months' : '' ?></td>
    </tr>
    <tr>
        <td><span class="pl-25">Interest-Rate</span></td>
        <td><?php echo $member_request ? $member_request['interest_rate'] . '%' : '' ?></td>
    </tr>

    <tr>
        <td><span class="pl-25">Purpose</span></td>
        <td><?php echo $member_request['purpose']; ?></td>
    </tr>


    <tr>
        <td colspan="2"><label class="control-label">System Analysis</label></td>
    </tr>
    <?php $income_monthly = 0; ?>
    <tr>
        <td colspan="2"><span class="pl-25">Income(Monthly)</span></td>
    </tr>
    <?php foreach($member_income as $item_key=>$list){?>
        <tr>
            <td colspan="2"><span class="pl-75"><?php echo $item_key;?></span></td>
        </tr>
        <?php foreach($list as $item){?>
            <tr>
                <td><span class="pl-125"><?php echo $item['credit_key']?></span></td>
                <td><?php echo $item['credit_val'] . '*' . $item['credit_rate'] . ' = '?><em><?php echo $item['credit'] ?></em></td>
            </tr>
            <tr>
                <td colspan="2" class="td-analysis-remark">
                    <span><?php echo $item['remark']?></span>
                </td>
            </tr>
        <?php }?>

    <?php }?>

    <tr>

    </tr>


    <?php $expense_monthly = 0; ?>
    <tr>
        <td colspan="2"><span class="pl-25">Expense(Monthly)</span></td>
    </tr>
    <?php foreach($member_expense as $item_key=>$list){?>
        <tr>
            <td colspan="2"><span class="pl-75"><?php echo $item_key;?></span></td>
        </tr>
        <?php foreach($list as $item){?>
            <tr>
                <td><span class="pl-125"><?php echo $item['credit_key']?></span></td>
                <td><em><?php echo $item['credit'] ?></em></td>
            </tr>
            <tr>
                <td colspan="2" class="td-analysis-remark">
                    <span><?php echo $item['remark']?></span>
                </td>
            </tr>
        <?php }?>

    <?php }?>

    <tr>
        <td colspan="2"><span class="pl-25">Repay Ability = Income(monthly)-Expense(Monthly)</span></td>
    </tr>
    <tr>
        <td></td>
        <td><em><?php echo ncPriceFormat($analysis['ability'])?></em></td>
    </tr>

    <tr>
        <td colspan="2"><span class="pl-25">System Suggest Credit</span></td>
    </tr>
    <tr>
        <td><span class="pl-75">Default Credit</span></td>
        <td><em><?php echo $suggest_profile['default_credit']?></em></td>
    </tr>
    <tr>
        <td colspan="2" class="td-analysis-remark">
            <span><?php echo $suggest_profile['default_credit_remark']?></span>
        </td>
    </tr>
    <tr>
        <td><span class="pl-75">Terms</span></td>
        <td><?php echo $suggest_profile['terms'] . 'Months'?></td>
    </tr>
    <tr>
        <td colspan="2" class="td-analysis-remark">
            <span><?php echo $suggest_profile['terms_remark']?></span>
        </td>
    </tr>
    <tr>
        <td><span class="pl-75">Max Credit</span></td>
        <td><em><?php echo ncPriceFormat($suggest_profile['max_credit'])?></em></td>
    </tr>
    <tr>
        <td colspan="2" class="td-analysis-remark">
            <span><?php echo $suggest_profile['max_credit_remark']?></span>
        </td>
    </tr>
    <tr>
        <td colspan="2"><span class="pl-75">Increase Credit By Mortgage</span></td>
    </tr>


    <?php if ($analysis['suggest']['increase']) { ?>
        <?php foreach($analysis['suggest']['increase'] as $item) {?>
            <tr>
                <td>
                                        <span class="pl-125">
                                            <span style="font-weight: 500"><?php echo $item['credit_key']; ?></span>

                                        </span>
                </td>
                <td>
                    <?php echo $item['credit_val'] . '*' . $item['credit_rate'] . ' = '?><em><?php echo $item['credit'] ?></em>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td-analysis-remark">
                    <span><?php echo $suggest_profile['increase_remark']?></span>
                </td>
            </tr>
        <?php }?>
    <?php } else { ?>
        <tr>
            <td><span class="pl-25"></span></td>
            <td>
                No Record
            </td>
        </tr>
    <?php } ?>
    <?php if(count($analysis['suggest']['tip'])){?>
    <tr>
        <td colspan="10">
            <div class="well">
                <?php foreach($analysis['suggest']['tip'] as $i=>$msg){?>
                    <p>
                        <span style="font-weight: bold;background-color: #ffff00"><?php echo ($i+1).". ".$msg?></span>
                    </p>
                <?php }?>
            </div>
        </td>
    </tr>
    <?php }?>
    </tbody>
</table>