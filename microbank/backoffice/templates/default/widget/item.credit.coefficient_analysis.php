<?php
$new_analysis=$output['new_analysis'];
?>
<style>
    .table{
        width: 100%;background-color: #FFFFFF;
        border-spacing: 0;
        border-collapse: collapse;
    }
    .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
        border: 1px solid #ddd;
    }
    .table > tbody > tr > td {
        padding: 3px;
        vertical-align: middle;
        border-top: 0;
    }
    .tr-caption{

    }
    .tr-caption>td{
        background-color: #f8f8f8;
        padding-top: 10px!important;
        font-weight: bold;
    }
    .tr-summary{
        font-weight: bold;
    }
    .text-right{
        text-align: right;
        padding-right: 10px!important;
    }
    .text-center{
        text-align: center;
    }

</style>
<table class="table table-bordered">
    <tr class="tr-caption">
        <td colspan="10">Client Request Credit</td>
    </tr>
    <tr>
        <td colspan="2"> Credit (D)</td>
        <td class="text-right">
            <?php echo $member_request ? ncPriceFormat($member_request['credit'],0) : '' ?>
        </td>
    </tr>
    <tr>
        <td colspan="2"> Terms </td>
        <td class="text-right">
            <?php echo $member_request ? $member_request['terms'] . 'Months' : '' ?>
        </td>
    </tr>
    <tr>
        <td colspan="2"> Interest-Rate </td>
        <td class="text-right">
            <?php echo $member_request ? $member_request['interest_rate'] . '%' : '' ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">Purpose</td>
        <td class="text-right"><?php echo $member_request['purpose']; ?></td>
    </tr>

    <?php if(!$new_analysis['is_only_super_loan']){?>
        <tr class="tr-caption">
            <td colspan="10">
                Payment ability calculation
            </td>
        </tr>
        <tr>
            <td class="text-center"> Description </td>
            <td class="text-center"> Income - Expense </td>
            <td class="text-center"> Net Income </td>
        </tr>
        <?php foreach($new_analysis['list_income'] as $item){?>
            <tr>
                <td><?php echo $item['desc']?></td>
                <td><?php echo $item['income']?> -  <?php echo $item['expense']?></td>
                <td class="text-right">
                    <?php echo $item['profit']?>
                </td>
            </tr>
        <?php }?>
        <?php if(!count($new_analysis['list_income'])){?>
            <tr>
                <td colspan="10">No Record</td>
            </tr>
        <?php }?>
        <tr class="tr-summary">
            <td colspan="2">Total Income (1)</td>
            <td class="text-right"> <?php echo $new_analysis['total_income']?></td>
        </tr>
        <?php foreach($new_analysis['list_expense'] as $item){?>
            <tr>
                <td colspan="2">
                    <?php echo $item['desc']?>
                </td>
                <td>
                    <?php echo $item['expense']?>
                </td>
            </tr>
        <?php }?>
        <tr class="tr-summary">
            <td colspan="2">Total Expense (2)</td>
            <td class="text-right"> <?php echo $new_analysis['total_expense']?></td>
        </tr>
        <tr class="tr-summary">
            <td colspan="2">Total Net Income(A)=(1-2)</td>
            <td class="text-right"> <?php echo $new_analysis['total_profit']?></td>
        </tr>
        <tr>
            <td colspan="2">
                Old SRS Loan (3)
            </td>
            <td class="text-right">
                <?php echo $new_analysis['list_repay']['srs_old']?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Other Bank/MFI Loan (4)
            </td>
            <td class="text-right">
                <?php echo $new_analysis['list_repay']['cbc']?>
            </td>
        </tr>
        <?php foreach($new_analysis['list_srs_new'] as $i=>$item){?>
            <tr>
                <td colspan="2">
                    New SRS Loan (5.<?php echo $i+1;?>)
                    <p><code><?php echo $item['category']?>:<?php echo $item['repay_type']?></code></p>
                    <p>Formula:<?php echo $item['formula']?></p>
                </td>
                <td class="text-right">
                    <?php echo $item['value']?>
                </td>
            </tr>
            <tr class="tr-summary">
                <td colspan="2">Monthly Repayment(B)=(3+4+5.<?php echo $i+1;?>)</td>
                <td class="text-right"> <?php echo $item['total_repay']?></td>
            </tr>
            <tr class="tr-summary">
                <td colspan="2">Ability Coefficient(A/B)</td>
                <td class="text-right"><kbd> <?php echo $item['ability_coefficient']?> </kbd> </td>
            </tr>
            <tr class="tr-summary">
                <td colspan="10">
                    <?php if($item['state_ability']){?>
                        <i class="fa fa-check" style="color: #008000;font-size: 20px"></i><?php echo $item['category']?> coefficient >= 1.3
                    <?php }else{?>
                        <i class="fa fa-close" style="color: red;font-size: 20px"></i><?php echo $item['category']?> coefficient <= 1.3
                    <?php }?>
                </td>
            </tr>
        <?php }?>
    <?php }?>
    <?php if($new_analysis['is_only_super_loan']){?>
        <tr class="tr-caption">
            <td colspan="10">Salary</td>
        </tr>
        <tr>
            <td colspan="2" class="text-center"> Description </td>
            <td class="text-center"> Salary </td>
        </tr>
        <?php $total_salary=0;?>
        <?php foreach($new_analysis['list_salary'] as $item){
            if(!$item['is_own']) continue;
            $total_salary+=$item['profit'];
            ?>
            <tr>
                <td colspan="2">
                    <?php echo $item['desc']?>
                </td>
                <td class="text-right">
                    <?php echo $item['profit']?>
                </td>
            </tr>
        <?php }?>
        <tr>
            <td colspan="2" class="text-center"> Total </td>
            <td class="text-right"> <kbd><?php echo ncPriceFormat($total_salary,0);?> </kbd></td>
        </tr>
        <?php if($total_salary<200){?>
            <tr>
                <td colspan="10">
                    <i class="weui-icon-cancel"></i>   Salary < <kbd>200</kbd>,Not Allowed to Super-Loan
                </td>
            </tr>
        <?php }else{?>
            <tr>
                <td colspan="10">
                    <i class="weui-icon-success"></i>   Salary >= <kbd>200</kbd>, Allowed to Super-Loan
                </td>
            </tr>
        <?php }?>
        <?php if($member_request>5000){?>
            <tr>
                <td colspan="10">
                    <i class="weui-icon-cancel"></i>  Reqeust Credit > <kbd>5,000</kbd>,Not Allowed to Super-Loan
                </td>
            </tr>
        <?php }?>
    <?php }?>


    <tr class="tr-caption">
        <td colspan="10">
            Collateral Assessment
        </td>
    </tr>
    <tr>
        <td class="text-center">Certificate Name</td>
        <td class="text-center">MarketValue * CreditRate</td>
        <td class="text-center">Assessment</td>
    </tr>
    <?php foreach($new_analysis['list_assessment'] as $item){
        if($new_analysis['is_only_super_loan'] && $item['is_mortgage']) continue;
        ?>
        <tr>
            <td>
                <?php echo $item['credit_key']?>
            </td>
            <td>
                <?php echo $item['credit_val']?> *  <?php echo $item['credit_rate']?>
            </td>
            <td>
                <?php echo $item['credit']?>
            </td>
        </tr>
    <?php }?>
    <?php if(!count($new_analysis['list_assessment'])){?>
        <tr>
            <td colspan="10">No Record</td>
        </tr>
    <?php }?>
    <?php if(!$new_analysis['is_only_super_loan']){?>
        <tr class="tr-summary">
            <td colspan="2">Total Collateral Value(C)</td>
            <td class="text-right"> <?php echo $new_analysis['total_credit']?></td>
        </tr>
        <tr class="tr-summary">
            <td colspan="2">Credit Coefficient (C/D)</td>
            <td class="text-right"><kbd>  <?php echo $new_analysis['credit_coefficient']?> </kbd></td>
        </tr>
        <tr class="tr-summary">
            <td colspan="10">
                <?php if($new_analysis['state_credit']){?>
                    <i class="fa fa-check" style="color: #008000;font-size: 20px"></i> coefficient >= 1.5
                <?php }else{?>
                    <i class="fa fa-close" style="color: red;font-size: 20px"></i> coefficient <= 1.5
                <?php }?>
            </td>
        </tr>
    <?php }?>

    <?php if(!$new_analysis['is_only_super_loan']){?>
        <?php foreach($new_analysis['list_error'] as $err){?>
            <tr>
                <td colspan="10">
                <span style="word-break: break-all">
                    <?php echo $err?>
                </span>
                </td>
            </tr>
        <?php }?>
    <?php }?>

</table>
