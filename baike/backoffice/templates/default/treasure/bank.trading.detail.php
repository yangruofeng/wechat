<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>


<div class="page" style="width: 700px">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Bank-Transaction-Detail</h3>
            <ul class="tab-base">
                <?php if($output['bank']['branch_id']>0){?>
                    <li><a href="<?php echo getUrl("treasure","branchList",array(),false,BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                    <li><a href="<?php echo getUrl("treasure","branchIndex",array('branch_id'=>$output['bank']['branch_id']),false,BACK_OFFICE_SITE_URL)?>"><span>Branch</span></a></li>
                    <li><a href="<?php echo getUrl("treasure","showBankTransaction",array("bank_id"=>$output['bank']['uid']),false,BACK_OFFICE_SITE_URL)?>"><span>Bank-Transaction</span></a></li>
                <?php }else{?>
                    <li><a href="<?php echo getUrl("financial","hqBank",array(),false,BACK_OFFICE_SITE_URL)?>"><span>Public-Bank</span></a></li>
                    <li><a href="<?php echo getUrl("treasure","showBankTransaction",array("bank_id"=>$output['bank']['uid']),false,BACK_OFFICE_SITE_URL)?>"><span>Bank-Transaction</span></a></li>
                <?php }?>
                <li><a class="current"><span>Transaction - <?php echo $output['bank']['bank_name']?></span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php foreach ($output['trading_flow'] as $value){ ?>
            <div class="register-div">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Trading Detail</h5>
                    </div>
                    <div class="content" style="padding-right: 50px">
                        <form class="form-horizontal" method="post">
                            <?php if($value['obj_type'] != passbookObjTypeEnum::GL_ACCOUNT){ ?>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Partner Name'?></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="" value="<?php echo $value['partner_name']?>" readonly>
                                    </div>
                                </div>
                            <?php }?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Trading Type'?></label>
                                <div class="col-sm-9">
                                    <div class="input-group" style="width: 100%">
                                        <input type="text" class="form-control"  placeholder="" value="<?php echo ucwords(str_replace('_', ' ', $output['trading_info']['trading_type'])) ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Trading Direction' ?></label>
                                <div class="col-sm-9">
                                    <div class="input-group" style="width: 100%">
                                        <input type="text" class="form-control" name="apply_amount" value="<?php echo $value['credit']>0 ? "Cash in": "Cash out"; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Trading Amount' ?></label>
                                <div class="col-sm-9">
                                    <div class="input-group" style="width: 100%">
                                        <input type="text" class="form-control" name="apply_amount" value="<?php echo $value['credit']>0 ? ncPriceFormat($value['credit']) : ncPriceFormat($value['debit']); ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Trading Time' ?></label>
                                <div class="col-sm-9">
                                    <div class="input-group" style="width: 100%">
                                        <input type="text" class="form-control" placeholder="" value="<?php echo $output['trading_info']['update_time'] ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }?>

    </div>
    <div class="form-group button">
        <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 310px;margin-top: 30px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
    </div>
</div>




