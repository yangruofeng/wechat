<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/5
 * Time: 10:28
 */
class loan_categoryClass
{
    /**
     * 获取所有category
     * @return ormCollection
     */
    public static function getAllCategoryList()
    {
        $m=new loan_categoryModel();
        $rows=$m->getCategoryList();
        return $rows;
    }


    /** 获得所有的分类产品和member的可贷情况
     * APP 专用!!!
     * @param $member_id
     * @return ormCollection
     */
    public static function getAllCreditCategoryListOfMember($member_id=0)
    {
        $member_id = intval($member_id);
        if( $member_id > 0 ){
            $where = " where  member_id='$member_id'";
        }else{
            $where = " where 1>2 ";
        }
        $r = new ormReader();
        // todo group by 暂时强制一个category只有一个product
        $sql = "select c.uid,c.category_code,c.category_name,c.category_lang,c.default_product_id,c.product_feature,c.is_only_loan_by_app,mcc.uid member_credit_category_id,
        mcc.sub_product_id,mcc.credit,mcc.credit_balance,mcc.is_close member_is_close
        from loan_category c left join (select * from member_credit_category $where) mcc on mcc.category_id=c.uid
        where  c.is_close='0' group by c.uid order by c.category_name";
        $rows = $r->getRows($sql);


        // 格式化一下,将null处理了,返回给APP
        foreach( $rows as $key=>$v ){
            $v['product_feature'] = urlencode(system_toolClass::formatWapHtmlContent($v['product_feature']));
            $v['member_credit_category_id'] = intval($v['member_credit_category_id']);
            $v['category_lang'] = my_json_decode($v['category_lang']);
            $v['sub_product_id'] = $v['sub_product_id']?:0;
            $v['loan_sub_product_id'] = $v['sub_product_id']?:$v['default_product_id'];
            $v['credit'] = $v['credit']?:0;
            $v['credit_balance'] = $v['credit_balance']?:0;
            $v['member_is_close'] = intval($v['member_is_close']);
            if( $v['member_credit_category_id'] && !$v['member_is_close'] ){
                $v['category_is_active'] = 1;
            }else{
                $v['category_is_active'] = 0;
            }
            $rows[$key] = $v;
        }
        return $rows;
    }

