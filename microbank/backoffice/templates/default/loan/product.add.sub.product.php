<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/product.css?v=5" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.config.js' ?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.all.js' ?>"></script>
<style>
    .btn-release,.btn-unshelve{
        position: absolute;
        top: 0px;
        right: 10px;
        height: 30px;
        line-height: 30px;
        padding: 0px 15px;
    }
    .base-info .size-info .content{
        overflow: auto;
        padding: 5px 0 10px;
        margin: 5px 15px;
    }

    .page-2 .content {
        padding: 5px 20px 10px;
    }

</style>
<?php
$main_product_info = $output['main_product_info'];
$product_info = $output['sub_product_info'];
$repayment_period_lang = enum_langClass::getLoanRepaymentPeriodLang();
$interest_type_lang = enum_langClass::getLoanInstallmentTypeLang();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'product', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Sub product</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <ul class="tab-top clearfix">
<!--            --><?php //if (!$product_info) { ?>
<!--                <li class="active" page="page-1"><a>Base Info</a></li>-->
<!--                <li page="page-2"><a>Details</a></li>-->
<!--            --><?php //} else { ?>
                <li page="page-1"></li>
<!--            --><?php //} ?>
        </ul>
        <div>
            <button id="btn_release_product" class="btn btn-default btn-release" style="display:<?php echo ($product_info && $product_info['state'] != loanProductStateEnum::ACTIVE) ? 'block;' : 'none;' ?>">Active</button>
            <button id="btn_unshelve_product" class="btn btn-default btn-unshelve" style="display:<?php echo $product_info['state'] == loanProductStateEnum::ACTIVE ? 'block;' : 'none;' ?>">Inactive</button>
        </div>

        <div class="page-1">
            <div class="base-info clearfix">
                <div class="product-info">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Sub product Info</h5></div>
                        <div class="col-sm-4">
                            <i class="fa fa-edit" title="Edit" onclick="edit_info()"></i>
                        </div>
                    </div>
                    <div class="content clearfix">
                        <div class="wrap1">
                            <table>
                                <tr style="font-weight: bold">
                                    <input type="hidden" id="uid" value="<?php echo $product_info['uid'];?>">
                                    <td>Product Name：</td>
                                    <td id="product_name"><?php echo $product_info['sub_product_name'];?></td>
                                </tr>
                                <tr>
                                    <td>Repayment Type: </td>
                                    <td id="repayment_type" val="<?php echo $product_info['repayment_type']; ?>"><?php  if( isset($product_info['repayment_type']) ){ echo $repayment_period_lang[$product_info['repayment_type']]; }  ?></td>
                                </tr>
                                <tr>
                                    <td>Interest Type: </td>
                                    <td id="interest_type" val="<?php echo $product_info['interest_type']; ?>"><?php  if( isset($product_info['interest_type']) ){ echo $interest_type_lang[$product_info['interest_type']]; }  ?></td>
                                </tr>
                                <tr>
                                    <td>State：</td>
                                    <td id="state" val="<?php echo $product_info['state']?>"><?php echo $lang['enum_loan_product_state_'.$product_info['state']]?></td>
                                </tr>
                                <tr style="display: none">
                                    <td colspan="2" id="sub_summary"  val="<?php echo $product_info['sub_summary']?>"><?php echo $product_info['sub_summary']; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="wrap2">
                            <table>
                                <tr style="font-weight: bold">
                                    <td>Product Code：</td>
                                    <td id="product_code"><?php echo $product_info['sub_product_code']?></td>
                                </tr>
                                <tr>
                                    <td>Is full interest prepayment：</td>
                                    <td id="is_full_interest_prepayment" val="<?php echo $product_info['is_full_interest_prepayment']?>"><?php echo (isset($product_info['is_full_interest_prepayment'])?($product_info['is_full_interest_prepayment']==1?'YES':'NO'):'')?></td>
                                </tr>
                                <tr>
                                    <td>Is Only For Counter：</td>
                                    <td id="is_only_for_counter" val="<?php echo $product_info['is_only_for_counter']?>"><?php echo (isset($product_info['is_only_for_counter'])?($product_info['is_only_for_counter']==1?'YES':'NO'):'')?></td>
                                </tr>

                                <!--<tr>
                                    <td>Max Contracts Per Client：</td>
                                    <td id="max_contracts_per_client" val="<?php /*echo $product_info['max_contracts_per_client']?:0; */?>"><?php /*echo $product_info['max_contracts_per_client']?:'Null';*/?></td>
                                </tr>-->

                            </table>
                        </div>
                    </div>
                </div>
                <div class="penalty-info">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Penalty</h5></div>
                        <div class="col-sm-4"><i class="fa fa-edit <?php echo !$product_info['uid']?'not-allowed':''?> allow-state" title="<?php echo $product_info['uid'] ? 'Edit' : 'Please save the prodcut info first.'?>" onclick="edit_penalty()"></i></div>
                    </div>
                    <div class="content">
                        <table>
                            <tr>
                                <td>Penalty Rate：</td>
                                <td id="penalty_rate" val="<?php echo $product_info['penalty_rate'] > 0 ? $product_info['penalty_rate'] : ""; ?>"><?php echo $product_info['penalty_rate'] > 0 ? ($product_info['penalty_rate'] . '%') : ''?></td>
                            </tr>
                            <tr>
                                <td>Divisor Days：</td>
                                <td id="penalty_divisor_days" val="<?php echo $product_info['penalty_divisor_days'] > 0 ? $product_info['penalty_divisor_days'] : ""?>"><?php echo $product_info['penalty_divisor_days'] > 0 ? ($product_info['penalty_divisor_days'].' Days'):''?></td>
                            </tr>
                            <tr>
                                <td>Grace Days: </td>
                                <td id="grace_days" val="<?php echo $product_info['grace_days'] > 0 ? $product_info['grace_days'] : ""?>"><?php echo $product_info['grace_days'] > 0 ? ($product_info['grace_days'].' Days'):''?></td>
                            </tr>
                            <tr>
                                <td>Is Compound Interest: </td>
                                <td id="grace_days" data-val="<?php echo $product_info['penalty_is_compound_interest']; ?>"><?php  if( isset($product_info['penalty_is_compound_interest']) ) {  echo $product_info['penalty_is_compound_interest']  ? 'Yes':'No'; } ?></td>

                            </tr>
                            <tr>
                                <td>Editable：</td>
                                <td id="is_editable_penalty" val="<?php echo $product_info['is_editable_penalty']?$product_info['is_editable_penalty']:''?>"><?php if( ($product_info['penalty_rate']) > 0 ){ echo $product_info['is_editable_penalty']==1?'YES':'NO'; } ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="base-info clearfix">
                <div class="size-info">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Size Rate</h5></div>
                        <div class="col-sm-4"><i class="fa fa-plus <?php echo !$product_info['uid']?'not-allowed':''?> allow-state" title="<?php echo $product_info['uid'] ? 'Add' : 'Please save the prodcut info first.'?>" onclick="add_size_rate()"></i></div>
                    </div>
                    <div class="content clearfix">
                    </div>
                </div>
            </div>
        </div>
