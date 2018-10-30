
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Value Date</td>
            <td>JNL#</td>
            <td>Transaction Description</td>
            <td>Reference</td>
            <td>Trn#</td>
            <td>Type</td>
            <td>Debits</td>
            <td>Credits</td>
            <td>Balance</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>
        <tr>
            <td colspan="2">31-05-2018</td>
            <td colspan="6">Balance Brought Forward:</td>  <!--接上页余额-->
            <td>21,494.32</td>
        <tr>
            <td>01-06-2018</td>
            <td>0002</td>
            <td>Cash WDL From HKL</td>
            <td>BTB-BR0250</td>
            <td>010579</td>
            <td>GLDR</td>
            <td>20,000.00</td>
            <td></td>
            <td>41,494.32</td>
        </tr>
        <tr>
            <td>01-06-2018</td>
            <td>0003</td>
            <td>Cash WDL From HKL</td>
            <td>BTB-BR0251</td>
            <td>010581</td>
            <td>GLDR</td>
            <td>20,000.00</td>
            <td></td>
            <td>61,494.32</td>
        </tr>
        <tr>
            <td>01-06-2018</td>
            <td>0005</td>
            <td>Paid To Fue House Water</td>
            <td>BTB-CP0101</td>
            <td>010589</td>
            <td>GLDR</td>
            <td></td>
            <td>33.55</td>
            <td>61,460.77</td>
        </tr>
        <tr  class="total_amount border_top">
            <td colspan="5">

            </td>
            <td>
                Totals
            </td>
            <td>
                40,000.00
            </td>
            <td>
                33.55
            </td>
            <td>
                61,460.77
            </td>
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