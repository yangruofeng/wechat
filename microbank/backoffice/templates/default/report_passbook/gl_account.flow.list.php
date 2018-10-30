<div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
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
        </thead>
        <tbody class="table-body">
            <?php $list = $data['data'];?>
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
                            <a title="" class="btn btn-link btn-xs" href="<?php echo getUrl('common', 'passbookAccountVoucherFlowPage', array('trade_id'=>$v['trade_id'], 'type' => $data['pageType']), false, BACK_OFFICE_SITE_URL)?>">
                                <span><i class="fa fa-vcard-o"></i>Voucher</span>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>