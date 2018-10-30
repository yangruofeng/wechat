<?php
$tradingTypeLang = enum_langClass::getPassbookTradingTypeLang();
$passbookAccountFlowStateLang = enum_langClass::getPassbookAccountFlowStateLang();
?>
<div style="margin: 10px">
    <div class="col-sm-12" style="text-align: center;font-size: 16px!important;font-weight: 600;margin-bottom: 30px">
        <?php echo 'Teller Cash on hand Flow'; ?>
    </div>

    <div style="margin-bottom: 30px">
        <div style="font-size: 14px;font-weight: 600;margin-bottom: 10px">
            <span style="margin-left: 30px"><?php echo 'User name' ?> : <span class="small"><?php echo $output['currency']?></span></span>
            <span style="margin-left: 30px"><?php echo $lang['print_from']; ?> : <span class="small"><?php echo $output['date_start']?></span> To : <span class="small"><?php echo $output['date_end']?></span> </span>
        </div>

        <table class="table">
            <thead>
            <tr class="table-header">
                <td><?php echo $lang['print_trading_id']; ?></td>
                <td><?php echo $lang['print_operate_time']; ?></td>
                <td><?php echo $lang['print_trade_type']; ?></td>
                <td><?php echo $lang['print_begin_balance']; ?></td>
                <td><?php echo $lang['print_income']; ?></td>
                <td><?php echo $lang['print_payment']; ?></td>
                <td><?php echo $lang['print_end_balance']; ?></td>
                <td>Trade State</td>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php if($output['data']){ ?>
                <?php foreach ($output['data'] as $row) { ?>
                    <tr>
                        <td>
                            <?php echo $row['trade_id'] ?>
                        </td>
                        <td>
                            <?php echo $row['update_time'] ?>
                        </td>
                        <td>
                            <?php echo  $tradingTypeLang[$row['trading_type']] ?>
                        </td>
                        <td>
                            <?php if($row['state'] == "90"){
                                echo '--';
                            }else{
                                echo ncPriceFormat($row['begin_balance']);
                            }?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($row['debit']) ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($row['credit']) ?>
                        </td>
                        <td>
                            <?php if($row['state'] == "90"){
                                echo '--';
                            }else{
                                echo ncPriceFormat($row['end_balance']);
                            }?>
                        </td>
                        <td>
                            <?php echo ucwords($passbookAccountFlowStateLang[$row['state']]) ?>
                        </td>
                    </tr>
                <?php }?>
            <?php }else{ ?>
                <tr>
                    <td colspan="9">No Record</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

    </div>


</div>
