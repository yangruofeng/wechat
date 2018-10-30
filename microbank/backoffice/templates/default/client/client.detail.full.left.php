<style>
    #identity_info .fa-check {
        cursor: pointer;
    }

    #asset_list .num {
        width: 20px;
        height: 20px;
        display: inline-block;
        border: 1px solid #FFE299;
        border-radius: 10px;
        line-height: 18px;
        text-align: center;
    }

    #business_scene img, #business_photo img {
        max-height: 100px;
        margin: 15px 10px 10px 10px;
        float: left;
    }

    .tr_1 {
        background-color: #FFF!important;
    }

    .tr_2 {
        background-color: #F3F4F6 !important;
    }

    #identity_info td {
        width: 33%;
    }

    #asset_list td {
        width: 20%;
    }

    #asset_list tr td:first-child {
        width: 40% !important;
    }

    .research_text_src {
        color: #9e9e9e;
        padding-left: 5px;
        font-style: italic;
    }
</style>
<?php $member_id = $item['uid']; ?>
<?php $reference_info = credit_researchClass::getMemberMemberReferenceInfo($member_id); ?>
<?php $work_type_lang = enum_langClass::getWorkTypeEnumLang(); ?>
<div class="pull-left">
    <div class="verify-wrap">
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#information" aria-controls="information" role="tab" data-toggle="tab">Information</a>
                </li>
                <li role="presentation">
                    <a href="#authority" aria-controls="authority" role="tab" data-toggle="tab">Authority</a>
                </li>
                <li role="presentation">
                    <a href="#cbc" aria-controls="cbc" role="tab" data-toggle="tab" style="border-left: 0">CBC Result</a>
                </li>
                <li role="presentation">
                    <a href="#business_income" aria-controls="business_income" role="tab" data-toggle="tab">Business Income</a>
                </li>
                <li role="presentation">
                    <a href="#salary_income" aria-controls="salary_income" role="tab" data-toggle="tab">Salary Income</a>
                </li>
                <li role="presentation">
                    <a href="#attachment_income" aria-controls="attachment_income" role="tab" data-toggle="tab">Attachment</a>
                </li>
                <li role="presentation">
                    <a href="#identity_info" aria-controls="identity_info" role="tab" data-toggle="tab">Identity Information</a>
                </li>
                <li role="presentation">
                    <a href="#asset_list" aria-controls="asset_list" role="tab" data-toggle="tab">Asset List</a>
                </li>
                <li role="presentation">
                    <a href="#credit_history" aria-controls="credit_history" role="tab" data-toggle="tab">Credit History</a>
                </li>
            </ul>
            <div class="tab-content client-verify-info verify-info">
                <?php include(template("widget/inc.client.reference.client_info")); ?>

                <?php include(template("widget/inc.client.reference.authority")); ?>

                <?php $client_cbc = $reference_info['client_cbc']; $cbc_not_show = true;?>
                <?php include(template("widget/inc.client.reference.cbc")); ?>

                <?php
                $co_research = $reference_info['co_research'];
                $member_industry_info = $reference_info['member_industry_info'];
                ?>
                <?php include(template("widget/inc.client.reference.business_income")); ?>

                <?php
                $salary_income = $reference_info['salary_income'];
                $viewer_width = 260;
                ?>
                <?php include(template("widget/inc.client.reference.salary_income")); ?>

                <?php $attachment = $reference_info['attachment']; ?>
                <?php include(template("widget/inc.client.reference.attachment")); ?>

                <?php $check_list = $reference_info['check_list']; ?>
                <?php include(template("widget/inc.client.reference.check_list")); ?>

                <?php $assets_group = $reference_info['assets_group']; ?>
                <?php $rental_research = $reference_info['rental_research']; ?>
                <?php $assets_evaluate_list = $reference_info['assets_evaluate_list']; ?>
                <?php include(template("widget/inc.client.reference.asset_list")); ?>

                <?php include(template("widget/inc.client.reference.credit_history")); ?>
            </div>
        </div>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>
