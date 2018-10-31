<style>
    .verify-table .locking {
        color: red;
        font-style: normal;
    }

    .verify-table .locking i {
        margin-right: 3px;
    }

</style>
<div>
    <table class="table verify-table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn'; ?></td>
            <td><?php echo 'Type'; ?></td>
            <td><?php echo 'Currency'; ?></td>
            <td><?php echo 'Amount'; ?></td>
            <td><?php echo 'Principal'; ?></td>
            <td><?php echo 'Fee'; ?></td>
            <td><?php echo 'State'; ?></td>
            <td><?php echo 'Apply Time'; ?></td>
            <td><?php echo 'Auditor'; ?></td>
            <td><?php echo 'Audit Time'; ?></td>
<!--            <td>--><?php //echo 'Handler'; ?><!--</td>-->
<!--            <td>--><?php //echo 'Handle Time'; ?><!--</td>-->
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($data['data'] as $row) { ?>
            <tr>
                <td>
                    <a href="<?php echo getUrl('financial', 'contractDetail', array('uid'=>$row['contract_id']), false, BACK_OFFICE_SITE_URL); ?>">
                        <?php echo $row['contract_sn']?>
                    </a>

                </td>
                <td>
                    <?php
                        switch( $row['prepayment_type'] ){
                            case prepaymentRequestTypeEnum::PARTLY:
                                echo 'Fixed repay principal';
                                break;
                            case prepaymentRequestTypeEnum::FULL_AMOUNT:
                                echo 'Full Payment';
                                break;
                            case prepaymentRequestTypeEnum::LEFT_PERIOD:
                                echo 'Fixed repay period';
                                break;
                            default:
                                echo 'Fixed repay principal';
                        }
                    ?>

                </td>
                <td><?php echo $row['currency']; ?></td>
                <td>
                    <?php echo ncAmountFormat($row['amount'], false, $row['currency']); ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['principal_amount'], false, $row['currency']); ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['fee_amount'], false, $row['currency']); ?>
                </td>
                <td>
                    <?php if ($row['state'] != prepaymentApplyStateEnum::AUDITING ){
                        echo '<span>' . $lang['request_prepayment_state_' . $row['state']] . '</span>';
                    } elseif ($data['cur_uid'] == $row['handler_id']) {
                        echo '<span class="locking"><i class="fa fa-gavel"></i>' . $lang['request_prepayment_state_' . $row['state']] . '</span>';
                    } else {
                        echo '<span class="locking">' . $lang['request_prepayment_state_' . $row['state']] . '</span>';
                    }
                    ?>
                </td>
                <td>
                    <?php echo timeFormat($row['apply_time']) ?>
                </td>
                <td>
                    <?php echo $row['auditor_name'] ?>
                </td>
                <td><?php echo timeFormat($row['audit_time']); ?></td>
<!--                <td>-->
<!--                    --><?php //echo $row['handler_name'] ?>
<!--                </td>-->
<!--                <td>--><?php //echo timeFormat($row['handle_time']); ?><!--</td>-->
                <td>
<!--                    --><?php //if($row['state'] == prepaymentApplyStateEnum::APPROVED) {?>
<!--                    <div class="custom-btn-group">-->
<!--                        <a title="" class="custom-btn custom-btn-secondary" href="--><?php //echo getUrl('loan', 'handleRequestPrepayment', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?><!--">-->
<!--                            <span><i class="fa fa-check-circle-o"></i>Handle</span>-->
<!--                        </a>-->
<!--                    </div>-->
<!--                    --><?php //}?>
                    <?php if($row['state'] == prepaymentApplyStateEnum::CREATE || $row['state'] == prepaymentApplyStateEnum::AUDITING) {?>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('financial', 'auditRequestPrepayment', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                <span><i class="fa fa-check-circle-o"></i>Handle</span>
                            </a>
                        </div>
                    <?php }?>
                    <?php if(!in_array($row['state'],array(prepaymentApplyStateEnum::CREATE,prepaymentApplyStateEnum::AUDITING))){ ?>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('financial', 'viewRequestPrepayment', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                <span><i class="fa fa-eye"></i>View</span>
                            </a>
                        </div>
                    <?php }?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
