<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Monthly Repayment Ability';?></td>
            <td><?php echo 'Invalid Terms';?></td>
            <td><?php echo 'Default Credit';?></td>
            <td><?php echo 'Increase Credit';?></td>
            <td><?php echo 'Max Credit';?></td>
            <td><?php echo 'Interest Package';?></td>
            <td><?php echo 'Grant Time';?></td>
            <td><?php echo 'Remark';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="9">
                    <?php include(template(":widget/no_record")); ?>
                </td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $credit_grant) { ?>
                <tr>
                    <td>
                        <?php echo ncPriceFormat($credit_grant['monthly_repayment_ability']);?>
                    </td>
                    <td>
                        <?php echo $credit_grant['credit_terms'] . ' Months';?>
                    </td>
                    <td >
                        <?php echo ncPriceFormat($credit_grant['default_credit']);?>
                    </td>
                    <td>
                        <?php
                        $increase_credit = 0;
                        foreach($credit_grant['grant_detail_list'] as $val) {
                            $increase_credit += $val['credit'];
                        }
                        echo ncPriceFormat($increase_credit);
                        ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($credit_grant['max_credit']);?>
                    </td>
                    <td>
                        <?php echo $credit_grant['package_name']?>
                    </td>
                    <td>
                        <?php echo $credit_grant['grant_time']?>
                    </td>
                    <td>
                        <?php echo $credit_grant['remark'];?>
                    </td>
                </tr>
            <?php }?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>