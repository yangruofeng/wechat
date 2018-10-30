<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/4
 * Time: 17:50
 */
class objectPartnerClass extends objectBaseClass
{
    public $partner_id;
    public $partner_code;
    public $partner_name;

    private $passbook=null;


    public function __construct($partnerId)
    {
        $this->_initObject($partnerId);
    }

    protected function _initObject($partnerId)
    {
        $m = new partnerModel();
        $info = $m->getRow($partnerId);
        if( !$info ){
            throw new Exception('Bank not exist.');
        }
        $this->object_id = $info->obj_guid;
        $this->object_type = objGuidTypeEnum::PARTNER;
        $this->object_info = $info;

        $this->partner_id = $info->uid;
        $this->partner_code = $info->partner_code;
        $this->partner_name = $info->partner_name;

    }

    public function checkValid()
    {
        if( $this->object_info['is_active'] != 1 ){
            return new result(false,'Invalid partner.',null,errorCodesEnum::INVALID_PARTNER);
        }
        return new result(true);
    }

    public function getPassbook()
    {
        if( !$this->passbook ){
            $this->passbook = passbookClass::getPartnerPassbook($this->partner_id);
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