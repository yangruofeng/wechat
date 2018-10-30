<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'NO.'; ?></td>
            <td><?php echo 'Member'; ?></td>
            <td><?php echo 'Currency'; ?></td>
            <td><?php echo 'Apply Amount'; ?></td>
            <!--<td><?php echo 'Propose'; ?></td>-->
            <td><?php echo 'Loan Cycle'; ?></td>
            <td><?php echo 'Start Date'; ?></td>
            <td><?php echo 'End Date'; ?></td>
            <td><?php echo 'State'; ?></td>
            <td><?php echo 'Insurance Price'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php $insurance = $data['insurance']; ?>
        <?php foreach ($data['data'] as $row) { ?>
            <tr>
                <td>
                    <?php echo $row['contract_sn'] ?>
                </td>
                <td>
                    <a href="<?php echo getUrl('client', 'clientDetail', array('uid' => $row['member_id'], 'show_menu' => 'client-client'), false, BACK_OFFICE_SITE_URL) ?>"><?php echo $row['display_name'] ?></a>
                </td>
                <td>
                    <?php echo $row['currency'] ?>
                </td>
                <td>
                    <?php echo ncPriceFormat($row['apply_amount']) ?>
                </td>
                <!--<td>
                  <?php echo $row['propose'] ?>
              </td>-->
                <td>
                    <?php echo $row['loan_cycle']; ?>
                </td>
                <td>
                    <?php echo $row['start_date'] ?>
                </td>
                <td>
                    <?php echo $row['end_date'] ?>
                </td>
                <td>
                    <?php $label;
                    switch ($row['state']) {
                        case loanContractStateEnum::CREATE :
                            $class = 'label-primary';
                            $label = 'Create';
                            break;
                        case loanContractStateEnum::PENDING_APPROVAL :
                            $class = 'label-primary';
                            $label = 'Pending Approval';
                            break;
                        case loanContractStateEnum::PENDING_DISBURSE :
                            $class = 'label-primary';
                            $label = 'Pending Disburse';
                            break;
                        case loanContractStateEnum::PROCESSING :
                            $class = 'label-success';
                            $label = 'Processing';
                            break;
                        case loanContractStateEnum::PAUSE :
                            $class = 'label-warning';
                            $label = 'Pause';
                            break;
                        case loanContractStateEnum::COMPLETE :
                            $class = 'label-warning';
                            $label = 'Complete';
                            break;
                        case loanContractStateEnum::WRITE_OFF :
                            $class = 'label-default';
                            $label = 'Write Off';
                            break;
                        default:
                            $class = 'label-default';
                            $label = 'Write Off';
                            break;
                    } ?>
                    <?php echo $label; ?>
                </td>
                <td>
                    <?php if ($insurance[$row['uid']]['price']) { ?>
                        <!--<a href="<?php echo getUrl('insurance', 'contractDetail', array('uid' => $row['insurance_contract_id'], 'show_menu' => 'insurance-contract'), false, BACK_OFFICE_SITE_URL) ?>"><?php echo $row['price']; ?></a>-->
                        <a href="<?php echo getUrl('insurance', 'contract', array('show_menu' => 'insurance-contract'), false, BACK_OFFICE_SITE_URL) ?>"><?php echo ncPriceFormat($insurance[$row['uid']]['price']); ?></a>
                    <?php } else { ?>
                        0.00
                    <?php } ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <a title="<?php echo $lang['common_delete']; ?>" class="custom-btn custom-btn-secondary"
                           href="<?php echo getUrl('loan', 'contractDetail', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                            <span><i class="fa  fa-vcard-o"></i>Detail</span>
                        </a>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <td>In Contract</td>
            <td><?php echo $data['count_in']; ?></td>
            <td>Write Off Number</td>
            <td><?php echo $data['count_write_off']; ?></td>
            <td colspan="6"></td>
        </tr>
        </tfoot>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
