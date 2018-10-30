<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/27
 * Time: 15:45
 */
class global_settingClass
{
    public function __construct()
    {
    }


    /** 重置数据库
     * @return bool
     */
    public static function resetSystemData()
    {
        return false;//先删除了，太危险了

    }

    public static function isMapCanSelectLocation()
    {
        return 0;
    }

    // 公共属性太危险了，还是用方法操作保护
    protected static $arr_currency_rate=array(); //modify by tim, use redis in the future.


    public static function getCurrencyRateBetween($from_ccy, $to_ccy)
    {
        if ($from_ccy == $to_ccy) return 1;
        if(self::$arr_currency_rate[$from_ccy."@".$to_ccy]){
            return self::$arr_currency_rate[$from_ccy."@".$to_ccy];
        }else{
            $m = new common_exchange_rateModel();
            $rate = $m->getRateBetween($from_ccy, $to_ccy);
            if ($rate <= 0) {
                throw new Exception('Not set currency exchange rate:' . $from_ccy . '-' . $to_ccy, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
            }
            self::$arr_currency_rate[$from_ccy."@".$to_ccy]=$rate;
            return $rate;
        }
    }


    public static function resetCurrencyExchangeRate($from_ccy,$to_ccy,$exchange_rate)
    {
        self::$arr_currency_rate[$from_ccy."@".$to_ccy] = $exchange_rate;
    }

    public static function unsetCurrencyExchangeRate($from_ccy,$to_ccy)
    {
        unset(self::$arr_currency_rate[$from_ccy."@".$to_ccy]);
    }


    public static function getAllDictionary()
    {
        $m = new core_dictionaryModel();
        $list = $m->getAll();
        $return = array();
        foreach ($list as $v) {
            $return[$v['dict_key']] = $v['dict_value'];
        }
        return $return;
    }

    /** 信用激活是否检查指纹
     * @return int
     */
    public static function isCheckCreditFingerprintCert()
    {
        $is = 1;
        $m = new core_dictionaryModel();
        $set = $m->getDictionary('close_credit_fingerprint_cert');
        if ($set && $set['dict_value'] == 1) {
            $is = 0;
        }
        return $is;
    }


    /** 信用激活是否检查授权合同
     * @return int
     */
    public static function isCheckCreditAuthorizedContract()
    {
        $is = 1;
        $m = new core_dictionaryModel();
        $set = $m->getDictionary('close_credit_authorized_contract');
        if ($set && $set['dict_value'] == 1) {
            $is = 0;
        }
        return $is;
    }

    /** 获取常用的setting
     * @return array|mixed
     */
    public static function getCommonSetting()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('global_settings');
        if ($row && $row['dict_value']) {
            $re = @json_decode($row['dict_value'], true);
        }
        return $re;
    }

