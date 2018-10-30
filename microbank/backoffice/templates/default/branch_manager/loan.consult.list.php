<div>
    <table class="table verify-table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Applied Amount';?></td>
            <td><?php echo 'Loan Purpose';?></td>
            <td><?php echo 'Loan Mortgage';?></td>
            <td><?php echo 'Contact Phone';?></td>
<!--            <td>--><?php //echo 'State';?><!--</td>-->
            <td><?php echo 'Apply Source';?></td>
            <td><?php echo 'Apply Time'; ?></td>
            <?php if ($data['verify_state'] > loanConsultStateEnum::BRANCH_REJECT) { ?>
                <td><?php echo 'CO Name'; ?></td>
            <?php } else if ($data['verify_state'] >= loanConsultStateEnum::ALLOT_BRANCH) { ?>
                <td><?php echo 'BM Name'; ?></td>
            <?php } ?>
            <?php if ($data['verify_state'] >= loanConsultStateEnum::CO_CANCEL) { ?>
                <td><?php echo 'CO Remark'; ?></td>
            <?php } else if ($data['verify_state'] >= loanConsultStateEnum::BRANCH_REJECT) { ?>
                <td><?php echo 'BM Remark'; ?></td>
            <?php } ?>
            <?php if ($data['verify_state'] < loanConsultStateEnum::CO_CANCEL && $data['verify_state'] != loanConsultStateEnum::BRANCH_REJECT) { ?>
                <td><?php echo 'Function';?></td>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php if( $row['member_id'] ){ ?>
                        <a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['member_id'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['applicant_name'] ?></a>
                    <?php }else{ ?>
                        <span><?php echo $row['applicant_name'] ?></span>
                    <?php } ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['apply_amount'])?>
                </td>
                <td>
                    <?php echo $row['loan_purpose']?>
                </td>
                <td>
                    <?php echo $row['mortgage']; ?>
                </td>
                <td>
                    <?php echo $row['contact_phone']?>
                </td>
<!--                <td>-->
<!--                    --><?php //echo $lang['loan_contract_state_' . $row['state']]; ?>
<!--                </td>-->
                <td>
                    <?php echo ucwords(str_replace('_', ' ', $row['request_source'])); ?>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time'])?>
                </td>
                <?php if ($data['verify_state'] > loanConsultStateEnum::BRANCH_REJECT) { ?>
                    <td><?php echo $row['co_name'];?></td>
                <?php }elseif ($data['verify_state'] >= loanConsultStateEnum::ALLOT_BRANCH) { ?>
                    <td><?php echo $row['bm_name'];?></td>
                <?php } ?>
                <?php if ($data['verify_state'] >= loanConsultStateEnum::CO_CANCEL) { ?>
                    <td><?php echo $row['co_remark'];?></td>
                <?php } else if ($data['verify_state'] >= loanConsultStateEnum::BRANCH_REJECT) { ?>
                    <td><?php echo $row['bm_remark'];?></td>
                <?php } ?>
                <?php if ($data['verify_state'] < loanConsultStateEnum::CO_CANCEL && $data['verify_state'] != loanConsultStateEnum::BRANCH_REJECT) { ?>
                <td>
                    <div class="custom-btn-group">
                        <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('branch_manager', 'handleLoanConsult', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                            <span><i class="fa fa-check-circle-o"></i><?php echo 'Handle'?></span>
                        </a>
                    </div>
                </td>
                <?php } ?>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>
