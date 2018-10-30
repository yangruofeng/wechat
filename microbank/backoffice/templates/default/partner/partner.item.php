<div class="panel panel-default" style="color: #808080;margin-bottom: 20px">
    <div class="panel-heading">
        <h5><i class="fa fa-credit-card"></i> <?php echo $bank['partner_name']?></h5>
    </div>
    <div class="panel-body" style="color: #808080;">
        <div class="col-sm-4">
            <ul class="list-group no-border">
                <li class="list-group-item">
                    <span style="font-size: 20px"><?php echo currencyEnum::USD?></span>
                </li>
                <li class="list-group-item">
                    <em><?php if($bank['book_account'][currencyEnum::USD]['balance']>=0){ echo 'Payable';}else{ echo 'Receivable';}?></em>
                    <span style="font-size: 20px"><?php echo abs($bank['book_account'][currencyEnum::USD]['balance'])?></span>
                </li>
                <li class="list-group-item">
                    <em>outstanding</em> <span style="font-size: 20px"><?php echo $bank['book_account'][currencyEnum::USD]['outstanding']?></span>
                </li>
                <li class="list-group-item">
                    <a class="btn btn-link btn-sm" href="<?php echo getUrl("common","passbookAccountFlowPage",array('obj_uid'=>$bank['uid'],'obj_type'=>objGuidTypeEnum::PARTNER,'currency'=>currencyEnum::USD,'title'=>$bank['partner_name']),false,BACK_OFFICE_SITE_URL)?>">
                       Flow
                   </a>
                </li>

            </ul>
        </div>
        <div class="col-sm-4">
            <ul class="list-group no-border">
                <li class="list-group-item">
                    <span style="font-size: 20px"><?php echo currencyEnum::KHR?></span>
                </li>
                <li class="list-group-item">
                    <em><?php if($bank['book_account'][currencyEnum::KHR]['balance']>=0){ echo 'Payable';}else{ echo 'Receivable';}?></em>
                    <span style="font-size: 20px"><?php echo abs($bank['book_account'][currencyEnum::KHR]['balance'])?></span>
                </li>
                <li class="list-group-item">
                    <em>balance</em> <span style="font-size: 20px"><?php echo $bank['book_account'][currencyEnum::KHR]['outstanding']?></span>
                </li>
                <li class="list-group-item">
                    <a class="btn btn-link btn-sm" href="<?php echo getUrl("common","passbookAccountFlowPage",array('obj_uid'=>$bank['uid'],'obj_type'=>objGuidTypeEnum::PARTNER,'currency'=>currencyEnum::KHR,'title'=>$bank['partner_name']),false,BACK_OFFICE_SITE_URL)?>">
                        Flow
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-sm-4">
            <ul class="list-group no-border">
                <li class="list-group-item" style="padding: 0">
                    <button class="btn btn-default  btn-block"  onclick="showModal(<?php echo $bank['uid'] ?>,'<?php echo $bank['partner_name'] ?>','deposit_hq','Deposit(From HQ CIV)')">
                        <?php echo 'Deposit(From HQ-CIV)'?>
                    </button>
                </li>
                <li class="list-group-item" style="padding: 0">
                    <button class="btn btn-default  btn-block"  onclick="showModal(<?php echo $bank['uid'] ?>,'<?php echo $bank['partner_name'] ?>','withdraw_hq','Withdraw(To HQ CIV)')">
                        <?php echo 'Withdraw(To HQ-CIV)'?>
                    </button>
                </li>
                <li class="list-group-item" style="padding: 0">
                    <a class="btn btn-default  btn-block" href="<?php echo getUrl("partner","apiLogPage",array("partner_id"=>$bank['uid']),false,BACK_OFFICE_SITE_URL)?>">
                        <?php echo 'API LOG'?>
                    </a>
                </li>
            </ul>
        </div>



    </div>
</div>