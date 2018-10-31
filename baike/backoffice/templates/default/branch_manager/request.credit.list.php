<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'No.';?></td>
            <td><?php echo 'Member Name';?></td>
            <td><?php echo 'Monthly Repayment Ability';?></td>
            <td><?php echo 'Invalid Terms';?></td>
            <td><?php echo 'Default Credit';?></td>
            <td><?php echo 'Increase Credit';?></td>
            <td><?php echo 'Max Credit';?></td>
            <td><?php echo 'Remark'; ?></td>
            <?php if (in_array($data['state'], array(memberCreditSuggestEnum::HQ_REJECT, memberCreditSuggestEnum::PASS, memberCreditSuggestEnum::NO_PASS))) { ?>
                <td><?php echo 'Time'; ?></td>
            <?php }?>
            <?php if ($data['state'] == memberCreditSuggestEnum::HQ_REJECT || $data['state'] == memberCreditSuggestEnum::CREATE) { ?>
                <td><?php echo 'Function';?></td>
            <?php }?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php $certification_type = enum_langClass::getCertificationTypeEnumLang();?>
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="9">No Record</td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $credit_suggest) { ?>
                <tr>
                    <td>
                        <?php echo $credit_suggest['uid'];?>
                    </td>
                    <td>
                        <?php echo $credit_suggest['display_name'] ? $credit_suggest['display_name'] . '(' . $credit_suggest['login_code'] . ')' : $credit_suggest['login_code'];?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_suggest['monthly_repayment_ability']);?>
                    </td>
                    <td>
                        <?php echo $credit_suggest['credit_terms'] . ' Months';?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_suggest['default_credit']);?>
                    </td>
                    <td>
                        <?php
                        $increase_credit = 0;
                        foreach($credit_suggest['suggest_detail_list'] as $val) {
                            $increase_credit += $val['credit'];
                        }
                        echo $increase_credit;
                        ?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_suggest['max_credit']);?>
                    </td>
                    <td>
                        <?php if($credit_suggest['state'] == memberCreditSuggestEnum::HQ_REJECT) { ?>
                            <span style="background-color: yellow;padding: 2px 5px"><?php echo 'HQ remark：' . $credit_suggest['remark']; ?></span>
                        <?php } else { ?>
                            <span><?php echo $credit_suggest['remark']; ?></span>
                        <?php }?>
                    </td>
                    <?php if (in_array($data['state'], array(memberCreditSuggestEnum::HQ_REJECT, memberCreditSuggestEnum::PASS, memberCreditSuggestEnum::NO_PASS))) { ?>
                        <td><?php echo $credit_suggest['vote_time']?timeFormat($credit_suggest['vote_time']):$credit_suggest['update_time']; ?></td>
                    <?php }?>
                    <?php if ($data['state'] == memberCreditSuggestEnum::HQ_REJECT) { ?>
                        <td>
                            <a style="margin-left: 10px" href="<?php echo getUrl('web_credit', 'editSuggestCreditPage', array('uid' => $credit_suggest['member_id']), false, BACK_OFFICE_SITE_URL);?>">
                                <span>Edit</span>
                            </a>
                        </td>
                    <?php }?>
                    <?php if ($data['state'] == memberCreditSuggestEnum::CREATE) { ?>
                        <td>
                            <a style="margin-left: 10px;cursor: pointer" onclick="submit_hq_onclick(<?php echo $credit_suggest['uid'] ?>)">
                                <?php echo 'Submit Headquarters' ?>
                            </a>
                        </td>
                    <?php }?>
                </tr>
            <?php }?>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
