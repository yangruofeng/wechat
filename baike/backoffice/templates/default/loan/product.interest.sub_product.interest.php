<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">

            <td rowspan="2"><?php echo 'Currency';?></td>
            <td rowspan="2">Min Amount</td>
            <td rowspan="2">Max Amount</td>
            <td rowspan="2">Min Days</td>
            <td rowspan="2">Max Days</td>
            <!--
            <td rowspan="2"><?php echo 'Admin Fee';?></td>
            <td rowspan="2"><?php echo 'Loan Fee';?></td>
            -->

            <td colspan="4" align="center">
                Interest Rate
            </td>
            <td colspan="4" align="center">Operate Fee</td>

            <?php if ($data['type'] != 'info') { ?>
                <td rowspan="2">
                    Function
                </td>
            <?php } ?>



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

        <?php if (!empty($data['data'])) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr <?php if($row['is_special']) echo ' style="background:red"'?>>
                    <td>
                        <input disabled title="Show For Client" type="checkbox" <?php if($row['is_show_for_client']) echo 'checked'?>>
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
                    <!--
                    <td>
                        <?php echo $row['admin_fee_type'] == 1 ?  $row['admin_fee'] : $row['admin_fee'] . '%';  ?>
                        <br/>
                    </td>

                    <td>
                        <?php echo $row['loan_fee_type'] == 1 ?  $row['loan_fee'] : $row['loan_fee'] . '%';  ?>
                        <br/>
                    </td>
                    -->

                    <td>
                        <?php echo $row['interest_rate_type'] == 1 ? $row['interest_rate'] : $row['interest_rate'] . '%';  ?>
                        (<?php echo $lang['enum_'.$row['interest_rate_unit']]; ?>)
                        <br/>
                    </td>

                    <td>
                        <?php echo $row['interest_rate_type'] == 1 ?  $row['interest_rate_mortgage1'] : $row['interest_rate_mortgage1'] . '%';  ?>
                        (<?php echo $lang['enum_'.$row['interest_rate_unit']]; ?>)
                        <br/>
                    </td>

                    <td>
                        <?php echo $row['interest_rate_type'] == 1 ?$row['interest_rate_mortgage2'] : $row['interest_rate_mortgage2'] . '%';  ?>
                        (<?php echo $lang['enum_'.$row['interest_rate_unit']]; ?>)
                        <br/>
                    </td>

                    <td>
                        <?php echo $row['interest_min_value']; ?>
                    </td>

                    <td>
                        <?php echo $row['operation_fee_type'] == 1 ?  $row['operation_fee'] : $row['operation_fee'] . '%';  ?>
                        (<?php echo $lang['enum_'.$row['operation_fee_unit']]; ?>)
                        <br/>
                    </td>

                    <td>
                        <?php echo $row['operation_fee_type'] == 1 ?  $row['operation_fee_mortgage1'] : $row['operation_fee_mortgage1'] . '%';  ?>
                        (<?php echo $lang['enum_'.$row['operation_fee_unit']]; ?>)
                        <br/>
                    </td>

                    <td>
                        <?php echo $row['operation_fee_type'] == 1 ?  $row['operation_fee_mortgage2'] : $row['operation_fee_mortgage2'] . '%';  ?>
                        (<?php echo $lang['enum_'.$row['operation_fee_unit']]; ?>)
                        <br/>
                    </td>

                    <td>
                        <?php echo  $row['operation_min_value'];  ?>
                        <br/>
                    </td>

                    <td>
                        <a title="<?php echo 'Special Rate'; ?>" style="margin-right: 5px;cursor: pointer"  onclick="showSpecialRate(<?php echo $row['uid'];?>)">
                            <i class="fa fa-list"></i>Special Rate
                        </a>
                    </td>

                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="15" align="center">No data</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

