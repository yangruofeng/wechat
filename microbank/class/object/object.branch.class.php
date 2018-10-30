<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/4
 * Time: 17:28
 */
class objectBranchClass extends objectBaseClass
{

    public $branch_id=null;
    public $branch_code = null;
    public $branch_name = null;

    private $passbook=null;

    public function __construct($branch_id)
    {
        $branch_id = intval($branch_id);
        $this->_initObject($branch_id);
    }

    protected function _initObject($branch_id)
    {
        $m = new site_branchModel();
        $branch_info = $m->getRow($branch_id);
        if( !$branch_info ){
            throw new Exception('Branch not exist:'.$branch_id,errorCodesEnum::NO_DATA);
        }
        $this->object_id = $branch_info['obj_guid'];
        $this->object_type = objGuidTypeEnum::SITE_BRANCH;
        $this->object_info = $branch_info;

        $this->branch_id = $branch_info->uid;
        $this->branch_code = $branch_info->branch_code;
        $this->branch_name = $branch_info->branch_name;

    }

    public function checkValid()
    {
        // todo 验证
        return new result(true);
    }

    public function getCredit()
    {
        return round($this->object_info['credit']);
    }

    public function getPassbook()
    {
        if( !$this->passbook ){
            $this->passbook = passbookClass::getBranchPassbook($this->branch_id);
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