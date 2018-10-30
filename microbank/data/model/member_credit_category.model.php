<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/30
 * Time: 10:25
 */
class member_credit_categoryModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('member_credit_category');
    }

    public function updateMemberState($p){
        return $this->updateMemberCategoryState($p);
    }


    public function updateMemberCategoryState($p)
    {
        $row = $this->getRow(array('member_id' => $p['member_id'], 'category_id' => $p['category_id']));
        if (!$row) {
            return new result(false, 'Invalid params.');
        }
        $row->update_time = Now();
        $row->update_operator_id = $p['officer_id'];
        $row->is_close = $p['is_close'];
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Change Successful.');
        } else {
            return new result(false, 'Change Failed.');
        }
    }


    public function editMemberLoanCategory($p)
    {
        $m_member_category = $this;
        $member_id = intval($p['member_id']);
        $category_id = intval($p['category_id']);
        $info = $m_member_category->getRow(array('member_id' => $member_id, 'category_id' => $category_id));
        if ($info) { //存在则更改is_close
            $officer = M('um_user')->getRow($p['officer_id']);
            if (!$officer) {
                return new result(false, 'Invalid operator', null, errorCodesEnum::USER_NOT_EXISTS);
            }
            $info = M('loan_category')->find($p['category_id']);
            if (!$info) {
                return new result(false, 'Category user does not exist.');
            }
            $row = $info;
            $row->sub_product_id = $info['default_product_id'];
            //$row->alias = $info['category_name'];
            //$row->alias_lang = $info['category_lang'];
            $row->is_one_time=intval($info['is_one_time']);
            $row->interest_package_id=intval($info['interest_package_id']);
            $row->creator_id = $officer['uid'];
            $row->creator_name = $officer['user_name'];
            $row->create_time = Now();
            //$row->is_close = 0;
            $rt = $row->insert();
            if ($rt->STS) {
                return new result(true, 'Change Successful.');
            } else {
                return new result(false, 'Change Failed.');
            }

        } else {//否则添加
            return $this->addMemberCategory($p);
        }

    }



    public function addMemberCategory($p){
        $officer = M('um_user')->getRow($p['officer_id']);
        if (!$officer) {
            return new result(false, 'Invalid operator', null, errorCodesEnum::USER_NOT_EXISTS);
        }
        $info = M('loan_category')->find($p['category_id']);
        if (!$info) {
            return new result(false, 'Category user does not exist.');
        }
        $row = $this->newRow();
        $row->member_id = $p['member_id'];
        $row->category_id = $info['uid'];
        $row->sub_product_id = $info['default_product_id'];
        $row->alias = $info['category_name'];
        $row->alias_lang = $info['category_lang'];
        $row->is_one_time=intval($info['is_one_time']);
        $row->interest_package_id=intval($info['interest_package_id']);
        $row->creator_id = $officer['uid'];
        $row->creator_name = $officer['user_name'];
        $row->create_time = Now();
        $row->is_close = 0;
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Change Successful.',$row->uid);
        } else {
            return new result(false, 'Change Failed.');
        }
    }



    public function getMortgagedAssetListByCategoryId($id,$grant_id)
    {
        $sql = "select a.* from member_credit_grant_assets ga inner join member_assets a on ga.member_asset_id=a.uid 
        where ga.grant_id=".qstr($grant_id)." and ga.member_credit_category_id=".qstr($id)." and a.mortgage_state='1' 
        and a.asset_state>=".qstr(assetStateEnum::CERTIFIED);
        return $this->reader->getRows($sql);

    }


    public function getLoanCategoryInfoByMemberCategoryId($member_category_id)
    {
        $member_category_id = intval($member_category_id);
        $sql = "select lc.* from member_credit_category mcc INNER JOIN loan_category lc  on mcc.category_id=lc.uid 
        where mcc.uid=".qstr($member_category_id);
        return $this->reader->getRow($sql);
    }
}