<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td>CID</td>
            <td>Account</td>
            <td>Name</td>
            <td>Branch</td>
            <td>Phone</td>
            <td>Credit</td>
            <td>Credit Balance</td>
            <td>Loan Balance</td>
            <td>Account Type</td>
            <td>Create Time</td>
            <td>Function</td>
        </tr>

        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['obj_guid']?:generateGuid($row['member_id'], objGuidTypeEnum::CLIENT_MEMBER);?>
                    </td>
                    <td>
                        <a href="<?php echo getUrl('common', 'showClientDetail', array('country_code' => $row['phone_country'],'phone_number' => $row['phone_number'], 'search_by'=>1), false, BACK_OFFICE_SITE_URL); ?>">
                            <?php echo $row['login_code']?>
                        </a>

                    </td>
                    <td>
                        <?php echo implode('/',array($row['display_name'],$row['kh_display_name']));?>
                    </td>
                    <td>
                        <?php echo $row['branch_name']?:'-'; ?>
                    </td>
                    <td>
                        <?php echo $row['phone_id']?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['credit'])?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['balance'])?>
                    </td>
                    <td>
                        <?php echo memberClass::getLoanBalance($row['member_id'])->DATA?ncPriceFormat(memberClass::getLoanBalance($row['member_id'])->DATA):'0.00'; ?>
                    </td>
                    <td>
                        <?php if($row['account_type'] == 0){echo 'Member';} ?>
                    </td>
                    <td>
                        <?php echo timeFormat($row['create_time'])?>
                    </td>
                    <td>
                        <a class="btn btn-link btn-xs"
                           href="<?php echo getUrl('common', 'showClientDetail', array('country_code' => $row['phone_country'],'phone_number' => $row['phone_number'], 'search_by'=>1), false, BACK_OFFICE_SITE_URL); ?>">
                            <span><i class="fa fa-vcard-o"></i>Detail</span>
                        </a>
                    </td>
                </tr>

            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="20">
                    <?php include(template(":widget/no_record")); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
