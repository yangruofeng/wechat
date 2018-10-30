<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/14
 * Time: 11:46
 */
class system_settingControl extends bank_apiControl
{


    public function configInitOp()
    {


        set_time_limit(120);

        // 用户定义的
        $define = (new userDefineEnum())->toArray();
        foreach( $define as $k=>$v ){
            $define[$k] = "'$v'";
        }
        $reader = new ormReader();
        $sql = "select * from core_definition where category in (".join(',',$define).") ";
        $rows = $reader->getRows($sql);
        $user_define = array();
        if( count($rows) > 0 ){
            foreach( $rows as $item ){
                $user_define[$item['category']][] = $item;
            }
        }

        $system_define = array();

        $system_define['phone_country_code'] = array(
            '86','855','66','84'
        );

        foreach( (new interestPaymentEnum())->toArray() as $v ){
            $system_define['repayment_type'][] = $v;
        }

        foreach( (new interestRatePeriodEnum())->toArray() as $v ){
            $system_define['repayment_period'][] = $v;
        }

        foreach( (new loanPeriodUnitEnum())->toArray() as $v ){
            $system_define['loan_time_unit'][] = $v;
        }

        $system_define['currency'] = (new currencyEnum())->toArray();

        foreach( (new workTypeEnum())->toArray() as $v ){
            $system_define['work_type'][] = $v;
        }


        $system_define['set_trading_password_way'] = global_settingClass::memberSetTradingPasswordWay();

        $system_define['member_app_closed_state'] = global_settingClass::getMemberAppClosedState();

        $system_define['credit_officer_app_closed_state'] = global_settingClass::getCreditOfficerAppClosedState();

        $system_define['asset_cert_type'] = array(
            assetsCertTypeEnum::SOFT => 'Soft',
            assetsCertTypeEnum::HARD => 'Hard'
        );

        $system_define['business_place'] = global_settingClass::getBusinessPlaceList();

        // 性别
        $gender = (new memberGenderEnum())->toArray();
        $system_define['gender'] = array_values($gender);

        $system_define['map_is_can_select'] = global_settingClass::isMapCanSelectLocation();

        return new result(true,'success',array(
            'user_define' => $user_define,
            'system_define' => $system_define
        ));

    }



    /**
     *  系统定义的列表
     * @return result
     */
    public function defineListOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $type = $params['type'];
        switch( $type )
        {
            case 1:  // 性别
                $category = userDefineEnum::GENDER;
                break;
            case 2:  // 职业
                $category = userDefineEnum::OCCUPATION;
                break;
            case 3:  // 家庭关系
                $category = userDefineEnum::FAMILY_RELATIONSHIP;
                break;
            case 4:  // 贷款用途
                $category = userDefineEnum::LOAN_USE;
                break;
            default:
                $category = '';
        }
        $reader = new ormReader();
        $sql = "select * from core_definition where category='$category' ";
        $rows = $reader->getRows($sql);
        return new result(true,'success',$rows);
    }

    /** 国家编码
     * @return result
     */
    public function countryCodeOp()
    {
        return new result(true,'success',array(
            '855','86','84','66'
        ));
    }


    public function getCompanyInfoOp()
    {
        $re = global_settingClass::getCompanyInfo();
        return new result(true,'success',$re);
    }

    public function getCompanyHotlineOp()
    {
        $re = global_settingClass::getCompanyHotline();
        return new result(true,'success',$re);
    }


    public function getCommonHelpListOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $type = $params['type'];
        $page_num = $params['page_num']?:1;
        $page_size = $params['page_size']?:100000;
        $re = global_settingClass::getSystemHelpList($type,$page_num,$page_size);
        return new result(true,'success',$re);
    }


    public function getCompanyGlobalReceiveBankAccountOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $currency = $params['currency'];
        $re = global_settingClass::getCompanyGlobalReceiveBankAccount($currency);
        return new result(true,'success',$re);
    }

    public function getAddressListOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $pid = intval($params['pid']);
        $list = global_settingClass::getChildrenAddressList($pid);
        return new result(true,'success',$list);
    }


    public function currencyExchangeRateOp()
    {
        $list = global_settingClass::currencyExchangeRate();
        return new result(true,'success',$list);
    }


    public function getBankListOp()
    {

        $r = new ormReader();
        $sql = "select * from common_bank_lists order by bank_code asc ";
        $list = $r->getRows($sql);
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    public function getIndustryListOp()
    {
        $r = new ormReader();
        $sql = "select * from common_industry where state=1 ";
        $list = $r->getRows($sql);
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    /** 服务器时间
     * @return result
     */
    public function getServerTimeOp()
    {
        $time = time();
        return new result(true,'success',array(
            'time' => date('Y-m-d H:i:s',$time),
            'timestamp' => $time
        ));
    }


    public function getAssetPageDataOp()
    {
        $data = (new member_assetsClass())->_initAPPCertPage();
        return new result(true,'success',$data);
    }

    public function getAssetPageDataByTypeOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $type = $params['asset_type'];
        $data = (new member_assetsClass())->_initAPPAssetCertPageByType($type);
        return new result(true,'success',array(
            'page_data' => $data
        ));
    }


}