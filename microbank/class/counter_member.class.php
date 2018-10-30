<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/29
 * Time: 下午3:50
 */
class counter_memberClass extends counter_baseClass{
    static function getCODofTeller($teller_id){
        return userClass::getPassbookBalanceOfUser($teller_id);
    }
    static function insertRequestToDeposit($p){
        $factory=bizFactoryClass::getInstance(bizSceneEnum::COUNTER,bizCodeEnum::MEMBER_DEPOSIT_BY_CASH);
        $ret=$factory->bizStart(1,2,200,currencyEnum::USD,"");
        $arr=$ret->DATA;
        $biz_id=intval($arr['biz_id']);
        if(!$biz_id) return new result(false,"");
        return new result(true,"",$biz_id);
    }
    static  function getRequestionOfDeposit($biz_id){
        $factory=bizFactoryClass::getInstance(bizSceneEnum::COUNTER,bizCodeEnum::MEMBER_DEPOSIT_BY_CASH);
        $ret=$factory->getBizRow($biz_id);
        return $ret;
    }
    static function checkDepositStepOfMember($biz_id,$trading_pwd){
        $factory=bizFactoryClass::getInstance(bizSceneEnum::COUNTER,bizCodeEnum::MEMBER_DEPOSIT_BY_CASH);

        return $factory->checkMemberTradingPassword($biz_id,$trading_pwd);


    }

    public static function getMemberLoanProductInterestList($member_credit_category_id,$sub_product_id,$member_id)
    {
        $credit_grant = member_credit_grantClass::getMemberLastGrantInfo($member_id);
        $m_member_category = new member_credit_categoryModel();

        $member_category = $m_member_category->find(array(
            'uid' => $member_credit_category_id
        ));


        $mortgage_asset = $m_member_category->getMortgagedAssetListByCategoryId($member_credit_category_id,$credit_grant['uid']);
        $mortgage_type = '';
        if( $mortgage_asset ){
            $mortgage_type = assetsCertTypeEnum::SOFT;
            foreach( $mortgage_asset as $v ){
                if( $v['asset_cert_type'] == assetsCertTypeEnum::HARD ){
                    $mortgage_type = assetsCertTypeEnum::HARD;
                    break;
                }
            }
        }

        switch( $mortgage_type ){
            case assetsCertTypeEnum::SOFT:
                $interest_key = 'interest_rate_mortgage1';
                $operation_fee_key = 'operation_fee_mortgage1';
                break;
            case assetsCertTypeEnum::HARD:
                $interest_key = 'interest_rate_mortgage2';
                $operation_fee_key = 'operation_fee_mortgage2';
                break;
            default:
                $interest_key = 'interest_rate';
                $operation_fee_key = 'operation_fee';
        }

        $package_id = intval($member_category['interest_package_id']);
        $sub_product_id = $member_category['sub_product_id']?:$sub_product_id;


        $list = loan_productClass::getSizeRateByPackageId($package_id,$sub_product_id);


        $return = array();
        foreach( $list as $key=>$item ){
            // 过滤特殊利率非active的
            if( $item['is_special'] && !$item['is_active'] ){
                continue;
            }
            $item['interest_rate_used'] = $item[$interest_key];
            $item['operation_fee_used'] = $item[$operation_fee_key];

            $return[] = $item;
        }

        return $return;

    }

    public static function getPendingRepayToday(){
        $target_date=date("Y-m-d 23:59:59");
        $sql="SELECT c.obj_guid,b.`contract_sn`,b.`currency`,a.`scheme_name`,a.`initial_principal`,a.`receivable_date`,a.`receivable_principal`,a.`receivable_interest`,a.`ref_amount`,";
        $sql.=" c.uid member_id,c.`display_name`,c.`member_icon`,c.`branch_id`,d.`branch_code`,d.`branch_name` FROM loan_installment_scheme a";
        $sql.=" INNER JOIN loan_contract b ON a.`contract_id`=b.`uid`";
        $sql.=" INNER JOIN client_member c ON b.`client_obj_guid`=c.`obj_guid` AND b.`client_obj_type`=1";
        $sql.=" INNER JOIN site_branch d ON c.`branch_id`=d.`uid`";
        $sql.=" WHERE a.`state`>=0 AND a.state<100 AND b.`state`>0 AND a.`receivable_date`<='".$target_date."' order by a.receivable_date desc";
        $r=new ormReader();
        $list=$r->getRows($sql);

        return $list;
    }
    /*
     * 获取等待签合同的任务给柜台
     * */
    public static function getPendingSignCreditAgreement(){

        $sql="SELECT c.obj_guid,a.member_id,a.grant_time,a.`max_credit` credit,a.`credit_terms`,c.`display_name`,c.`member_icon`,c.`branch_id`,d.`branch_code`,d.`branch_name` FROM member_credit_grant a ";
        $sql.=" INNER JOIN client_member c ON a.`member_id`=c.uid";
        $sql.=" INNER JOIN site_branch d ON c.`branch_id`=d.`uid`";
        $sql.=" LEFT JOIN `member_authorized_contract` b";
        $sql.=" ON a.uid=b.grant_credit_id";
        $sql.=" WHERE b.grant_credit_id IS NULL AND a.`state`=".commonApproveStateEnum::PASS;
        $sql.=" ORDER BY a.uid DESC";

        $r=new ormReader();
        $list=$r->getRows($sql);

        return $list;
    }
    /*
    * 获取等待签合同的任务给柜台
    * */
    public static function getPendingDisburse(){

        $sql="SELECT c.obj_guid,a.member_id,a.grant_time,a.max_credit credit,a.`credit_terms`,c.`display_name`,c.`member_icon`,c.`branch_id`,d.`branch_code`,d.`branch_name` FROM member_credit_grant a ";
        $sql.=" INNER JOIN client_member c ON a.`member_id`=c.uid";
        $sql.=" INNER JOIN site_branch d ON c.`branch_id`=d.`uid`";
        $sql.=" LEFT JOIN `member_authorized_contract` b";
        $sql.=" ON a.uid=b.grant_credit_id";
        $sql.=" LEFT JOIN `loan_contract` e";
        $sql.=" ON a.uid=e.credit_grant_id and e.state>'".loanContractStateEnum::CREATE."'";
        $sql.=" WHERE b.grant_credit_id IS not NULL and b.state>'".authorizedContractStateEnum::CREATE."' and e.credit_grant_id is null";
        $sql.=" ORDER BY a.uid DESC";

        $r=new ormReader();
        $list=$r->getRows($sql);

        return $list;
    }

}