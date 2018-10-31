<div>
    <table class="table">
        <thead>
        <?php $currency_list = $data['currency_list'] ?>
        <tr class="table-header t1">
            <td rowspan="2">No.</td>
            <td rowspan="2">Account Number</td>
            <td rowspan="2">Client Name</td>
            <td colspan="<?php echo count($currency_list)?>">Amount Loan</td>
            <td rowspan="2">Disburse Date</td>
            <td colspan="2">Gender</td>
            <td rowspan="2">District</td>
            <td rowspan="2">Commune</td>
            <td rowspan="2">Village</td>
            <td rowspan="2">Circle</td>
            <td rowspan="2">Loan Purpose</td>
        </tr>
        <tr class="table-header t1">
            <?php foreach ($currency_list as $key => $currency) { ?>
                <td><?php echo $currency?></td>
            <?php } ?>
            <td>Male</td>
            <td>Female</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <!-- Credit officer -->
        <?php
        $co_info = $data['co_info'];
        $new_client_loan = $data['new_client_loan'];
        $repeat_client_loan = $data['repeat_client_loan'];
        $total_amount = $data['total_amount'];
        ?>
        <tr>
            <td colspan="20">
                Credit officer's name：
                <span class="fw-600"><?php echo $co_info['user_name']?></span>
            </td>
        </tr>

        <!-- new client -->
        <tr class="border_top"><td colspan="20" class="fontweight600">New Client</td></tr>
        <?php if($new_client_loan['loan_list']){?>
            <?php $i = 0;
            foreach ($new_client_loan['loan_list'] as $loan) {
                ++$i ?>
                <tr>
                    <td class="number"><?php echo $i?></td>
                    <td class="number"><?php echo $loan['virtual_contract_sn']?></td>
                    <td class="name"><?php echo $loan['display_name'] ?: $loan['login_code']?></td>
                    <?php foreach ($currency_list as $key => $currency) { ?>
                        <td class="currency">
                            <?php if ($key == $loan['currency']) { ?>
                                <?php echo $loan['apply_amount']; ?>
                            <?php } else { ?>
                                <?php echo '-'; ?>
                            <?php }?>
                        </td>
                    <?php } ?>
                    <td class="number"><?php echo $loan['disburse_date'] ? dateFormat($loan['disburse_date']) : ''?></td>
                    <td class="number"><?php echo $loan['gender'] == memberGenderEnum::MALE ? "M" : "-"?></td>
                    <td class="number"><?php echo $loan['gender'] == memberGenderEnum::FEMALE ? "F" : "-"?></td>
                    <td><?php echo $loan['address']['addr2']?></td>
                    <td><?php echo $loan['address']['addr3']?></td>
                    <td><?php echo $loan['address']['addr4']?></td>
                    <td><?php echo $loan['loan_actual_cycle']?></td>
                    <td><?php echo $loan['propose']?></td>
                </tr>
            <?php } ?>
            <tr class="fw-600">
                <td colspan="2">Total：</td>
                <td><?php echo count($new_client_loan['loan_list'])?></td>
                <?php foreach ($currency_list as $key => $currency) { ?>
                    <td class="currency"><?php echo ncPriceFormat($new_client_loan['loan_total']['amount_' . $key])?></td>
                <?php } ?>
                <td></td>
                <td class="number"><?php echo intval($new_client_loan['loan_total']['gender_m'])?></td>
                <td class="number"><?php echo intval($new_client_loan['loan_total']['gender_f'])?></td>
                <td colspan="5"></td>
            </tr>
        <?php } else {?>
            <tr>
                <td colspan="20">
                    <?php include(template(":widget/no_record")); ?>
                </td>
            </tr>
        <?php } ?>


        <tr class="border_top"><td colspan="20" class="fontweight600">Repeat Client</td></tr>
        <?php if($repeat_client_loan['loan_list']){?>
            <?php $i = 0;
            foreach ($repeat_client_loan['loan_list'] as $loan) {
                ++$i ?>
                <tr>
                    <td class="number"><?php echo $i?></td>
                    <td class="number"><?php echo $loan['virtual_contract_sn']?></td>
                    <td class="name"><?php echo $loan['display_name'] ?: $loan['login_code']?></td>
                    <?php foreach ($currency_list as $key => $currency) { ?>
                        <td class="currency">
                            <?php if ($key == $loan['currency']) { ?>
                                <?php echo $loan['apply_amount']; ?>
                            <?php } else { ?>
                                <?php echo '-'; ?>
                            <?php }?>
                        </td>
                    <?php } ?>
                    <td class="number"><?php echo $loan['disburse_date'] ? dateFormat($loan['disburse_date']) : ''?></td>
                    <td class="number"><?php echo $loan['gender'] == memberGenderEnum::MALE ? "M" : "-"?></td>
                    <td class="number"><?php echo $loan['gender'] == memberGenderEnum::FEMALE ? "F" : "-"?></td>
                    <td><?php echo $loan['address']['addr2']?></td>
                    <td><?php echo $loan['address']['addr3']?></td>
                    <td><?php echo $loan['address']['addr4']?></td>
                    <td><?php echo $loan['loan_actual_cycle']?></td>
                    <td><?php echo $loan['propose']?></td>
                </tr>
            <?php } ?>
            <tr class="fw-600">
                <td colspan="2">Total：</td>
                <td><?php echo count($repeat_client_loan['loan_list'])?></td>
                <?php foreach ($currency_list as $key => $currency) { ?>
                    <td class="currency"><?php echo ncPriceFormat($repeat_client_loan['loan_total']['amount_' . $key])?></td>
                <?php } ?>
                <td></td>
                <td class="number"><?php echo intval($repeat_client_loan['loan_total']['gender_m'])?></td>
                <td class="number"><?php echo intval($repeat_client_loan['loan_total']['gender_f'])?></td>
                <td colspan="5"></td>
            </tr>
        <?php } else {?>
            <tr>
                <td colspan="20">
                    <?php include(template(":widget/no_record")); ?>
                </td>
            </tr>
        <?php } ?>

        <?php if ($total_amount['loan_count']) { ?>
            <tr class="total_amount border_top">
                <td colspan="2">Total Amount：</td>
                <td><?php echo $total_amount['loan_count']?></td>
                <?php foreach ($currency_list as $key => $currency) { ?>
                    <td class="currency"><?php echo ncPriceFormat($total_amount['amount_' . $key])?></td>
                <?php } ?>
                <td></td>
                <td class="number"><?php echo intval($total_amount['gender_m'])?></td>
                <td class="number"><?php echo intval($total_amount['gender_f'])?></td>
                <td colspan="5"></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>