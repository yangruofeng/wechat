<style>

</style>

<div>
    <table class="table">
        <thead>
        <tr class="table-header t1">
            <td>GL Account</td>
            <td>Account Title</td>
            <td>Code</td>
            <td>Value date</td>
            <td>Debit</td>
            <td>Credit</td>
        </tr>
        <tr class="table-header t2">
            <td></td>
            <td colspan="2">Narrative</td>
            <td colspan="3">Reference</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>

        <!--        1 -->
        <tr class="tr_odd">
            <td>6-5340-2-1-2</td>
            <td>Small item Fur,Fix&EquipNotCapit_USD_ Fix &Equip</td>
            <td>GLDR</td>
            <td>21-06-2018</td>
            <td>37.50</td>
            <td></td>
        </tr>
        <tr class="tr_odd">
            <td></td>
            <td>IBA Key for Motor Client</td>
            <td></td>
            <td>BTB-JV0681</td>
            <td></td>
            <td></td>
        </tr>

        <tr class="tr_even">
            <td>2-9650-2-0-09</td>
            <td>IBA_USD_BattamBang</td>
            <td>GLDR</td>
            <td>21-06-2018</td>
            <td></td>
            <td>37.50</td>
        </tr>
        <tr class="tr_even">
            <td></td>
            <td>IBA Key for Motor Client</td>
            <td></td>
            <td>BTB-JV0681</td>
            <td></td>
            <td></td>
        </tr>

        <!--        total-->
        <tr class="total_amount border_top">
            <td  colspan="3"></td>
            <td><?php echo 'Total Debits：'?></td>
            <td><?php echo '37.50'?></td>
            <td></td>
        </tr>
        <tr class="total_amount">
            <td  colspan="3"></td>
            <td><?php echo 'Total Credits: '?></td>
            <td></td>
            <td><?php echo '37.50'?></td>
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