<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Counter Business</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Cash On Hand</span></a></li>
            </ul>
        </div>
    </div>
    <?php $currency_list = (new currencyEnum())->Dictionary();?>
    <div class="container">
        <div class="business-content">
            <div class="business-list">
                <table class="table">
                    <thead>
                    <tr class="table-header">
                        <td>User Name</td>
                        <td>Type</td>
                        <?php foreach ($currency_list as $key => $currency) { ?>
                        <td>Balance(<?php echo $currency?>)</td>
                        <?php } ?>
                        <?php foreach ($currency_list as $key => $currency) { ?>
                        <td>Outstanding(<?php echo $currency?>)</td>
                        <?php } ?>
                        <td>Function</td>
                    </tr>
                    </thead>
                    <tbody class="table-body">
                    <?php $cash_on_hand = $output['cash_on_hand']; ?>
                    <?php if ($cash_on_hand) { ?>
                        <?php foreach ($cash_on_hand as $row) { ?>
                            <tr>
                                <td>
                                    <?php echo $row['user_name'] ?>
                                </td>
                                <td>
                                    <?php echo ucwords(str_replace('_', ' ', $row['user_position'])) ?>
                                </td>
                                <?php foreach ($currency_list as $key => $currency) { ?>
                                    <td>
                                        <?php echo ncPriceFormat($row['balance'][$key]) ?>
                                    </td>
                                <?php } ?>
                                <?php foreach ($currency_list as $key => $currency) { ?>
                                    <td>
                                        <?php echo ncPriceFormat($row['outstanding'][$key]) ?>
                                    </td>
                                <?php } ?>
                                <td>
                                    <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('branch_manager', 'branchCashierTransaction', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                        <span><i class="fa fa-address-card-o"></i>Flow</span>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <td colspan="5">No Record</td>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
