<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/6
 * Time: 9:54
 */
class member_authorized_contractModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('member_authorized_contract');
    }


    /** 生成合同号
     * @return string
     */
    public function generateAuthorizedContractSn()
    {
        // 序号
        /*
        $day = date('Y-m-d');
        $sql = "select count(*) from member_authorized_contract where date_format(create_time,'%Y-%m-%d')";
        $num = $this->reader->getOne($sql);
        $num = $num ? $num : 0;
        return $day .'-'. str_pad($num + 1, 6, '0', STR_PAD_LEFT);
        */

        $ym = date("Ym");
        $gen = new core_gen_idModel();
        $id_key = "authorize_contract_" . $ym;
        $new_id = $gen->genId($id_key);
        $new_id = $ym . str_pad($new_id, 6, 0, STR_PAD_LEFT);
        return $new_id;
    }

    /**
     * 获取合同基本信息
     */
    public function getConstructBaseInfo($uid)
    {
        $sql = "select c.*,m.obj_guid,m.login_code from member_authorized_contract c left join client_member m on c.member_id = m.uid where c.uid = '$uid'";
        $info = $this->reader->getRow($sql);
        return $info;
    }

    /**
     * 获取合同图片
     */
    public function getConstructImages($uid)
    {
        $sql = "select image_path from member_authorized_contract_image where authorized_contract_id = '$uid'";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * 获取合同抵押物
     */
    public function getConstructMortgages($contract_no)
    {
        $sql = "select m.*,a.asset_type,a.asset_name,a.asset_cert_type,a.asset_sn from member_asset_mortgage m "
            . " left join member_assets a on m.member_asset_id = a.uid"
            . " where m.contract_no  = '$contract_no'";
        $list = $this->reader->getRows($sql);
        $list = resetArrayKey($list, "uid");
        $id_list = array_keys($list);
        $str_ids = implode("','", $id_list);
        $sql = "select * from member_asset_mortgage_image where asset_mortgage_id in ('" . $str_ids . "')";
        $img_list = $this->reader->getRows($sql);
        foreach ($list as $k => $item) {
            foreach ($img_list as $img) {
                if ($img['asset_mortgage_id'] == $k) {
                    $item['image_path'][] = $img['image_path'];
                }
            }

            // 资产拥有人列表
            $sql = "select * from member_assets_owner where member_asset_id=" . qstr($item['member_asset_id']) . " order by relative_id asc ";
            $relative_list = $this->reader->getRows($sql);
            $item['relative_list'] = $relative_list;

            $list[$k] = $item;
        }

        return $list;
    }

    /**
     * 获取合同基本信息
     */
    public function getConstructInfoByUid($uid)
    {
        $sql = "select * from member_authorized_contract where grant_credit_id = '" . $uid . "' order by uid asc limit 1;";
        $info = $this->reader->getRow($sql);
        return $info;
    }

    /**
     * 获取合同基本信息
     */
    public function getConstructInfoByMemberId($member_id)
    {
        $sql = "select * from member_authorized_contract where member_id = '$member_id' order by uid desc";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * 获取合同基本信息
     */
    public function getConstructListByOfficerId($officer_id, $pageNumber, $pageSize)
    {
        $sql = "select c.*,m.obj_guid,m.login_code from member_authorized_contract c left join client_member m on c.member_id = m.uid where c.officer_id = '$officer_id' order by uid desc";
        $list = $this->reader->getPage($sql, $pageNumber, $pageSize);
        return $list;
    }

    /**
     * 获取合同基本信息
     * @param $grant_id
     * @return ormCollection
     */
    public function getConstructListByGrantId($grant_id)
    {
        $grant_id = intval($grant_id);
        $sql = "select mac.*,sb.branch_name from member_authorized_contract mac LEFT JOIN site_branch sb ON mac.branch_id = sb.uid where mac.grant_credit_id = $grant_id and state = 100 order by uid desc";
        $list = $this->reader->getRows($sql);
        foreach ($list as $k => $v) {
            $images = $this->getConstructImages($v['uid']);
            $list[$k]['images'] = $images;

            $sql = "select m.*,i.image_path,a.asset_type,a.asset_name from member_asset_mortgage m left join member_asset_mortgage_image i on m.uid =  i.asset_mortgage_id left join member_assets a on m.member_asset_id = a.uid where m.contract_type = 0 AND m.contract_no = " . qstr($v['contract_no']);
            $mortgage_list = $this->reader->getRows($sql);
            $mortgage_list_new = array();
            foreach ($mortgage_list as $row) {
                if ($mortgage_list_new[$row['uid']]) {
                    $mortgage_list_new[$row['uid']]['images'][] = $row['image_path'];
                } else {
                    $mortgage_list_new[$row['uid']] = $row;
                    $mortgage_list_new[$row['uid']]['images'][] = $row['image_path'];
                }
            }
            $list[$k]['mortgage_list'] = $mortgage_list_new;
        }

        return $list;
    }


    /** 获取member合计应该支付的授权合同费用
     * @param $member_id
     * @return int
     */
    public function getTotalPendingPayFeeOfMember($member_id)
    {
        $member_id = intval($member_id);
        $sql = "select sum(fee) cnt from member_authorized_contract where member_id='$member_id' and is_paid='0'
        and state>".qstr(authorizedContractStateEnum::CREATE);
        $authorized_fee = $this->reader->getOne($sql);
        $authorized_fee = $authorized_fee ?: 0;
        return round($authorized_fee, 2);
    }


    public function getAllPendingPayContractFeeByBalance()
    {
        $sql = "select * from member_authorized_contract where is_paid='0' and fee >0 and 
        payment_way='" . repaymentWayEnum::PASSBOOK . "' and state> ".qstr(authorizedContractStateEnum::CREATE);
        return $this->reader->getRows($sql);
    }

}