
<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td>Account No.</td>
            <td>Customer Name</td>
            <td>Loan Type</td>
            <td>Disbursed Amount</td>
            <td>Term</td>
            <td>loan Balance</td>
            <td>Due Date</td>
            <td>Scheduled</td>
            <td>Total Due as of report Date</td>
            <td>Total Close Amount</td>
            <td>Recovery</td>
        </tr>
        <tr class="table-header t2">
            <td></td>
            <td>Phone#</td>
            <td></td>
            <td></td>
            <td>Type</td>
            <td></td>
            <td></td>
            <td>Actul</td>
            <td></td>
            <td>Odue Pri</td>
            <td>RecoveryA/cBal</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>
        <tr>
            <td>32-565</td>
            <td>lily</td>
            <td>12</td>
            <td>2000.00</td>
            <td>42</td>
            <td>124.00</td>
            <td>2018-1-1</td>
            <td>100.00</td>
            <td>0.00</td>
            <td>415.00</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Monthly</td>
            <td></td>
            <td></td>
            <td>73</td>
            <td></td>
            <td>0.00</td>
            <td></td>
        </tr>

        <tr class="total_amount border_top">
            <td colspan="2"><?php echo 'Grant Total '?></td>
            <td>11</td>
            <td colspan="2" class="currency">49,300.00</td>
            <td colspan="2" class="currency">46,278.09</td>
            <td class="currency">1,280.49</td>
            <td class="currency">0.00</td>
            <td class="currency">46,271.68</td>
            <td class="currency"></td>
        </tr>
        <tr class="total_amount tr_td_pd_top_0">
            <td colspan="7"></td>
            <td class="currency">752.11</td>
            <td></td>
            <td class="currency">-38.59</td>
            <td class="currency">0.00</td>
        </tr>

        <!--                --><?php //}?>
        <!--            --><?php //}else{ ?>
        <!--                <tr>-->
        <!--                    <td colspan="11">No Record</td>-->
        <!--                </tr>-->
        <!--            --><?php //} ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>