<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td rowspan="2"><?php echo 'Currency';?></td>
            <td rowspan="2">Min Amount</td>
            <td rowspan="2">Max Amount</td>
            <td rowspan="2">Min Days</td>
            <td rowspan="2">Max Days</td>
            <td rowspan="2"><?php echo 'Admin Fee';?></td>
            <td rowspan="2"><?php echo 'Loan Fee';?></td>
            <td colspan="4" align="center">
                Interest Rate
            </td>
            <td colspan="4" align="center">Operate Fee</td>
        </tr>
        <tr class="table-header">
            <td>No mortgage</td>
            <td>MortgageSoft</td>
            <td>MortgageHard</td>
            <td>Min value</td>
            <td>No mortgage</td>
            <td>MortgageSoft</td>
            <td>MortgageHard</td>
            <td>Min value</td>
        </tr>
        </thead>
        <tbody class="table-body">

        <?php if(!empty($data['data'])){?>
            <?php foreach($data['data'] as $row){?>
                <tr>
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>
                    <td>
                        <?php echo $row['loan_size_min']; ?>
                    </td>
                    <td>
                        <?php echo $row['loan_size_max']; ?>
                    </td>
                    <td>
                        <?php echo $row['min_term_days']; ?>
                    </td>
                    <td>
                        <?php echo $row['max_term_days']; ?>
                    </td>
                    <td class="<?php echo $row['is_diff']['admin_fee'] ? 'bg-red' : ''?>" onclick="inputClick(this, 'admin_fee', <?php echo $row['uid'] ?>, <?php echo $row['admin_fee_type'] ?>)" val="<?php echo $row['admin_fee'] ?>">
                        <span class="val">
                            <?php echo $row['admin_fee_type'] == 1 ?  $row['admin_fee'] : $row['admin_fee'] . '%';  ?>
                        </span>
                    </td>
                    <td class="<?php echo $row['is_diff']['loan_fee'] ? 'bg-red' : ''?>" onclick="inputClick(this, 'loan_fee', <?php echo $row['uid'] ?>, <?php echo $row['loan_fee_type'] ?>)" val="<?php echo $row['loan_fee'] ?>">
                        <span class="val">
                            <?php echo $row['loan_fee_type'] == 1 ?  $row['loan_fee'] : $row['loan_fee'] . '%';  ?>
                        </span>
                    </td>
                    <td class="<?php echo $row['is_diff']['interest_rate'] ? 'bg-red' : ''?>" onclick="inputClick(this, 'interest_rate', <?php echo $row['uid'] ?>, <?php echo $row['interest_rate_type'] ?>)" val="<?php echo $row['interest_rate'] ?>">
                        <span class="val">
                            <?php echo $row['interest_rate_type'] == 1 ? $row['interest_rate'] : $row['interest_rate'] . '%';  ?>
                        </span>
                        <span>
                            (<?php echo $lang['enum_'.$row['interest_rate_unit']]; ?>)
                        </span>
                    </td>
                    <td class="<?php echo $row['is_diff']['interest_rate_mortgage1'] ? 'bg-red' : ''?>" onclick="inputClick(this, 'interest_rate_mortgage1', <?php echo $row['uid'] ?>, <?php echo $row['interest_rate_type'] ?>)" val="<?php echo $row['interest_rate_mortgage1'] ?>">
                        <span class="val">
                            <?php echo $row['interest_rate_type'] == 1 ? $row['interest_rate_mortgage1'] : $row['interest_rate_mortgage1'] . '%'; ?>
                        </span>
                        <span>
                            (<?php echo $lang['enum_' . $row['interest_rate_unit']]; ?>)
                        </span>
                    </td>
                    <td class="<?php echo $row['is_diff']['interest_rate_mortgage2'] ? 'bg-red' : ''?>" onclick="inputClick(this, 'interest_rate_mortgage2', <?php echo $row['uid'] ?>, <?php echo $row['interest_rate_type'] ?>)" val="<?php echo $row['interest_rate_mortgage2'] ?>">
                        <span class="val">
                            <?php echo $row['interest_rate_type'] == 1 ?$row['interest_rate_mortgage2'] : $row['interest_rate_mortgage2'] . '%';  ?>
                        </span>
                        <span>
                            (<?php echo $lang['enum_'.$row['interest_rate_unit']]; ?>)
                        </span>
                    </td>
                    <td class="<?php echo $row['is_diff']['interest_min_value'] ? 'bg-red' : ''?>" onclick="inputClick(this, 'interest_min_value', <?php echo $row['uid'] ?>, '')" val="<?php echo $row['interest_min_value'] ?>">
                        <span class="val">
                            <?php echo $row['interest_min_value']; ?>
                        </span>
                    </td>
                    <td class="<?php echo $row['is_diff']['operation_fee'] ? 'bg-red' : ''?>" onclick="inputClick(this, 'operation_fee', <?php echo $row['uid'] ?>, <?php echo $row['operation_fee_type'] ?>)" val="<?php echo $row['operation_fee'] ?>">
                        <span class="val">
                            <?php echo $row['operation_fee_type'] == 1 ?  $row['operation_fee'] : $row['operation_fee'] . '%';  ?>
                        </span>
                        <span>
                            (<?php echo $lang['enum_'.$row['operation_fee_unit']]; ?>)
                        </span>
                    </td>
                    <td class="<?php echo $row['is_diff']['operation_fee_mortgage1'] ? 'bg-red' : ''?>" onclick="inputClick(this, 'operation_fee_mortgage1', <?php echo $row['uid'] ?>, <?php echo $row['operation_fee_type'] ?>)" val="<?php echo $row['operation_fee_mortgage1'] ?>">
                        <span class="val">
                            <?php echo $row['operation_fee_type'] == 1 ?  $row['operation_fee_mortgage1'] : $row['operation_fee_mortgage1'] . '%';  ?>
                        </span>
                        <span>
                            (<?php echo $lang['enum_'.$row['operation_fee_unit']]; ?>)
                        </span>
                    </td>
                    <td class="<?php echo $row['is_diff']['operation_fee_mortgage2'] ? 'bg-red' : ''?>" onclick="inputClick(this, 'operation_fee_mortgage2', <?php echo $row['uid'] ?>, <?php echo $row['operation_fee_type'] ?>)" val="<?php echo $row['operation_fee_mortgage2'] ?>">
                        <span class="val">
                            <?php echo $row['operation_fee_type'] == 1 ?  $row['operation_fee_mortgage2'] : $row['operation_fee_mortgage2'] . '%';  ?>
                        </span>
                        <span>
                            (<?php echo $lang['enum_'.$row['operation_fee_unit']]; ?>)
                        </span>
                    </td>
                    <td class="<?php echo $row['is_diff']['operation_min_value'] ? 'bg-red' : ''?>" onclick="inputClick(this, 'operation_min_value', <?php echo $row['uid'] ?>, '')" val="<?php echo $row['operation_min_value'] ?>">
                       <span class="val">
                           <?php echo  $row['operation_min_value'];  ?>
                       </span>
                    </td>
                </tr>
            <?php } ?>
        <?php }else{ ?>
            <tr>
                <td colspan="<?php echo $data['type'] != 'info' ? 16 : 15; ?>" align="center" >No data!</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

