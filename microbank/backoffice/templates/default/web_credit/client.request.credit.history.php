<style>
    .tr_1 {
        background-color: #FFF!important;
    }
    .tr_2 {
        background-color: #F3F4F6!important;
    }

    .cl-info {
        height: 30px;line-height: 40px;font-size: 16px;
    }

    .cl-info .col-sm-4 {
        overflow: hidden;
        text-overflow:ellipsis;
        white-space: nowrap;
    }

    .cl-info span {
        font-weight: 600;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'showClientDetail', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Credit Process</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'showRequestCredit', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Request Credit</span></a></li>
                <li><a class="current"><span>History</span></a></li>
            </ul>
        </div>
    </div>
    <?php $certification_type = enum_langClass::getCertificationTypeEnumLang();?>
    <div class="container" style="max-width: 1300px">
        <div class="cl-info">
            <div class="col-sm-4">
                Client Name:
                <span><?php echo $output['client_info']['display_name']?></span>
            </div>
            <div class="col-sm-4">
                Operator Name:
                <span><?php echo $output['user_info']['user_name']?></span>
            </div>
        </div>
        <div class="business-content">
            <div class="business-list">
                <table class="table">
                    <thead>
                    <tr class="table-header">
                        <td><?php echo 'Client Request Credit';?></td>
                        <td><?php echo 'Monthly Repayment Ability';?></td>
                        <td><?php echo 'Invalid Terms';?></td>
                        <td><?php echo 'Default Credit';?></td>
                        <td><?php echo 'Increase Credit';?></td>
                        <td><?php echo 'Max Credit';?></td>
                        <td><?php echo 'Product';?></td>
                        <td><?php echo 'NoMortgage';?></td>
                        <td><?php echo 'MortgageSoft';?></td>
                        <td><?php echo 'MortgageHard';?></td>
                        <?php if (reset($output['credit_suggest'])['request_type'] == 1) { ?>
                            <td><?php echo 'State';?></td>
                        <?php } ?>
                        <td><?php echo 'Remark';?></td>
                    </tr>
                    </thead>
                    <tbody class="table-body">
                    <?php if (!$output['credit_suggest']) { ?>
                        <tr>
                            <td colspan="9">No Record</td>
                        </tr>
                    <?php } else { ?>
                        <?php $count = count($output['product_list']);
                        $j = 0;
                        foreach ($output['credit_suggest'] as $credit_suggest) { ++$j;$suggest_rate = $credit_suggest['suggest_rate'];?>
                        <tr class="<?php echo $j % 2 == 1 ? 'tr_1' : 'tr_2'?>">
                            <td rowspan="<?php echo $count ? : 1?>">
                                <?php echo ncAmountFormat($credit_suggest['client_request_credit']);?>
                            </td>
                            <td rowspan="<?php echo $count ? : 1?>">
                                <?php echo ncAmountFormat($credit_suggest['monthly_repayment_ability']);?>
                            </td>
                            <td rowspan="<?php echo $count ? : 1?>">
                                <?php echo $credit_suggest['credit_terms'] . ' Months';?>
                            </td>
                            <td rowspan="<?php echo $count ? : 1?>">
                                <?php echo ncAmountFormat($credit_suggest['default_credit']);?>
                            </td>
                            <td rowspan="<?php echo $count ? : 1?>">
                                <?php
                                    $increase_credit = 0;
                                    foreach($credit_suggest['suggest_detail_list'] as $val) {
                                        $increase_credit += $val['credit'];
                                    }
                                    echo $increase_credit;
                                ?>
                            </td>
                            <td rowspan="<?php echo $count ? : 1?>">
                                <?php echo ncAmountFormat($credit_suggest['max_credit']);?>
                            </td>
                            <?php
                            $first_product = reset($output['product_list']);
                            $product_rate = $suggest_rate[$first_product['uid']]
                            ?>
                            <td>
                                <?php echo $first_product['sub_product_name'];?>
                            </td>
                            <td>
                                <?php echo $product_rate['rate_no_mortgage'] ? ($product_rate['rate_no_mortgage'] . '%') : '';?>
                            </td>
                            <td>
                                <?php echo $product_rate['rate_mortgage1'] ? ($product_rate['rate_mortgage1'] . '%') : '';?>
                            </td>
                            <td>
                                <?php echo $product_rate['rate_mortgage2'] ? ($product_rate['rate_mortgage2'] . '%') : '';?>
                            </td>
                            <?php if ($credit_suggest['request_type'] == 1) { ?>
                                <td rowspan="<?php echo $count ? : 1?>">
                                    <?php echo $lang['member_credit_suggest_' . $credit_suggest['state']]; ?>
                                </td>
                            <?php } ?>
                            <td rowspan="<?php echo $count ? : 1?>">
                                <?php echo $credit_suggest['remark'];?>
                            </td>
                        </tr>
                            <?php $i = 0;foreach ($output['product_list'] as $product) { ++$i;$product_rate = $suggest_rate[$product['uid']]?>
                                <?php if($i == 1) continue;?>
                                <tr class="<?php echo $j % 2 == 1 ? 'tr_1' : 'tr_2'?>">
                                    <td>
                                        <?php echo $product['sub_product_name'];?>
                                    </td>
                                     <td>
                                         <?php echo $product_rate['rate_no_mortgage'] ? ($product_rate['rate_no_mortgage'] . '%') : '';?>
                                     </td>
                                     <td>
                                         <?php echo $product_rate['rate_mortgage1'] ? ($product_rate['rate_mortgage1'] . '%') : '';?>
                                     </td>
                                     <td>
                                         <?php echo $product_rate['rate_mortgage2'] ? ($product_rate['rate_mortgage2'] . '%') : '';?>
                                     </td>
                                </tr>
                            <?php } ?>
                    <?php }?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>