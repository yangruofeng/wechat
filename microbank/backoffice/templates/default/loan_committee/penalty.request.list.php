<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Client Account'; ?></td>
            <td><?php echo 'Currency'; ?></td>
            <td><?php echo 'Due Amount'; ?></td>
            <td><?php echo 'Reduce Amount'; ?></td>
            <td><?php echo 'Actual Amount'; ?></td>
            <?php if ($data['state'] >= loanPenaltyReceiptStateEnum::AUDITING) { ?>
                <td><?php echo 'Auditor'; ?></td>
            <?php } ?>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="7">No Record</td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $apply) { ?>
                <tr>
                    <td>
                        <?php echo $apply['login_code']; ?>
                    </td>
                    <td>
                        <?php echo $apply['currency']; ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($apply['receivable']); ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($apply['deducting']); ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($apply['paid']); ?>
                    </td>
                    <?php if ($data['state'] >= loanPenaltyReceiptStateEnum::AUDITING) { ?>
                        <td><?php echo $apply['auditor_name']; ?></td>
                    <?php } ?>
                    <td>
                        <?php if ($data['state'] == loanPenaltyReceiptStateEnum::CREATE || ($data['state'] == loanPenaltyReceiptStateEnum::AUDITING && $data['user_id'] == $apply['auditor_id'])) { ?>
                            <div class="custom-btn-group">
                                <a title="" class="custom-btn custom-btn-secondary"
                                   href="<?php echo getUrl('loan_committee', 'handlePenaltyRequest', array('uid' => $apply['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <span><i class="fa fa-vcard-o"></i>Handle</span>
                                </a>
                            </div>
                        <?php } ?>

                        <?php if ($data['state'] > loanPenaltyReceiptStateEnum::AUDITING) { ?>
                            <div class="custom-btn-group">
                                <a title="" class="custom-btn custom-btn-secondary"
                                   href="<?php echo getUrl('loan_committee', 'showPenaltyRequest', array('uid' => $apply['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <span><i class="fa fa-vcard-o"></i>Detail</span>
                                </a>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
