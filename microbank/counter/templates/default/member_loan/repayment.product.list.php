<div>
    <table class="table">
        <thead>
        <tr style="background-color: #DEDEDE">
            <td>Product Name</td>
            <td>Product Type</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody>
        <?php $list_contract_state=(new loanContractStateEnum())->Dictionary();?>

        <?php if ($output['repayment_plan']) { ?>
            <?php foreach ($output['repayment_plan'] as $k1 => $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['alias'] ?>
                    </td>
                    <td>
                        <?php echo $row['product_name'] ?>
                    </td>
                    <td>
                        <button class="btn btn-primary"
                                onclick="showNextRepaymentDetail(<?php echo $output['member_id'] ?>,'<?php echo $row['member_credit_category_id'] ?>')"><i class="fa fa-address-card-o"></i>
                            Next Repayment
                        </button>
                    </td>
                </tr>
                <?php if($row['contract_list']){?>
                    <tr>
                        <td colspan="10">
                            <table class="table table-no-background">
                                <tr class="table-header">
                                    <td>Contract No.</td>
                                    <td>Create Time</td>
                                    <td>Principal</td>
                                    <td>State</td>
                                    <td>Function</td>
                                </tr>
                                <?php foreach($row['contract_list'] as $contract){?>
                                    <tr>
                                        <td><?php echo $contract['contract_sn']?></td>
                                        <td><?php echo $contract['create_time']?></td>
                                        <td><?php echo ncPriceFormat($contract['receivable_principal']) ?></td>
                                        <td><?php echo $list_contract_state[$contract['state']]?></td>
                                        <td>
                                            <button class="btn btn-default"
                                                    onclick="showContractRepaymentDetail(<?php echo $output['member_id'] ?>,<?php echo $contract['uid'] ?>)"><i class="fa fa-address-card-o"></i>
                                                 Repayment
                                            </button>
                                        </td>
                                    </tr>
                                <?php }?>
                            </table>

                        </td>
                    </tr>
                <?php }?>

            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="4">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

