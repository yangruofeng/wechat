<style>
    .detail{

        border-bottom: 1px solid black;
        padding-bottom: 10px;
        min-width: 300px;
        display: inline-block
    }

</style>
<div>
    <table class="table" style="margin-bottom: 50px">
<!--            --><?php //if($data['data']){ ?>
<!--                --><?php //foreach ($data['data'] as $row) { ?>

<!--        1 -->
        <tr class="table-header">
            <td width="20%"></td>
            <td width="60%" style="text-align: center;font-weight: 600">Protfolio Quality Ratios</td>
            <td width="20%"></td>
        </tr>
        <tr class="tr_odd">
            <td>Portfolio in Arrears %</td>
            <td style="text-align: center;padding-top: 10px!important;">
                <p><span class="detail">(Unpaid Principal Past Due)</span></p>
                <p>(Total Loan Balances)</p>
            </td>
            <td>0.03  %</td>
        </tr>
        <tr class="tr_even">
            <td>Portfolio At Risk %</td>
            <td style="text-align: center">
                <p><span class="detail">(Unpaid balance of all loans with overdue)</span></p>
                <p>(Total Loan Balances)</p>
            </td>
            <td>0.65 %</td>
        </tr>
        <tr class="tr_odd">
            <td>Loan Loss Reserve Ratio %</td>
            <td style="text-align: center;padding-top: 10px!important;">
                <p><span class="detail">(Accumulated Loan Loss Reserve)</span></p>
                <p>(Total Loan Balances)</p>
            </td>
            <td>0.00  %</td>
        </tr>

        <tr class="table-header">
            <td width="20%"></td>
            <td width="60%" style="text-align: center;font-weight: 600">Operating Efficiency Ratios</td>
            <td width="20%"></td>
        </tr>
        <tr class="tr_odd">
            <td>Cost per unit of currency %</td>
            <td style="text-align: center;padding-top: 10px!important;">
                <p><span class="detail">(Total Operating Expenses)</span></p>
                <p>(Amount disbursed during period)</p>
            </td>
            <td>0.00  %</td>
        </tr>
        <tr class="tr_even">
            <td>Cost per Loan Made</td>
            <td style="text-align: center">
                <p><span class="detail">(Total Operating Expenses)</span></p>
                <p>(Total number of disbursements)</p>
            </td>
            <td>0.00 %</td>
        </tr>
        <tr class="tr_odd">
            <td>Field Staff Efficiency</td>
            <td style="text-align: center;padding-top: 10px!important;">
                <p><span class="detail">(Number of active borrowers)</span></p>
                <p>(Number of field agents)</p>
            </td>
            <td>n.a  %</td>
        </tr>
        <tr class="tr_even">
            <td>Salaries to Loans Outstanding % </br>Annualized</td>
            <td style="text-align: center">
                <p><span class="detail">(Salaries and benefits)</span></p>
                <p>(Average loans outstanding)</p>
            </td>
            <td>0.00 %</td>
        </tr>
        <tr class="tr_odd">
            <td>Portfolio per Credit Officer</td>
            <td style="text-align: center;padding-top: 10px!important;">
                <p><span class="detail">(Average loans outstanding)</span></p>
                <p>(Number of field agents)</p>
            </td>
            <td>n.a  %</td>
        </tr>


        <tr class="table-header">
            <td width="20%"></td>
            <td width="60%" style="text-align: center;font-weight: 600">Financial Strength Ratios</td>
            <td width="20%"></td>
        </tr>
        <tr class="tr_odd">
            <td>Return on Performing Assets %</br>Annualized</td>
            <td style="text-align: center;padding-top: 10px!important;">
                <p><span class="detail">(Total Financial Income)</span></p>
                <p>(Total Current Assets)</p>
            </td>
            <td>0.00 %</td>
        </tr>
        <tr class="tr_even">
            <td>Operating Cost Ratio %</br>Annualized</td>
            <td style="text-align: center;padding-top: 10px!important;">
                <p><span class="detail">(Total Operating Expenses)</span></p>
                <p>(Performing Assets)</p>
            </td>
            <td>0.00  %</td>
        </tr>
        <tr class="tr_odd">
            <td>Operating Self Sufficiency %</td>
            <td style="text-align: center;padding-top: 10px!important;">
                <p><span class="detail">(Total Financial Income)</span></p>
                <p>(Financial Cost + Operating Cost + Loan Loss Prov.)</p>
            </td>
            <td>0.00  %</td>
        </tr>



        <!--                --><?php //}?>
        <!--            --><?php //}else{ ?>
        <!--                <tr>-->
        <!--                    <td colspan="5">No Record</td>-->
        <!--                </tr>-->
        <!--            --><?php //} ?>

    </table>
</div>
