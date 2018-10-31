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

    #business_scene img:not(.img-source), #business_photo img:not(.img-source) {
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

<?php $member_id = $client_info['uid']; ?>
<?php $reference_info = credit_researchClass::getMemberMemberReferenceInfo($member_id,$grant_id?:0); ?>
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
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
<div class="tab-content">
    <?php $client_cbc = $reference_info['client_cbc']; ?>
    <?php include(template("widget/inc.client.reference.cbc")); ?>

    <?php
    $co_research = $reference_info['co_research'];
    $member_industry_info = $reference_info['member_industry_info'];
    ?>
    <?php include(template("widget/inc.client.reference.business_income")); ?>

    <?php $salary_income = $reference_info['salary_income']; ?>
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
<?php include(template(":widget/item.image.viewer.js"));?>
<script>
    function showMyGoogleMap(_cx,_cy){
        yo.dynamicTpl({
            tpl: "widget/google.map.point",
            ext:{data:{point:{x:_cx,y:_cy},no_js:1}},
            callback: function (_tpl) {
                var _div=$('<div style="margin-bottom: 20px;height: 500px;width: 500px;display: block"></div>');
                _div.html(_tpl);
                showMask();
                setTimeout(function(){
                    yo.dialog.show({
                        title:"Google Map",
                        content:_div,
                        buttons:[{text:"Close",handler:function(){
                            yo.dialog.close();
                        }}]
                    });
                    hideMask();
                },1000);

            }
        });

    }
</script>