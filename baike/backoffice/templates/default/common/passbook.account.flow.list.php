<div>
    <table class="table table-striped table-bordered table-hover">
        <tr class="table-header">
            <td>Flow ID</td>
            <td>Time</td>
            <td>Begin Balance</td>
            <td class="number">Credit</td>
            <td class="number">Debit</td>
            <td class="number">End Balance</td>
            <td>Subject</td>
            <td>Remark</td>
            <td>Handle</td>
        </tr>
        <?php $list = $data['data'];?>
        <?php if ($list) { ?>
            <?php foreach ($list as $k => $v) { ?>
                <tr>
                    <td><?php echo $v['uid'];?></td>
                    <td>
                        <?php echo timeFormat($v['update_time']); ?>
                    </td>
                    <td class="currency">
                        <?php echo ncPriceFormat($v['begin_balance']); ?>
                    </td>
                    <td class="currency"><?php echo $v['credit']?ncPriceFormat($v['credit']):'-';?></td>
                    <td class="currency"><?php echo $v['debit']?ncPriceFormat($v['debit']):'-';?></td>
                    <td class="currency"><?php echo $v['end_balance']?ncPriceFormat($v['end_balance']):'-';?></td>
                    <td><?php echo $v['subject']?:'-';?></td>
                    <td>
                        <?php echo $v['remark']; ?>
                    </td>
                    <td>
                        <div class="custom-btn-group">
                            <a title="" class="btn btn-link btn-xs" href="<?php echo !$data['is_ajax']?getUrl('common', 'passbookAccountVoucherFlowPage', array('trade_id'=>$v['trade_id']), false, BACK_OFFICE_SITE_URL):'javascript:;';?>" <?php if($data['is_ajax']){?>onclick="ajaxVoucher(<?php echo $v["trade_id"];?>);"<?php }?>>
                                <span><i class="fa fa-vcard-o"></i>Voucher</span>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php }?>
        <?php } else { ?>
            <tr>
                <td colspan="9"> <?php include(template(":widget/no_record")); ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php if (count($list) > 0 || $data['pageNumber'] != 1) { ?>
    <?php include_once(template("widget/inc_content_pager")); ?>
<?php } ?>
<script>
    function ajaxVoucher(trade_id){
        showMask();
        yo.dynamicTpl({
            tpl: "common/passbook.account.voucher.flow",
            dynamic: {
                api: "common",
                method: "passbookAccountVoucherFlowPage",
                param: {trade_id: trade_id, is_ajax: 1}
            },
            callback: function (_tpl) {
                hideMask();
                $(".data-center-list").html(_tpl);
            }
        });
    }
</script>
