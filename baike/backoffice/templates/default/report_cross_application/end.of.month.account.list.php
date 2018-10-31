
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Account Number</td>
            <td>Maturity Date</td>
            <td>Principal Amount</td>
            <td>Loan Balance</td>
            <td>Overdue Principal</td>
            <td>Interest To Date</td>
            <td>Penalty To Date</td>
            <td>Interest This Month</td>
            <td>Date Of Last Trn</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>

        <!--        1 -->
        <tr class="tr_odd">
            <td>403-005150-02-6</td>
            <td>18-12-2022</td>
            <td>10,000.00</td>
            <td>9,165.00</td>
            <td>0.00</td>
            <td>55.60</td>
            <td>0.00</td>
            <td>55.60</td>
            <td>18-05-2018</td>
        </tr>
        <tr class="tr_even">
            <td>403-005602-01-7</td>
            <td>11-08-2020</td>
            <td>12,000.00</td>
            <td>8,994.00</td>
            <td>0.00</td>
            <td>75.55</td>
            <td>0.00</td>
            <td>75.55</td>
            <td>11-05-2018</td>
        </tr>

        <!--        总和-->
        <tr class="total_amount border_top">
            <td colspan="9"><?php echo 'Totals'?></td>
        </tr>

        <tr class="total_amount">
            <td colspan="2"><?php echo '357'?></td>
            <td>2,326,125.34</td>
            <td>2,186,758.17</td>
            <td>19,796.24</td>
            <td>20,380.44</td>
            <td>0.00</td>
            <td>20,437.23</td>
            <td></td>
        </tr>

        <!--                --><?php //}?>
        <!--            --><?php //}else{ ?>
        <!--                <tr>-->
        <!--                    <td colspan="5">No Record</td>-->
        <!--                </tr>-->
        <!--            --><?php //} ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>