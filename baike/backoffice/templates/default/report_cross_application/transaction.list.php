
<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td>Trn#</td>
            <td>Account No.</td>
            <td>Type</td>
            <td>Date</td>
            <td>Tlr No.</td>
            <td>TrnAmount</td>
            <td>Principal</td>
            <td>Penalty</td>
            <td>Tax</td>
            <td></td>
            <td>Debit Interest</td>
        </tr>
        <tr class="table-header t2">
            <td>Previous</td>
            <td>CID No.</td>
            <td>User Type</td>
            <td>Prev. Trn Date</td>
            <td>Value Date</td>
            <td>Balance</td>
            <td>Interest</td>
            <td>Charges</td>
            <td>Added Tax</td>
            <td>Trn Desc</td>
            <td></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>

        <!--        1 -->
        <tr class="tr_odd">
            <td>350220</td>
            <td>416-006068-01-1</td>
            <td>INSP</td>
            <td>22-06-2018 </td>
            <td>404</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>0.00</td>
        </tr>
        <tr class="tr_odd">
            <td>348575</td>
            <td>006068</td>
            <td></td>
            <td>22-05-2018</td>
            <td>22-06-20180</td>
            <td></td>
            <td></td>
            <td></td>
            <td>0.00</td>
            <td>Installment Posted</td>
            <td></td>
        </tr>

        <tr class="tr_even">
            <td>350243</td>
            <td>1-1110-2-1-1</td>
            <td>RCPT</td>
            <td>22-06-2018</td>
            <td>301</td>
            <td>15,000.00</td>
            <td>15,000.00</td>
            <td>0.00</td>
            <td>0.00</td>
            <td></td>
            <td>0.00</td>
        </tr>
        <tr class="tr_even">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>0.00</td>
            <td>0.00</td>
            <td>0.00</td>
            <td>BTB-CT0434 From Chief Teller_Teller01</td>
            <td></td>
        </tr>

<!--        总和-->
        <tr class="total_amount border_top">
            <td colspan="2"><?php echo 'Total'?></td>
            <td colspan="3"><?php echo '50' ?></td>
            <td><?php echo '35,278.01 DR '?></td>
            <td><?php echo '35,278.01 DR '?></td>
            <td><?php echo '0.00 DR '?></td>
            <td><?php echo '0.00 DR '?></td>
            <td></td>
            <td><?php echo '0.00 DR '?></td>
        </tr>
        <tr class="total_amount tr_td_pd_top_0">
            <td colspan="5"></td>
            <td><?php echo '35,949.55 CR '?></td>
            <td><?php echo '35,792.27 CR '?></td>
            <td><?php echo '0.00 DR '?></td>
            <td><?php echo '0.00 DR '?></td>
            <td></td>
            <td><?php echo '0.00 DR '?></td>
        </tr>
        <tr class="total_amount border_top">
            <td colspan="5"></td>
            <td></td>
            <td><?php echo '0.00 DR '?></td>
            <td><?php echo '0.00 DR '?></td>
            <td><?php echo '0.00 DR '?></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="total_amount tr_td_pd_top_0">
            <td colspan="5"></td>
            <td></td>
            <td><?php echo '157.28 CR '?></td>
            <td><?php echo '0.00 DR '?></td>
            <td><?php echo '0.00 DR '?></td>
            <td></td>
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