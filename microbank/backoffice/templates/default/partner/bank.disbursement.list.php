<div class="important-info clearfix">
    <div class="item">
        <p>Total Amount</p>
        <div class="c"><?php echo ncAmountFormat($data['total_amount'])?></div>
    </div>
    <div class="item">
        <p>Sub-Total(Success)</p>
        <div class="c"><?php echo ncAmountFormat($data['total_success'])?></div>
    </div>
    <div class="item">
        <p>Sub-Total(Failure)</p>
        <div class="c"><?php echo ncAmountFormat($data['total_failure'])?></div>
    </div>
    <div class="item">
        <p>Sub-Total(In Hand)</p>
        <div class="c"><?php echo ncAmountFormat($data['total_in_hand'])?></div>
    </div>
</div>

<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn';?></td>
            <td><?php echo 'Amount';?></td>
            <td><?php echo 'Type';?></td>
            <td><?php echo 'Receiver Name';?></td>
            <td><?php echo 'Receiver Account';?></td>
            <td><?php echo 'Teller';?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Creator';?></td>
            <td><?php echo 'Time';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <a href="<?php echo getUrl('loan', 'contractDetail', array('uid' => $row['contract_id'], 'show_menu' => 'loan-contract'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['contract_sn'] ?></a><br/>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['amount']) ?><br/>
                </td>
                <td>
                    <?php switch ($row['receiver_type']) {
                        case 0:
                            echo 'Cash';
                            break;
                        case 10:
                            echo 'Bank';
                            break;
                        case 21:
                            echo 'Asiaweiluy';
                            break;
                        case 30:
                            echo 'Passbook';
                            break;
                        default:
                            echo '';
                    } ?><br/>
                </td>
                <td>
                    <?php echo $row['receiver_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['receiver_account'] ?><br/>
                </td>
                <td>
                    <?php echo $row['teller_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['state'] == 10 ? 'In Hand' : ($row['state'] == 11 ? 'Failure' : 'Success') ?><br/>
                </td>
                <td>
                    <?php echo $row['creator_name'] ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']) ?><br/>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

