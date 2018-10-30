<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/4
 * Time: 17:41
 */
class objectSysBankClass extends objectBaseClass
{

    public $bank_id;
    public $bank_code;
    public $bank_name;
    public $currency;
    public $bank_account_no;
    public $bank_account_name;
    public $branch_id;

    public $passbook;




    public function __construct($bank_id)
    {
        $bank_id = intval($bank_id);
        $this->_initObject($bank_id);
    }

    protected function _initObject($bank_id)
    {
        $m = new site_bankModel();
        $bank_info = $m->getRow($bank_id);
        if( !$bank_info ){
            throw new Exception('Bank not exist:'.$bank_id,errorCodesEnum::NO_DATA);
        }
        $this->object_id = $bank_info->obj_guid;
        $this->object_type = objGuidTypeEnum::BANK_ACCOUNT;
        $this->object_info = $bank_info;

        $this->bank_id = $bank_info->uid;
        $this->bank_code = $bank_info->bank_code;
        $this->bank_name = $bank_info->bank_name;
        $this->currency = $bank_info->currency;
        $this->bank_account_no = $bank_info->bank_account_no;
        $this->bank_account_name = $bank_info->bank_account_name;
        $this->branch_id = $bank_info->branch_id;

    }

    public function checkValid()
    {
        return new result(true);
    }

    public function getPassbook()
    {
        if(!$this->passbook){
            $this->passbook=passbookClass::getBankAccountPassbook($this->bank_id);
            return $this->passbook;
        }else{
            return $this->passbook;
        }

    }

    public function getPassbookCurrencyBalance()
    {
        $passbook = $this->getPassbook();
        return $passbook->getAccountBalance();
    }

    public function getPassbookCurrencyAccountDetail()
    {
        $passbook = $this->getPassbook();
        return $passbook->getAccountAllCurrencyDetail();
    }
}