
<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td>Gl Account</td>
            <td></td>
            <td style="text-align: right;padding-right: 25px">Previous</td>
            <td></td>
            <td style="text-align: right;padding-right: 25px">This Month</td>
            <td></td>
            <td style="text-align: right;padding-right: 25px">Current Balance</td>
            <td></td>
        </tr>
        <tr class="table-header t2">
            <td colspan="2"></td>
            <td>Debit</td>
            <td>Credit</td>
            <td>Debit</td>
            <td>Credit</td>
            <td>Debit</td>
            <td>Credit</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>

        <!--        1级 -->
        <tr class="tr_even" style="font-weight: 600">
            <td>1</td>
            <td>Total Assets Control Account</td>
            <td>2,061,703.40</td>
            <td></td>
            <td>2,031,430.60</td>
            <td></td>
            <td>2,270,188.39</td>
            <td></td>
        </tr>
        <tr class="tr_even" style="font-weight: 600">
            <td colspan="2"></td>
            <td></td>
            <td></td>
            <td></td>
            <td>-1,822,945.61</td>
            <td></td>
            <td></td>
        </tr>

<!--        2级-->
        <tr class="tr_odd" style="font-weight: 500">
            <td>1-1110-2</td>
            <td>Cash in Vault and on hand</td>
            <td>35,570.58</td>
            <td> </td>
            <td>716,079.31</td>
            <td> </td>
            <td>21,894.32</td>
            <td> </td>
        </tr>
        <tr class="tr_odd" style="font-weight: 500">
            <td colspan="2"></td>
            <td></td>
            <td></td>
            <td></td>
            <td>-729,755.57</td>
            <td></td>
            <td></td>
        </tr>

<!--        3级-->
        <tr class="tr_even">
            <td>1-1110-2-1-1</td>
            <td>Cash in Vault and Hand_USD</td>
            <td>35,570.58</td>
            <td> </td>
            <td>262,237.53</td>
            <td> </td>
            <td>21,894.32</td>
            <td> </td>
        </tr>
        <tr class="tr_even">
            <td colspan="2"></td>
            <td></td>
            <td></td>
            <td></td>
            <td>-276,113.79</td>
            <td></td>
            <td></td>
        </tr>



        <!--        总和-->
        <tr class="total_amount border_top">
            <td></td>
            <td><?php echo 'Total Debits'?></td>
            <td><?php echo '121,530.80' ?></td>
            <td></td>
            <td><?php echo '2,183,014.81' ?></td>
            <td></td>
            <td><?php echo '198,047.67' ?></td>
            <td></td>
        </tr>
        <tr class="total_amount">
            <td></td>
            <td><?php echo 'Total Credits'?></td>
            <td></td>
            <td><?php echo '121,530.80' ?></td>
            <td></td>
            <td><?php echo '-2,183,014.81' ?></td>
            <td></td>
            <td><?php echo '198,047.67' ?></td>
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