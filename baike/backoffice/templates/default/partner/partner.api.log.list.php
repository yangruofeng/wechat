<div>
    <table class="table table-striped table-bordered table-hover">
        <tr class="table-header">
            <td class="number">API trx id</td>
            <td class="number">Trx Time</td>
            <td class="number">Trx Amount</td>
            <td class="number">API Status</td>
            <td class="number">API Parameter</td>
        </tr>
        <?php $list = $data['data'];?>
        <?php foreach ($list as $k => $v) { ?>
            <tr>
                <td class="number"><?php echo $v['api_trx_id'];?></td>
                <td class="number"><?php echo timeFormat($v['trx_time']); ?></td>
                <td class="currency"><?php echo ncPriceFormat($v['trx_amount']); ?></td>
                <td class="number">
                    <?php $str = "";
                    switch($v['api_state']){
                        case apiStateEnum::CANCELLED :
                            $str = "Cancelled";
                            break;
                        case apiStateEnum::CREATED :
                            $str = "Created";
                            break;
                        case apiStateEnum::STARTED :
                            $str = "Started";
                            break;
                        case apiStateEnum::PENDING_CHECK :
                            $str = "Pending Check";
                            break;
                        case apiStateEnum::FINISHED :
                            $str = "Finished";
                            break;
                        default:
                            break;
                    }
                    echo $str;
                    ?>
                </td>
                <td>
                    <?php $api_parameter = json_decode($v['api_parameter'], true);
                        foreach($api_parameter as $k => $v){ ?>
                            <p><?php echo $k;?>: <?php echo $v;?></p>
                    <?php }?>
                </td>
            </tr>
        <?php }?>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
