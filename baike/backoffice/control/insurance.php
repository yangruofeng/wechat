<?php

class insuranceControl extends back_office_baseControl{
    public function __construct(){
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "User List");
        Tpl::setDir("insurance");
    }

    public function productOp(){
      //$this->addProductOp();
      $r = new ormReader();
      $sql1 = "select product_id,count(uid) as count from insurance_product_item GROUP BY product_id";
      $item = $r->getRows($sql1);
      $item_count = array();
      foreach ($item as $key => $value) {
        $item_count[$value['product_id']] = $value['count'];
      }
      $sql = "SELECT MAX(uid) uid FROM insurance_product WHERE state < 40 GROUP BY product_key";
      $product_ids = $r->getRows($sql);
      if ($product_ids) {
        $product_ids = array_column($product_ids, 'uid');
        $sql = "SELECT * FROM insurance_product WHERE uid IN (" . implode(',', $product_ids) . ") ORDER BY uid DESC";
        $product_list = $r->getRows($sql);
        $sql1 = "SELECT uid,account_id,product_id,product_item_id,start_insured_amount,price FROM insurance_contract";
        $contracts = $r->getRows($sql1);
        $contract_list = array();
        foreach ($contracts as $key => $value) {
          if($contract_list[$value['product_id']]){
            $contract_list[$value['product_id']]['count'] += 1;
            if(!in_array($value['account_id'], $contract_list[$value['product_id']]['accounts'])){
              array_push($contract_list[$value['product_id']]['accounts'], $value['account_id']);
            }
            $contract_list[$value['product_id']]['start_insured_amount'] += $value['start_insured_amount'];
            $contract_list[$value['product_id']]['price'] += $value['price'];
          }else{
            $contract_list[$value['product_id']]['count'] = 1;
            $contract_list[$value['product_id']]['accounts'] = array();
            array_push($contract_list[$value['product_id']]['accounts'], $value['account_id']);
            $contract_list[$value['product_id']]['start_insured_amount'] = $value['start_insured_amount'];
            $contract_list[$value['product_id']]['price'] = $value['price'];
          }
        }
      }else{
        $product_list = array();
      }

      Tpl::output("list", $product_list);
      Tpl::output("contract_list", $contract_list);
      Tpl::output("item_count", $item_count);
      Tpl::showPage("product");
    }

    public function addProductOp(){
      //$_GET['uid'] = 17;
      $m_insurance_product = M('insurance_product');
      $insurance_product_item = M('insurance_product_item');
      $item = $m_insurance_product->getRow(array('uid' => $_GET['uid']));
      $items = $insurance_product_item->getRows(array('product_id' => $_GET['uid']));
      $items = $items->toArray();
      Tpl::output("item", $item);
      Tpl::output("items", $items);
      $r = new ormReader();
      $sql = "select uid,product_code,product_name from loan_product where state = ".loanProductStateEnum::ACTIVE;
      $loan_product = $r->getRows($sql);
      Tpl::output("loan_product", $loan_product);
      Tpl::showPage('product.add');
    }

    /**
     * 保存产品主要信息
     * @param $p
     * @return result
     */
    public function insertProductMainOp($p){
      $p['creator_id'] = $this->user_id;
      $p['creator_name'] = $this->user_name;
      $class_product = new insurance_productClass();
      $rt = $class_product->insertInsuranceProductMain($p);
      return $rt;
    }

    public function editProductOp($p){
      $class_product = new insurance_productClass();
      $rt = $class_product->updateInsuranceProductMain($p);
      return $rt;
    }

    public function insertProductItemOp($p){
      $class_product = new insurance_productClass();
      $p['creator_id'] = $this->user_id;
      $p['creator_name'] = $this->user_name;
      $rt = $class_product->insertInsuranceProductItem($p);
      return $rt;
    }

    public function contractOp(){
      Tpl::showPage("contract");
    }

    public function getContractListOp($p){
        $r = new ormReader();
        $sql = "SELECT contract.*,account.obj_guid,member.uid as member_id,member.display_name,member.alias_name,member.phone_id,member.email FROM insurance_contract as contract"
            . " inner join insurance_account as account on contract.account_id = account.uid"
            . " left join client_member as member on account.obj_guid = member.obj_guid where 1 = 1 ";
        if ($p['item']) {
            $sql .= " and contract.contract_sn = " . $p['item'];
        }
        if ($p['member_name']) {
            $name = ' and member.display_name like "%' . $p['member_name'] . '%"';
        }
        if ($p['state'] > -1) {
            $sql .= " and contract.state = " . $p['state'];
        }
        $sql .= " ORDER BY contract.create_time desc";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
        Tpl::showPage("contract.list");
    }

    public function contractDetailOp(){
      $p = array_merge(array(), $_GET, $_POST);
      if ($p['uid']) {
          $r = new ormReader();
          $sql = "SELECT contract.*,account.obj_guid,account.account_type FROM insurance_contract as contract"
              . " inner join insurance_account as account on contract.account_id = account.uid where contract.uid = " . $p['uid'];
          $data = $r->getRow($sql);
          $sql1 = "select * from insurance_product where uid = ".$data['product_id'];
          $product = $r->getRow($sql1);
          $sql2 = "select * from insurance_contract_beneficiary where contract_id = ".$data['uid'];
          $beneficiary = $r->getRows($sql2);
          $sql3 = "SELECT account.*,member.uid as member_id,member.display_name,member.alias_name,member.phone_id,member.email FROM client_member as member"
              . " inner join loan_account as account on account.obj_guid = member.obj_guid where member.obj_guid = " . $data['obj_guid'];
          $member = $r->getRow($sql3);
      }
      Tpl::output("detail", $data);
      Tpl::output("product", $product);
      Tpl::output("beneficiary", $beneficiary);
      Tpl::output("member", $member);
      Tpl::showPage("contract.detail");
    }

    /**
     * 发布产品
     * @param $p
     * @return result
     */
    public function releaseProductOp($p){
        $uid = intval($p['uid']);
        $class_product = new insurance_productClass();
        $rt = $class_product->changeProductState($uid, 20);
        if ($rt->STS) {
            return new result(true, 'Release Successful!');
        } else {
            return new result(false, 'Release Failure!');
        }
    }

    /**
     * 产品下架
     * @param $p
     * @return result
     */
    public function unShelveProductOp($p){
        $uid = intval($p['uid']);
        $class_product = new insurance_productClass();
        $rt = $class_product->changeProductState($uid, 30);
        if ($rt->STS) {
            return new result(true, 'Inactive Successful!');
        } else {
            return new result(false, 'Inactive Failure!');
        }
    }

    /**
     * 系列历史版本
     */
    public function showProductHistoryOp(){
      $r = new ormReader();
      $uid = intval($_REQUEST['uid']);
      $m_product = M('insurance_product');
      $row = $m_product->getRow($uid);
      if (!$row) {
        showMessage('Invalid Id!');
      }
      $product_history = $m_product->orderBy('uid DESC')->select(array('product_key' => $row['product_key']));
      $sql1 = "SELECT uid,account_id,product_id,product_item_id,start_insured_amount,price FROM insurance_contract";
      $contracts = $r->getRows($sql1);
      $contract_list = array();
      foreach ($contracts as $key => $value) {
        if($contract_list[$value['product_id']]){
          $contract_list[$value['product_id']]['count'] += 1;
          if(!in_array($value['account_id'], $contract_list[$value['product_id']]['accounts'])){
            array_push($contract_list[$value['product_id']]['accounts'], $value['account_id']);
          }
          $contract_list[$value['product_id']]['start_insured_amount'] += $value['start_insured_amount'];
          $contract_list[$value['product_id']]['price'] += $value['price'];
        }else{
          $contract_list[$value['product_id']]['count'] = 1;
          $contract_list[$value['product_id']]['accounts'] = array();
          array_push($contract_list[$value['product_id']]['accounts'], $value['account_id']);
          $contract_list[$value['product_id']]['start_insured_amount'] = $value['start_insured_amount'];
          $contract_list[$value['product_id']]['price'] = $value['price'];
        }
      }
      $sql = "select product_id,count(uid) as count from insurance_product_item GROUP BY product_id";
      $item = $r->getRows($sql);
      $item_count = array();
      foreach ($item as $key => $value) {
        $item_count[$value['product_id']] = $value['count'];
      }
      Tpl::output("item_count", $item_count);
      Tpl::output("product_history", $product_history);
      Tpl::output("contract_list", $contract_list);
      Tpl::showPage("product.history");
    }

    /**
     * 展示产品信息
     */
    public function showProductOp(){
      $m_insurance_product = M('insurance_product');
      $insurance_product_item = M('insurance_product_item');
      $item = $m_insurance_product->getRow(array('uid' => $_GET['uid']));
      $items = $insurance_product_item->getRows(array('product_id' => $_GET['uid']));
      $items = $items->toArray();
      Tpl::output("item", $item);
      Tpl::output("items", $items);
      $r = new ormReader();
      $sql = "select uid,product_code,product_name from loan_product where state = ".loanProductStateEnum::ACTIVE;
      $loan_product = $r->getRows($sql);
      Tpl::output("loan_product", $loan_product);
      Tpl::showPage('product.info');
    }
}