<!--        <div class="page-2 no_clear_ul_style">-->
<!--            <div class="base-info clearfix">-->
<!--                <div class="description">-->
<!--                    <div class="ibox-title">-->
<!--                        <div class="col-sm-8"><h5>Description</h5></div>-->
<!--                        <div class="col-sm-4">-->
<!--                            <i class="fa fa-edit --><?php //echo !$product_info['uid']?'not-allowed':''?><!-- allow-state" title="--><?php //echo $product_info['uid'] ? 'Save' : 'Please save the prodcut info first.'?><!--" onclick="edit_text('description')"></i>-->
<!--                            <i class="fa fa-mail-reply" onclick="cancel_text('description')"></i>-->
<!--                            <i class="fa fa-floppy-o" onclick="save_text('description')"></i>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="content clearfix">-->
<!--                        <div>--><?php //echo $product_info['product_description']?><!--</div>-->
<!--                        <textarea name="description" id="description" style="display: none;">--><?php //echo $product_info['product_description']?><!--</textarea>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="qualification">-->
<!--                    <div class="ibox-title">-->
<!--                        <div class="col-sm-8"><h5>Qualification</h5></div>-->
<!--                        <div class="col-sm-4">-->
<!--                            <i class="fa fa-edit --><?php //echo !$product_info['uid']?'not-allowed':''?><!-- allow-state" title="--><?php //echo $product_info['uid'] ? 'Save' : 'Please save the prodcut info first.'?><!--" onclick="edit_text('qualification')"></i>-->
<!--                            <i class="fa fa-mail-reply" onclick="cancel_text('qualification')"></i>-->
<!--                            <i class="fa fa-floppy-o" onclick="save_text('qualification')"></i>-->
<!--                        </div>-->
<!---->
<!--                    </div>-->
<!--                    <div class="content clearfix">-->
<!--                        <div>--><?php //echo $product_info['product_qualification']?><!--</div>-->
<!--                        <textarea name="qualification" id="qualification" style="display: none;">--><?php //echo $product_info['product_qualification']?><!--</textarea>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="base-info clearfix">-->
<!--                <div class="feature">-->
<!--                    <div class="ibox-title">-->
<!--                        <div class="col-sm-8"><h5>Feature</h5></div>-->
<!--                        <div class="col-sm-4">-->
<!--                            <i class="fa fa-edit --><?php //echo !$product_info['uid']?'not-allowed':''?><!-- allow-state" title="--><?php //echo $product_info['uid'] ? 'Save' : 'Please save the prodcut info first.'?><!--" onclick="edit_text('feature')"></i>-->
<!--                            <i class="fa fa-mail-reply" onclick="cancel_text('feature')"></i>-->
<!--                            <i class="fa fa-floppy-o" onclick="save_text('feature')"></i>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="content clearfix">-->
<!--                        <div>--><?php //echo $product_info['product_feature']?><!--</div>-->
<!--                        <textarea name="feature" id="feature" style="display: none;">--><?php //echo $product_info['product_feature']?><!--</textarea>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="required">-->
<!--                    <div class="ibox-title">-->
<!--                        <div class="col-sm-8"><h5>Required</h5></div>-->
<!--                        <div class="col-sm-4">-->
<!--                            <i class="fa fa-edit --><?php //echo !$product_info['uid']?'not-allowed':''?><!-- allow-state" title="--><?php //echo $product_info['uid'] ? 'Save' : 'Please save the prodcut info first.'?><!--" onclick="edit_text('required')"></i>-->
<!--                            <i class="fa fa-mail-reply" onclick="cancel_text('required')"></i>-->
<!--                            <i class="fa fa-floppy-o" onclick="save_text('required')"></i>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="content clearfix">-->
<!--                        <div>--><?php //echo $product_info['product_required']?><!--</div>-->
<!--                        <textarea name="required" id="required" style="display: none;">--><?php //echo $product_info['product_required']?><!--</textarea>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="base-info clearfix">-->
<!--                <div class="notice">-->
<!--                    <div class="ibox-title">-->
<!--                        <div class="col-sm-8"><h5>Notice</h5></div>-->
<!--                        <div class="col-sm-4">-->
<!--                            <i class="fa fa-edit --><?php //echo !$product_info['uid']?'not-allowed':''?><!-- allow-state" title="--><?php //echo $product_info['uid'] ? 'Save' : 'Please save the prodcut info first.'?><!--" onclick="edit_text('notice')"></i>-->
<!--                            <i class="fa fa-mail-reply" onclick="cancel_text('notice')"></i>-->
<!--                            <i class="fa fa-floppy-o" onclick="save_text('notice')"></i>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="content clearfix">-->
<!--                        <div>--><?php //echo $product_info['product_notice']?><!--</div>-->
<!--                        <textarea name="notice" id="notice" style="display: none;">--><?php //echo $product_info['product_notice']?><!--</textarea>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

    </div>
