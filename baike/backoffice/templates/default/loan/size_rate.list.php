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

            <td rowspan="2">Service Charges</td>

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

        <?php if(!empty($data['data'])){?>
            <?php foreach($data['data'] as $row){?>
                <tr <?php if($row['is_special']) echo ' style="background:red"'?> uid="<?php echo $row['uid'] ?>"
                                                                                  currency="<?php echo $row['currency'] ?>"
                                                                                  loan_size_min="<?php echo $row['loan_size_min'] ?>"
                                                                                  loan_size_max="<?php echo $row['loan_size_max'] ?>"
                                                                                  min_term_days="<?php echo $row['min_term_days'] ?>"
                                                                                  max_term_days="<?php echo $row['max_term_days'] ?>"
                                                                                  guarantee_type="<?php echo $row['guarantee_type'] ?>"
                                                                                  mortgage_type="<?php echo $row['mortgage_type'] ?>"
                                                                                  interest_payment="<?php echo $row['interest_payment'] ?>"
                                                                                  interest_rate_type="<?php echo $row['interest_rate_type'] ?>"
                                                                                  interest_min_value="<?php echo $row['interest_min_value'] ?>"
                                                                                  interest_rate_period="<?php echo $row['interest_rate_period'] ?>"
                                                                                  interest_rate="<?php echo $row['interest_rate'] ?>"
                                                                                  interest_rate_type="<?php echo $row['interest_rate_type'] ?>"
                                                                                  interest_rate_unit="<?php echo $row['interest_rate_unit']; ?>"
                                                                                  interest_rate_mortgage1="<?php echo $row['interest_rate_mortgage1']; ?>"
                                                                                  interest_rate_mortgage2="<?php echo $row['interest_rate_mortgage2']; ?>"
                                                                                  admin_fee="<?php echo $row['admin_fee'] ?>"
                                                                                  admin_fee_type="<?php echo $row['admin_fee_type'] ?>"
                                                                                  loan_fee="<?php echo $row['loan_fee'] ?>"
                                                                                  loan_fee_type="<?php echo $row['loan_fee_type'] ?>"
                                                                                  operation_fee="<?php echo $row['operation_fee'] ?>"
                                                                                  operation_fee_mortgage1="<?php echo $row['operation_fee_mortgage1']; ?>"
                                                                                  operation_fee_mortgage2="<?php echo $row['operation_fee_mortgage2']; ?>"
                                                                                  operation_fee_unit="<?php echo $row['operation_fee_unit'] ?>"
                                                                                  operation_fee_type="<?php echo $row['operation_fee_type'] ?>"
                                                                                  operation_min_value="<?php echo $row['operation_min_value'] ?>"
                                                                                  grace_days="<?php echo $row['grace_days'] ?>"
                                                                                  is_full_interest="<?php echo $row['is_full_interest'] ?>"
                                                                                  is_show_for_client="<?php echo $row['is_show_for_client']?>"
                                                                                  prepayment_interest="<?php echo $row['prepayment_interest'] ?>"
                                                                                  prepayment_interest_type="<?php echo $row['prepayment_interest_type'] ?>"
                                                                                  service_fee="<?php echo $row['service_fee']; ?>"
                                                                                  service_fee_type="<?php echo $row['service_fee_type']; ?>"
                    >
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
                        <?php echo $row['service_fee'].($row['service_fee_type'] == 1?'':'%'); ?>
                    </td>


                    <?php if ($data['type'] != 'info') { ?>
                        <td>

                            <a href="<?php echo getBackOfficeUrl('loan', 'sizeRateLoanPreview', array('size_rate_id' => $row['uid'])); ?>" title="Preview">
                                <i class="fa fa-eye"></i>
                            </a>


                            <a title="<?php echo 'Special Rate'; ?>" style="margin-right: 5px"  href="<?php echo getBackOfficeUrl('loan', 'specialRate', array('size_rate_id' => $row['uid'])); ?>">
                                <i class="fa fa-list"></i>
                            </a>

                            <a title="<?php echo $lang['common_edit'] ;?>" style="margin-right: 5px" onclick="edit_size_rate(this)">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a title="<?php echo $lang['common_delete'];?>" onclick="remove_size_rate(this)">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    <?php } ?>
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

