
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>GL Account</td>
            <td>GL Account Name</td>
            <td>GL Account Balance</td>
            <td>Customer Balance</td>
            <td>Balanced</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>

        <!--        1 -->
        <tr class="tr_odd">
            <td>1-3120-2-1-0</td>
            <td>STDL - Individuals<=1 year_USD</td>
            <td>19,053.25</td>
            <td>19,053.25</td>
            <td>OK</td>
        </tr>
        <tr class="tr_even">
            <td>1-3120-2-1-1</td>
            <td> STDL - Individuals<=1 year_USD_PD<30</td>
            <td>29.00</td>
            <td>29.00</td>
            <td>OK</td>
        </tr>
        <tr class="tr_odd">
            <td>1-3220-2-1-0</td>
            <td>STDL - Individuals>1 year_USD</td>
            <td>2,148,819.68</td>
            <td>2,148,819.68</td>
            <td>OK</td>
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