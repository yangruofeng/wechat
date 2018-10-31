<style>
    td{
        padding: 8px!important;
    }
</style>

<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Sort Code</td>
            <td>Description</td>
            <td>Debit</td>
            <td>Credit</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!--            --><?php //if($data['data']){ ?>
        <!--                --><?php //foreach ($data['data'] as $row) { ?>
        <tr style="font-weight: 600" class="<?php echo $i % 2 == 0 ? 'tr_even' : 'tr_odd'?>">
            <td width="20%">
                1
            </td>
            <td width="40%">
                Total Assets Control Account
            </td>
            <td width="20%">
                2,280,259.19
            </td>
            <td width="20%">

            </td>
        </tr>
        <tr class="<?php echo $i % 2 == 0 ? 'tr_even' : 'tr_odd'?>">
            <td colspan="4" style="padding: 0px!important;">
                <table width="100%">
                    <tr>
                        <td width="20%">
                            1-1110
                        </td>
                        <td width="40%">
                            Cash in Vault and on Hand
                        </td>
                        <td width="20%">
                            35,711.48
                        </td>
                        <td width="20%">

                        </td>
                    </tr>

                </table>
            </td>
        </tr>

        <tr class="total_amount border_top">
            <td></td>
            <td style="text-align: right"><?php echo 'Total：'?></td>
            <td><?php echo '157,572.10 '?></td>
            <td><?php echo '157,572.10 '?></td>
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