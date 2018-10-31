
<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td>Account No.</td>
            <td colspan="2">Name</td>
            <td>Interest Rate</td>
            <td>Balance</td>
        </tr>
        <tr class="table-header t2">
            <td>CID</td>
            <td>Product Type</td>
            <td colspan="3">GL Code</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>

        <!--        1 -->
        <tr class="tr_even" style="font-weight: 500">
            <td colspan="5">Loan</td>
        </tr>
        <tr class="tr_odd">
            <td>412-005820-03-4</td>
            <td colspan="2">Heng Sambath</td>
            <td>18.00</td>
            <td>2,000.00</td>
        </tr>
        <tr class="tr_odd">
            <td>005820</td>
            <td>Consumption Loan</td>
            <td>IndLn>1Y</td>
            <td></td>
            <td></td>
        </tr>

        <tr class="tr_even">
            <td>412-005820-01-5</td>
            <td colspan="2">Chey Thida</td>
            <td>16.80</td>
            <td>1,000.00</td>
        </tr>
        <tr class="tr_even">
            <td>005834</td>
            <td>Personal_Annuity (HL)</td>
            <td>IndLn>1Y</td>
            <td></td>
            <td></td>
        </tr>

        <!--        总和-->
        <tr class="total_amount border_top">
            <td><?php echo 'Total'?></td>
            <td colspan="2"><?php echo '5' ?></td>
            <td></td>
            <td>33,000.00</td>
        </tr>
        <tr class="total_amount border_top">
            <td><?php echo 'Grand Total'?></td>
            <td colspan="2"><?php echo '5' ?></td>
            <td></td>
            <td>33,000.00</td>
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