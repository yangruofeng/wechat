<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Currency';?></td>
            <td><?php echo 'Size';?></td>
            <td><?php echo 'Term Days';?></td>
            <td><?php echo $data['guarantee_type']['name'];?></td>
            <td><?php echo $data['mortgage_type']['name'];?></td>
            <td><?php echo 'Interest Payment';?></td>
            <td><?php echo 'Interest Rate';?></td>
            <td><?php echo 'Operation Fee Rate';?></td>
            <td><?php echo 'Min Interest';?></td>
            <td><?php echo 'Min Operation Fee';?></td>
            <!--
            <td><?php echo 'Admin Fee';?></td>
            <td><?php echo 'Loan Fee';?></td>
            -->
            <td><?php echo 'Grace Days';?></td>
            <td><?php echo 'Prepayment';?></td>
            <?php if ($data['type'] != 'info') { ?>
                <td><?php echo 'Operation'; ?></td>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if(!empty($data['data'])){?>
        <?php foreach($data['data'] as $row){?>
            <tr uid="<?php echo $row['uid'] ?>"
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
                admin_fee="<?php echo $row['admin_fee'] ?>"
                admin_fee_type="<?php echo $row['admin_fee_type'] ?>"
                loan_fee="<?php echo $row['loan_fee'] ?>"
                loan_fee_type="<?php echo $row['loan_fee_type'] ?>"
                operation_fee="<?php echo $row['operation_fee'] ?>"
                operation_fee_unit="<?php echo $row['operation_fee_unit'] ?>"
                operation_fee_type="<?php echo $row['operation_fee_type'] ?>"
                operation_min_value="<?php echo $row['operation_min_value'] ?>"
                grace_days="<?php echo $row['grace_days'] ?>"
                is_full_interest="<?php echo $row['is_full_interest'] ?>"
                prepayment_interest="<?php echo $row['prepayment_interest'] ?>"
                prepayment_interest_type="<?php echo $row['prepayment_interest_type'] ?>"
                >
                <td>
                    <?php echo $row['currency'] ?><br/>
                </td>
                <td>
                    <?php echo '$' . $row['loan_size_min'] . '--' . $row['loan_size_max'] ?><br/>
                </td>
                <td>
                    <?php echo $row['min_term_days'] . '--' . $row['max_term_days'] .'Days' ?><br/>
                </td>
                <td>
                    <?php echo $data['guarantee_type']['item_list'][$row['guarantee_type']]; ?>
                    <br/>
                </td>
                <td>
                    <?php echo $data['mortgage_type']['item_list'][$row['mortgage_type']]; ?>
                    <br/>
                </td>
                <td>
                    <?php echo $lang['enum_' . $row['interest_payment']] . ($row['interest_rate_period'] ? '(' . $lang['enum_' . $row['interest_rate_period']] . ')' : ''); ?>
                    <br/>
                </td>
                <td>
                    <?php echo $row['interest_rate_type'] == 1 ? '$' . $row['interest_rate'] : $row['interest_rate'] . '%';  ?>
                    (<?php echo $lang['enum_'.$row['interest_rate_unit']]; ?>)
                    <br/>
                </td>

                <td>
                    <?php echo $row['operation_fee_type'] == 1 ? '$' . $row['operation_fee'] : $row['operation_fee'] . '%';  ?>
                    (<?php echo $lang['enum_'.$row['operation_fee_unit']]; ?>)
                    <br/>
                </td>

                <td>
                    <?php echo  '$' . $row['interest_min_value'];  ?>
                    <br/>
                </td>

                <td>
                    <?php echo  '$' . $row['operation_min_value'];  ?>
                    <br/>
                </td>
                <!--
                <td>
                    <?php echo $row['admin_fee_type'] == 1 ? '$' . $row['admin_fee'] : $row['admin_fee'] . '%';  ?>
                    <br/>
                </td>
                <td>
                    <?php echo $row['loan_fee_type'] == 1 ? '$' . $row['loan_fee'] : $row['loan_fee'] . '%';  ?>
                    <br/>
                </td>
                -->
                <td>
                    <?php echo $row['grace_days'] . 'Days';  ?>
                    <br/>
                </td>
                <td>
                    <?php echo $row['is_full_interest'] == 1 ? 'Full Interest' : ($row['prepayment_interest_type'] == 1 ? '$' . $row['prepayment_interest'] : $row['prepayment_interest'] . '%');  ?>
                    <br/>
                </td>
                <?php if ($data['type'] != 'info') { ?>
                    <td>
                        <a title="<?php echo 'Special Rate'; ?>" style="margin-right: 5px" href="<?php echo getUrl('loan', 'specialRate', array('size_rate_id' => $row['uid'], 'product_id'=>$row['product_id']), false, BACK_OFFICE_SITE_URL) ?>"">
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
                <td colspan="<?php echo $data['type'] != 'info' ? 12 : 11?>">Null</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

