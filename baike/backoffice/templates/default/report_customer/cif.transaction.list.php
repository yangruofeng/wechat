
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>CID</td>
            <td>Customer Name</td>
            <td>Teller</td>
            <td>Date</td>
            <td>Computer Date</td>
            <td>Trn#</td>
            <td>Trn Type</td>
        </tr>

        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>

        <!--        1 -->
        <tr class="tr_odd">
            <td>005774</td>
            <td>Poeurm Channa</td>
            <td>302</td>
            <td>22-06-2018</td>
            <td>22-06-2018 13:37:24</td>
            <td>350247</td>
            <td>679 - Set Customer Status </td>
        </tr>

        <tr class="tr_even">
            <td>005774</td>
            <td>Poeurm Channa</td>
            <td>302</td>
            <td>22-06-2018</td>
            <td>22-06-2018 13:42:41</td>
            <td>350248</td>
            <td>674 - Change/Edit CIF </td>
        </tr>

        <tr class="tr_odd">
            <td>005820 </td>
            <td>Heng Sambath</td>
            <td>301</td>
            <td>22-06-2018</td>
            <td>22-06-2018 11:37:51</td>
            <td>350230</td>
            <td>674 - Change/Edit CIF </td>
        </tr>

        <tr class="total_amount border_top">
            <td><?php echo 'Total：'?></td>
            <td colspan="6"><?php echo '24'?></td>
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