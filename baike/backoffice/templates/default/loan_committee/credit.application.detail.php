<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<?php $certification_type = enum_langClass::getCertificationTypeEnumLang();?>
<?php $cert_type = array(
    certificationTypeEnum::LAND => 'land_credit_rate',
    certificationTypeEnum::HOUSE => 'house_credit_rate',
    certificationTypeEnum::MOTORBIKE => 'motorbike_credit_rate',
    certificationTypeEnum::CAR => 'car_credit_rate',
    certificationTypeEnum::STORE=>'store_credit_rate'
)?>
<?php $bm_suggest = $output['bm_suggest'];
$member_assets = $output['member_assets'];
$analysis=$output['analysis'];
$member_request = $analysis['member_request'];
$member_income = $analysis['income'];
$member_expense = $analysis['expense'];
$suggest_profile = $analysis['suggest'];
$product_list = $output['product_list'];
$client_loan_account_info = $output['client_loan_account_info'];
?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Committee</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Back</span></a></li>
                <li><a class="current"><span>Credit Grant</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $client_info = $output['client_info']; $credit = memberClass::getCreditBalance($client_info['uid']);?>
        <div class="col-sm-12">
            <?php require_once template('widget/item.member.summary1'); ?>
        </div>
        <div class="col-sm-12" style="/*padding-left: 200px*/">
            <?php require_once template('widget/item.member.summary.relative'); ?>
        </div>

        <?php include(template("loan_committee/credit.application.detail.left")); ?>
        <?php include(template("loan_committee/credit.application.detail.right")); ?>


        <div style="margin-top:10px;margin-bottom: 30px" class="col-sm-12">
            <?php $source_mark = 'grant_committee'; ?>
            <?php include(template("widget/item.client.reference")); ?>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>

    $(document).ready(function () {


    });
    $('#grant_submit').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }
        $('.form-horizontal').submit();
    });

    $('#grant_reject').click(function () {
        var _remark = $('textarea[name="remark"]').val();
        if (!$.trim(_remark)) {
            alert('Please input the remark.');
            return;
        }
        $('input[name="type"]').val(0);
        $('.form-horizontal').submit();
    });

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            remark: {
                required: true
            }
        },
        messages: {
            remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>