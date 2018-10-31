<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/22
 * Time: 18:16
 */
return array(
    array(
        'name' => 'Loan disbursement execute',
        'url' => 'index.php?act=loan&op=exec_disbursement_schema'
    ),
    array(
        'name' => 'Loan repayment execute',
        'url' => 'index.php?act=loan&op=schemaRepaymentExecute'
    ),
    array(
        'name' => 'Update cert expire list',
        'url' => 'index.php?act=script_cert&op=updateExpireCertList'
    ),
    array(
        'name' => 'Receive credit contract fee',
        'url' => 'index.php?act=script_credit&op=receiveCreditContractFeeByBalance'
    )
);