</div>

<div class="modal" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Base Info'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">

                    <form class="form-horizontal" enctype="multipart/form-data" id="info_form">

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Product Code'?></label>
                            <div class="col-sm-9">
                                <input type="text"  class="form-control" name="product_code" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Product Name'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="product_name" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>


                       <!-- <div class="form-group">
                            <label for="" class="col-sm-3 control-label"><span class="required-options-xing">*</span>Product icon</label>
                            <div class="col-sm-9">
                                <input type="file" name="product_icon" class="form-control">
                                <div class="error_msg"></div>
                            </div>
                        </div>-->

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Interest Type'?></label>
                            <div class="col-sm-9">
                                <select name="interest_type" id="" class="form-control">
                                    <?php foreach( $output['interest_type'] as $way ){ ?>
                                        <option value="<?php echo $way; ?>"><?php echo enum_langClass::getLoanInstallmentTypeLang()[$way]; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Repayment Frequency'; ?></label>
                            <div class="col-sm-9">
                                <select name="repayment_type" id="" class="form-control">

                                    <?php foreach( $output['repayment_frequency'] as $way ){ ?>
                                        <option value="<?php echo $way; ?>"  ><?php echo $repayment_period_lang[$way]; ?></option>
                                    <?php } ?>

                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Summary'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="sub_summary" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <!--<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php /*echo 'Max contracts per client';*/?></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="max_contracts_per_client" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>-->

                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Is full interest prepayment</label>
                            <div class="col-sm-9">
                                <input type="checkbox" style="margin-top: 10px;"  name="is_full_interest_prepayment" value="1" checked placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>

                       <!-- <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Is approved prepayment request</label>
                            <div class="col-sm-9">
                                <input type="checkbox" style="margin-top: 10px;"  name="is_approved_prepayment_request" checked value="1" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>-->

                       <!-- <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Is Advance Interest</label>
                            <div class="col-sm-9">
                                <input type="checkbox" style="margin-top: 10px;"  name="is_advance_interest" checked value="1" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>-->

                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Is Only For Counter</label>
                            <div class="col-sm-9">
                                <input type="checkbox" style="margin-top: 10px;"  name="is_only_for_counter"  value="1" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
            <div class="modal-footer" style="text-align: center">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_info()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="penaltyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Penalty'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="penalty_form">

                       <!-- <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php /*echo 'Penalty On'*/?></label>
                            <div class="col-sm-9">
                                <select class="form-control" name="penalty_on">
                                    <?php /*foreach ($output['penalty_on'] as $key => $val) { */?>
                                        <option value="<?php /*echo $key*/?>"><?php /*echo $lang['enum_' . $key]*/?></option>
                                    <?php /*} */?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Penalty Rate'?></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="penalty_rate" value="">
                                    <span class="input-group-addon" style="min-width: 55px;border-left: 0">%</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Divisor Days'?></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="penalty_divisor_days" value="">
                                    <span class="input-group-addon" style="min-width: 55px;border-left: 0">Days</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Grace Days'?></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="grace_days" value="">
                                    <span class="input-group-addon" style="min-width: 55px;border-left: 0">Days</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Is compound interest'?></label>
                            <div class="col-sm-9 checkbox-div">
                                <label><input type="checkbox" name="penalty_is_compound_interest" value="1"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Editable Penalty'?></label>
                            <div class="col-sm-9 checkbox-div">
                                <label><input type="checkbox" name="is_editable_penalty"></label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_penalty()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="sizeRateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Size Rate'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="size_rate_form">
                        <input type="hidden" name="size_rate_id" value="">
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Currency'?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="currency">
                                    <?php foreach ( (new currencyEnum())->toArray() as $key => $currency) { ?>
                                        <option value="<?php echo $currency?>"><?php echo $currency?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Minimum'?></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="loan_size_min" value="">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">Amount</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Maximum'?></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="loan_size_max" value="">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">Amount</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Min Term Days'?></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="min_term_days" value="">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">Days</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Max Term Days'?></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="max_term_days" value="">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">Days</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <!--<div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span><?php /*echo $output['mortgage_type']['name']*/?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="mortgage_type">
                                    <option value=""><?php /*echo $lang['common_select']*/?></option>
                                    <?php /*foreach ($output['mortgage_type']['item_list'] as $key => $val) { */?>
                                        <option value="<?php /*echo $key*/?>"><?php /*echo $val*/?></option>
                                    <?php /*} */?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->

                        <!--<div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span><?php /*echo $output['guarantee_type']['name']*/?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="guarantee_type">
                                    <option value=""><?php /*echo $lang['common_select']*/?></option>
                                    <?php /*foreach ($output['guarantee_type']['item_list'] as $key => $val) { */?>
                                        <option value="<?php /*echo $key*/?>"><?php /*echo $val*/?></option>
                                    <?php /*} */?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->

                       <!-- <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php /*echo 'Interest Payment'*/?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="interest_payment">
                                    <?php /*foreach ($output['interest_payment'] as $key => $val) { */?>
                                        <option value="<?php /*echo $key*/?>"><?php /*echo $lang['enum_' . $key]*/?></option>
                                    <?php /*} */?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        -->

                        <!--<div class="col-sm-6 form-group" style="display: none;">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php /*echo 'Installment Period'*/?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="interest_rate_period">
                                    <?php /*foreach ($output['interest_rate_period'] as $key => $val) { */?>
                                        <option value="<?php /*echo $key*/?>"><?php /*echo $lang['enum_' . $key]*/?></option>
                                    <?php /*} */?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->

                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Interest Rate'?>(%)</label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="interest_rate" value="" style="width: 60%">
                                    <!--<select class="form-control" name="interest_rate_type" style="width: 60px">
                                        <option value="0">%</option>
                                        <option value="1">$</option>
                                    </select>-->
                                    <select class="form-control" name="interest_rate_unit" style="width: 40%;">
                                        <option value="<?php echo interestRatePeriodEnum::YEARLY; ?>" selected >Yearly</option>
                                        <option value="<?php echo interestRatePeriodEnum::MONTHLY; ?>">Monthly</option>
                                        <option value="<?php echo interestRatePeriodEnum::DAILY; ?>">Daily</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>


                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Interest rate of mortgageSoft(%)';?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%;">
                                    <input type="number" class="form-control" required="true" name="interest_rate_mortgage1" value="">
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Interest rate of mortgageHard(%)';?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%;" >
                                    <input type="number" class="form-control" required="true" name="interest_rate_mortgage2" value="">
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>




                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Min Interest'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="interest_min_value" value="">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">Amount</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Operation Fee'?>(%)</label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width:100%;">
                                    <input type="number" class="form-control" required="true" name="operation_fee" value="" style="width:60%;">
                                    <!--<select class="form-control" name="operation_fee_type" style="width: 60px" >
                                        <option value="0">%</option>
                                        <option value="1">$</option>
                                    </select>-->
                                    <select class="form-control" name="operation_fee_unit" style="width: 40%;">
                                        <option value="<?php echo interestRatePeriodEnum::YEARLY; ?>" selected >Yearly</option>
                                        <option value="<?php echo interestRatePeriodEnum::MONTHLY; ?>">Monthly</option>
                                        <option value="<?php echo interestRatePeriodEnum::DAILY; ?>">Daily</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>


                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Operate fee of mortgageSoft(%)';?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%;">
                                    <input type="number" class="form-control" required="true" name="operation_fee_mortgage1" value="">
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Operate fee of mortgageHard(%)';?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%;" >
                                    <input type="number" class="form-control" required="true" name="operation_fee_mortgage2" value="">
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>




                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Min Operation Fee'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="operation_min_value" value="">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">Amount</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <!--
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Admin Fee'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="admin_fee" value="" style="width:192px;">
                                    <select class="form-control" name="admin_fee_type" style="width: 80px">
                                        <option value="1">Value</option>
                                        <option value="0">%</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Loan Fee'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="loan_fee" value="" style="width:192px;">
                                    <select class="form-control" name="loan_fee_type" style="width: 80px">
                                        <option value="1">Value</option>
                                        <option value="0">%</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        -->


                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><?php echo 'Service Charges'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="service_fee" value="" style="width: 60%">
                                    <select class="form-control" name="service_fee_type" style="width: 40%;">
                                        <option value="0" selected >%</option>
                                        <option value="1">Amount</option>
                                    </select>

                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>


                        <div class="form-group col-sm-6">
                            <label class="col-sm-4 control-label">Show For Client</label>
                            <div class="col-sm-8">
                                <div class="checkbox">
                                    <input type="checkbox" name="is_show_for_client">
                                </div>
                            </div>
                        </div>

                        <!--<div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php /*echo 'Grace Days'*/?></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" required="true" name="grace_days" value="">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">Days</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->


                       <!--
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php /*echo 'Prepayment'*/?></label>
                            <div class="col-sm-8">
                                <label style="margin-top: 7px;padding-left: 0px"><input type="checkbox" name="is_full_interest">Is paying full interest</label>
                            </div>
                        </div>-->


                       <!-- <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php /*echo 'Prepayment Interest'*/?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" name="prepayment_interest" value="" style="width:212px;">
                                    <select class="form-control" name="prepayment_interest_type" style="width: 60px">
                                        <option value="0">%</option>
                                        <option value="1">$</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>-->


                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_size_rate()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
//        var height = $('.product-info .content').height();
//        $('.penalty-info .content').height(height);
//        $('.tab-top li').click(function () {
//            var _page = $(this).attr('page');
//            $('.tab-top li').removeClass('active');
//            $(this).addClass('active');
//            $('.page-1,.page-2').hide();
//            $('.' + _page).show();
//        })
    });

    var main_product_id = '<?php echo intval($main_product_info['uid']); ?>';
    var uid = '<?php echo intval($product_info['uid'])?>';
    if (uid != 0) {
        getSizeRateList(uid);
    }

    function edit_info() {
        var _product_name = $('#product_name').html();
        var _product_code = $('#product_code').html();
        var _interest_type = $('#interest_type').attr('val');
        var _repayment_type = $('#repayment_type').attr('val');
        var _is_full_interest_prepayment = $('#is_full_interest_prepayment').attr('val');
        //var _is_approved_prepayment_request = $('#is_approved_prepayment_request').attr('val');
        var _state = $('#state').attr('val');
        var _summary=$('#sub_summary').attr('val');
        //var _max_contracts_per_client = $('#max_contracts_per_client').attr('val');

        //var _is_advance_interest = $('#is_advance_interest').attr('val');
        var _is_only_for_counter = $('#is_only_for_counter').attr('val');

        $('#infoModal input[name="product_name"]').val(_product_name);

        $('#infoModal input[name="product_code"]').val(_product_code);
        if( uid > 0 ){
            //不可编辑
            $('#infoModal input[name="product_code"]').attr("readonly","readonly");
        }

        $('#infoModal select[name="interest_type"] option[value="'+_interest_type+'"]').attr('selected',true);

        $('#infoModal select[name="repayment_type"] option[value="'+_repayment_type+'"]').attr('selected',true);
        $('#infoModal input[name="sub_summary"]').val(_summary);

        /*if( _max_contracts_per_client > 0 ){
            $('#infoModal input[name="max_contracts_per_client"]').val(_max_contracts_per_client);
        }*/



        if( _is_full_interest_prepayment == 1 ){
            $('#infoModal  input[name="is_full_interest_prepayment"]').attr('checked',true);
        }else{
            $('#infoModal input[name="is_full_interest_prepayment"]').attr('checked',false);
        }

       /* if( _is_approved_prepayment_request == 1 ){
            $('#infoModal input[name="is_approved_prepayment_request"]').attr('checked',true);
        }else{
            $('#infoModal input[name="is_approved_prepayment_request"]').attr('checked',false);
        }*/

       /* if( _is_advance_interest == 1 ){
            $('#infoModal input[name="is_advance_interest"]').attr('checked',true);
        }else{
            $('#infoModal input[name="is_advance_interest"]').attr('checked',false);
        }*/

        if( _is_only_for_counter == 1 ){
            $('#infoModal input[name="is_only_for_counter"]').attr('checked',true);
        }else{
            $('#infoModal input[name="is_only_for_counter"]').attr('checked',false);
        }

       /* if (_state) {
            $('#infoModal input[name="state"][value="' + _state + '"]').attr('checked', true);
        }*/

        $('#infoModal').modal('show');
    }



    function save_info() {
        if (!$("#info_form").valid()) {
            return;
        }
        var _product_name = $('#infoModal input[name="product_name"]').val();
        var _product_code = $('#infoModal input[name="product_code"]').val();
        if (!_product_code || !_product_name) return;

        //var is_multi_contract = $('#infoModal input[name="is_multi_contract"]').is(':checked');
        var is_advance_interest = $('#infoModal input[name="is_advance_interest"]').is(':checked');
        //var is_editable_interest = $('#infoModal input[name="is_editable_interest"]').is(':checked');
        //var is_editable_grace_days = $('#infoModal input[name="is_editable_grace_days"]').is(':checked');


        var values = $('#info_form').getValues();
        values.main_product_id = main_product_id;


        if ( uid == 0 ) {
            var _m = 'addSubProductSubmit';
        } else {
            var _m = 'editSubProductSubmit';
            values.uid=uid;
        }


        /*var formData = new FormData();
        for( var i in values ){
            formData.append(i,values[i]);
        }
        formData.append('product_icon',$('#infoModal input[name="product_icon"]')[0].files[0]);
        $.ajax({
            url: _url,
            type: 'POST',
            data: formData,                    // 上传formdata封装的数据
            dataType: 'JSON',
            cache: false,                      // 不缓存
            processData: false,                // jQuery不要去处理发送的数据
            contentType: false,                // jQuery不要去设置Content-Type请求头
            success:function (data) {           //成功回调
                console.log(data);
            }
        });*/

        yo.loadData({
            _c: 'loan',
            _m: _m,
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    if (data && data.uid > 0) {
                        window.location.href = '<?php echo getUrl('loan', 'editSubProduct', array(), false, BACK_OFFICE_SITE_URL);?>' + '&uid=' + data.uid;
                        return;
                    }
                    $('#product_name').html(_product_name);
                    $('#product_code').html(_product_code);

                    if (is_multi_contract) {
                        $('#is_multi_contract').attr('val', 1).html('YES');
                    } else {
                        $('#is_multi_contract').attr('val', 0).html('NO');
                    }
                    if (is_advance_interest) {
                        $('#is_advance_interest').attr('val', 1).html('YES');
                    } else {
                        $('#is_advance_interest').attr('val', 0).html('NO');
                    }
                    if (is_editable_interest) {
                        $('#is_editable_interest').attr('val', 1).html('YES');
                    } else {
                        $('#is_editable_interest').attr('val', 0).html('NO');
                    }
                    if (is_editable_grace_days) {
                        $('#is_editable_grace_days').attr('val', 1).html('YES');
                    } else {
                        $('#is_editable_grace_days').attr('val', 0).html('NO');
                    }

                    $('#state').html("Temporary");
                    if (uid == 0) {
                        uid = _o.DATA.uid;
                        $('#uid').val(uid);
                        $('.allow-state').removeClass('not-allowed');
                        $('.btn-release').show();
                        $('.product-info .ibox-title .fa-minus').show();
                    }

                    $('#infoModal').modal('hide');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#btn_release_product').click(function () {
        if (!uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "releaseProduct",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $(this).hide();
                    $('.btn-unshelve').show();
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    });

    $('#btn_unshelve_product').click(function () {
        if (!uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "unShelveProduct",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $(this).hide();
                    $('.btn-release').show();
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    });

    function edit_penalty() {

        if (uid == 0) return;
        //var _penalty_on_val = $('#penalty_on').attr('val');
        var _penalty_rate = $('#penalty_rate').attr('val');
        var _penalty_divisor_days = $('#penalty_divisor_days').attr('val');
        var _grace_days = $('#grace_days').attr('val');
        var is_editable_penalty = $('#is_editable_penalty').attr('val');
        var _penalty_is_compound_interest = $('#penalty_is_compound_interest').attr('data-val');

        //if (_penalty_on_val) $('#penaltyModal select[name="penalty_on"]').val(_penalty_on_val);
        $('#penaltyModal input[name="penalty_rate"]').val(_penalty_rate ? _penalty_rate : '');
        $('#penaltyModal input[name="penalty_divisor_days"]').val(_penalty_divisor_days ? _penalty_divisor_days : '');
        $('#penaltyModal input[name="grace_days"]').val(_grace_days ? _grace_days : '');

        if (is_editable_penalty) {
            $('#penaltyModal input[name="is_editable_penalty"]').attr('checked', true);
        } else {
            $('#penaltyModal input[name="is_editable_penalty"]').attr('checked', false);
        }

        if( _penalty_is_compound_interest > 0 ){
            $('#penaltyModal input[name="penalty_is_compound_interest"]').attr('checked', true);
        }else{
            $('#penaltyModal input[name="penalty_is_compound_interest"]').attr('checked', false);
        }

        $('#penaltyModal').modal('show');
    }

    function save_penalty() {

        if (!$("#penalty_form").valid()) {
            return;
        }
        //var _penalty_on_val = $('#penaltyModal select[name="penalty_on"]').val();
        //var _penalty_on_text = $('#penaltyModal select[name="penalty_on"] option[value="' + _penalty_on_val + '"]').text();
        var _penalty_rate = $('#penaltyModal input[name="penalty_rate"]').val();
        var _penalty_divisor_days = $('#penaltyModal input[name="penalty_divisor_days"]').val();
        var _grace_days = $('#penaltyModal input[name="grace_days"]').val();
        var is_editable_penalty = $('#penaltyModal input[name="is_editable_penalty"]').is(':checked');


        var values = $('#penalty_form').getValues();
        values.uid = uid;
        yo.loadData({
            _c: 'loan',
            _m: 'updateProductPenalty',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    if (data && data.uid > 0) {
                        window.location.href = '<?php echo getUrl('loan', 'editProduct', array(), false, BACK_OFFICE_SITE_URL);?>' + '&uid=' + data.uid;
                        return;
                    }
                    window.location.reload();
                    return true;
                    //$('#penalty_on').attr('val', _penalty_on_val).html(_penalty_on_text);
                    $('#penalty_rate').attr('val', Number(_penalty_rate).toFixed(2)).html(Number(_penalty_rate).toFixed(2) + '%');
                    $('#penalty_divisor_days').attr('val', parseInt(_penalty_divisor_days)).html(parseInt(_penalty_divisor_days) + ' Days');
                    $('#grace_days').attr('val', parseInt(_grace_days)).html(parseInt(_grace_days) + ' Days');

                    if (is_editable_penalty) {
                        $('#is_editable_penalty').attr('val', 1).html('YES');
                    } else {
                        $('#is_editable_penalty').attr('val', 0).html('NO');
                    }
                    $('#penaltyModal').modal('hide');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function save_condition() {
        if (uid == 0) return;
        var values = $('#condition_form').getValues();
        values.product_id = uid;
        yo.loadData({
            _c: 'loan',
            _m: 'updateProductCondition',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    if (data && data.uid > 0) {
                        window.location.href = '<?php echo getUrl('loan', 'editProduct', array(), false, BACK_OFFICE_SITE_URL);?>' + '&uid=' + data.uid;
                        return;
                    }
                }
                alert(_o.MSG);
            }
        });
    }

    function add_size_rate() {
        if (uid == 0) return;
        $('#sizeRateModal input').val('');
        $("#sizeRateModal select").each(function () {
            $(this).find('option').first().prop("selected", 'selected');
        });
        $("#sizeRateModal select[name='interest_rate_period']").closest('.form-group').hide();

        $('input[name="is_full_interest"]').prop('checked', false);
        $('input[name="is_show_for_client"]').prop('checked', false);

        $('input[name="prepayment_interest"]').closest('.form-group').show();
        $('#sizeRateModal').modal('show');
    }

    $('select[name="interest_payment"]').change(function () {
        if ($(this).val() == 'single_repayment') {
            $('select[name="interest_rate_period"]').closest('.form-group').hide();
        } else {
            $('select[name="interest_rate_period"]').closest('.form-group').show();
        }
    });

    $('input[name="is_full_interest"]').click(function () {
        if ($(this).prop('checked')) {
            $('input[name="prepayment_interest"]').closest('.form-group').hide();
        } else {
            $('input[name="prepayment_interest"]').closest('.form-group').show();
        }
    });

    function save_size_rate() {
        if (!$("#size_rate_form").valid()) {
            return;
        }

        var values = $('#size_rate_form').getValues();
        values.product_id = uid;

        if (values.size_rate_id) {
            var _m = 'updateSizeRate';
        } else {
            var _m = 'insertSizeRate';
        }
        yo.loadData({
            _c: 'loan',
            _m: _m,
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    if (data && data.uid > 0) {
                        window.location.href = '<?php echo getBackOfficeUrl('loan', 'editSubProduct', array());?>' + '&uid=' + data.uid;
                        return;
                    }
                    getSizeRateList(uid);
                    $('#sizeRateModal').modal('hide');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function getSizeRateList(product_id) {
        if(product_id == 0) return;
        yo.dynamicTpl({
            tpl: "loan/size_rate.list",
            dynamic: {
                api: "loan",
                method: "getSizeRateList",
                param: {product_id: product_id}
            },
            callback: function (_tpl) {
                $(".size-info .content").html(_tpl);
            }
        });
    }

    function edit_size_rate(_e) {
        var _tr = $(_e).closest('tr');
        var _size_rate_id = _tr.attr('uid');
        var _currency = _tr.attr('currency');
        var _loan_size_min = _tr.attr('loan_size_min');
        var _loan_size_max = _tr.attr('loan_size_max');
        var _min_term_days = _tr.attr('min_term_days');
        var _max_term_days = _tr.attr('max_term_days');
        var _guarantee_type = _tr.attr('guarantee_type');
        var _mortgage_type = _tr.attr('mortgage_type');
        var _interest_payment = _tr.attr('interest_payment');
        var _interest_rate_period = _tr.attr('interest_rate_period');
        var _interest_rate = _tr.attr('interest_rate');
        var _interest_rate_type = _tr.attr('interest_rate_type');
        var _interest_rate_unit = _tr.attr('interest_rate_unit');
        var _interest_min_value = _tr.attr('interest_min_value');
        var _interest_rate_mortgage1 = _tr.attr('interest_rate_mortgage1');
        var _interest_rate_mortgage2 = _tr.attr('interest_rate_mortgage2');

        var _admin_fee = _tr.attr('admin_fee');
        var _admin_fee_type = _tr.attr('admin_fee_type');
        var _loan_fee = _tr.attr('loan_fee');
        var _loan_fee_type = _tr.attr('loan_fee_type');
        var _operation_fee = _tr.attr('operation_fee');
        var _operation_fee_mortgage1 = _tr.attr('operation_fee_mortgage1');
        var _operation_fee_mortgage2 = _tr.attr('operation_fee_mortgage2');
        var _operation_fee_type = _tr.attr('operation_fee_type');
        var _operation_fee_unit = _tr.attr('operation_fee_unit');
        var _operation_min_value = _tr.attr('operation_min_value');
        var _grace_days = _tr.attr('grace_days');
        var _is_full_interest = _tr.attr('is_full_interest');
        var _prepayment_interest = _tr.attr('prepayment_interest');
        var _prepayment_interest_type = _tr.attr('prepayment_interest_type');
        var _is_show_for_client=_tr.attr('is_show_for_client');
        var _service_fee = _tr.attr('service_fee');
        var _service_fee_type = _tr.attr('service_fee_type');

        $('#sizeRateModal').find('input[name="size_rate_id"]').val(_size_rate_id);
        $('#sizeRateModal select[name="currency"]').val(_currency);
        $('#sizeRateModal input[name="loan_size_min"]').val(_loan_size_min);
        $('#sizeRateModal input[name="loan_size_max"]').val(_loan_size_max);
        $('#sizeRateModal input[name="min_term_days"]').val(_min_term_days);
        $('#sizeRateModal input[name="max_term_days"]').val(_max_term_days);
        $('#sizeRateModal select[name="guarantee_type"]').val(_guarantee_type);
        $('#sizeRateModal select[name="mortgage_type"]').val(_mortgage_type);
        $('#sizeRateModal input[name="interest_rate"]').val(_interest_rate);
        $('#sizeRateModal select[name="interest_rate_unit"]').val(_interest_rate_unit);
        $('#sizeRateModal select[name="interest_rate_type"]').val(_interest_rate_type);
        $('#sizeRateModal input[name="interest_min_value"]').val(_interest_min_value);

        $('#sizeRateModal input[name="interest_rate_mortgage1"]').val(_interest_rate_mortgage1);
        $('#sizeRateModal input[name="interest_rate_mortgage2"]').val(_interest_rate_mortgage2);

        $('#sizeRateModal select[name="interest_payment"]').val(_interest_payment);
        if (_interest_rate_period) {
            $('#sizeRateModal select[name="interest_rate_period"]').val(_interest_rate_period).closest('.form-group').show();
        } else {
            $('#sizeRateModal select[name="interest_rate_period"]').closest('.form-group').hide();
            $("#sizeRateModal select[name='interest_rate_period']:first").prop("selected", 'selected');
        }
        $('#sizeRateModal input[name="admin_fee"]').val(_admin_fee);
        $('#sizeRateModal select[name="admin_fee_type"]').val(_admin_fee_type);
        $('#sizeRateModal input[name="loan_fee"]').val(_loan_fee);
        $('#sizeRateModal select[name="loan_fee_type"]').val(_loan_fee_type);
        $('#sizeRateModal input[name="operation_fee"]').val(_operation_fee);
        $('#sizeRateModal input[name="operation_fee_mortgage1"]').val(_operation_fee_mortgage1);
        $('#sizeRateModal input[name="operation_fee_mortgage2"]').val(_operation_fee_mortgage2);
        $('#sizeRateModal select[name="operation_fee_unit"]').val(_operation_fee_unit);
        $('#sizeRateModal select[name="operation_fee_type"]').val(_operation_fee_type);
        $('#sizeRateModal input[name="operation_min_value"]').val(_operation_min_value);
        $('#sizeRateModal input[name="grace_days"]').val(_grace_days);
        $('#sizeRateModal input[name="service_fee"]').val(_service_fee);
        $('#sizeRateModal select[name="service_fee_type"]').val(_service_fee_type);

        if(_is_show_for_client){
            $('input[name="is_show_for_client"]').prop('checked', true);
        }else{
            $('input[name="is_show_for_client"]').prop('checked', false);
        }

        if (_is_full_interest == 1) {
            $('input[name="is_full_interest"]').prop('checked', true);
            $('input[name="prepayment_interest"]').val('').closest('.form-group').hide();
            $('select[name="prepayment_interest_type"] option').first().prop("selected", 'selected');
        } else {
            $('input[name="is_full_interest"]').prop('checked', false);
            $('input[name="prepayment_interest"]').val(_prepayment_interest).closest('.form-group').show();
            $('select[name="prepayment_interest_type"]').val(_prepayment_interest_type);
        }

        $('#sizeRateModal').modal('show');
    }

    function remove_size_rate(_e) {
        var size_rate_id = $(_e).closest('tr').attr('uid');
        $.messager.confirm("<?php echo 'Remove'?>", "<?php echo 'Are you sure to remove?'?>", function (_r) {
            if (!_r) return;
            yo.loadData({
                _c: "loan",
                _m: "removeSizeRate",
                param: {size_rate_id: size_rate_id},
                callback: function (_o) {
                    if (_o.STS) {
                        var data = _o.DATA;
                        if (data && data.uid > 0) {
                            window.location.href = '<?php echo getBackOfficeUrl('loan', 'editSubProduct', array());?>' + '&uid=' + data.uid;
                            return;
                        }
                        $(_e).closest('tr').remove();
                    } else {
                        alert(_o.MSG);
                    }
                }
            });
        });
    }

    function edit_text(_name) {
        if (uid == 0) return;
        $('.' + _name).find('.fa-edit').hide();
        $('.' + _name).find('.fa-mail-reply').show();
        $('.' + _name).find('.fa-floppy-o').show();
        $('.' + _name).find('.content div').first().hide();
        $('.' + _name).find('#' + _name).show();
        ue(_name);
    }

    function cancel_text(_name) {
        $('.' + _name).find('.fa-edit').show();
        $('.' + _name).find('.fa-mail-reply').hide();
        $('.' + _name).find('.fa-floppy-o').hide();
        $('.' + _name).find('.content div').first().show();
        $('.' + _name).find('#' + _name).hide();
    }

    function save_text(_name) {
        var _val = ueArr[_name].getContent();
        yo.loadData({
            _c: "loan",
            _m: "updateSubDescription",
            param: {product_id: uid, name: _name, val: _val},
            callback: function (_o) {
                if (_o.STS) {
//                    var data = _o.DATA;
//                    if (data && data.uid > 0) {
//                        window.location.href = '<?php //echo getUrl('loan', 'editSubProduct', array(), false, BACK_OFFICE_SITE_URL);?>//' + '&uid=' + data.uid;
//                        return;
//                    }
                    $('.' + _name).find('.fa-edit').show();
                    $('.' + _name).find('.fa-mail-reply').hide();
                    $('.' + _name).find('.fa-floppy-o').hide();
                    $('.' + _name).find('.content div').first().html(_val).show();
                    $('.' + _name).find('#' + _name).hide();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    var ueArr = [];
    function ue(_name) {
        ueArr[_name] = UE.getEditor(_name, {
            toolbars: [[
                'source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|',
                'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                'indent', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',
                'link', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                'simpleupload', 'background', '|',
                'horizontal', 'date', 'time', 'spechars','inserttable',
            ]],
            initialFrameHeight: 300,
            enableAutoSave: false,
            autoHeightEnabled: false,
            lang: 'en'
        });
    }

    $('#info_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            product_name: {
                required: true
            },
            product_code: {
                required: true,
                checkNumAndStr: true
            }
        },
        messages: {
            product_name: {
                required: '<?php echo 'Required!'?>'
            },
            product_code: {
                required: '<?php echo 'Required!'?>',
                checkNumAndStr: '<?php echo 'It can only be Numbers or letters!'?>'
            }
        }
    });

    $('#penalty_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            penalty_on: {
                required: true
            },
            penalty_rate: {
                required: true
            },
            penalty_divisor_days: {
                required: true
            }
        },
        messages: {
            penalty_on: {
                required: '<?php echo 'Required!'?>'
            },
            penalty_rate: {
                required: '<?php echo 'Required!'?>'
            },
            penalty_divisor_days: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

    $('#size_rate_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            loan_size_min: {
                required: true
            },
            loan_size_max: {
                required: true
            },
            min_term_days: {
                required: true
            },
            max_term_days: {
                required: true
            },
            interest_rate: {
                required: true
            },
            interest_min_value: {
                required: true
            },
            admin_fee: {
                required: true
            },
            loan_fee: {
                required: true
            },
            operation_fee: {
                required: true
            },
            operation_min_value: {
                required: true
            },
            grace_days: {
                required: true
            },
            prepayment_interest: {
                checkRequired: true
            }
        },
        messages: {
            loan_size_min: {
                required: '<?php echo 'Required!'?>'
            },
            loan_size_max: {
                required: '<?php echo 'Required!'?>'
            },
            min_term_days: {
                required: '<?php echo 'Required!'?>'
            },
            max_term_days: {
                required: '<?php echo 'Required!'?>'
            },
            interest_rate: {
                required: '<?php echo 'Required!'?>'
            },
            interest_min_value: {
                required: '<?php echo 'Required!'?>'
            },
            admin_fee: {
                required: '<?php echo 'Required!'?>'
            },
            loan_fee: {
                required: '<?php echo 'Required!'?>'
            },
            operation_fee: {
                required: '<?php echo 'Required!'?>'
            },
            operation_min_value: {
                required: '<?php echo 'Required!'?>'
            },
            grace_days: {
                required: '<?php echo 'Required!'?>'
            },
            prepayment_interest: {
                checkRequired: '<?php echo 'Required!'?>'
            }
        }
    });

    jQuery.validator.addMethod("checkNumAndStr", function (value, element) {
        value = $.trim(value);
        if (!/^[-_A-Za-z0-9]+$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("checkRequired", function (value, element) {
        value = $.trim(value);
        if ($('input[name="is_full_interest"]').prop('checked')) {
            return true;
        } else {
            if (value) {
                return true;
            } else {
                return false;
            }
        }
    });

</script>
