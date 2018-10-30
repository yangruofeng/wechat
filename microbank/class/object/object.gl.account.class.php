<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/4/13
 * Time: 11:09
 */

class objectGlAccountClass extends objectBaseClass {

    public $gl_account_id;
    public $gl_account_code;
    public $gl_account_name;

    private $passbook = null;

    public function __construct($accountCode)
    {
        if( !$accountCode ){
            throw new Exception('Empty param.',errorCodesEnum::INVALID_PARAM);
        }
        $this->_initObject($accountCode);
    }

    protected function _initObject($accountCode)
    {

        $account_info = gl_accountClass::getSystemAccount($accountCode);
        if( !$account_info ){
            throw new Exception('GL account not exist:'.$accountCode,errorCodesEnum::NO_DATA);
        }
        $this->object_id = $account_info['obj_guid'];
        $this->object_type = objGuidTypeEnum::GL_ACCOUNT;
        $this->object_info = $account_info;

        $this->gl_account_id = $account_info->uid;
        $this->gl_account_code = $account_info->obj_key;
        $this->gl_account_name = $account_info->book_name;

    }

    function checkValid()
    {
        return new result(true);
    }

    public function getPassbook()
    {
        if( !$this->passbook ){
            $this->passbook = passbookClass::getSystemPassbook($this->gl_account_code);
        }
        return $this->passbook;
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