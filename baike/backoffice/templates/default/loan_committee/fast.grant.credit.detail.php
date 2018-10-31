<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .input-h30 {
        height: 30px !important;
    }

    .explain {
        padding-left: 10px;
        font-style: italic;
        color: #b3b3b3;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    em {
        font-weight: 500;
        font-size: 15px;
    }

    #check_list td {
        width: 25%;
    }

    #check_list .num {
        width: 20px;
        height: 20px;
        display: inline-block;
        border: 1px solid #FFE299;
        border-radius: 10px;
        line-height: 18px;
        text-align: center;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        /*background-color: #FFF;*/
        overflow: hidden;
    }

    .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    /*.nav-tabs {*/
        /*height: 34px!important;*/
    /*}*/

    /*.nav-tabs li a {*/
        /*padding: 7px 12px !important;*/
    /*}*/

    .tab-content label {
        margin-bottom: 0px!important;
    }

    .form-horizontal {
        margin-bottom: 0px;
    }

    .form-horizontal .control-label {
        text-align: left;
    }
    .pl-75 {
        padding-left: 75px;
        font-weight: 500;
    }

    .pl-125 {
        padding-left: 125px;
        font-weight: 400;
    }
</style>
<?php $certification_type = enum_langClass::getCertificationTypeEnumLang();?>
<?php $cert_type = array(
    certificationTypeEnum::LAND => 'land_credit_rate',
    certificationTypeEnum::HOUSE => 'house_credit_rate',
    certificationTypeEnum::MOTORBIKE => 'motorbike_credit_rate',
    certificationTypeEnum::CAR => 'car_credit_rate',
)?>
<?php
$member_assets = $output['member_assets'];
$analysis=$output['analysis'];
$member_request = $analysis['member_request'];
$member_income = $analysis['income'];
$member_expense = $analysis['expense'];
$suggest_profile = $analysis['suggest'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Fast Grant Credit</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan_committee', 'fastGrantCredit', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Client List</span></a></li>
                <li><a class="current"><span>Grant Credit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $client_info = $output['client_info']; $credit = memberClass::getCreditBalance($client_info['uid']);?>
        <div class="col-sm-12">
            <?php require_once template('widget/item.member.summary1'); ?>
        </div>
        <div class="col-sm-12" style="padding-left: 200px">
            <?php require_once template('widget/item.member.summary.relative'); ?>
        </div>

        <?php include(template("loan_committee/fast.grant.credit.detail.left")); ?>
        <?php include(template("loan_committee/fast.grant.credit.detail.right")); ?>


        <div style="margin-top:10px;margin-bottom: 30px" class="col-sm-12">
            <?php $source_mark = 'fast_grant'; ?>
            <?php include(template("widget/item.client.reference")); ?>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>

    $(document).ready(function () {


    });

    $('#fast-submit').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        /* 可以不选，后台判断
        if($('input[name="committee_member[]"]:checked').length == 0){
            alert('Please select committee member!');
        }
        */

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            monthly_repayment_ability: {
                required: true
            },
            invalid_terms: {
                required: true
            },
            default_credit: {
                required: true
            },
            max_credit: {
                required: true
            }
        },
        messages: {
            monthly_repayment_ability: {
                required: '<?php echo 'Required!'?>'
            },
            invalid_terms: {
                required: '<?php echo 'Required!'?>'
            },
            default_credit: {
                required: '<?php echo 'Required!'?>'
            },
            max_credit: {
                required: '<?php echo 'Required!'?>'
            }

        }
    });
</script>