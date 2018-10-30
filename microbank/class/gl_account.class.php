<?php

class gl_accountClass {

    /**
     * 获得收入类的gl_account
     * @param $incomingType
     * @param $businessType
     * @return mixed
     * @throws Exception
     */
    public static function getIncomingAccount($incomingType, $businessType) {

        $account_model = new gl_accountModel();
        //override by tim
        $leaf_account=$account_model->getRow(array(
            "obj_type"=>objGuidTypeEnum::SYSTEM,
            "obj_key"=>systemAccountCodeEnum::BIZ_REVENUE,
            "business_type"=>$businessType,
            "business_key"=>$incomingType,
            "is_leaf"=>1
        ));
        if(!$leaf_account){
            throw new Exception("Cannot found account of business type -  $businessType/$incomingType");
        }
        if (!$leaf_account->obj_guid) {
            $leaf_account->obj_guid = generateGuid($leaf_account->uid, objGuidTypeEnum::GL_ACCOUNT);
            $ret = $leaf_account->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for account failed - " . $ret->MSG);
            }
        }

        /*
        // 获得收入类型账户
        $parent_account = $account_model->getRow(array('account_code' => $incomingType));

        if (!$parent_account) {
            throw new Exception("Cannot found account of incoming type - " . $incomingType);
        }

        // 找收入类型账户下面具体的业务类型账户
        $leaf_account = $account_model->getRow(array(
            'account_code' => $businessType,
            'account_parent' => $parent_account->uid
        ));

        if (!$leaf_account) {
            throw new Exception("Cannot found account of business type -  $businessType/$incomingType");
        }
        // 如果业务类型账户没有obj_guid，创建
        if (!$leaf_account->obj_guid) {
            $leaf_account->obj_guid = generateGuid($leaf_account->uid, objGuidTypeEnum::GL_ACCOUNT);
            $ret = $leaf_account->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for account failed - " . $ret->MSG);
            }
        }
        */