    /**
     * 获取member的category-id
     * @param $member_id
     */
    public static function getMemberCreditCategoryList($member_id, $is_close = 0){
        $sql="select a.*,b.category_code,b.product_code_usd,b.product_code_khr,b.is_special,b.special_key,b.category_name,b.is_only_loan_by_app,d.package interest_package_name,b.category_lang,c.product_id,c.sub_product_code,c.sub_product_name,c.interest_type from member_credit_category a";
        $sql.=" join loan_category b on a.category_id=b.uid";
        $sql.=" join loan_sub_product c on a.sub_product_id=c.uid";
        $sql.=" left join loan_product_package d on a.interest_package_id=d.uid";
        if($is_close){
            $sql.=" where a.member_id=".qstr($member_id)." and b.is_close=0";
        }else{
            $sql.=" where a.member_id=".qstr($member_id)." and a.is_close=0 and b.is_close=0";
        }

        $r=new ormReader();
        $rows=$r->getRows($sql);
        $rows=resetArrayKey($rows,"uid");
        $m_credit=new member_creditModel();
        $main_credit=$m_credit->find(array("member_id"=>$member_id));

        foreach($rows as $k=>$item){
            if(!$item['alias']){
                $rows[$k]['alias']=$item['category_name'];
            }
            if(!$item['alias_lang']){
                $rows[$k]['alias_lang']=my_json_decode($item['category_lang']);
            }else{
                $rows[$k]['alias_lang']=my_json_decode($item['alias_lang']);
            }
            //获取利息表
            $interest_list=loan_productClass::getSizeRateByPackageId($item['interest_package_id'],$item['sub_product_id']);
            foreach($interest_list as $rate_item){
                if($rate_item['is_active']){
                    $rows[$k]['interest_rate_list'][]=$rate_item;
                }
            }
            $rows[$k]['credit_terms']=$main_credit['credit_terms'];
            $rows[$k]['expire_time']=$main_credit['expire_time'];
            $rows[$k]['grant_time']=$main_credit['grant_time'];
        }
        return $rows;
    }
    public static function getMemberCreditCategorySetting($member_id){
        $default_list=self::getAllCategoryList();
        $default_list=resetArrayKey($default_list,"uid");
        $m=new member_credit_categoryModel();
        $member_setting=$m->select(array("member_id"=>$member_id));
        $member_setting=resetArrayKey($member_setting,"category_id");
        $ret=array();
        foreach($default_list as $k=>$item){
            if($item['is_close']) continue;
            if(!$member_setting[$k]){
                $ret[$k]=array(
                    "uid"=>0,
                    "category_id"=>$item['uid'],
                    "sub_product_id"=>$item['default_product_id'],
                    "alias"=>$item['category_name'],
                    "alias_lang"=>my_json_decode($item['category_lang']),
                    "credit"=>0,
                    "credit_balance"=>0,
                    "is_one_time"=>$item['is_one_time'],
                    "interest_package_id"=>$item['interest_package_id'],
                    "is_close"=>1,
                );
            }else{
                $ret[$k]=$member_setting[$k];
                $ret[$k]['alias_lang']=my_json_decode($member_setting[$k]['alias_lang']);
            }
        }
        //格式化package_name
        $package_list=loan_productClass::getProductPackageList();
        foreach($ret as $k=>$item){
            $ret[$k]['interest_package_name']=$package_list[$item['interest_package_id']]['package']?:'Default';
        }
        //格式化sub_product_name
        $prod_list=loan_productClass::getAllActiveSubProductList();
        $prod_list=resetArrayKey($prod_list,"uid");
        foreach($ret as $k=>$item){
            $ret[$k]['sub_product_name']=$prod_list[$item['sub_product_id']]['sub_product_name']?:'Error!';
        }
        return $ret;
    }
    public static function getMemberCreditCategoryItemById($member_category_id){
        $sql="select a.*,b.category_code,b.category_name,b.category_lang,c.product_id,c.sub_product_code,c.sub_product_name,d.login_code from member_credit_category a";
        $sql.=" join loan_category b on a.category_id=b.uid";
        $sql.=" join loan_sub_product c on a.sub_product_id=c.uid";
        $sql.=" join client_member d on a.member_id=d.uid";
        $sql.=" where a.uid=".qstr($member_category_id);

        $r=new ormReader();
        $row=$r->getRow($sql);
        if(!$row['alias']){
            $row['alias']=$row['category_name'];
        }
        if(!$row['alias_lang']){
            $row['alias_lang']=my_json_decode($row['category_lang']);
        }else{
            $row['alias_lang']=my_json_decode($row['alias_lang']);
        }

        return $row;
    }

    /**
     * 根据member_credit_category.uid得到loan_sub_product.uid
     * @param $member_category_id
     */
    public static function getProductIdByMemberCreditCategoryId($member_category_id){
       $item=self::getMemberCreditCategoryItemById($member_category_id);
       return $item['sub_product_id'];
    }


    public static function getCategoryDetailInfoById($id)
    {
        $m=new loan_categoryModel();
        $info = $m->find(array(
            'uid' => $id
        ));
        if( $info ){
            $info['category_lang'] = my_json_decode($info['category_lang']);
            $info['product_description'] = urlencode($info['product_description']);
            $info['product_qualification'] = urlencode($info['product_qualification']);
            $info['product_feature'] = urlencode($info['product_feature']);
            $info['product_required'] = urlencode($info['product_required']);
            $info['product_notice'] = urlencode($info['product_notice']);
        }
/*
        $sub_product_id = $info['default_product_id'];
        $re = loan_productClass::getProductDescribeRateList($sub_product_id, 1, 100000);
*/
        $ret=loan_productClass::getCategoryDescribeRateList($info['interest_package_id'],$info['default_product_id']);
        $rate_list = $ret['list'];
        return array(
            'product_info' => $info,
            'rate_list' => $rate_list,
        );
    }

