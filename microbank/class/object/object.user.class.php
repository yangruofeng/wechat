<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/14
 * Time: 10:47
 */
class objectUserClass extends objectBaseClass
{

    public $user_id;
    public $user_name;
    public $user_code;
    public $branch_id=0;
    public $branch_name=''; //应该要添加一个objectBranch对象的
    public $branchObj=null;
    public $department_id=0;
    public $position;
    public $trading_password;

    private $passbook=null;



    public function __construct($user_id)
    {
        $user_id = intval($user_id);
        $this->_initObject($user_id);
    }

    protected function _initObject($user_id)
    {
        $m = new um_userModel();
        $user = $m->getRow($user_id);
        if( !$user ){
            throw new Exception('User not found:'.$user_id,errorCodesEnum::NO_DATA);
        }

        $this->object_id = $user->obj_guid;
        $this->object_type = objGuidTypeEnum::UM_USER;
        $this->object_info = $user;
        $this->user_id = $user->uid;
        $this->user_name = $user->user_name;
        $this->user_code = $user->user_code;
        $this->position = $user->user_position;
        $this->trading_password = $user->trading_password;

        $m_depart = new site_departModel();
        $depart = $m_depart->getRow($user->depart_id);
        $this->department_id = $depart?$depart->uid:0;
        if($depart){
            $m_branch=new site_branchModel();
            $branch=$m_branch->getRow($depart->branch_id);
            if($branch){
                $this->branch_id=$branch->uid;
                $this->branch_name=$branch->branch_name;
                $this->branchObj = new objectBranchClass($branch['uid']);
            }
        }


    }

    /** 检查是否合法
     * @return result
     */
    public function checkValid()
    {
        $user_status = $this->object_info['user_status'];
        if( $user_status != 1 ){
            return new result(false,'User locked',null,errorCodesEnum::USER_LOCKED);
        }
        return new result(true);
    }

    /** 检查交易密码
     * @param $password
     * @return result
     */
    public function checkTradingPassword($password)
    {
        if( !$this->object_info['trading_password']){
            return new result(false,'User not set trading password',null,errorCodesEnum::NOT_SET_TRADING_PASSWORD);
        }

        if( $this->object_info['trading_password'] != md5($password) ){
            return new result(false,'User password error',null,errorCodesEnum::PASSWORD_ERROR);
        }

        return new result(true);

    }


    public function getCredit()
    {
        return round($this->object_info['credit']);
    }

    /** user 的账本
     * @return passbookClass
     */
    public function getUserPassbook()
    {
        if( !$this->passbook ){
            $this->passbook = passbookClass::getUserPassbook($this->user_id);
        }
        return $this->passbook;

    }

    /** user 账本的余额
     * @return array
     */
    public function getPassbookBalance()
    {
        $passbook = $this->getUserPassbook();
        $cny_balance = $passbook->getAccountBalance();
        return $cny_balance;
    }

    public function getAccountAllCurrencyDetail()
    {
        $passbook = $this->getUserPassbook();
        $cny_balance = $passbook->getAccountAllCurrencyDetail();
        return $cny_balance;
    }


}