<div class="panel panel-default" style="min-height: 350px">
    <div class="panel-heading">
        <h5 class="panel-title"><i class="fa fa-info-circle"></i> Summary</h5>
    </div>
    <table class="table table-condensed table-no-background  record-base-table">
        <tr>
            <td><label class="control-label">Monthly Repayment Ability</label></td>
            <td><?php echo ncPriceFormat($detail['monthly_repayment_ability']);?></td>
        </tr>
        <?php
        $all_assets=array_merge(array(),$detail['assets'],$detail['is_assets']);
        ?>

        <tr>
            <td><label class="control-label">Default Credit</label></td>
            <td>
                <em><?php echo ncPriceFormat($detail['default_credit']);?></em>
                <span style="padding-left: 10px">
                     <?php echo $output['credit_category'][$detail['default_credit_category_id']]['alias'] ?>
                </span>
            </td>
        </tr>

        <tr>
            <td><label class="control-label">Required To Mortgage</label></td>
            <td></td>
        </tr>
        <?php if (count($all_assets) > 0) { ?>
            <?php foreach($all_assets as $k => $v) {
                if($v['credit']<=0) continue;
                ?>
                <tr>
                    <td style="padding-left: 50px">
                        <span> <?php echo $v['asset_name']?></span>
                        <span style="font-size: 12px;font-weight: 400">(<?php $str = $asset_enum[$v['asset_type']];
                            echo $str;
                            ?>)</span>
                    </td>
                    <td>
                        <em><?php echo ncPriceFormat($v['credit']) ?></em>
                        <span style="padding-left: 10px">
                            <?php echo $output['credit_category'][$v['member_credit_category_id']]['alias'] ?>
                        </span>
                    </td>
                </tr>
            <?php }?>
        <?php } else { ?>
            <tr>
                <td><span class="pl-25"></span></td>
                <td>
                    No Record
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td><label class="control-label">Required To Collateral</label></td>
            <td></td>
        </tr>
        <?php foreach($all_assets as $k => $v) {
            if($v['credit']>0) continue;
            ?>
            <tr>
                <td style="padding-left: 50px">
                    <span> <?php echo $v['asset_name']?></span>
                        <span style="font-size: 12px;font-weight: 400">(<?php $str = $asset_enum[$v['asset_type']];
                            echo $str;
                            ?>)</span>
                </td>
                <td>
                        <span style="padding-left: 10px">
                            <?php echo $output['credit_category'][$v['member_credit_category_id']]['alias'] ?>
                        </span>
                </td>
            </tr>
        <?php }?>



        <tr>
            <td><label class="control-label">Max Credit</label></td>
            <td><kbd><?php echo ncPriceFormat($detail['max_credit']);?></kbd></td>
        </tr>
        <tr>
            <td><label class="control-label">Grant Time</label></td>
            <td><?php echo timeFormat($detail['grant_time']);?></td>
        </tr>
        <tr>
            <td><label class="control-label">Valid Terms</label></td>
            <td>
                <kbd><?php echo $detail['credit_terms'] . ' Months'?> </kbd>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Authorized Time</label></td>
            <td>
                <?php echo $detail['contract'] ? timeFormat($detail['contract']['create_time']) : 'Not Yet';?>
            </td>
        </tr>
    </table>
</div>
