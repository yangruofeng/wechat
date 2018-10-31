
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Account</td>
            <td>Trn#</td>
            <td>Batch</td>
            <td>Teller</td>
            <td>Type</td>
            <td>Trans. Amount</td>
            <td>Balance Amount</td>
            <td>Details</td>
            <td>Previous Trn</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>

        <!--        1 -->
        <tr class="tr_odd">
            <td>1-1110-2-1-1BTB</td>
            <td>350201</td>
            <td></td>
            <td>301</td>
            <td>RCPT</td>
            <td>2,000.00</td>
            <td>0.00</td>
            <td>BTB-CT0426 From Chief Teller_Teller01 </td>
            <td></td>
        </tr>
        <tr class="tr_even">
            <td>425-005735-02-9 </td>
            <td>350208</td>
            <td>AUTO</td>
            <td>404</td>
            <td>MMDR</td>
            <td>300.00</td>
            <td>300.00</td>
            <td>13222021->13222010 </td>
            <td>350189 21-06-2018</td>
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