    /**
     * 传入已经取好的利息表，匹配具体金额和时间
     */
    public static function matchInterestForCategory($rate_list,$cate_info,$include_all=true){

        $credit_terms=$cate_info['credit_terms'];
        $max_days=$credit_terms*30;
        $ccy_list=array("usd","khr");
        $ret=array();
        foreach($ccy_list as $ccy){
            $new_item=array('is_matched'=>false,'list'=>array(),'msg'=>array());
            $max_amt=$cate_info['credit_'.$ccy];
            if(!$max_amt){//只需要匹配有金额的
                $new_item['is_matched']=true;
                $ret[$ccy]=$new_item;
                continue;
            }

            $no_match_size=true;
            $no_match_term=true;
            $no_match_currency=true;
            $no_terms=false;
            $no_credit=false;
            if(!$cate_info['credit_'.$ccy]){
                $no_credit=true;
            }
            if(!$credit_terms){
                $no_terms=true;
            }
            foreach($rate_list as $item){
                if($item['currency']==strtoupper($ccy)){
                    if($cate_info['is_one_time']){
                        $no_match_currency=false;
                        $same_size=false;$same_terms=false;
                        if($max_amt>=$item['loan_size_min'] && $max_amt<=$item['loan_size_max']){
                            $no_match_size=false;
                            $same_size=true;
                        }
                        if($max_days>=$item['min_term_days'] && $max_days<=$item['max_term_days']){
                            $no_match_term=false;
                            $same_terms=true;
                        }
                        if($same_size && $same_terms){
                            $item['is_matched']=true;
                            $new_item['is_matched']=true;
                        }
                    }else{
                        $item['is_matched']=true;
                        $new_item['is_matched']=true;
                    }
                    $new_item['list'][]=$item;
                }
            }
            if(!$new_item['is_matched']){
                $msg=array();
                if($no_credit){
                    $msg[]="No Set Credit";
                }else{
                    if($no_match_size){
                        $msg[]="No Matched Amount Size";
                    }
                }
                if($no_terms){
                    $msg[]="No Set Terms";
                }else{
                    if($no_match_term){
                        $msg[]="No Matched Term Size";
                    }
                }
                if($no_match_currency){
                    $msg[]="No set Interest For ".strtoupper($ccy);
                }

                $new_item['msg']=$msg;
            }
            $ret[$ccy]=$new_item;
        }

        return $ret;
    }
    /**
     * 根据产品类型获取利息匹配的类型（无抵押或软硬）, add by tim
     * @param $member_credit_category_id
     * @grant_id 因为committee和counter要查看的条件不同,committee会根据grant_id查
     */
    static function getCategoryInterestTypeByGrant($member_credit_category_id,$grant_id=0){
        //根据member_credit_category_id获取member-id，然后获取
        $m_cc=new member_credit_categoryModel();
        $row_cc=$m_cc->find($member_credit_category_id);
        if(!$row_cc){
            return 1;
        }
        $member_id=$row_cc['member_id'];
        $m_cg=new member_credit_grantModel();
        if(!$grant_id){
            $row_cg=$m_cg->orderBy("uid desc")->find(array("member_id"=>$member_id,"state"=>commonApproveStateEnum::PASS));
            $grant_id=$row_cg['uid'];
            if(!$grant_id) return 1;
        }
        $sql="SELECT min(CASE WHEN b.asset_cert_type='soft' THEN 2 ELSE 3 END) cert_type FROM member_credit_grant_assets a ";
        $sql.="INNER JOIN member_assets b ON a.`member_asset_id`=b.`uid`";
        $sql.="WHERE a.`grant_id`=".qstr($grant_id);
        $row_cert=$m_cc->reader->getRow($sql);
        if(!$row_cert){
            return 1;//no mortgage;
        }else{
            return $row_cert['cert_type'];//2 mortgage-soft,3=>mortgage-hard
        }
    }
    //todo 根据建议获取利息类型
    static function getCategoryInterestTypeBySuggest($member_credit_category_id,$suggest_id=0){

    }

}