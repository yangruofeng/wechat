
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Account No</td>
            <td>Account Title</td>
            <td>Balance</td>
            <td>Accounts</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>

        <!--        1 -->
        <tr class="tr_even" style="font-weight: 500">
            <td colspan="4">Loan Account </td>
        </tr>
        <tr class="tr_odd">
            <td>1-3120-2-1-0</td>
            <td>STDL - Individuals<=1 year_USD1</td>
            <td>19,053.25</td>
            <td>47</td>
        </tr>
        <tr class="tr_even">
            <td>1-3120-2-1-1</td>
            <td>STDL - Individuals<=1 year_USD_PD<30</td>
            <td>29.00</td>
            <td>1</td>
        </tr>

<!--        总和-->
        <tr class="total_amount border_top">
        <td colspan="2"><?php echo 'Total'?></td>
        <td>2,196,543.17</td>
        <td>354</td>
        </tr>

        <tr class="total_amount border_top">
        <td colspan="3"><?php echo 'Total'?></td>
        <td>354</td>
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