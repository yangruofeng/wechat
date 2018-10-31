<div role="tabpanel" class="tab-pane" id="authority">
    <div class="black-info">
        <div class="list clearfix">
            <?php $black = $output['black'] ?:$data['black'];
            $count = count($black); ?>
            <?php if ($count > 0) { ?>
                <?php foreach ($black as $key => $val) {
                    $label = '';
                    $state = '';
                    $field = '';
                    switch ($val['type']) {
                        case blackTypeEnum::LOGIN :
                            $label = 'Login';
                            $state = $val['check'];
                            $type = $val['type'];
                            break;
                        case blackTypeEnum::DEPOSIT :
                            $label = 'Deposit';
                            $state = $val['check'];
                            $type = $val['type'];
                            break;
                        case blackTypeEnum::INSURANCE :
                            $label = 'Insurance';
                            $state = $val['check'];
                            $type = $val['type'];
                            break;
                        case blackTypeEnum::CREDIT_LOAN :
                            $label = 'Credit Loan';
                            $state = $val['check'];
                            $type = $val['type'];
                            break;
                        case blackTypeEnum::MORTGAGE_LOAN :
                            $label = 'Mortgage Loan';
                            $state = $val['check'];
                            $type = $val['type'];
                            break;
                        default:
                            $label = 'Login';
                            $state = $val['check'];
                            $type = $val['type'];
                            break;
                    }
                    ?>
                    <span class="<?php echo $state == 1 ? 'disabled' : ''; ?>">
                                        <i class="fa <?php echo $state == 1 ? 'fa-remove' : 'fa-check'; ?>"></i><?php echo $label; ?>
                        <!--<em onclick="_confirm(<?php echo $type; ?>, <?php echo $state ? 0 : 1; ?>);"><?php echo $state ? '<i class="fa fa-minus"></i>' : '<i class="fa fa-plus"></i>'; ?></em>-->
                                     </span>
                <?php } ?>
            <?php } else { ?>
                <span><i class="fa fa-check"></i>登录<em
                        onclick="_confirm('<?php echo blackTypeEnum::LOGIN; ?>', 1);"><!--<i
                                            class="fa fa-plus"></i></em>--></span>
                <span><i class="fa fa-check"></i>存款<em
                        onclick="_confirm('<?php echo blackTypeEnum::DEPOSIT; ?>', 1);"><!--<i
                                            class="fa fa-plus"></i></em>--></span>
                <span><i class="fa fa-check"></i>保险<em
                        onclick="_confirm('<?php echo blackTypeEnum::INSURANCE; ?>', 1);"><!--<i
                                            class="fa fa-plus"></i></em>--></span>
                <span><i class="fa fa-check"></i>信用贷<em
                        onclick="_confirm('<?php echo blackTypeEnum::CREDIT_LOAN; ?>', 1);"><!--<i
                                            class="fa fa-plus"></i></em>--></span>
                <span><i class="fa fa-check"></i>抵押贷<em
                        onclick="_confirm('<?php echo blackTypeEnum::MORTGAGE_LOAN; ?>', 1);"><!--<i
                                            class="fa fa-plus"></i></em>--></span>
            <?php } ?>
        </div>
    </div>
</div>