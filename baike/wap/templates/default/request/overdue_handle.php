<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL; ?>/resource/css/request.css?v=2">
<?php include_once(template('widget/inc_header')); ?>
<div class="wrap overdue-handle-wrap">
    <?php $detail = $output['detail'];
    $list = $output['list']; ?>
    <div>
        <ul class="aui-list overdue-detail-ul">
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label label">
                        Contract Sn
                    </div>
                    <div class="aui-list-item-input label-on">
                        <?php echo $detail['contract_sn'] . ' ' . $detail['scheme_name']; ?>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label label">
                        Member Name
                    </div>
                    <div class="aui-list-item-input label-on">
                        <?php echo $detail['login_code']; ?>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label label">
                        Phone
                    </div>
                    <div class="aui-list-item-input label-on">
                        <?php echo $detail['phone_id']; ?>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner total">
                    <div class="aui-list-item-label label">
                        Payable Amount
                    </div>
                    <div class="aui-list-item-input label-on">
                        <?php echo ncPriceFormat($detail['payable_amount']); ?>
                        <em><?php echo $detail['currency']; ?></em>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner total">
                    <div class="aui-list-item-label label">
                        Penalty
                    </div>
                    <div class="aui-list-item-input label-on">
                        <?php echo ncPriceFormat($detail['penalty']); ?>
                        <em><?php echo $detail['currency']; ?></em>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner total">
                    <div class="aui-list-item-label label">
                        Total
                    </div>
                    <div class="aui-list-item-input label-on">
                        <?php echo ncPriceFormat($detail['penalty'] + $detail['payable_amount']); ?>
                        <em><?php echo $detail['currency']; ?></em>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner total">
                    <div class="aui-list-item-label label">
                        Receivable Date
                    </div>
                    <div class="aui-list-item-input label-on">
                        <?php echo dateFormat($detail['receivable_date']); ?>
                    </div>
                </div>
            </li>
            <?php if ($detail['state'] == 2) { ?>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner total">
                        <div class="aui-list-item-label label">
                            State
                        </div>
                        <div class="aui-list-item-input label-on">
                            <?php echo 'Done'; ?>
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner total">
                        <div class="aui-list-item-label label">
                            Done Time
                        </div>
                        <div class="aui-list-item-input label-on">
                            <?php echo timeFormat($detail['update_time']); ?>
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner total">
                        <div class="aui-list-item-label label">
                            Remark
                        </div>
                        <div class="aui-list-item-input label-on">
                            <?php echo $detail['remark']; ?>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
    <?php if ($detail['task_state'] != 2) { ?>
        <div style="padding: .2rem .8rem;">
            <div class="aui-btn aui-btn-block custom-btn custom-btn-purple aui-margin-t-15"
                 onclick="javascript:location.href='<?php echo getUrl('request', 'overdueOper', array('uid' => $_GET['uid'], 'type' => '1'), false, WAP_OPERATOR_SITE_URL) ?>'">
                Add Log
            </div>
            <div class="aui-btn aui-btn-block custom-btn custom-btn-purple aui-margin-t-10"
                 onclick="javascript:location.href='<?php echo getUrl('request', 'overdueOper', array('uid' => $_GET['uid'], 'type' => '2'), false, WAP_OPERATOR_SITE_URL) ?>'">
                Done
            </div>
        </div>
    <?php } ?>
    <?php if (count($list) > 0) { ?>
        <ul class="aui-list overdue-history-ul aui-margin-t-15">
            <li class="aui-list-item title">
                <div>Name</div>
                <div>Time</div>
                <div>Remark</div>
            </li>
            <?php foreach ($list as $k => $v) { ?>
                <li class="aui-list-item item">
                    <div><?php echo $v['officer_name']; ?></div>
                    <div><?php echo $v['update_time'] ? timeFormat($v['update_time']) : timeFormat($v['create_time']); ?></div>
                    <div><?php echo $v['dun_response'] ?: 'None'; ?></div>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <div class="no-record">No Data.</div>
    <?php } ?>
</div>

<script type="text/javascript">

</script>
