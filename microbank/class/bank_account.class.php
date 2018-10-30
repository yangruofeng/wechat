<?php

class bank_accountClass {

    public static function getGUID($accountId,$return_account=false) {
        $account_model = new site_bankModel();
        $account_info = $account_model->getRow($accountId);
        if (!$account_info) throw new Exception("Bank account $accountId not found");

        if (!$account_info->obj_guid) {
            $account_info->obj_guid = generateGuid($account_info->uid, objGuidTypeEnum::BANK_ACCOUNT);
            $ret = $account_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for bank account failed - " . $ret->MSG);
            }
        }
        if($return_account){
            return $account_info->toArray();
        }else{
            return $account_info->obj_guid;
        }
    }


    public static function getHQPublicBankList()
    {
        $m  = new site_bankModel();

        $rows = $m->getRows(array(
            'is_private' => 0,
            'allow_client_deposit' => 1,
            'account_state' => 1,
            'branch_id' => 0   // todo 是否更换标记的方式
        ));
        return $rows->toArray();
    }

    public static function getHQBillPayBankList()
    {
        $m = new site_bankModel();
        $rows = $m->orderBy('bank_name asc')->select(array(
            'is_private' => 0,
            //'allow_client_deposit' => 1,
            'account_state' => 1,
            'branch_id' => 0,   // todo 是否更换标记的方式
            'is_allow_billpay' => 1
        ));
        return $rows;
    }

    /** 12-15位
     * billcode的规则（分行id三位数字）（产品id三位数字）（客户id6位数字）（后面三位数字保证不重复），总共15位
     * @param $branch_id
     * @param $sub_product_id
     * @param $member_id
     */
    public static function generateBillPayCode($branch_id,$loan_category_id,$member_id)
    {
        $branch_id = intval($branch_id);
        $loan_category_id = intval($loan_category_id);

        $branch_id = str_pad($branch_id,3,0,STR_PAD_LEFT);
        $loan_category_id = str_pad($loan_category_id,2,0,STR_PAD_LEFT);
        $member_id = str_pad($member_id,6,0,STR_PAD_LEFT);
        $key = 'billpay_code_'.$branch_id.$loan_category_id.$member_id;
        $m = new core_gen_idModel();
        $num = $m->genId($key);
        $num = str_pad($num,3,0,STR_PAD_LEFT);
        return $branch_id.$loan_category_id.$member_id.$num;
    }

    public static function getBillPayCodeByContractSn($sn)
    {
        $sn = rtrim($sn,'-X');
        // 去掉最后的序号
        $arr = explode('-',$sn);
        array_pop($arr);
        return implode('',$arr);
    }

}