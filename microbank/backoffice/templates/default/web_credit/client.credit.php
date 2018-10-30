<style>
    .row {
        padding: 20px;
    }

    .ibox-title {
        min-height: 34px !important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .account-basic {
        margin-bottom: 0;
    }

    #member-icon {
        width: 90px;
        height: 90px;
        border-radius: 3px;
    }

    .pull-left {
        margin-right: 15px;
    }

    .margin-large-left p {
        line-height: 15px;
    }

    .btn {
        padding: 4px 12px;
        border-radius: 0;
        min-width: 70px;
    }

    .table .content {
        padding-top: 5px;
    }

    .table .content .col-second, .content .col-third {
        margin-left: 15px;
    }

    .table .content .text {
        line-height: 20px;
    }

    .table .content .currency {
        font-style: normal;
        margin-left: 5px;
    }

    .link-item:hover {
        color: #3b3bf1;
        cursor: pointer;
    }
    .table-no-background{

    }

    .table.table-no-background tbody tr:nth-child(even){
        background: transparent;
    }

    .change-state {
        min-width: 80px;
        margin-right: 5px;
        margin-bottom: 3px;
    }
    .panel-item{
        padding: 0px;
        margin-bottom: 0px;
        border: none;
        border-radius: 0;
    }
    .panel-item .panel-heading{
        padding-top: 3px;
        padding-bottom: 3px;
        border-radius: 0;
    }
    .panel-item .panel-heading .panel-title{
        font-size: 14px;
        font-weight: bold;
    }
    .panel-item .panel-heading .btn{
        float: right;
        position: relative;
        border: none;
        min-width: auto;
        padding-top: 2px!important;
        padding-bottom: 2px!important;
        padding-left: 0;padding-right: 0;
        background-color: transparent;


    }

    #myModal .modal-dialog {
        margin-top: 20px!important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3><?php echo $output['is_bm'] ? 'Client' : 'Client-Control-Panel'; ?></h3>
            <ul class="tab-base">
                <?php if($output['hide_top_menu']){?>
                    <li><a href="#" onclick="javascript:history.back(-1);"><span> Back </span></a></li>
                <?php }else{?>
                    <?php if($output['is_bm']){?>
                        <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <?php }else{ ?>
                        <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <?php }?>

                <?php }?>
                <li><a  class="current"><span>Client Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php 
            $work_type_lang = enum_langClass::getWorkTypeEnumLang();
            $client_info = $output['client_info'];
            $is_bm = $output['is_bm'];
            $work_type = $output['work_type'];
            $residence = $output['residence'];
            $map_detail = $output['map_detail'];
            $member_co_list = $output['member_co_list'];
            $member_operator = $output['member_operator'];
            $product_list = $output['allow_product'];
            $identity_list = $output['identity_list'];
            $member_cbc = $output['member_cbc'];
            $client_request = $output['client_request'];
            $is_voting_suggest = $output['is_voting_suggest'];
            $assets = $output['assets'];
            $business_income = $output['business_income'];
            $salary_list = $output['salary_income'];
            $attachment_list = $output['attachment_income'];
            $suggest_list = $output['suggest_list'];
            $credit_category=$output['credit_category'];
        ?>
        <?php include template("web_credit/client.credit.part.left")?>
        <?php include template("web_credit/client.credit.part.right")?>
    </div>
</div>
<script>
    function submit_hq_onclick(uid) {
        if(!uid){
            return;
        }
        $.messager.confirm("Confirm","Confirm To Submit",function(_r){
           if(!_r) return;
            yo.loadData({
                _c: "web_credit",
                _m: "submitRequestCreditToHq",
                param: {uid: uid},
                callback: function (_o) {
                    if (_o.STS) {
                        alert(_o.MSG,1,function(){
                            window.location.reload();
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });

    }
    function cancel_submit_hq_onclick(uid){
        if(!uid){
            return;
        }
        $.messager.confirm("Confirm","Are you sure to revoke the application from head-office",function(_r){
            if(!_r) return;
            yo.loadData({
                _c: "web_credit",
                _m: "cancelSubmitRequestCreditToHq",
                param: {uid: uid},
                callback: function (_o) {
                    if (_o.STS) {
                        alert(_o.MSG,1,function(){
                            window.location.reload();
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }

    function submit_bm_onclick(uid){
        if(!uid){
            return;
        }

        $.messager.confirm("Confirm","Confirm To Submit",function(_r){
            if(!_r) return;
            yo.loadData({
                _c: "web_credit",
                _m: "submitRequestCreditToFastGrant",
                param: {uid: uid},
                callback: function (_o) {
                    if (_o.STS) {
                        alert(_o.MSG,1,function(){
                            window.location.reload();
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }

</script>