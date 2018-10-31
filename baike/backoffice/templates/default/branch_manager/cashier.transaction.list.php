<?php
$tradingTypeLang = enum_langClass::getPassbookTradingTypeLang();
$passbookAccountFlowStateLang = enum_langClass::getPassbookAccountFlowStateLang();
?>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Trading ID</td>
            <td>Transfer Time</td>
            <td>Trade Type</td>
            <td>Begin Balance</td>
            <td>Income</td>
            <td>Payment</td>
            <td>End Balance</td>
            <td>Trade State</td>
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
                        <?php echo ucwords(str_replace('_', ' ', $row['trading_type'])) ?>
                    </td>
                    <td>
                        <?php if($row['state'] == "90"){
                            echo '--';
                        }else{
                            echo $row['begin_balance'];
                        }?>
                    </td>
                    <td>
                        <?php echo $row['debit'] ?>
                    </td>
                    <td>
                        <?php echo $row['credit'] ?>
                    </td>
                    <td>
                        <?php if($row['state'] == "90"){
                            echo '--';
                        }else{
                            echo $row['end_balance'];
                        }?>
                    </td>
                    <td>
                        <?php echo $passbookAccountFlowStateLang[$row['state']] ?>
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
<?php include_once(template("widget/inc_content_pager"));?>