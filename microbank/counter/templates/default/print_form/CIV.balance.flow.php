<?php
$tradingTypeLang = enum_langClass::getPassbookTradingTypeLang();
$passbookAccountFlowStateLang = enum_langClass::getPassbookAccountFlowStateLang();
?>
<div style="margin: 5px">
    <div class="col-sm-12" style="text-align: center;font-size: 16px!important;font-weight: 600;margin-bottom: 30px">
        <?php echo 'Branch Cash In Vault Flow'; ?>
    </div>

    <div style="margin-bottom: 30px">
        <div style="font-size: 14px;font-weight: 600;margin-bottom: 10px">
            <span style="margin-left: 10px"><?php echo 'Branch Name' ?>：
                <span class="small"><?php echo $output['branch_name'] ?></span>
            </span>
            <span style="margin-left: 30px"><?php echo 'Currency' ?>：
                <span class="small"><?php echo $output['currency'] ?></span>
            </span>
            <span style="margin-left: 30px">
                <?php echo 'From'; ?>：<span class="small"><?php echo $output['date_start'] ?></span>&nbsp;
                To：<span class="small"><?php echo $output['date_end'] ?></span>
            </span>
        </div>

        <table class="table">
            <thead>
            <tr class="table-header">
                <td>Trading </br> ID</td>
                <td>Time</td>
                <td>Trade </br> Type</td>
                <td>Begin </br> Balance</td>
                <td>Cash </br> in</td>
                <td>Cash </br> out</td>
                <td>End </br> Balance</td>
                <td>Trade </br> State</td>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php foreach ($output['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['trade_id'] ?>
                    </td>
                    <td>
                        <?php echo $row['update_time'] ?>
                    </td>
                    <td>
                        <?php echo $tradingTypeLang[$row['trading_type']] ?>
                    </td>
                    <td>
                        <?php if ($row['state'] == "90") {
                            echo '--';
                        } else {
                            echo ncPriceFormat($row['begin_balance']);
                        } ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['debit']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['credit']) ?>
                    </td>
                    <td>
                        <?php if ($row['state'] == "90") {
                            echo '--';
                        } else {
                            echo ncPriceFormat($row['end_balance']);
                        } ?>
                    </td>
                    <td>
                        <?php echo ucwords($passbookAccountFlowStateLang[$row['state']]) ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
