<?php
$tradingTypeLang = enum_langClass::getPassbookTradingTypeLang();
$passbookAccountFlowStateLang = enum_langClass::getPassbookAccountFlowStateLang();
?>
    <div>
        <table class="table">
            <thead>
            <tr class="table-header">
                <td>Trading ID</td>
                <td>Time</td>
                <td>Trade Type</td>
                <td>Begin Balance</td>
                <td>Cash in</td>
                <td>Cash out</td>
                <td>End Balance</td>
                <td>Trade State</td>
                <td>Remark</td>
                <td>Function</td>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php if($data['data']){ ?>
                <?php foreach ($data['data'] as $row) { ?>
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
                        <td>
                            <?php echo $row['tmark'] ?>
                        </td>
                        <td>
                            <a type="button" class="btn btn-default" href="<?php echo getUrl('cash_in_vault', 'tradeDetail', array('uid'=>$row['trade_id']), false, ENTRY_COUNTER_SITE_URL)?>">
                                <i class="fa fa-address-card-o"></i>
                                <?php echo 'Detail'; ?>
                            </a>
                        </td>
                    </tr>
                <?php }?>
            <?php }else{ ?>
                <tr>
                    <td colspan="8">No Record</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php include_once(template("widget/inc_content_pager"));?>
<?php if($data['data']){ ?>
    <div style="text-align: center;margin-top: 10px">
        <a class="btn btn-danger" style="min-width: 80px;" onclick="print_civ_flow()"><i class="fa fa-print"></i><?php echo ' Print' ?></a>
    </div>
<?php }?>