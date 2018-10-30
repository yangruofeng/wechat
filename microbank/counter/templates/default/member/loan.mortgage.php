<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/magnifier/magnifier.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        border-radius: 0px;
    }

    .search-table input {
        height: 34px !important;
    }

    .info-div {
        margin-bottom: 20px;
    }

    .info-div .content {
        padding: 5px 0 0;
    }

    .info-div .content .table td {
        padding: 8px 20px;
    }

    .info-div .content .table.contract-table td:nth-child(1) {
        width: 20%;
    }

    .info-div .content .table.contract-table td:nth-child(2) {
        width: 30%;
    }

    .info-div .content .table.contract-table td:nth-child(3) {
        width: 20%;
    }

    .info-div .content .table.contract-table td:nth-child(4) {
        width: 30%;
    }

    .info-div .content .table td a {
        margin-left: 10px;
    }

    .info-div .content .table td label {
        margin-bottom: 0px;
    }

    .custom-btn-group {
        float: inherit;
    }

    .loan-exp-wrap {
        filter: alpha(Opacity=0);
        opacity: 0;
        z-index: 99;
        -moz-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -o-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -webkit-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        transition: top .2s ease-in-out, opacity .2s ease-in-out;
        visibility: hidden;
        position: absolute;
        top: 37px;
        right: 3px;
        padding: 7px 10px;
        border: 1px solid #ddd;
        background-color: #f6fcff;
        color: #5b9fe2;
        font-size: 12px;
        font-family: Arial, "Hiragino Sans GB", simsun;
    }

    .loan-exp-wrap .pos {
        position: relative;
    }

    .triangle-up {
        background-position: 0 -228px;
        height: 8px;
        width: 12px;
        display: block;
        position: absolute;
        top: -15px;
        right: 240px;
        bottom: auto;
    }

    .triangle-up {
        background-image: url(./resource/img/member/common-slice-s957d0c8766.png);
        background-repeat: no-repeat;
        overflow: hidden;
    }

    .loan-exp-table .t {
        color: #a5a5a5;
        font-size: 12px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a {
        color: #000;
        font-size: 14px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a .y {
        color: #ea544a;
    }

    tr.t td, tr.a td {
        padding: 4px 0px !important;
    }

    .contract-btn .btn {
        padding: 5px 7px;
    }

    #repaymentModal .modal-dialog {
        margin-top: 10px !important;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }

    .snapshot_div {
        height: 130px!important;
        position: relative;
    }

    .snapshot_div .remove-btn {
        width: 15px;
        height: 15px;
        padding: 0!important;
        color: #9E9E9C;
        position: absolute;
        top: 3px;
        right: 3px;
        border: 1px solid;
        border-radius: 15px;
        font-size: 12px;
    }

    .snapshot_div .remove-btn:hover {
        color: #6e6969;
    }

    .content{
        padding-left: 15px!important;

    }

    .btn-danger{
        margin-bottom:40px;
    }
</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <?php $info = $output['contract_info'];?>
    <div class="container">
        <div class="info-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Contract Information</h5>
                </div>
                <div class="content">
                    <input type="hidden" id="uid" value="<?php echo $info['uid'] ?>"/>
                    <table class="table contract-table">
                        <tbody class="table-body">
                        <tr>
                            <td><label class="control-label">Client-ID</label></td>
                            <td><?php echo $info['member_id'] ?></td>
                            <td><label class="control-label">Client-Name</label></td>
                            <td><?php echo $info['display_name'] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Client-Phone</label></td>
                            <td><?php echo $info['phone_id'] ?></td>
                            <td><label class="control-label">Contract No.</label></td>
                            <td><?php echo $info['contract_sn'] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Status</label></td>
                            <td>
                                <?php switch ($info['state']) {
                                    case loanContractStateEnum::CREATE :
                                        $label = 'Create';
                                        break;
                                    case loanContractStateEnum::PENDING_APPROVAL :
                                        $label = 'Pending Approval';
                                        break;
                                    case loanContractStateEnum::PENDING_DISBURSE :
                                        $label = 'Pending Disburse';
                                        break;
                                    case loanContractStateEnum::PROCESSING :
                                        $label = 'Ongoing';
                                        break;
                                    case loanContractStateEnum::PAUSE :
                                        $label = 'Pause';
                                        break;
                                    case loanContractStateEnum::COMPLETE :
                                        $label = 'Complete';
                                        break;
                                    case loanContractStateEnum::WRITE_OFF :
                                        $label = 'Write Off';
                                        break;
                                    default:
                                        $label = 'Write Off';
                                        break;
                                }
                                echo $label;
                                ?>
                            </td>
                            <td><label class="control-label">Product Name</label></td>
                            <td><?php echo $info['product_name'] ?></td>
                        </tr>

                        <tr>
                            <td><label class="control-label">Apply Amount</label></td>
                            <td class="money-style"><?php echo ncAmountFormat($info['apply_amount']) ?></td>
                            <td><label class="control-label">Period</label></td>
                            <td><?php echo $info['loan_period_value'] . ' ' . $info['loan_period_unit'] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Repayment Type</label></td>
                            <td><?php echo ucwords(str_replace('_', ' ', $info['repayment_type'])) ?></td>
                            <td><label class="control-label">Installment</label></td>
                            <td style="position:relative;">
                                <em style="padding-left: 0px"><?php echo count($info['installment']) . ' Periods' ?></em>
                                <div class="loan-exp-wrap">
                                    <div class="pos">
                                        <em class="triangle-up"></em>
                                        <table class="loan-exp-table">
                                            <tr class="t">
                                                <td>Amount</td>
                                                <td></td>
                                                <td>Principal</td>
                                                <td></td>
                                                <td>Interest</td>
                                                <td></td>
                                                <td>Admin Fee</td>
                                                <td></td>
                                                <td>Operation Fee</td>
                                            </tr>

                                            <?php foreach ($info['installment'] as $key => $value) { ?>
                                                <tr class="a">
                                                    <td class="y"><?php echo $value['amount']; ?></td>
                                                    <td>&nbsp;=&nbsp;</td>
                                                    <td><?php echo $value['receivable_principal']; ?></td>
                                                    <td>&nbsp;+&nbsp;</td>
                                                    <td><?php echo $value['receivable_interest']; ?></td>
                                                    <td>&nbsp;+&nbsp;</td>
                                                    <td><?php echo $value['receivable_admin_fee']; ?></td>
                                                    <td>&nbsp;+&nbsp;</td>
                                                    <td><?php echo $value['receivable_operation_fee']; ?></td>
                                                </tr>
                                            <?php }?>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Interest Rate</label></td>
                            <td><?php echo ($info['interest_rate_type'] == 1 ? "$" . $info['interest_rate'] : $info['interest_rate'] . '%') . ' Per ' . $info['interest_rate_unit']  ?></td>

                            <td><label class="control-label">Operation Fee</label></td>
                            <td><?php echo ($info['operation_fee_type'] == 1 ? "$" . $info['operation_fee'] : $info['operation_fee'] . '%') . ' Per ' . $info['operation_fee_unit']  ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Admin Fee</label></td>
                            <td><?php echo $info['admin_fee_type'] == 1 ? "$" . $info['admin_fee'] : $info['admin_fee'] . '%' ?></td>
                            <td><label class="control-label">Loan Fee</label></td>
                            <td><?php echo $info['loan_fee_type'] == 1 ? "$" . $info['loan_fee'] : $info['loan_fee'] . '%' ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Insurance Fee</label></td>
                            <td><?php echo ncAmountFormat($info['insurance'][$info['uid']]['price']) ?></td>
                            <td><label class="control-label">Due Date</label></td>
                            <td><?php echo dateFormat($info['end_date']) ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="info-div mortgage-div">
                <div class="basic-info">
                    <div class="ibox-title" style="position: relative">
                        <h5><i class="fa fa-id-card-o"></i>Mortgage</h5>
                    </div>
                    <div class="content">
                        <?php if($output['mortgage_list']){?>
                            <div>
                                <table class="table verify-table">
                                    <tbody class="table-body">
                                    <?php foreach ($output['mortgage_list'] as $key => $row) { ?>
                                        <tr>
                                            <td class="magnifier<?php echo $key; ?>" style="width: 380px;padding: 2px">
                                                <div class="magnifier" index="<?php echo $key; ?>">
                                                    <div class="magnifier-container" style="display:none;">
                                                        <div class="images-cover"></div>
                                                        <div class="move-view"></div>
                                                    </div>
                                                    <div class="magnifier-assembly">
                                                        <div class="magnifier-btn">
                                                            <span class="magnifier-btn-left">&lt;</span>
                                                            <span class="magnifier-btn-right">&gt;</span>
                                                        </div>
                                                        <!--按钮组-->
                                                        <div class="magnifier-line">
                                                            <ul class="clearfix animation03">
                                                                <?php foreach ($row['img_list'] as $value) { ?>
                                                                    <li>
                                                                        <a target="_blank" href="<?php echo getImageUrl($value); ?>">
                                                                            <div class="small-img">
                                                                                <img src="<?php echo getImageUrl($value, imageThumbVersion::SMALL_IMG); ?>"/>
                                                                            </div>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                        <!--缩略图-->
                                                    </div>
                                                    <div class="magnifier-view"></div>
                                                    <!--经过放大的图片显示容器-->
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cert-info">
                                                    <p><label class="lab-name">Type : </label><?php echo $lang['certification_type_' . $row['cert_type']]; ?></p>
                                                    <p><label class="lab-name">Source Type : </label>
                                                        <?php echo $lang['cert_source_type_' . $row['source_type']]?>
                                                        <?php if ($row['creator_name']) { ?>
                                                            <span>【<?php echo $row['creator_name']; ?>】</span>
                                                        <?php } ?>
                                                    </p>
                                                    <p><label class="lab-name">Submit Time : </label><?php echo timeFormat($row['create_time']); ?></p>
                                                    <p><label class="lab-name">Remark : </label><?php echo $row['verify_remark'] ?: ' /'; ?></p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else {?>
                            <div style="min-height: 30px;padding: 5px 20px">Null</div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        <div class="col-sm-12 form-group" style="text-align: center;margin-top: 20px">
            <a class="btn btn-danger" href="<?php echo getUrl('member', 'loan', array('uid' => $info['uid']), false, ENTRY_COUNTER_SITE_URL); ?>">Back</a>
        </div>
    </div>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script>

    $(function(){
        $('.info-div').on('mouseover', 'em', function () {
            $(this).closest('tr').find('.loan-exp-wrap').css({'opacity': 1, 'visibility': 'visible'});
        })

        $('.info-div').on('mouseleave', 'em', function () {
            $(this).closest('tr').find('.loan-exp-wrap').css({'opacity': 0, 'visibility': 'hidden'});
        })

        $('.magnifier-btn-left').on('click', function () {
            var el = $(this).parents('.magnifier'), thumbnail = el.find('.magnifier-line > ul'), index = $(this).index();
            move(el, thumbnail, index);
        });

        $('.magnifier-btn-right').on('click', function () {
            var el = $(this).parents('.magnifier'), thumbnail = el.find('.magnifier-line > ul'), index = $(this).index();
            move(el, thumbnail, index);
        });
    })


    function move(magnifier, thumbnail, _boole) {
        magnifier.index = _boole;
        (_boole) ? magnifier.index++ : magnifier.index--;
        var thumbnailImg = thumbnail.find('>*'), lineLenght = thumbnailImg.length;
        var _deviation = Math.ceil(magnifier.width() / thumbnailImg.width() / 2);
        if (lineLenght < _deviation) {
            return false;
        }
        (magnifier.index < 0) ? magnifier.index = 0 : (magnifier.index > lineLenght - _deviation) ? magnifier.index = lineLenght - _deviation : magnifier.index;
        var endLeft = (thumbnailImg.width() * magnifier.index) - thumbnailImg.width();
        thumbnail.css({
            'left': ((endLeft > 0) ? -endLeft : 0) + 'px'
        });
    }

    function callWin_snapshot_master(){
        if(window.external){
            try{
                var _img_path= window.external.getSnapshot("0");
                if(_img_path!="" && _img_path!=null){
                    var html = $('#snapshot_div').html();
                    html = $(html);
                    html.find('img').attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    html.find('input[name="scan_img[]"]').val(_img_path);
                    $('.scene-photo .content').append(html);
                }
            }catch (ex){
                alert(ex.Message);
            }
        }
    }

    function scan_remove(e) {
        $(e).closest('.snapshot_div').remove();
    }

    function submit_contract() {
        $('#contract_form').submit();
    }



    }
</script>
