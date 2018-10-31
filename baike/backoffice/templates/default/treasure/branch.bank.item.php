<div class="panel panel-default" style="color: #808080;margin-bottom: 20px">
    <div class="panel-heading">
        <h5><i class="fa fa-credit-card"></i> <?php echo $bank['bank_name']?></h5>
    </div>
    <div class="panel-body" style="color: #808080;">
        <div class="col-sm-4">
           <ul class="list-group no-border">
               <li class="list-group-item">
                   <span style="padding-left: 10px"><?php echo $bank['bank_account_name']?></span>
               </li>
               <li class="list-group-item">
                   <span style="padding-left: 10px"><?php echo $bank['bank_account_no']?></span>
               </li>
               <li class="list-group-item " style="padding: 0;padding-top: 5px;">
                   <button class="btn btn-default  btn-block"  onclick="showModal(<?php echo $bank['uid'] ?>,'<?php echo $bank['bank_name'] ?>','deposit_hq','Deposit(From HQ-CIV)','<?php echo $bank['currency'] ?>')">
                       <?php echo 'Deposit(From HQ-CIV)'?>
                   </button>
               </li>
               <li class="list-group-item " style="padding: 0">
                   <button class="btn btn-default  btn-block"  onclick="showModal(<?php echo $bank['uid'] ?>,'<?php echo $bank['bank_name'] ?>','withdraw_hq','Withdraw(To HQ-CIV)','<?php echo $bank['currency'] ?>')">
                       <?php echo 'Withdraw(To HQ-CIV)'?>
                   </button>
               </li>
           </ul>
        </div>
        <div class="col-sm-4">
            <ul class="list-group no-border">
                <li class="list-group-item">
                    <span style="font-size: 20px"><?php echo $bank['currency']?></span>
                </li>
                <li class="list-group-item">
                    <span style="font-size: 20px"><?php echo $bank['balance'][$bank['currency']]?></span>
                </li>
            </ul>
        </div>
        <div class="col-sm-4">
            <ul class="list-group no-border">
                <li class="list-group-item" style="padding: 0; padding-top: 5px;">
                    <a class="btn btn-default btn-block" href="<?php echo getUrl('treasure', 'showBankTransaction', array('bank_id'=>$bank['uid']), false, BACK_OFFICE_SITE_URL)?>">
                        <?php echo 'Transaction'?>
                    </a>
                </li>
                <li class="list-group-item " style="padding: 0;">
                    <button class="btn btn-default  btn-block"  onclick="showModal(<?php echo $bank['uid'] ?>,'<?php echo $bank['bank_name'] ?>','deposit_br','Deposit(From Branch CIV)','<?php echo $bank['currency'] ?>')">
                        <?php echo 'Deposit(From Branch)'?>
                    </button>
                </li>
                <li class="list-group-item " style="padding: 0;">
                    <button class="btn btn-default  btn-block"  onclick="showModal(<?php echo $bank['uid'] ?>,'<?php echo $bank['bank_name'] ?>','withdraw_br','Withdraw(To Branch CIV)','<?php echo $bank['currency'] ?>')">
                        <?php echo 'Withdraw(To Branch)'?>
                    </button>
                </li>
                <li class="list-group-item " style="padding: 0;">
                    <button class="btn btn-default  btn-block"  onclick="showModal(<?php echo $bank['uid'] ?>,'<?php echo $bank['bank_name'] ?>','adjust','Adjust Fee/Interest','<?php echo $bank['currency'] ?>')">
                        <?php echo 'Adjust Fee/Interest'?>
                    </button>
                </li>
            </ul>
        </div>
        <div class="col-sm-12">
            <?php if($bank['last']){?>
                <span>RECENTLY TRANSACTION: </span>
                <label style="padding-left: 20px"> <?php echo $bank['last']['debit']+$bank['last']['credit']?></label>
                <span style="padding-left: 20px"><?php echo $bank['last']['subject']?></span>
                <span style="font-size: 8px;font-style: italic"><?php echo $bank['last']['update_time']?></span>
            <?php }else{
                echo 'No Transaction';
            } ?>
        </div>


    </div>
</div>