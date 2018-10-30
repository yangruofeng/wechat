<?php
$getLoanConsultStateLang = enum_langClass::getLoanConsultStateLang();
?>
<div>
    <table class="table table-bordered">
        <thead>
            <tr class="table-header">
                <td>Name</td>
                <td>Applied Amount</td>
                <td>Loan Purpose</td>
                <td>Loan Mortgage</td>
                <td>Contact Phone</td>
                <td>Apply Time</td>
                <td>State</td>
                <td>Function</td>
            </tr>
        </thead>
        <tbody class="table-body">
    <?php if ($data['data']) { ?>
        <?php foreach ($data['data'] as $row) { ?>
            <tr>
                <td>
                    <?php echo $row['applicant_name'] ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['apply_amount']) ?>
                </td>
                <td>
                    <?php echo $row['loan_purpose'] ?>
                </td>
                <td>
                    <?php echo str_replace(",", "/", $row['mortgage']) ?>
                </td>
                <td>
                    <?php echo $row['contact_phone'] ?>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']) ?>
                </td>
                <td>
                    <?php echo $getLoanConsultStateLang[$row['state']] ?>
                </td>
                <td>
                    <a class="btn btn-default" href="<?php echo getUrl('service', 'consultDetail', array('uid' => $row["uid"]), false, ENTRY_COUNTER_SITE_URL) ?>"><?php echo 'Detail'; ?></a>
                    <?php if($row['state'] == loanConsultStateEnum::ALLOT_BRANCH){?>
                        <a class="btn btn-danger" href="<?php echo getUrl('service', 'deleteLoanConsult', array('uid' => $row["uid"]), false, ENTRY_COUNTER_SITE_URL) ?>"><?php echo 'Delete'; ?></a>
                    <?php }else{?>
                        <button class="btn btn-danger" disabled><?php echo 'Delete'; ?></button>
                    <?php }?>
                </td>
            </tr>
        <?php }?>
    <?php } else { ?>
        <tr>
            <td colspan="8">No Record</td>
        </tr>
    <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>