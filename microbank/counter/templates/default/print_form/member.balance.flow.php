<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .small {
        padding-left: 5px;
        font-weight: 400;
        font-size: 95%;
    }
</style>
<div class="page">
    <div class="col-sm-12" style="text-align: center;font-size: 16px!important;font-weight: 600;margin-bottom: 30px">
        <?php echo $lang['print_member_balance_flow']; ?>
    </div>
    <div class="container">
        <div class="business-content" style="margin-bottom: 30px">
            <div style="font-size: 14px;font-weight: 600;margin-bottom: 10px">
                <span><?php echo $lang['print_member_code']; ?> : <span class="small"><?php echo $output['member_info']['login_code']?></span></span>
                <span style="margin-left: 30px"><?php echo $lang['print_currency']; ?> : <span class="small"><?php echo $output['currency']?></span></span>
                <span style="margin-left: 30px"><?php echo $lang['print_from']; ?> : <span class="small"><?php echo $output['date_start']?></span> To : <span class="small"><?php echo $output['date_end']?></span> </span>
            </div>

            <div>
                <div>
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td><?php echo $lang['print_trading_id']; ?></td>
                            <td><?php echo $lang['print_trade_type']; ?></td>
                            <td><?php echo $lang['print_begin_balance']; ?></td>
                            <td><?php echo $lang['print_income']; ?></td>
                            <td><?php echo $lang['print_payment']; ?></td>
                            <td><?php echo $lang['print_end_balance']; ?></td>
                            <td><?php echo $lang['print_operate_time']; ?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if($output['data']){; ?>

                            <?php foreach ($output['data'] as $row) { ?>
                                <tr>
                                    <td>
                                        <?php echo $row['trade_id'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['trading_type'] ?>
                                    </td>
                                    <td>
                                        <?php echo ncPriceFormat($row['begin_balance']) ?>
                                    </td>
                                    <td>
                                        <?php echo ncPriceFormat($row['credit']) ?>
                                    </td>
                                    <td>
                                        <?php echo ncPriceFormat($row['debit']) ?>
                                    </td>
                                    <td>
                                        <?php echo ncPriceFormat($row['end_balance']) ?>
                                    </td>
                                    <td>
                                        <?php echo $row['update_time'] ?>
                                    </td>
                                </tr>
                            <?php }?>
                        <?php }else{ ?>
                            <tr>
                                <td colspan="7"><?php echo $lang['print_no_record']; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.btn-group').hide();
        $('.easyui-panel').hide();
    });
</script>