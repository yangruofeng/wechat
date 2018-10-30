<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .container {
        width: 800px !important;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    .collection-div {
        margin-bottom: 70px;
    }

    .authorize_input {
        margin-top: -8px!important;
        margin-bottom: 10px;
        position: relative;
    }

    .table{
        background-color: white!important;
    }

    #notCheck, #notCheckCashier,#notCheckPassword{
        width: 20px;
        position: absolute;
        top: 6px;
        right: 6px;
    }

    #checkCashierFailure, #checkFailure, #checkPasswordFailure{
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 6px;
    }

    #checkDone, #checkCashierDone,#checkPasswordDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 6px;
    }
    .margin40{
        margin-top: 40px;
    }
</style>


<div class="page">
    <?php //require_once template('widget/item.member.business.nav'); ?>
    <div class="collection-div">

        <?php include template('member_loan/loan.contract.info.item');?>

    </div>
</div>



