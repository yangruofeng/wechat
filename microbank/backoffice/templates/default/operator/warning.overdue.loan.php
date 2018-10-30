<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=1" rel="stylesheet" type="text/css"/>

<?php
$list = $output['list'];
?>

<div class="page">

    <div class="fixed-bar">
        <div class="item-title">
            <h3>Warning Of Overdue Loan</h3>
            <ul class="tab-base">
                <li><a  class="current"><span>Main</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="business-content">
            <div class="business-list">
                <table class="table table-hover">
                    <thead>
                    <tr class="table-header t1">
                        <td>Client Name</td>
                        <td>Client CID</td>
                        <td>Contact Phone</td>
                        <td>Contract SN.</td>
                        <td>Currency</td>
                        <td>Overdue Amount</td>
                        <td>Receivable Date</td>
                    </tr>
                    </thead>
                    <tbody class="table-body">
                    <?php if ($list) { ?>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?php echo $row['login_code'] ?></td>
                                <td><?php echo $row['client_obj_guid'] ?></td>
                                <td><?php echo $row['phone_id'] ?></td>
                                <td><?php echo $row['currency'] ?></td>
                                <td><?php echo $row['contract_sn'] ?></td>
                                <td><?php echo ncPriceFormat($row['amount']) ?></td>
                                <td>
                                    <?php
                                    $date1 = strtotime(date('Y-m-d',strtotime($row['receivable_date'])));
                                    $date2 = strtotime(date('Y-m-d'));
                                    $date = $date2 - $date1;
                                    echo $date/86400 . 'Days';
                                    ?><br/>
                                </td>
                            </tr>
                        <?php } ?>
                        <!--<tr class="total_amount border_top">
                            <td><?php echo 'Totals'?></td>
                            <td><?php echo $data['amount_total']['loan_count']?></td>
                            <?php foreach ($data['currency_list'] as $key => $currency) { ?>
                                <td class=""><?php echo ncPriceFormat($data['amount_total']['loan_amount_' . $key])?></td>
                            <?php } ?>
                        </tr>-->
                    <?php } else { ?>
                        <tr>
                            <td colspan="5">No Record</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
