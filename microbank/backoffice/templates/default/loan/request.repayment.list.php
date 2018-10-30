<style>
    .verify-table .locking {
        color: red;
        font-style: normal;
    }

    .verify-table .locking i {
        margin-right: 3px;
    }

    .currency {
        color: #999;
    }

    .amount {
        font-weight: 500;
    }

    .prepayment {
        color: #d40000;
        border: 1px solid #d40000;
        border-radius: 3px;
        display: inline-block;
        padding: 0 3px;
        margin-left: 2px;
    }

    .label {
        color: #000;
        font-weight: 500;
    }

</style>
<div>
    <table class="table verify-table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn'; ?></td>
            <td><?php echo 'Type'; ?></td>
            <td><?php echo 'Amount'; ?></td>
            <td><?php echo 'Payer Name'; ?></td>
            <td><?php echo 'State'; ?></td>
            <td><?php echo 'Scheme'; ?></td>
            <td><?php echo 'Repayment Time'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($data['data'] as $row) { ?>
            <tr>
                <td>
                    <?php echo $row['contract_sn'] . ' ' . $row['scheme_name']; ?>
                </td>
                <td>
                    <?php echo ucwords(str_replace('_', ' ', $row['repayment_type'])); ?>
                </td>
                <td>
                    <span class="amount"><?php echo ncAmountFormat($row['amount'], false, $row['currency']); ?></span>
                    <?php if (!$row['scheme_id'] || $row['scheme_id'] <= 0) { ?>
                        <span class="prepayment">prepayment</span>
                    <?php } ?>
                </td>
                <td>
                    <?php echo $row['payer_name']; ?>
                </td>
                <td>
                    <?php if ($data['state'] != requestRepaymentStateEnum::PROCESSING) {
                        echo '<span>' . $lang['request_repayment_state_' . $row['state']] . '</span>';
                    } elseif ($data['cur_uid'] == $row['handler_id']) {
                        echo '<span class="locking"><i class="fa fa-gavel"></i>' . $lang['request_repayment_state_' . $row['state']] . '</span>';
                    } else {
                        echo '<span class="locking">' . $lang['request_repayment_state_' . $row['state']] . '</span>';
                    }
                    ?>
                </td>
                <td>
                    <div><label class="label">Receivable
                            Principal:</label> <?php echo ncAmountFormat($row['receivable_principal'], false, $row['currency']); ?>
                    </div>
                    <div><label class="label">Receivable
                            Interest:</label> <?php echo ncAmountFormat($row['receivable_interest'], false, $row['currency']); ?>
                    </div>
                    <div><label
                            class="label">operation_fee:</label> <?php echo ncAmountFormat($row['receivable_operation_fee'], false, $row['currency']); ?>
                    </div>
                    <div><label class="label">Receivable
                            Amount:</label> <?php echo ncAmountFormat($row['r_amount'], false, $row['currency']); ?>
                    </div>
                    <div><label class="label">Actual Payment
                            Amount:</label> <?php echo ncAmountFormat($row['actual_payment_amount'], false, $row['currency']); ?>
                    </div>
                    <div><label class="label">Receivable
                            Date:</label> <?php echo timeFormat($row['receivable_date']); ?></div>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>

        <tr>
            <?php $currency_list = (new currencyEnum())->Dictionary(); ?>
            <?php foreach ($currency_list as $key => $currency) { ?>
                <td><?php echo $currency;?>: <?php echo $data['count'][$key] ? ncPriceFOrmat($data['count'][$key]) : '0.00'; ?></td>
            <?php } ?>
            <td colspan="<?php echo 7 - count($currency_list)?>"></td>
        </tr>
        </tfoot>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
