<table class="table table-bordered table-striped table-hover">
    <tr class="table-header">
        <td>No.</td>
        <td>Branch Name</td>
        <td>CID</td>
        <td>Client Name</td>
        <td>Phone</td>
        <td>Contract SN</td>
        <td>Time</td>
        <td>Amount</td>
        <td>Service Fee</td>
        <td>Outstanding</td>
        <td>Left Days</td>

    </tr>
    <?php foreach($data['data'] as $i=>$item){?>
        <tr>
            <td>
                <?php echo $i+1;?>
            </td>
            <td><?php echo $item['branch_name']; ?></td>
            <td>
                <?php echo $item['client_obj_guid']?>
            </td>
            <td>
                <?php echo $item['display_name']?>
            </td>
            <td>
                <?php echo $item['phone_id']?>
            </td>
            <td>
                <?php echo $item['contract_sn']?>
            </td>
            <td>
                <?php echo $item['create_time']?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['apply_amount'],0)?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['receivable_service_fee'],0)?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['outstanding'],0)?>
            </td>
            <td>
                <?php
                    if($item['outstanding']>0){
                        $end_date=date("Y-m-d 23:59:59",strtotime($item['end_date']));
                        if($end_date>=Now()){
                            $diff=floor((strtotime($end_date)-time())/(60*60*24));
                            echo $diff." Days";
                        }else{
                            $diff=floor((time()-strtotime($end_date))/(60*60*24));
                            echo '<kbd> - '.$diff." Days".'</kbd>';
                        }
                    }
                ?>
            </td>

        </tr>
    <?php }?>
</table>
<?php if (!$is_print) { ?>
<?php include_once(template("widget/inc_content_pager")); ?>
<?php } ?>
