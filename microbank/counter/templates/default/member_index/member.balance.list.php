<?php if (!$data['sts']) { ?>
    <div>
        <span style="padding: 15px;font-size: 16px"><?php echo $data['msg'];?></span>
    </div>
<?php } else { ?>
    <div>
        <table class="table">
            <thead>
            <tr class="table-header">
                <td>Trading ID</td>
                <td>Trade Type</td>
                <td>Begin Balance</td>
                <td>Cash In</td>
                <td>Cash Out</td>
                <td>End Balance</td>
                <td>Memo</td>
                <td>Operate Time</td>
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
                            <?php echo $row['trading_type'] ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($row['begin_balance']) ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($row['credit']) ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($row['debit']) ?>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($row['end_balance']) ?>
                        </td>
                        <td>
                            <?php echo $row['sys_memo']; ?>
                        </td>
                        <td>
                            <?php echo $row['update_time'] ?>
                        </td>
                    </tr>
                <?php }?>
            <?php }else{ ?>
                <tr>
                    <td colspan="7">No Record</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include_once(template("widget/inc_content_pager"));?>
<?php } ?>
