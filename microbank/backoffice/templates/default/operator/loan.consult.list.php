<div>
    <table class="table verify-table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Name';?></td>
            <td><?php echo 'Applied Amount';?></td>
            <td><?php echo 'Loan Purpose';?></td>
            <td><?php echo 'Loan Mortgage';?></td>
            <td><?php echo 'Contact Phone';?></td>

            <td><?php echo 'Apply Source';?></td>
            <td><?php echo 'Apply Time';?></td>
            <td><?php echo $data['verify_state'] == loanConsultStateEnum::CREATE ? 'Function' : 'Operator';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php if( $row['member_id'] ){ ?>
                        <a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$row['member_id'], 'show_menu'=>'client-client','pre'=>'consult'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['applicant_name'] ?></a>
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

                <td>
                    <?php echo ucwords(str_replace('_', ' ', $row['request_source'])); ?>
                </td>

                <td>
                    <?php echo timeFormat($row['create_time'])?>
                </td>
                <td>
                    <?php if ($row['state'] == loanConsultStateEnum::CREATE) { ?>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('operator', 'getTaskOfConsult', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                <span><i class="fa fa-check-circle-o"></i><?php echo 'Get'?></span>
                            </a>
                        </div>
                    <?php }else{ ?>
                        <?php echo $row['operator_name']?>
                    <?php }?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>
