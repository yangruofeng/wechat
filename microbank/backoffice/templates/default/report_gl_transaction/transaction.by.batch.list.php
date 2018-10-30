<style>

</style>

<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td width="10%">GL Account</td>
            <td width="10%">Trn#</td>
            <td width="20%">Account Title</td>
            <td width="10%">type</td>
            <td width="10%">Branch</td>
            <td width="20%">Ref</td>
            <td width="10%">Debit</td>
            <td width="10%">Credit</td>
        </tr>
        <tr class="table-header t2">
            <td colspan="5">Narrative</td>
            <td colspan="3">Value date</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>

        <!--        1 -->
        <tr style="font-weight: 600;" class="tr_odd">
            <td width="10%" style="padding-bottom: 20px">22-06-2018</td>
            <td colspan="7" style="padding-bottom: 20px">Batch Number: 0001</td>
        </tr>

<!--        1-1 -->
        <tr class="tr_odd border_top">
            <td width="10%" style="padding-bottom:0px">1-110-2-1-1</td>
            <td width="10%">012096</td>
            <td width="20%">Cash in Vault & Hand_USD</td>
            <td width="10%">GLDR</td>
            <td width="10%"></td>
            <td width="20%" style="padding-bottom:0px">BTB-BR0270</td>
            <td width="10%">10,000.00</td>
            <td width="10%"></td>
        </tr>
        <tr class="tr_odd">
            <td colspan="5" style="padding-top:0px">Cash Withdrawal From KHL </td>
            <td colspan="3" style="padding-top:0px">22-06-2018</td>
        </tr>

        <!--        1-2 -->
        <tr class="tr_odd border_top">
            <td width="10%" style="padding-bottom:0px">1-1630-2-7 </td>
            <td width="10%">012097</td>
            <td width="20%">DD &SAV BK rated below A-_US</td>
            <td width="10%">GLDR</td>
            <td width="10%"></td>
            <td width="20%" style="padding-bottom:0px">BTB-BR0270</td>
            <td width="10%"></td>
            <td width="10%">10,000.00</td>
        </tr>
        <tr class="tr_odd">
            <td colspan="5" style="padding-top:0px">Cash Withdrawal From KHL </td>
            <td colspan="3" style="padding-top:0px">22-06-2018</td>
        </tr>

        <tr class="border_top t1 tr_even" style="font-weight: 500">
            <td colspan="5" style="padding-top: 8px!important"><?php echo '0001'?></td>
            <td style="padding-top: 8px!important"><?php echo 'Total Debits: '?></td>
            <td style="padding-top: 8px!important "><?php echo '10,000.00 '?></td>
            <td style="padding-top: 8px!important"></td>
        </tr>
        <tr class="t2 tr_even" style="font-weight: 500">
            <td colspan="2"><?php echo 'Created/Mod/Appr/Posted by: '?></td>
            <td colspan="3"><?php echo '404/404/404/404'?></td>
            <td><?php echo 'Total Credits: '?></td>
            <td></td>
            <td><?php echo '10,000.00 '?></td>
        </tr>


        <!--        2 -->
        <tr style="font-weight: 600;" class="tr_odd">
            <td width="10%" style="padding-bottom: 20px">22-06-2018</td>
            <td colspan="7" style="padding-bottom: 20px">Batch Number: 0002</td>
        </tr>

        <!--        2-1 -->
        <tr class="tr_odd border_top">
            <td width="10%" style="padding-bottom:0px">1-110-2-3-3</td>
            <td width="10%">012056</td>
            <td width="20%">Small item Fur,Fix&EquipNotCap</td>
            <td width="10%">GLDR</td>
            <td width="10%"></td>
            <td width="20%" style="padding-bottom:0px">BTB-CP0109</td>
            <td width="10%">343.00</td>
            <td width="10%"></td>
        </tr>
        <tr class="tr_odd">
            <td colspan="5" style="padding-top:0px">Paid To Curtain</td>
            <td colspan="3" style="padding-top:0px">22-06-2018</td>
        </tr>

        <!--        2-2 -->
        <tr class="tr_odd border_top">
            <td width="10%" style="padding-bottom:0px">1-1630-2-7 </td>
            <td width="10%">012097</td>
            <td width="20%">Cash in Vault & Hand_USD </td>
            <td width="10%">GLDR</td>
            <td width="10%"></td>
            <td width="20%" style="padding-bottom:0px">BTB-CP0109</td>
            <td width="10%"></td>
            <td width="10%">343.00</td>
        </tr>
        <tr class="tr_odd">
            <td colspan="5" style="padding-top:0px">Paid To Curtain</td>
            <td colspan="3" style="padding-top:0px">22-06-2018</td>
        </tr>

        <tr class="border_top t1 tr_even" style="font-weight: 500">
            <td colspan="5"><?php echo '0002'?></td>
            <td><?php echo 'Total Debits: '?></td>
            <td><?php echo '343.00 '?></td>
            <td></td>
        </tr>
        <tr class="t2 tr_even" style="font-weight: 500">
            <td colspan="2"><?php echo 'Created/Mod/Appr/Posted by: '?></td>
            <td colspan="3"><?php echo '404/404/404/404'?></td>
            <td><?php echo 'Total Credits: '?></td>
            <td></td>
            <td><?php echo '343.00 '?></td>
        </tr>

<!--        total-->
        <tr class="total_amount border_top">
            <td colspan="5"></td>
            <td><?php echo 'Grand Total Debits：'?></td>
            <td><?php echo '10,343.00'?></td>
            <td></td>
        </tr>
        <tr class="total_amount">
            <td colspan="5"></td>
            <td><?php echo 'Grand Total Credits: '?></td>
            <td></td>
            <td><?php echo '10,343.00'?></td>
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