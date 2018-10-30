<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    em {
        font-weight: 500;
        font-size: 15px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        background-color: #FFF;
        overflow: hidden;
    }

    .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    .voting-list .fa {
        font-size: 18px;
        margin-left: 10px;
        color: #666666;
    }

    .voting-list .fa-check {
        color: #008000!important;
    }

    .voting-list .fa-close {
        color: red!important;
    }

</style>
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
        </td>
    </tr>
    <tr>
        <td>
            <span style="padding-left: 50px">For Credit Category：</span>
        </td>
        <td>
            <?php echo $output['credit_category'][$detail['default_credit_category_id']]['alias'] ?>
        </td>
    </tr>
    <tr>
        <td><label class="control-label">Increase Credit By</label></td>
        <td></td>
    </tr>

    <?php if (count($all_assets) > 0) { ?>
        <?php foreach($all_assets as $k => $v) {?>
            <tr>
                <td>
                    <span class="pl-25">
                        <span> <?php echo $v['asset_name']?></span>
                        <span style="font-size: 12px;font-weight: 400">(<?php $str = $asset_enum[$v['asset_type']];
                            echo $str;
                            ?>)</span>
                    </span>
                </td>
                <td>
                    <em><?php echo ncPriceFormat($v['credit']) ?></em>
                </td>
            </tr>
            <tr style="<?php if(!$v['credit']) echo 'display:none'?>">
                <td>
                    <span  style="padding-left: 50px">For Credit Category：</span>
                </td>
                <td>
                    <?php echo $output['credit_category'][$v['member_credit_category_id']]['alias'] ?>
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
            <?php echo $detail['credit_terms'] . 'Months'?>
        </td>
    </tr>
    <tr>
        <td><label class="control-label">Loan Fee</label></td>
        <td>
            <?php echo $detail['loan_fee'] . " ".($detail['loan_fee_type']?'$':'%')?>
        </td>
    </tr>
    <tr>
        <td><label class="control-label">Admin Fee</label></td>
        <td>
            <?php echo $detail['admin_fee'] . " ".($detail['admin_fee_type']?'$':'%')?>
        </td>
    </tr>


    <tr>
        <td><label class="control-label">Authorized Time</label></td>
        <td>
            <?php echo $detail['contract'] ? timeFormat($detail['contract']['create_time']) : 'Not Yet';?>
        </td>
    </tr>
    <?php if ($detail['relative_list']) { ?>
        <tr>
            <td><label class="control-label">Request Relative</label></td>
            <td>
                <ul class="list-group">
                    <?php foreach($detail['relative_list'] as $rel){?>
                        <li class="list-group-item">
                            <table class="table table-no-background">
                                <tr>
                                    <td rowspan="3">
                                        <a href="<?php echo getImageUrl($rel['headshot']) ?>" target="_blank" title="Head portraits">
                                            <img class="img-icon"
                                                 src="<?php echo getImageUrl($rel['headshot'], imageThumbVersion::SMALL_ICON) ?>">
                                        </a>
                                    </td>
                                    <td> <label><?php echo $rel['name']?></label></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $rel['relation_type']." / ".$rel['relation_name']?>
                                    </td>
                                    <td>
                                        <?php echo $rel['contact_phone']?>
                                    </td>
                                </tr>
                            </table>
                        </li>
                    <?php }?>
                </ul>
            </td>
        </tr>
    <?php }?>

</table>