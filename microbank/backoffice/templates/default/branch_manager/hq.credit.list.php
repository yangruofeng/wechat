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
            <td>Grant Time</td>
            <td><?php echo 'Remark';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="9">No Record</td>
            </tr>
        <?php } else { ?>
            <?php $count = 1;
            $j = 0;
            foreach ($data['data'] as $credit_grant) { ++$j;$grant_rate = $credit_grant['grant_rate'];?>
                <tr class="<?php echo $j % 2 == 1 ? 'tr_1' : 'tr_2'?>">

                    <td rowspan="<?php echo $count ? : 1?>">
                        <?php echo ncPriceFormat($credit_grant['monthly_repayment_ability']);?>
                    </td>
                    <td rowspan="<?php echo $count ? : 1?>">
                        <?php echo $credit_grant['credit_terms'] . ' Months';?>
                    </td>
                    <td rowspan="<?php echo $count ? : 1?>">
                        <?php echo ncPriceFormat($credit_grant['default_credit']);?>
                    </td>
                    <td rowspan="<?php echo $count ? : 1?>">
                        <?php
                        $increase_credit = 0;
                        foreach($credit_grant['grant_detail_list'] as $val) {
                            $increase_credit += $val['credit'];
                        }
                        echo ncPriceFormat($increase_credit);
                        ?>
                    </td>
                    <td rowspan="<?php echo $count ? : 1?>">
                        <?php echo ncPriceFormat($credit_grant['max_credit']);?>
                    </td>
                    <td>
                        <?php echo $credit_grant['package_name']?>
                    </td>
                    <td>
                        <?php echo $credit_grant['grant_time']?>
                    </td>
                    <td rowspan="<?php echo $count ? : 1?>">
                        <?php echo $credit_grant['remark'];?>
                    </td>
                </tr>

            <?php }?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>