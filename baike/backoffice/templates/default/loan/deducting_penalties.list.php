<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn'; ?></td>
            <td><?php echo 'Type'; ?></td>
            <td><?php echo 'Received Penalties'; ?></td>
            <td><?php echo 'Remark'; ?></td>
            <td><?php echo 'State'; ?></td>
            <td><?php echo 'Creator'; ?></td>
            <td><?php echo 'Create Time'; ?></td>
            <td><?php echo 'Auditor'; ?></td>
            <td><?php echo 'Audit Time'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['contract_sn'] ?><br/>
                </td>
                <td>
                    <?php echo $lang['deducting_penalties_type_' . $row['type']] ?><br/>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['deducting_penalties']) ?><br/>
                </td>
                <td>
                    <?php echo $row['remark']?><br/>
                </td>
                <td>
                    <?php echo $lang['deducting_penalties_state_' . $row['state']]?><br/>
                </td>
                <td>
                    <?php echo $row['creator_name']?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time'])?><br/>
                </td>
                <td>
                    <?php echo $row['auditor_name']?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['audit_time'])?><br/>
                </td>
                <td>
                    <?php if($row['state'] == loanDeductingPenaltiesState::CREATE){?>
                    <div class="custom-btn-group">
                        <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'showAuditPenalties', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                            <span><i class="fa fa-check"></i>Audit</span>
                        </a>
                    </div>
                    <?php }?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