    /** 获取所有功能状态
     * @return array|mixed
     */
    public static function getFunctionSwitch()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('function_switch');
        if ($row && $row['dict_value']) {
            $re = @json_decode($row['dict_value'], true);
        }
        return $re;
    }

    /*
     * 获取所有业务开关
     */
    public static function getBusinessSwitch()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('business_switch');
        if ($row && $row['dict_value']) {
            $re = @json_decode($row['dict_value'], true);
        }
        return $re;
    }

    /** 是否允许自动从ACE扣款
     * @return int
     */
    public static function isAllowAutoDeductFromACE()
    {
        if (self::isACEBusinessClosed()) {
            return 0;
        }
        $re = self::getBusinessSwitch();
        if (isset($re['is_auto_deduct_from_ace']) && $re['is_auto_deduct_from_ace']) {
            return 1;
        }
        return 0;
    }


    /** 是否需要锁定账户余额
     * @return int
     */
    public static function isNeedToLockMemberBalance()
    {
        $re = self::getBusinessSwitch();
        if (isset($re['is_need_lock_client_balance']) && $re['is_need_lock_client_balance']) {
            return 1;
        }
        return 0;
    }


    /** 是否开启重置密码功能
     * @return int|mixed
     */
    public static function isCanResetPassword()
    {
        $is = 1;
        $re = self::getFunctionSwitch();
        if ($re['close_reset_password']) {
            $is = 0;
        }
        return $is;
    }


    /** 是否开启信用贷提现
     * @return int|mixed
     */
    public static function isCanCreditLoanWithdraw()
    {
        $is = 1;
        $re = self::getFunctionSwitch();
        if ($re['close_loan']) {
            $is = 0;
        }
        return $is;
    }

    /** 是否开启注册就送信用
     * @return int
     */
    public static function isAllowRegisterToSendCredit()
    {
        $is = 0;
        $re = self::getBusinessSwitch();
        if ($re['free_credit_for_register']) {
            $is = 1;
        }
        return $is;
    }

    /*
     * co提交的资产资料是否需要operator审核
     */
    public static function isAllowOperatorApproveAssetsByCO()
    {
        $is = 0;
        $re = self::getBusinessSwitch();
        if ($re['approve_co_upload_assets']) {
            $is = 1;
        }
        return $is;
    }

    /**
     * 提前还款申请是否需要审批
     */
    public static function isPrepaymentRequestNeedApproved()
    {
        $is = 0;
        $re = self::getBusinessSwitch();
        if (isset($re['require_approve_prepayment']) && $re['require_approve_prepayment']) {
            $is = 1;
        }
        return $is;
    }


    /*还没使用，先禁止，没明白使用场景， add by tim
    public static function isAllowPassbookTradingByBank()
    {
        $is = 0;
        $re = self::getFunctionSwitch();
        if( $re['open_passbook_trading_by_bank'] ){
            $is = 1;
        }
        return $is;
    }
    */


    /** 获取公司信息
     * @return array|mixed
     */
    public static function getCompanyInfo()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('company_config');
        if ($row && $row['dict_value']) {
            $re = @json_decode($row['dict_value'], true);
            $re['company_icon'] = getCompanyIconUrl($re['company_icon']);
            $re['address_detail'] = $re['address_region'] . ',' . $re['address_detail'];
            $re['branch_list'] = self::getCompanyBranchList();
        }
        return $re;
    }

    /** 获取公司所有分部
     * @return array|ormCollection
     */
    public static function getCompanyBranchList()
    {
        $list = array();
        $reader = new ormReader();
        $sql = "select b.*,u.user_code,u.user_name manager_name,u.mobile_phone manager_phone,u.email manager_email from site_branch b left join um_user u on b.manager=u.uid
        where b.status='1' ";
        $rows = $reader->getRows($sql);
        if (count($rows) > 0) {
            $list = $rows;
        }
        return $list;
    }

    /** 获取公司热线电话
     * @return array
     */
    public static function getCompanyHotline()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('company_config');
        if ($row && $row['dict_value']) {
            $arr = @json_decode($row['dict_value'], true);
            $re = $arr['hotline'];
        }
        return $re;
    }


    public static function getCertSampleImage()
    {
        Language::read('certification');

        $url = PROJECT_RESOURCE_SITE_URL . '/certificate_sample';

        return array(

            certificationTypeEnum::ID => array(
                certImageKeyEnum::ID_FRONT => array(
                    'des' => L('cert_sample_des_id_front'),
                    'image' => $url . '/id/front.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::ID_BACK => array(
                    'des' => L('cert_sample_des_id_back'),
                    'image' => $url . '/id/back.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::ID_HANDHELD => array(
                    'des' => L('cert_sample_des_id_handheld'),
                    'image' => $url . '/id/handheld.png',
                    'is_required' => 0
                ),
            ),

            certificationTypeEnum::FAIMILYBOOK => array(
                certImageKeyEnum::FAMILY_BOOK_FRONT => array(
                    'des' => L('cert_sample_des_family_book_front'),
                    'image' => $url . '/family_book/front.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::FAMILY_BOOK_BACK => array(
                    'des' => L('cert_sample_des_family_book_back'),
                    'image' => $url . '/family_book/back.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::FAMILY_BOOK_HOUSEHOLD => array(
                    'des' => L('cert_sample_des_family_householder'),
                    'image' => $url . '/family_book/household.png',
                    'is_required' => 1
                ),
            ),

            certificationTypeEnum::PASSPORT => array(
                certImageKeyEnum::PASSPORT_FRONT => array(
                    'des' => L('cert_sample_des_passport_front'),
                    'image' => $url . '/passport/passport_front.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::PASSPORT_IN => array(
                    'des' => L('cert_sample_des_passport_in'),
                    'image' => $url . '/passport/passport_in.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::PASSPORT_VISA => array(
                    'des' => L('cert_sample_des_passport_visa'),
                    'image' => $url . '/passport/passport_visa.jpg',
                    'is_required' => 0
                ),
            ),

            certificationTypeEnum::RESIDENT_BOOK => array(
                certImageKeyEnum::RESIDENT_BOOK_FRONT => array(
                    'des' => L('cert_sample_des_resident_book_front'),
                    'image' => $url . '/resident_book/front.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::RESIDENT_BOOK_BACK => array(
                    'des' => L('cert_sample_des_resident_book_back'),
                    'image' => $url . '/resident_book/back.png',
                    'is_required' => 1
                )
            ),

            certificationTypeEnum::WORK_CERTIFICATION => null,

            certificationTypeEnum::BIRTH_CERTIFICATE => array(
                certImageKeyEnum::BIRTH_CARD => array(
                    'des' => L('cert_sample_des_birth_card'),
                    'image' => $url . '/birthday/birthday_card.jpg',
                    'is_required' => 1
                )
            ),

            certificationTypeEnum::MOTORBIKE => array(
                certImageKeyEnum::MOTORBIKE_CERT_FRONT => array(
                    'des' => L('cert_sample_des_motorbike_certificate_front'),
                    'image' => $url . '/motorbike/certificate_front.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::MOTORBIKE_CERT_BACK => array(
                    'des' => L('cert_sample_des_motorbike_certificate_back'),
                    'image' => $url . '/motorbike/certificate_back.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::MOTORBIKE_PHOTO => array(
                    'des' => L('cert_sample_des_motorbike_photo'),
                    'image' => $url . '/motorbike/motorbike.jpg',
                    'is_required' => 0
                ),
            ),

            certificationTypeEnum::CAR => array(
                certImageKeyEnum::CAR_CERT_FRONT => array(
                    'des' => L('cert_sample_des_car_certificate_front'),
                    'image' => $url . '/car/certificate_front.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::CAR_CERT_BACK => array(
                    'des' => L('cert_sample_des_car_certificate_back'),
                    'image' => $url . '/car/certificate_back.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::CAR_FRONT => array(
                    'des' => L('cert_sample_des_car_front'),
                    'image' => $url . '/car/car_photo.png',
                    'is_required' => 0
                ),
                certImageKeyEnum::CAR_BACK => array(
                    'des' => L('cert_sample_des_car_back'),
                    'image' => $url . '/car/car_photo.png',
                    'is_required' => 0
                ),
            ),

            certificationTypeEnum::HOUSE => array(
                certImageKeyEnum::HOUSE_PROPERTY_CARD => array(
                    'des' => L('cert_sample_des_house_property_card'),
                    'image' => $url . '/house/property_card.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::HOUSE_FRONT => array(
                    'des' => L('cert_sample_des_house_front'),
                    'image' => $url . '/house/house_front.png',
                    'is_required' => 0
                ),
                certImageKeyEnum::HOUSE_FRONT_ROAD => array(
                    'des' => L('cert_sample_des_house_front_road'),
                    'image' => $url . '/house/house_front_road.png',
                    'is_required' => 0
                ),
                certImageKeyEnum::HOUSE_SIDE_FACE => array(
                    'des' => L('cert_sample_des_house_side_face'),
                    'image' => $url . '/house/house_side_face.png',
                    'is_required' => 0
                ),
                certImageKeyEnum::HOUSE_INSIDE => array(
                    'des' => 'House Inside',
                    'image' => $url . '/house/house_inside.png',
                    'is_required' => 0
                ),
                certImageKeyEnum::HOUSE_RELATIONSHIPS_CERTIFY => array(
                    'des' => 'House Relation Certify',
                    'image' => $url . '/house/house_relationship.png',
                    'is_required' => 0
                ),
            ),

            certificationTypeEnum::LAND => array(
                certImageKeyEnum::LAND_PROPERTY_CARD => array(
                    'des' => L('cert_sample_des_land_property_card'),
                    'image' => $url . '/land/property_card.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::LAND_TRADING_RECORD => array(
                    'des' => L('cert_sample_des_land_trading_record'),
                    'image' => $url . '/land/trading_record.png',
                    'is_required' => 0
                ),

            ),

            certificationTypeEnum::STORE => array(
                certImageKeyEnum::STORE_BUSINESS_LICENSE => array(
                    'des' => L('cert_sample_des_store_licence'),
                    'image' => $url . '/store/business_license.png',
                    'key' => certImageKeyEnum::STORE_BUSINESS_LICENSE,
                    'is_required' => 1
                ),
                certImageKeyEnum::STORE_POSITION => array(
                    'des' => L('cert_sample_des_store_position'),
                    'image' => $url . '/store/position.png',
                    'key' => certImageKeyEnum::STORE_POSITION,
                    'is_required' => 0
                ),
                certImageKeyEnum::STORE_STORE_PHOTO => array(
                    'des' => L('cert_sample_des_store_photo'),
                    'image' => $url . '/store/store_photo.png',
                    'key' => certImageKeyEnum::STORE_STORE_PHOTO,
                    'is_required' => 0
                ),
                certImageKeyEnum::STORE_MARKET_PHOTO => array(
                    'des' => L('cert_sample_des_store_market_photo'),
                    'image' => $url . '/store/market_photo.png',
                    'key' => certImageKeyEnum::STORE_MARKET_PHOTO,
                    'is_required' => 0
                ),
            ),
            certificationTypeEnum::DEGREE=>array(certImageKeyEnum::DEGREE_CARD => array(
                'des' => 'Graduation Certificate',
                'image' => $url . '/degree/degree_cert_01.jpg',
                'key' => certImageKeyEnum::DEGREE_CARD,
                'is_required' => 1
            ))
        );
    }


    public static function getCertTypeIcon()
    {
        $resource_url = PROJECT_RESOURCE_SITE_URL . '/cert_icon';
        return array(
            certificationTypeEnum::ID => $resource_url . '/icon_credit_identity.png',
            certificationTypeEnum::FAIMILYBOOK => $resource_url . '/icon_family_book.png',
            certificationTypeEnum::RESIDENT_BOOK => $resource_url . '/icon_credit_resident.png',
            certificationTypeEnum::PASSPORT => $resource_url . '/icon_passport.png',
            certificationTypeEnum::BIRTH_CERTIFICATE => $resource_url . '/icon_birth_cert.png',
            certificationTypeEnum::DEGREE => $resource_url . '/icon_birth_cert.png',


            certificationTypeEnum::HOUSE => $resource_url . '/icon_credit_house.png',
            certificationTypeEnum::CAR => $resource_url . '/icon_credit_car.png',
            certificationTypeEnum::LAND => $resource_url . '/icon_credit_land.png',
            certificationTypeEnum::MOTORBIKE => $resource_url . '/icon_credit_motorbike.png',
            certificationTypeEnum::STORE => $resource_url . '/icon_store.png'
        );
    }

    public static function getAssetTypeIcon()
    {
        $resource_url = PROJECT_RESOURCE_SITE_URL . '/cert_icon';
        return array(
            certificationTypeEnum::HOUSE => $resource_url . '/icon_credit_house.png',
            certificationTypeEnum::CAR => $resource_url . '/icon_credit_car.png',
            certificationTypeEnum::LAND => $resource_url . '/icon_credit_land.png',
            certificationTypeEnum::MOTORBIKE => $resource_url . '/icon_credit_motorbike.png',
            certificationTypeEnum::STORE => $resource_url . '/icon_store.png',
            certificationTypeEnum::DEGREE => $resource_url . '/icon_birth_cert.png',

        );
    }

    /*
     * 获取证件定义的key以及样图
     */
    public static function getCertImageStructure()
    {
        Language::read('certification');

        $structure_data = array();
        $sample  = self::getCertSampleImage();
        foreach( $sample as $type=>$data ){
            $temp = array();
            foreach( $data as $key=>$v ){
                $temp[] = array(
                    'key' => $key,
                    'des' => $v['des'],
                    'image' => $v['image'],
                    'file_key' => $key
                );
            }
            $structure_data[$type] = $temp;
        }

        return $structure_data;


        /*return array(

            certificationTypeEnum::ID => array(
                array(
                    'key' => certImageKeyEnum::ID_HANDHELD,
                    'des' => L('cert_sample_des_id_handheld'),
                    'image' => $url . '/id/handheld.png',
                    'file_key' => 'hand_photo'
                ),
                array(
                    'key' => certImageKeyEnum::ID_FRONT,
                    'des' => L('cert_sample_des_id_front'),
                    'image' => $url . '/id/front.png',
                    'file_key' => 'front_photo'
                ),
                array(
                    'key' => certImageKeyEnum::ID_BACK,
                    'des' => L('cert_sample_des_id_back'),
                    'image' => $url . '/id/back.png',
                    'file_key' => 'back_photo'
                ),
            ),

            certificationTypeEnum::FAIMILYBOOK => array(
                array(
                    'key' => certImageKeyEnum::FAMILY_BOOK_FRONT,
                    'des' => L('cert_sample_des_family_book_front'),
                    'image' => $url . '/family_book/front.png',
                    'file_key' => 'front_photo'
                ),
                array(
                    'des' => L('cert_sample_des_family_book_back'),
                    'image' => $url . '/family_book/back.png',
                    'key' => certImageKeyEnum::FAMILY_BOOK_BACK,
                    'file_key' => 'back_photo'

                ),
                array(
                    'des' => L('cert_sample_des_family_householder'),
                    'image' => $url . '/family_book/household.png',
                    'key' => certImageKeyEnum::FAMILY_BOOK_HOUSEHOLD,
                    'file_key' => 'householder_photo'
                ),
//                array(
//                    'key'=>certImageKeyEnum::FAMILY_RELATION_CERT_PHOTO,
//                    'des'=>L('cert_sample_des_family_relation_cert_photo'),
//                    'image' => $url.'/family_book/family_members.png',
//                    'file_key'=>'relation_photo'
//                )
            ),

            certificationTypeEnum::PASSPORT => array(
                certImageKeyEnum::PASSPORT_FRONT => array(
                    'des' => L('cert_sample_des_passport_front'),
                    'image' => $url . '/passport/passport_front.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::PASSPORT_IN => array(
                    'des' => L('cert_sample_des_passport_in'),
                    'image' => $url . '/passport/passport_in.png',
                    'is_required' => 1
                ),
                certImageKeyEnum::PASSPORT_VISA => array(
                    'des' => L('cert_sample_des_passport_visa'),
                    'image' => $url . '/passport/passport_visa.jpg',
                    'is_required' => 0
                ),
            ),

            certificationTypeEnum::RESIDENT_BOOK => array(
                array(
                    'des' => L('cert_sample_des_resident_book_front'),
                    'image' => $url . '/resident_book/front.png',
                    'key' => certImageKeyEnum::RESIDENT_BOOK_FRONT,
                    'file_key' => 'front_photo'
                ),
                array(
                    'des' => L('cert_sample_des_resident_book_back'),
                    'image' => $url . '/resident_book/back.png',
                    'key' => certImageKeyEnum::RESIDENT_BOOK_BACK,
                    'file_key' => 'back_photo'
                )
            ),

            certificationTypeEnum::WORK_CERTIFICATION => array(
                array(
                    'key' => certImageKeyEnum::WORK_CARD,
                    'des' => 'WORK CARD',
                    'image' => $url . '/work/card.png',
                ),
                array(
                    'key' => certImageKeyEnum::WORK_EMPLOYMENT_CERTIFICATION,
                    'desc' => 'EMPLOYMENT CERTIFICATION'
                )
            ),

            certificationTypeEnum::MOTORBIKE => array(
                array(
                    'des' => L('cert_sample_des_motorbike_certificate_front'),
                    'image' => $url . '/motorbike/certificate_front.png',
                    'key' => certImageKeyEnum::MOTORBIKE_CERT_FRONT,
                    'file_key' => 'certificate_front'
                ),
                array(
                    'des' => L('cert_sample_des_motorbike_certificate_back'),
                    'image' => $url . '/motorbike/certificate_back.png',
                    'key' => certImageKeyEnum::MOTORBIKE_CERT_BACK,
                    'file_key' => 'certificate_back'

                ),
                array(
                    'des' => L('cert_sample_des_motorbike_photo'),
                    'image' => $url . '/motorbike/motorbike.jpg',
                    'key' => certImageKeyEnum::MOTORBIKE_PHOTO,
                    'file_key' => 'motorbike_photo'
                ),
            ),

            certificationTypeEnum::CAR => array(
                array(
                    'des' => L('cert_sample_des_car_certificate_front'),
                    'image' => $url . '/car/certificate_front.png',
                    'key' => certImageKeyEnum::CAR_CERT_FRONT,
                    'file_key' => 'certificate_front'
                ),
                array(
                    'des' => L('cert_sample_des_car_certificate_back'),
                    'image' => $url . '/car/certificate_back.png',
                    'key' => certImageKeyEnum::CAR_CERT_BACK,
                    'file_key' => 'certificate_back'
                ),
                array(
                    'des' => L('cert_sample_des_car_front'),
                    'image' => $url . '/car/car_photo.png',
                    'key' => certImageKeyEnum::CAR_FRONT,
                    'file_key' => 'car_front',
                ),
                array(
                    'des' => L('cert_sample_des_car_back'),
                    'image' => $url . '/car/car_photo.png',
                    'key' => certImageKeyEnum::CAR_BACK,
                    'file_key' => 'car_back'
                ),
            ),

            certificationTypeEnum::HOUSE => array(
                array(
                    'des' => L('cert_sample_des_house_front'),
                    'image' => $url . '/house/house_front.png',
                    'key' => certImageKeyEnum::HOUSE_FRONT,
                    'file_key' => 'house_front'
                ),
                array(
                    'des' => L('cert_sample_des_house_front_road'),
                    'image' => $url . '/house/house_front_road.png',
                    'key' => certImageKeyEnum::HOUSE_FRONT_ROAD,
                    'file_key' => 'house_front_road'
                ),
                array(
                    'des' => L('cert_sample_des_house_side_face'),
                    'image' => $url . '/house/house_side_face.png',
                    "key" => certImageKeyEnum::HOUSE_SIDE_FACE,
                    'file_key' => 'house_side_face'
                ),
                array(
                    'des' => L('cert_sample_des_house_in_side'),
                    'image' => $url . '/house/house_inside.png',
                    'key' => certImageKeyEnum::HOUSE_INSIDE,
                    'file_key' => 'house_inside'
                ),
                array(
                    'des' => L('cert_sample_des_house_property_card'),
                    'image' => $url . '/house/property_card.png',
                    'key' => certImageKeyEnum::HOUSE_PROPERTY_CARD,
                    'file_key' => 'property_card'
                ),
                array(
                    'des' => L('cert_sample_des_house_relationship'),
                    'image' => $url . '/house/house_relationship.png',
                    'key' => certImageKeyEnum::HOUSE_RELATIONSHIPS_CERTIFY,
                    'file_key' => 'house_relationships_certify'
                )
            ),

            certificationTypeEnum::LAND => array(
                array(
                    'des' => L('cert_sample_des_land_property_card'),
                    'image' => $url . '/land/property_card.png',
                    'key' => certImageKeyEnum::LAND_PROPERTY_CARD,
                    'file_key' => 'property_card'
                ),
                array(
                    'des' => L('cert_sample_des_land_trading_record'),
                    'image' => $url . '/land/trading_record.png',
                    'key' => certImageKeyEnum::LAND_TRADING_RECORD,
                    'file_key' => 'trading_record'
                ),
            ),

            certificationTypeEnum::STORE => array(
                array(
                    'des' => L('cert_sample_des_store_licence'),
                    'image' => $url . '/store/business_license.png',
                    'key' => certImageKeyEnum::STORE_BUSINESS_LICENSE,
                    'file_key' => certImageKeyEnum::STORE_BUSINESS_LICENSE
                ),
                array(
                    'des' => L('cert_sample_des_store_position'),
                    'image' => $url . '/store/position.png',
                    'key' => certImageKeyEnum::STORE_POSITION,
                    'file_key' => certImageKeyEnum::STORE_POSITION
                ),
                array(
                    'des' => L('cert_sample_des_store_photo'),
                    'image' => $url . '/store/store_photo.png',
                    'key' => certImageKeyEnum::STORE_STORE_PHOTO,
                    'file_key' => certImageKeyEnum::STORE_STORE_PHOTO,
                ),
                array(
                    'des' => L('cert_sample_des_store_market_photo'),
                    'image' => $url . '/store/market_photo.png',
                    'key' => certImageKeyEnum::STORE_MARKET_PHOTO,
                    'file_key' => certImageKeyEnum::STORE_MARKET_PHOTO
                ),
            )

        );*/

    }


    public static function getSystemHelpList($type, $page_num, $page_size)
    {
        $page_num = $page_num ?: 1;
        $page_size = $page_size ?: 100000;
        switch ($type) {
            case 'all':
                $sql = "select * from common_help where is_system='1' and state='" . helpStateEnum::SHOW . "' order by sort desc";
                break;
            case helpCategoryEnum::CREDIT_LOAN :
                $sql = "select * from common_help where is_system='1' and state='" . helpStateEnum::SHOW . "' and category='$type' order by sort desc";
                break;
            case helpCategoryEnum::INSURANCE :
                $sql = "select * from common_help where is_system='1' and state='" . helpStateEnum::SHOW . "' and category='$type' order by sort desc";
                break;
            default:
                $sql = "select * from common_help where is_system='1' and state='" . helpStateEnum::SHOW . "' order by sort desc";
                break;
        }
        $r = new ormReader();
        $re = $r->getPage($sql, $page_num, $page_size);
        return new result(true, 'success', array(
            'total_num' => $re->count,
            'total_pages' => $re->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $re->rows
        ));
    }


    public static function getCompanyGlobalReceiveBankAccount($currency = '')
    {
        $where = '';
        if ($currency) {
            $where .= " and currency='$currency' ";
        }
        $r = new ormReader();
        $sql = "select * from site_bank
        where is_private=0 and account_state=1 $where  group by bank_code,currency,bank_account_no";
        $rows = $r->getRows($sql);
        return $rows;
    }

    public static function getChildrenAddressList($pid = 0)
    {
        $r = new ormReader();
        $sql = "select * from core_tree where root_key='region' and pid='$pid' ";
        $list = $r->getRows($sql);
        return $list;
    }


    public static function currencyExchangeRate()
    {

        $r = new ormReader();
        $sql = "select * from common_exchange_rate";
        $list = $r->getRows($sql);
        return $list;
    }


    public static function getBankLogo()
    {
        return array(
            'ace' => 'asiaweiluy_logo.png',
            'wing' => 'wing_logo.png',
            'ftb' => 'ftb_logo.png',
            'aba' => 'aba_logo.png',
            'sacom' => 'sacom_logo.png',
            'cpb' => 'cpb_logo.png',
            'canadia' => 'canadia_logo.png',
            'acleda' => 'acleda_logo.png',
            'maybank' => 'maybank_logo.png',
            'smartluy' => 'smartluy_logo.png',
            'truemoney' => 'truemoney_logo.png',
            'anzroyal' => 'anzroyal_logo.png',
            'default_logo' => 'default_logo.png'
        );
    }

    public static function getBankLogoByBankCode($bank_code)
    {
        $source_url = trim(getConf('global_resource_site_url'), '/') . '/images/bank';
        $bank_logo = self::getBankLogo();

        $logo_url = $source_url . '/' . $bank_logo[$bank_code];
        if (!fopen($logo_url, 'r')) {
            $logo_url = $source_url . '/default_logo.png';
        }
        return $logo_url;
    }


    public static function getBranchBizLimitSetting($branch_id, $biz_key)
    {
        $m = new site_branch_limitModel();
        $row = $m->find(array(
            'branch_id' => $branch_id,
            'limit_key' => $biz_key
        ));
        return $row;
    }


    /** 获得客户某一等级的业务限制
     * @param $biz_key
     * @param int $grade
     * @return bool|mixed
     */
    public static function getMemberBizLimitByGrade($biz_key, $grade = 0)
    {
        $m = new common_limit_memberModel();
        $row = $m->find(array(
            'member_grade' => $grade,
            'limit_key' => $biz_key
        ));
        return $row;
    }

    /** 获取设置的各项资产信用计算比例和贷款利率
     * @return array
     */
    public static function getAssetsCreditGrantRateAndDefaultInterest()
    {
        $array = self::getCreditGrantRateAndDefaultInterest();
        $array[certificationTypeEnum::HOUSE] = $array['house_credit_rate'];
        $array[certificationTypeEnum::CAR] = $array['car_credit_rate'];
        $array[certificationTypeEnum::LAND] = $array['land_credit_rate'];
        $array[certificationTypeEnum::MOTORBIKE] = $array['motorbike_credit_rate'];
        $array[certificationTypeEnum::STORE] = $array['store_credit_rate'];

        return $array;
    }


    /** 获取设置的各项信用计算比例和贷款利率
     * @return array
     */
    public static function getCreditGrantRateAndDefaultInterest()
    {
        $m_dict = (new core_dictionaryModel());
        $data = $m_dict->getDictionary(dictionaryKeyEnum::CREDIT_GRANT_RATE);
        if ($data) {
            $setting = my_json_decode($data['dict_value']);
            return array(
                'default_credit_rate' => $setting['default_credit_rate'] / 100,
                'land_credit_rate' => $setting['land_credit_rate'] / 100,
                'house_credit_rate' => $setting['house_credit_rate'] / 100,
                'motorbike_credit_rate' => $setting['motorbike_credit_rate'] / 100,
                'car_credit_rate' => $setting['car_credit_rate'] / 100,
                'store_credit_rate' => $setting['store_credit_rate'] / 100,
                'default_terms' => $setting['default_terms'],
                'default_max_terms' => $setting['default_max_terms'],
                'default_salary_rate' => $setting['default_salary_rate'] / 100,
                'default_rental_rate' => $setting['default_rental_rate'] / 100,
                'default_attachment_rate' => $setting['default_attachment_rate'] / 100,
                'allow_operator_submit_to_hq' => $setting['allow_operator_submit_to_hq'],
            );

        } else {
            return array(
                'default_credit_rate' => 0,
                'land_credit_rate' => 0,
                'house_credit_rate' => 0,
                'motorbike_credit_rate' => 0,
                'car_credit_rate' => 0,
                'store_credit_rate' => 0,
                'default_terms' => 0,
                'default_max_terms' => 0,
                'default_salary_rate' => 0,
                'default_rental_rate' => 0,
                'default_attachment_rate' => 0,
                'allow_operator_submit_to_hq' => 0,
            );
        }

    }


    /** 是否信用贷使用授信时的利率
     * @return int
     */
    public static function creditLoanIsUseCreditGrantRate()
    {
        $function = self::getBusinessSwitch();
        return intval($function['is_loan_use_credit_grant_interest']);
    }


    /** 贷款是否固定客户的还款日期
     * @return int
     */
    public static function loanIsFixClientRepaymentDate()
    {
        $function = self::getBusinessSwitch();
        return intval($function['is_fix_loan_repayment_day']);
    }


    /** 会员设置交易密码方式
     * @return int
     */
    public static function memberSetTradingPasswordWay()
    {
        return intval(getConf('member_set_trading_password_way'));
    }

    public static function memberAuthorizedContractFeeRate($grant_id)
    {
        $grant_info = (new member_credit_grantModel())->find(array(
            'uid' => $grant_id
        ));


        $member_category = (new member_credit_categoryModel())->find(array(
            'uid' => $grant_info['default_credit_category_id']
        ));
        $cate_id = $member_category['category_id'] ?: 0;
        $max_credit = round($grant_info['max_credit'], 2);
        $credit_currency = currencyEnum::USD;

        $rate_list = self::getLoanFeeSettingOfCategoryId($cate_id);
        $rate = null;
        // 先检查是否有符合区间的
        foreach ($rate_list as $v) {
            if ($v['currency'] == $credit_currency
                && $max_credit >= $v['min_amount']
                && $max_credit <= $v['max_amount']
            ) {
                $rate = $v;
                break;
            }
        }

        $max_amount = 0;

        // 如果没有匹配区间，找最邻近的设置
        if( !$rate ){
            foreach ($rate_list as $v) {
                if ($v['currency'] == $credit_currency
                    && $max_credit >= $v['max_amount']
                ) {
                    if ($v['max_amount'] > $max_amount) {
                        $max_amount = $v['max_amount'];
                        $rate = $v;
                    }
                }
            }
        }


        if ($rate) {
            // 检查是否有个性化设置
            if( $grant_info['loan_fee'] > 0 ){
                $rate['loan_fee'] = $grant_info['loan_fee'];
                $rate['loan_fee_type'] = $grant_info['loan_fee_type'];
                $rate['admin_fee'] = $grant_info['admin_fee'];
                $rate['admin_fee_type'] = $grant_info['admin_fee_type'];
            }
            return $rate;
        }

        return array(
            'loan_fee' => 0,
            'loan_fee_type' => 0,
            'admin_fee' => 0,
            'admin_fee_type' => 0
        );


        // 查询设置的利率
        /* $m_s = new loan_fee_settingModel();
         $sql = "select * from loan_fee_setting where currency=".qstr(currencyEnum::USD).
         " and min_amount<='$max_credit' and max_amount>='$max_credit' ";
         $info = $m_s->reader->getRow($sql);
         if( !$info ){
             $info = array(
                 'loan_fee' => 0,
                 'loan_fee_type' => 0,
                 'admin_fee' => 0,
                 'admin_fee_type' => 0
             );
         }
         return $info;*/

    }

    /** 授权合同收费比例
     * @return array
     */
    public static function memberAuthorizedContractFeeRate_old()
    {
        $m_dict = (new core_dictionaryModel());
        $data = $m_dict->getDictionary(dictionaryKeyEnum::AUTHORIZED_CONTRACT_FEE);
        if ($data) {
            $setting = my_json_decode($data['dict_value']);
            return array(
                'first_sign_contract_rate_value' => $setting['first_sign_contract_rate_value'],  // 值
                'first_sign_contract_rate_type' => $setting['first_sign_contract_rate_type'], // 0 百分比 1 固定金额
                'min_first_sign_contract_fee' => $setting['min_first_sign_contract_fee'],  // 最低值
                'follow_sign_contract_rate_value' => $setting['follow_sign_contract_rate_value'],  // 值
                'follow_sign_contract_rate_type' => $setting['follow_sign_contract_rate_type'], // 0 百分比 1 固定金额
                'min_follow_sign_contract_fee' => $setting['min_follow_sign_contract_fee'],  // 最低值
            );
        } else {
            return array(
                'first_sign_contract_rate_value' => 0,  // 值
                'first_sign_contract_rate_type' => 0, // 0 百分比 1 固定金额
                'min_first_sign_contract_fee' => 0,  // 最低值
                'follow_sign_contract_rate_value' => 0,  // 值
                'follow_sign_contract_rate_type' => 0, // 0 百分比 1 固定金额
                'min_follow_sign_contract_fee' => 0,  // 最低值
            );
        }


    }


    /** 所有交易类型
     * @return array
     */
    public static function getAllTradingType()
    {
        return array(
            'adjust' => 'Adjust',
            'bank_adjust' => 'Bank Adjust',
            'bank_to_branch' => 'Bank To Branch',
            'bank_to_headquarter' => 'Bank To Headquarter',
            'branch_adjust' => 'Branch Adjust',
            'branch_out_system_income' => 'Branch Out System Income',
            'branch_out_system_payment' => 'Branch Out System Payment',
            'branch_to_bank' => 'Branch To Bank',
            'branch_to_cashier' => 'Branch To Cashier',
            'branch_to_headquarter' => 'Branch To Headquarter',
            'capital_receive' => 'Capital Receive',
            'cashier_out_system_income' => 'Out System Income',
            'cashier_out_system_payment' => 'Out System Payment',
            'cashier_to_branch' => 'Cashier To Branch',
            'civ_ext_in' => 'Civ Ext In',
            'civ_ext_out' => 'Ext Out',
            'client_adjust' => 'Client Adjust',
            'client_deposit_by_bank' => 'Deposit By Bank',
            'client_deposit_by_cash' => 'Deposit By Cash',
            'client_deposit_by_partner' => 'Client Deposit By Partner',
            'client_payment_to_client' => 'Client Payment To Client',
            'client_to_client' => 'Transfer To Client',
            'client_withdraw_by_bank' => 'Withdraw By Bank',
            'client_withdraw_by_cash' => 'Withdraw By Cash',
            'client_withdraw_by_partner' => 'Withdraw By Partner',
            'client_purchase_savings_product_by_balance' => 'Financial-Buy',
            'client_redeem_savings_product_by_balance' => 'Financial-Redeem',
            'exchange' => 'Exchange',
            'headquarter_to_bank' => 'Headquarter To Bank',
            'headquarter_to_branch' => 'Headquarter To Branch',
            'headquarter_to_cod' => 'Headquarter To Cod',
            'headquarter_to_partner' => 'Headquarter To Partner',
            'income_from_balance' => 'Other Income(Balance)',
            'income_from_bank' => 'Bank Adjust Interest',
            'income_from_cash' => 'Other Income(Cash)',
            'income_operation_fee_balance' => 'Operation Fee(Balance)',
            'income_operation_fee_cash' => 'Operation Fee(Cash)',
            'loan_deduct' => 'Loan Deduct',
            'loan_disburse' => 'Loan Disburse',
            'loan_prepayment' => 'Loan Prepayment',
            'loan_repayment' => 'Loan Repayment',
            'loan_written_off' => 'Loan Written Off',
            'manual_voucher' => 'Manual Voucher',
            'member_adjust' => 'Client Adjust',
            'member_deposit_by_bank' => 'Deposit By Bank',
            'member_deposit_by_cash' => 'Deposit By Cash',
            'member_deposit_by_partner' => 'Deposit By Partner',
            'member_exchange' => 'Member Exchange',
            'member_payment_to_member' => 'Scan Payment',
            'member_to_member' => 'Transfer To Member',
            'member_withdraw_by_bank' => 'Withdraw By Bank',
            'member_withdraw_by_cash' => 'Withdraw By Cash',
            'member_withdraw_by_partner' => 'Withdraw By Partner',
            'member_purchase_savings_product_by_balance' => 'Financial-Buy',
            'member_redeem_savings_product_by_balance' => 'Financial-Redeem',
            'partner_to_headquarter' => 'Partner To Headquarter',
            'payout_from_bank' => 'Bank Adjust Fee',
            'user_adjust' => 'User Adjust',
            'user_to_user' => 'Transfer To User',
            'Client_deposit_by_partner' => 'Deposit By Partner',
            'Headquarter_to_branch' => 'Headquarter To Branch',
        );
    }


    /** 客户使用的交易类型
     * @return array
     */
    public static function getMemberTradingType()
    {
        $all = self::getAllTradingType();

        $member = array(
            'capital_receive' => 'Capital Receive',
            'loan_deduct' => 'Loan Deduct',
            'loan_disburse' => 'Loan Disburse',
            'loan_repayment' => 'Loan Repayment',
            'loan_prepayment' => 'Loan Prepayment',
            'member_adjust' => 'Client Adjust',
            'member_deposit_by_cash' => 'Deposit By Cash',
            //'member_deposit_by_bank' => 'Deposit By Bank',
            'member_deposit_by_partner' => 'Deposit By Partner',
            'member_payment_to_member' => 'Payment',
            'member_to_member' => 'Transfer To Member',
            //'member_withdraw_by_bank' => 'Withdraw By Bank',
            'member_withdraw_by_cash' => 'Withdraw By Cash',
            'member_withdraw_by_partner' => 'Withdraw By Partner',
            'income_from_balance' => 'Pay the Fees',
            'income_from_cash' => 'Pay the Fees',
            'income_operation_fee_balance' => 'Operation Fee(Balance)',
            'income_operation_fee_cash' => 'Operation Fee(Cash)',
            'member_exchange' => 'Exchange',
            'member_purchase_savings_product_by_balance' => 'Financial-Buy',
            'member_redeem_savings_product_by_balance' => 'Financial-Redeem',
        );
        return array_merge($all,$member);
    }


    public static function allTradingTypeIcon()
    {
        return array(
            'adjust' => 'system_adjust.png',
            'bank_adjust' => 'system_adjust.png',
            'bank_to_branch' => 'bank_withdraw.png',
            'bank_to_headquarter' => 'bank_withdraw.png',
            'branch_adjust' => 'system_adjust.png',
            'branch_to_bank' => 'bank_deposit.png',
            'branch_to_cashier' => 'branch_to_cashier.png',
            'branch_to_headquarter' => 'client_transfer.png',
            'capital_receive' => 'capital_receive.png',
            'cashier_to_branch' => 'cashier_to_branch.png',
            'client_adjust' => 'system_adjust.png',
            'client_deposit_by_cash' => 'deposit.png',
            'client_deposit_by_bank' => 'deposit.png',
            'Client_deposit_by_partner' => 'deposit.png',
            'client_to_client' => 'client_transfer.png',
            'client_withdraw_by_bank' => 'withdraw.png',
            'client_withdraw_by_cash' => 'withdraw.png',
            'client_withdraw_by_partner' => 'withdraw.png',
            'headquarter_to_bank' => 'bank_deposit.png',
            'Headquarter_to_branch' => 'client_transfer.png',
            'loan_deduct' => 'loan_deduct.png',
            'loan_disburse' => 'loan_disbursement.png',
            'loan_repayment' => 'loan_repayment.png',
            'loan_prepayment' => 'loan_prepayment.png',
            'member_adjust' => 'system_adjust.png',
            'member_deposit_by_cash' => 'deposit.png',
            'member_deposit_by_bank' => 'deposit.png',
            'member_deposit_by_partner' => 'deposit.png',
            'member_payment_to_member' => 'payment.png',
            'member_to_member' => 'client_transfer.png',
            'member_withdraw_by_bank' => 'withdraw.png',
            'member_withdraw_by_cash' => 'withdraw.png',
            'member_withdraw_by_partner' => 'withdraw.png',
            'user_to_user' => 'user_transfer.png',
            'balance' => 'balance.png',
            'income_operation_fee_balance' => 'operation_fee.png',
            'income_operation_fee_cash' => 'operation_fee.png',
            'default' => 'capital_receive.png',
            'income_from_balance' => 'capital_receive.png',
            'income_from_cash' => 'capital_receive.png',
            'member_exchange' => 'exchange.png',
            'member_purchase_savings_product_by_balance' => 'financial_management.png',
            'member_redeem_savings_product_by_balance' => 'financial_management.png',
            'client_purchase_savings_product_by_balance' => 'financial_management.png',
            'client_redeem_savings_product_by_balance' => 'financial_management.png',
        );
    }


    /** 获得交易类型的icon
     * @param $trading_type
     * @return string
     */
    public static function getTradingTypeIcon($trading_type)
    {
        $icon_array = self::allTradingTypeIcon();
        if ($icon_array[$trading_type]) {
            $icon = $icon_array[$trading_type];
        } else {
            $icon = $icon_array['default'];
        }

        return PROJECT_RESOURCE_SITE_URL . '/trading_icon/' . $icon;
    }

    /**
     * member修改交易密码及手机号码费用
     */
    public static function getChangeProfileFee()
    {
        $m = new core_dictionaryModel();
        $data = $m->getDictionary(dictionaryKeyEnum::GLOBAL_SETTINGS);
        $value = my_json_decode($data['dict_value']);
        return array(
            'change_phone_number' => $value['member_change_phone_number_fee'] ?: 0,
            'change_trade_password' => $value['member_change_trading_password_fee'] ?: 0,
        );

    }


    /** 获取贷款时间内最低计算利息天数
     * @param $days
     * @return int
     */
    public static function getInterestMindaysByLoanDays($loan_days, $category_id = 0)
    {
        $loan_days = intval($loan_days);
        $category_id = intval($category_id);
        $r = new ormReader();

        // 个性设置
        $sql = "select * from loan_repayment_limit where category_id='$category_id' and  loan_days<='$loan_days' order by loan_days desc ";
        $row = $r->getRow($sql);

        if (!$row) {
            // 查询默认设置
            $sql = "select * from loan_repayment_limit where category_id='0' and  loan_days<='$loan_days' order by loan_days desc ";
            $row = $r->getRow($sql);
            if (!$row) {
                return 0;
            }
        }
        $min_days = intval($row['limit_days']);
        if ($min_days > $loan_days) {
            $min_days = $loan_days;
        }

        return $min_days;
    }

    /*
     * 获取credit的授权参与人数设置
     * 结构：array(array("min_credit"=>0,"max_credit"=>1000,"voter"=>2));
     */
    public static function getVoterOfGrantingCredit()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('voter_of_granting_credit');
        if ($row && $row['dict_value']) {
            $re = @json_decode($row['dict_value'], true);
        }
        return $re;
    }

    /*
     * 根据金额获取credit的授权参与人数设置
     */
    public static function getVoterOfGrantingCreditByAmount($amt)
    {
        $amt = intval($amt);
        $ret = self::getVoterOfGrantingCredit();
        $ret = my_array_sort($ret, "max_credit", SORT_DESC, SORT_NUMERIC);

        $cur_item = null;
        $max_item = current($ret);
        foreach ($ret as $item) {
            if (intval($item['min_credit']) <= $amt && intval($item['max_credit']) >= $amt) {
                $cur_item = $item;
                break;
            }
        }
        if (!$cur_item) {
            if ($amt > $max_item['max_credit']) {
                return $max_item['voter'];
            } else {
                return 1;
            }
        } else {
            return intval($cur_item['voter']);
        }
    }

    /*
     * 获取credit的授权参与人数设置
     * 结构：array(array("min_credit"=>0,"max_credit"=>1000,"voter"=>2));
     */
    public static function getVoterOfWrittenOff()
    {
        $re = array();
        $m = new core_dictionaryModel();
        $row = $m->getDictionary('voter_of_written_off');
        if ($row && $row['dict_value']) {
            $re = @json_decode($row['dict_value'], true);
        }
        return $re;
    }

    /*
     * 根据金额获取credit的授权参与人数设置
     */
    public static function getVoterOfWrittenOffByLossAmount($amt)
    {
        $amt = intval($amt);
        $ret = self::getVoterOfWrittenOff();
        $ret = my_array_sort($ret, "max_loss", SORT_DESC, SORT_NUMERIC);

        $cur_item = null;
        $max_item = current($ret);
        foreach ($ret as $item) {
            if (intval($item['min_loss']) <= $amt && intval($item['max_loss']) >= $amt) {
                $cur_item = $item;
                break;
            }
        }
        if (!$cur_item) {
            if ($amt > $max_item['max_loss']) {
                return $max_item['voter'];
            } else {
                return 1;
            }
        } else {
            return intval($cur_item['voter']);
        }
    }


    public static function getMemberAppClosedState()
    {
        $m = new core_dictionaryModel();
        $row = $m->getDictionary(dictionaryKeyEnum::SYSTEM_CLOSE_MEMBER_APP);
        if (!$row) {
            return array(
                'is_closed' => 0,
                'closed_reason' => ''
            );
        }
        $data = @my_json_decode($row['dict_value']);
        if ($data['state']) {
            $is_closed = 0;
        } else {
            $is_closed = 1;
        }
        return array(
            'is_closed' => $is_closed,
            'closed_reason' => $data['remark']
        );
    }


    public static function getCreditOfficerAppClosedState()
    {
        $m = new core_dictionaryModel();
        $row = $m->getDictionary(dictionaryKeyEnum::SYSTEM_CLOSE_CREDIT_OFFICER_APP);
        if (!$row) {
            return array(
                'is_closed' => 0,
                'closed_reason' => ''
            );
        }
        $data = @my_json_decode($row['dict_value']);
        if ($data['state']) {
            $is_closed = 0;
        } else {
            $is_closed = 1;
        }
        return array(
            'is_closed' => $is_closed,
            'closed_reason' => $data['remark']
        );
    }


    public static function isForbiddenDeposit()
    {
        // 检查是否功能被关闭
        $function_switch = self::getFunctionSwitch();
        if (isset($function_switch['close_deposit']) && $function_switch['close_deposit']) {
            return true;
        }
        return false;
    }

    public static function isForbiddenWithdraw()
    {
        // 检查是否功能被关闭
        $function_switch = self::getFunctionSwitch();
        if (isset($function_switch['close_withdraw']) && $function_switch['close_withdraw']) {
            return true;
        }
        return false;
    }

    public static function isForbiddenTransfer()
    {
        // 检查是否功能被关闭
        $function_switch = self::getFunctionSwitch();
        if (isset($function_switch['close_transfer']) && $function_switch['close_transfer']) {
            return true;
        }
        return false;
    }


    public static function isForbiddenPay()
    {
        // 检查是否功能被关闭
        $function_switch = self::getFunctionSwitch();
        if (isset($function_switch['close_pay']) && $function_switch['close_pay']) {
            return true;
        }
        return false;
    }

    public static function isForbiddenCollect()
    {
        // 检查是否功能被关闭
        $function_switch = self::getFunctionSwitch();
        if (isset($function_switch['close_collect']) && $function_switch['close_collect']) {
            return true;
        }
        return false;
    }


    public static function isForbiddenLoan()
    {
        // 检查是否功能被关闭
        $function_switch = global_settingClass::getFunctionSwitch();
        if (isset($function_switch['close_loan']) && $function_switch['close_loan']) {
            return true;
        }
        return false;
    }


    public static function isForbiddenReturnLoan()
    {
        // 检查是否功能被关闭
        $function_switch = global_settingClass::getFunctionSwitch();
        if (isset($function_switch['close_return_loan']) && $function_switch['close_return_loan']) {
            return true;
        }
        return false;
    }


    /** 商圈列表
     * @return ormCollection
     */
    public static function getBusinessPlaceList()
    {
        $r = new ormReader();
        $sql = "select * from common_industry_place order by place";
        return $r->getRows($sql);
    }

    /** 贷款提前还款处理期限（天数）
     * @return int
     */
    public static function getLoanPrepaymentApplyValidDays()
    {
        return 3;
    }


    public static function getCounterBizCTApproveDetail($biz_code,$branch_id=0)
    {
        $info = array(
            'is_require' => 1,
            'min_amount' => 0
        );
        $m = new common_counter_biz_settingModel();
        $row = $m->find(array(
            'biz_code' => $biz_code
        ));

        if( $branch_id ){
            $branch_info = (new site_branchModel())->getBranchInfoById($branch_id);
            if( $branch_info ){
                $special_set = my_json_decode($branch_info['profile']);
                $biz_setting = $special_set['limit_chief_teller_approve'][$biz_code];
                if( $biz_setting ){
                    $row['is_require_ct_approve'] = $biz_setting['is_require_ct_approve'];
                    $row['min_approve_amount'] = $biz_setting['min_approve_amount'];
                }
            }
        }

        if (!$row) {
            return $info;
        }

        $info['is_require'] = $row['is_require_ct_approve'];
        $info['min_amount'] = $row['min_approve_amount'];
        return $info;
    }

    public static function getGlCodeRule($type)
    {
        $m = new core_dictionaryModel();
        $row = $m->getDictionary(dictionaryKeyEnum::GL_CODE_RULE);
        if ($row && $row['dict_value']) {
            $re = @json_decode($row['dict_value'], true);
            return $re[$type];
        } else
            return null;
    }

    /** 是否关闭了ACE业务
     * @return bool
     */
    public static function isACEBusinessClosed()
    {
        $function = self::getFunctionSwitch();
        if (isset($function['close_ace_business']) && $function['close_ace_business']) {
            return true;
        }
        return false;
    }

    public static function getLoanProductIconByInterestType($interest_type)
    {
        $url = PROJECT_RESOURCE_SITE_URL . '/loan_product_icon/' . $interest_type . '.png';
        if (fopen($url, 'r')) {
            return $url;
        }
        return PROJECT_RESOURCE_SITE_URL . '/loan_product_icon/default.png';
    }

    public static function getModuleBusinessSetting($sceneType = '')
    {
        $m = new common_module_entranceModel();
        $rows = $m->select(array("platform" => $sceneType));
        $rows = resetArrayKey($rows, "module_code");
        $lang_list = enum_langClass::getModuleBusinessLang();
        switch ($sceneType) {
            case bizSceneEnum::APP_MEMBER:
                unset($lang_list[moduleBusinessEnum::MODULE_APPROVE_CREDIT]);
                break;
            case bizSceneEnum::COUNTER:
                unset($lang_list[moduleBusinessEnum::MODULE_APPROVE_CREDIT]);
                unset($lang_list[moduleBusinessEnum::MODULE_BRANCH]);
                break;
            case bizSceneEnum::BACK_OFFICE:
                $lang_list = array(
                    moduleBusinessEnum::MODULE_APPROVE_CREDIT => $lang_list[moduleBusinessEnum::MODULE_APPROVE_CREDIT]
                );
                break;
            default:
                break;
        }
        $ret = array();
        foreach ($lang_list as $k => $v) {
            $def = array(
                "module_code" => $k,
                "module_name" => $v,
                "platform" => $sceneType,
                "is_close" => 1,
                "is_show" => 1,
                "is_new" => 0,
                "update_time" => Now()
            );
            $set = $rows[$k];
            $item = array_merge($def, $set ?: array());
            unset($item['uid']);
            unset($item['platform']);
            $ret[$k] = $item;
        }
        return $ret;
    }

    public static function getLoanPrepaymentLimitKey()
    {
        $arr = array();
        for ($i = 1; $i < 30; $i++) {
            $arr[$i] = $i . " Days";
        }
        for ($j = 1; $j <= 84; $j++) {
            $v = $j * 30;
            $arr[$v] = $j . " Months";
        }
        return $arr;
    }

    public static function getLoanPrepaymentLimitSetting()
    {
        $limit_key = self::getLoanPrepaymentLimitKey();
        $m = new loan_repayment_limitModel();
        $limit = $m->select(array(
            "category_id" => 0
        ));
        $limit = resetArrayKey($limit, "loan_days");

        $ret = array();
        foreach ($limit_key as $k => $title) {
            $item = array(
                "loan_terms" => $title,
                "loan_days" => $k,
                "loan_months" => 0,
                "limit_days" => 0,
                "limit_months" => 0,
                "limit_title" => ""
            );
            if ($k >= 30) {
                $item['loan_months'] = intval($k / 30);
            }

            $limit_days = intval($limit[$k]['limit_days']);
            if ( $limit_days && $limit_days <= $k) {
                $item['limit_days'] = $limit_days;
                $item['limit_title'] = $limit_days . " Day";
                if ( $limit_days >= 30 ) {
                    $item['limit_months'] = intval($limit[$k]['limit_days'] / 30);
                    $item['limit_title'] = $item['limit_months'] . " Month";
                }
            }
            $ret[$k] = $item;
        }

        return $ret;
    }

    public static function getLoanPrepaymentLimitSettingOfCategoryId($category_id)
    {
        $limit_key = self::getLoanPrepaymentLimitKey();
        $m = new loan_repayment_limitModel();
        $limit = $m->select(array(
            "category_id" => $category_id
        ));

        $limit = resetArrayKey($limit, "loan_days");
        $default_limit = self::getLoanPrepaymentLimitSetting();

        $ret = array();
        foreach ($limit_key as $k => $title) {
            $item = array(
                "loan_terms" => $title,
                "loan_days" => $k,
                "loan_months" => 0,
                "limit_days" => 0,
                "limit_months" => 0,
                "limit_title" => ""
            );
            if ($k >= 30) {
                $item['loan_months'] = intval($k / 30);
            }
            if ($limit[$k]['limit_days'] && $limit[$k]['limit_days'] <= $k) {
                $item['limit_days'] = $limit[$k]['limit_days'];
                $item['limit_title'] = $limit[$k]['limit_days'] . " Day";
                if ($limit[$k]['limit_days'] >= 30) {
                    $item['limit_months'] = intval($limit[$k]['limit_days'] / 30);
                    $item['limit_title'] = $item['limit_months'] . " Month";
                }
            } else {
                if ($default_limit[$k]['limit_days'] && $default_limit[$k]['limit_days'] <= $k) {
                    $item['limit_days'] = $default_limit[$k]['limit_days'];
                    $item['limit_title'] = $default_limit[$k]['limit_days'] . " Day";
                    if ($default_limit[$k]['limit_days'] >= 30) {
                        $item['limit_months'] = intval($default_limit[$k]['limit_days'] / 30);
                        $item['limit_title'] = $item['limit_months'] . " Month";
                    }
                }
            }
            $ret[$k] = $item;
        }

        return $ret;

    }

    public static function getLoanFeeSettingOfCategoryId($category_id)
    {
        $m = new loan_fee_settingModel();
        return $m->getSpecialSettingListOfCategoryId($category_id);
    }

}