        return $leaf_account;
    }

    /**
     * 获得支出类的gl_account
     * @param $outgoingType
     * @param $businessType
     * @return mixed
     * @throws Exception
     */
    public static function getOutgoingAccount($outgoingType, $businessType) {
        $account_model = new gl_accountModel();
        //override by tim
        $leaf_account=$account_model->getRow(array(
            "obj_type"=>objGuidTypeEnum::SYSTEM,
            "obj_key"=>systemAccountCodeEnum::BIZ_EXPENSE,
            "business_type"=>$businessType,
            "business_key"=>$outgoingType,
            "is_leaf"=>1
        ));
        if(!$leaf_account){
            throw new Exception("Cannot found account of business type -  $businessType/$outgoingType");
        }
        if (!$leaf_account->obj_guid) {
            $leaf_account->obj_guid = generateGuid($leaf_account->uid, objGuidTypeEnum::GL_ACCOUNT);
            $ret = $leaf_account->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for account failed - " . $ret->MSG);
            }
        }
        /*

        // 获得收入类型账户
        $parent_account = $account_model->getRow(array('account_code' => $outgoingType));
        if (!$parent_account) {
            throw new Exception("Cannot found account of outgoing type - " . $outgoingType);
        }

        // 找收入类型账户下面具体的业务类型账户
        $leaf_account = $account_model->getRow(array(
            'account_code' => $businessType,
            'account_parent' => $parent_account->uid
        ));
        if (!$leaf_account) {
            throw new Exception("Cannot found account of business type -  $businessType/$outgoingType");
        }

        // 如果业务类型账户没有obj_guid，创建
        if (!$leaf_account->obj_guid) {
            $leaf_account->obj_guid = generateGuid($leaf_account->uid, objGuidTypeEnum::GL_ACCOUNT);
            $ret = $leaf_account->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for account failed - " . $ret->MSG);
            }
        }
        */

        return $leaf_account;
    }

    /**
     * 获取系统账户
     * @param $systemAccountCode
     * @return mixed
     * @throws Exception
     */
    public static function getSystemAccount($systemAccountCode) {

        $account_model = new gl_accountModel();
        //override by tim
        $leaf_account=$account_model->getRow(array(
            "obj_type"=>objGuidTypeEnum::SYSTEM,
            "obj_key"=>$systemAccountCode,
        ));


        if(!$leaf_account){
            throw new Exception("Cannot found System-Account");
        }

        if (!$leaf_account->obj_guid) {
            $leaf_account->obj_guid = generateGuid($leaf_account->uid, objGuidTypeEnum::GL_ACCOUNT);
            $ret = $leaf_account->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for account failed - " . $ret->MSG);
            }
        }
        return $leaf_account;

        /*

        $account_info = $account_model->getRow(array('account_code' => $systemAccountCode));
        if (!$account_info) {
            throw new Exception("Cannot found system account - " . $systemAccountCode);
        }

        // 如果系统账户没有obj_guid
        if (!$account_info->obj_guid) {
            $account_info->obj_guid = generateGuid($account_info->uid, objGuidTypeEnum::GL_ACCOUNT);
            $ret = $account_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for account failed - " . $ret->MSG);
            }
        }


        return $account_info;
        */
    }

    /**
     * 获取动态账户的，用于产生客户存款之类的动态账户
     */
    public static function getDynamicAccount($obj_type){
        if(!$obj_type || $obj_type==objGuidTypeEnum::SYSTEM){
            throw new Exception("Invalid Account Type!");
        }
        $account_model = new gl_accountModel();
        $leaf_account=$account_model->getRow(array(
            "obj_type"=>$obj_type,
        ));
        if(!$leaf_account){
            throw new Exception("Cannot found account");
        }
        if (!$leaf_account->obj_guid) {
            $leaf_account->obj_guid = generateGuid($leaf_account->uid, objGuidTypeEnum::GL_ACCOUNT);
            $ret = $leaf_account->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for account failed - " . $ret->MSG);
            }
        }
        return $leaf_account;
    }
    /**
     * 获取用户自定义科目的上级类型
     */
    public static function getAccountOfUserDefineByPassbookType($category){
        $arr_extra=array(
            passbookTypeEnum::ASSET=>array(
                'book_code'=>'1-1000',
                'book_name'=>'Assets Extra',
                "parent_book_code"=>"1",
                "category"=>passbookTypeEnum::ASSET
            ),
            passbookTypeEnum::DEBT=>array(
                'book_code'=>'2-1000',
                'book_name'=>'Liabilities Extra',
                "parent_book_code"=>"2",
                "category"=>passbookTypeEnum::DEBT
            ),
            passbookTypeEnum::COMMON=>array(
                'book_code'=>'3-1000',
                'book_name'=>'Common Extra',
                "parent_book_code"=>"3",
                "category"=>passbookTypeEnum::COMMON
            ),
            passbookTypeEnum::EQUITY=>array(
                'book_code'=>'5-1000',
                'book_name'=>'EQUITY Extra',
                "parent_book_code"=>"5",
                "category"=>passbookTypeEnum::EQUITY
            ),
            passbookTypeEnum::PROFIT_INCOME=>array(
                'book_code'=>'6-001-1000',
                'book_name'=>'Income Extra',
                "parent_book_code"=>"6-001",
                "category"=>passbookTypeEnum::PROFIT_INCOME
            ),
            passbookTypeEnum::PROFIT_EXPENSE=>array(
                'book_code'=>'6-002-1000',
                'book_name'=>'Expense Extra',
                "parent_book_code"=>"6-002",
                "category"=>passbookTypeEnum::PROFIT_EXPENSE
            )
        );
        if(!$arr_extra[$category]){
            return new result(false,"Invalid Account Category:".$category.",bug from getAccountOfUserDefineByPassbookType");
        }
        $book_code=$arr_extra[$category]['book_code'];
        $account_model = new gl_accountModel();
        $item=$account_model->find(array("book_code"=>$book_code));
        if($item){
            return new result(true,"",$item);
        }
        $row=$account_model->newRow($arr_extra[$category]);
        $row->is_system=1;
        $ret=$row->insert();
        if($ret->STS){
            return new result(true,"",$row->toArray());
       }else{
            return $ret;
        }
    }

    /**
     * 获取手工编号的科目
     * @param $gl_code
     * @param $currency
     * @return bool|mixed
     * @throws Exception
     */
    public static function getManualAccount($gl_code,$currency){
        $account_model = new gl_accountModel();
        if($currency==currencyEnum::KHR){
            $fld="gl_code_khr";
        }elseif($currency==currencyEnum::USD){
            $fld="gl_code_usd";
        }else{
            throw new Exception("Not Support Currency -".$currency);
        }
        $leaf_account=$account_model->getRow(array(
            "obj_type"=>objGuidTypeEnum::SYSTEM,
            "is_system"=>0,
            $fld=>$gl_code,
        ));
        if(!$leaf_account){
            throw new Exception("Cannot found System-Account:".$gl_code);
        }

        if (!$leaf_account->obj_guid) {
            $leaf_account->obj_guid = generateGuid($leaf_account->uid, objGuidTypeEnum::GL_ACCOUNT);
            $ret = $leaf_account->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for account failed - " . $ret->MSG);
            }
        }
        return $leaf_account;

    }
}