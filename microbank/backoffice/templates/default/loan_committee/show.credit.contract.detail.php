<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    em {
        font-weight: 500;
        font-size: 15px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
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

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        background-color: #FFF;
        overflow: hidden;
    }

    .content td {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }

    .voting-list .fa {
        font-size: 18px;
        margin-left: 10px;
        color: #666666;
    }

    .voting-list .fa-check {
        color: #008000 !important;
    }

    .voting-list .fa-close {
        color: red !important;
    }

    .contract-img {
        padding: 3px 5px 3px 0;
    }

</style>
<?php
$authorized_contract = $output['authorized_contract'];
$contract_images = $output['contract_images'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Committee</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('loan_committee', 'grantCreditHistory', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>History</span></a>
                </li>
                <li><a class="current"><span>Credit Contract</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $client_info = $output['client_info']; ?>
        <div class="col-sm-12">
            <?php require_once template('widget/item.member.summary1'); ?>
        </div>

        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Credit Contract</h5>
                </div>
                <div class="content">
                    <table class="table">
                        <tr>
                            <td><label class="control-label">Contract Sn</label></td>
                            <td>
                                <em><?php echo $authorized_contract['contract_no']; ?></em>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Credit</label></td>
                            <td>
                                <em><?php echo ncAmountFormat($authorized_contract['total_credit']); ?></em>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Fee</label></td>
                            <td>
                                <em><?php echo ncAmountFormat($authorized_contract['fee']); ?></em>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Loan Fee Amount</label></td>
                            <td>
                                <?php echo ncAmountFormat($authorized_contract['loan_fee_amount']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Admin Fee Amount</label></td>
                            <td>
                                <?php echo ncAmountFormat($authorized_contract['admin_fee_amount']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Creator</label></td>
                            <td>
                                <?php echo $authorized_contract['officer_name']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Create Time</label></td>
                            <td>
                                <?php echo $authorized_contract['create_time']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Pay Time</label></td>
                            <td>
                                <?php echo $authorized_contract['pay_time']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Contract Image</label></td>
                            <td>
                                <?php
                                    $image_list=array();
                                    foreach($contract_images as $img_item){
                                        $image_list[] = array(
                                            'url' => $img_item['image_path']
                                        );
    //                              $image_list[]=$img_item['image_url'];
                                    }
                                include(template(":widget/item.image.viewer.list"));
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center">
                                <button class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                                <?php if($output['allowed_delete']){?>
                                    <button type="button" class="btn btn-primary" onclick="btn_disable_contract_onclick(<?php echo $authorized_contract['uid']?>)">Disable</button>
                                <?php }?>
                            </td>
                        </tr>
                        <?php if($output['allowed_delete']){?>
                            <tr>
                               <td colspan="10">
                                   Allowed to Disable when the contract was invalid,not return any fee.
                               </td>
                            </tr>
                        <?php }?>


                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    function btn_disable_contract_onclick(_uid){
        yo.confirm("Confirm","Are you sure to disable the contract?",function(_r){
            if(!_r) return;
            showMask();
            yo.loadData({
               _c:"loan_committee",
               _m:"cancelCreditAgreement",
                param:{contract_id:_uid},
                callback:function(_o){
                    hideMask();
                    if(_o.STS){
                        alert("Cancel Success!",1,function(){
                           history.back(-1);
                        });
                    }else{
                        alert(_o.MSG,2);
                    }
                }
            });

        });
    }
</script>
<?php include(template(":widget/item.image.viewer.js"));?>