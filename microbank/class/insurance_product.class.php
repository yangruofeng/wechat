<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class insurance_productClass
{



  /**
   * 重新创建temporary 产品
   * @param $uid
   * @return result
   */
  private function copyTemporaryProduct($uid)
  {
      $product = M('insurance_product');
      $product_item = M('insurance_product_item');
      $item_info = $product_item->find(array('uid' => $uid));
      $item_list = $product_item->select(array('product_id' => $item_info['product_id']));
      if (empty($item_info)) {
          return new result(false, 'Invalid Id!');
      }
      $product_info = $product->find(array('uid' => $item_info['product_id']));
      if (empty($product_info)) {
          return new result(false, 'Invalid Id!');
      }
      $product_key = $product_info['product_key'];
      $item_product_key = $item_info['product_key'];
      $chk_temporary = $product->find(array('product_key' => $product_key, 'state' => 10));
      if ($chk_temporary) {
          return new result(true, '', array('is_copy' => false, 'uid' => $uid));
      }
      $conn = ormYo::Conn();
      $conn->startTransaction();
      try {
          //更新product_item
          $item_row = $product_item->newRow($item_info);
          $rt = $item_row->insert();
          if (!$rt->STS) {
              $conn->rollback();
              return new result(false, 'Failed!--' . $rt->MSG);
          }

          //更新product
          $product_row = $product->newRow($product_info);
          $product_row->state = 10;
          $product_row->update_time = Now();
          $rt_1 = $product_row->insert();
          if (!$rt_1->STS) {
              $conn->rollback();
              return new result(false, 'Failed!--' . $rt_1->MSG);
          }
          $pid = $rt_1->AUTO_ID;
          $item_list = $product_item->select(array('product_id' => $item_info['product_id']));

          // 单一语句执行，循环执行速度超鸡慢
          $field_array = array(
              'product_id',
              'item_code',
              'item_name',
              'is_fixed_amount',
              'fixed_amount',
              'bind_type',
              'is_fixed_price',
              'fixed_price',
              'price_rate',
              'is_fixed_valid_days',
              'fixed_valid_days',
              'product_key',
          );
          $insert_sql = "insert into insurance_product_item(".join(',',$field_array).") values  ";
          $sql_array = array();
          foreach( $item_list as $value ){
              // 严格按照上面定义的字段插入顺序
              $temp = array(
                  'product_id' => $item_info['product_id'],
                  'item_code' => $value['item_code'],
                  'item_name' => $value['item_name'],
                  'is_fixed_amount' => $value['is_fixed_amount'],
                  'fixed_amount' => $value['fixed_amount'],
                  'bind_type' => $value['bind_type'],
                  'is_fixed_price' => $value['is_fixed_price'],
                  'fixed_price' => $value['fixed_price'],
                  'price_rate' => $value['price_rate'],
                  'is_fixed_valid_days' => $value['is_fixed_valid_days'],
                  'fixed_valid_days' => $value['is_fixed_valid_days'],
                  'product_key' => $value['product_key']
              );
              $str = "( '".$temp['product_id']."',";
              $str .= "'".$temp['item_code']."',";
              $str .= "'".$temp['item_name']."',";
              $str .= "'".$temp['is_fixed_amount']."',";
              $str .= "'".$temp['fixed_amount']."',";
              $str .= "'".$temp['bind_type']."',";
              $str .= "'".$temp['is_fixed_price']."',";
              $str .= "'".$temp['fixed_price']."',";
              $str .= "'".$temp['price_rate']."',";
              $str .= "'".$temp['is_fixed_valid_days']."',";
              $str .= "'".$temp['fixed_valid_days']."',";
              $str .= "'".$temp['product_key']."' )";

              $sql_array[] = $str;
              $new_payment_schema[] = $temp;
          }

          // 拼接sql
          $insert_sql .= trim(join(',',$sql_array),',');
          $re = $product_item->conn->execute($insert_sql);
          if( !$re->STS ){
              $conn->rollback();
              return new result(false,'Insert product item fail: '.$re->MSG,null,errorCodesEnum::DB_ERROR);
          }

          $conn->submitTransaction();
          $uid = $rt_1->AUTO_ID;
          return new result(true, '', array('is_copy' => true, 'uid' => $uid, 'size_rate_map' => $size_rate_map));
      } catch (Exception $ex) {
          $conn->rollback();
          return new result(false, $ex->getMessage());
      }
  }
    /**
     * 保存保险产品主要信息
     * @param $p
     * @return result
     */
    public function insertInsuranceProductMain($p){
      $uid = trim($p['uid']);
      $product_name = trim($p['product_name']);
      $product_code = trim($p['product_code']);
      $product_state = trim($p['product_state']);
      $creator_id = intval($p['creator_id']);
      $creator_name = trim($p['creator_name']);

      if (empty($product_name)) {
          return new result(false, 'Name cannot be empty!');
      }
      if (empty($product_code)) {
          return new result(false, 'Code cannot be empty!');
      }

      $m_insurance_product = M('insurance_product');
      $chk_code = $m_insurance_product->find(array('product_code' => $product_code));
      if (!$uid && $chk_code) {
          return new result(false, 'Code Exist!');
      }
      if($uid){
        $row = $m_insurance_product->getRow(array('uid' => $uid));
        $row->product_code = $product_code;
        $row->product_name = $product_name;
        $row->product_state = $product_state;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Update Successful!', $row);
        } else {
            return new result(false, 'Update Failure!');
        }
      }else{
        $row = $m_insurance_product->newRow();
        $row->product_code = $product_code;
        $row->product_name = $product_name;
        $row->product_state = $product_state;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->create_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Save Successful!', $row);
        } else {
            return new result(false, 'Save Failure!');
        }
      }
    }

    public function updateInsuranceProductMain($p){
      $uid = trim($p['uid']);
      $val = trim($p['val']);
      $m_insurance_product = M('insurance_product');
      $row = $m_insurance_product->getRow(array('uid' => $uid));
      if (!$row) {
          return new result(false, 'Invalid Id!');
      }

      $row->$p['filed'] = $val;
      $row->update_time = Now();
      $rt = $row->update();
      if ($rt->STS) {
          return new result(true, 'Save Successful!');
      } else {
          return new result(false, 'Save Failure!');
      }
    }

    public function insertInsuranceProductItem($p){
      $itemid = trim($p['itemid']);
      $product_id = trim($p['product_id']);
      $item_code = trim($p['item_code']);
      $item_name = trim($p['item_name']);
      $is_fixed_amount = trim($p['is_fixed_amount']);
      $fixed_amount = trim($p['fixed_amount'])?:0;
      $loan_product_ids = trim($p['productids']);
      $is_fixed_price = trim($p['is_fixed_price']);
      $fixed_price = trim($p['fixed_price'])?:0;
      $price_rate = round(trim($p['price_rate']),3)/100;
      $is_fixed_valid_days = trim($p['is_fixed_valid_days']);
      $fixed_valid_days = trim($p['fixed_valid_days'])?:0;
      $creator_id = $p['creator_id'];
      $creator_name = $p['creator_name'];

      if (empty($item_code)) {
        return new result(false, 'Name cannot be empty!');
      }
      if (empty($item_name)) {
        return new result(false, 'Code cannot be empty!');
      }
      $product = M('insurance_product');
      $product_item = M('insurance_product_item');
      $product_relationship = M('insurance_product_relationship');

      if($itemid){
        $item_info = $product_item->find(array('uid' => $itemid));
        $item_list = $product_item->select(array('product_id' => $item_info['product_id']));
        if (empty($item_info)) {
            return new result(false, 'Invalid Id!');
        }
        $product_info = $product->find(array('uid' => $item_info['product_id']));
        if (empty($product_info)) {
            return new result(false, 'Invalid Id!');
        }
        $product_key = $product_info['product_key'];
        $item_product_key = $item_info['product_key'];
        $chk_temporary = $product->find(array('uid' => $item_info['product_id'], 'state' => 10));
        //状态为temp直接修改当前item
        if ($chk_temporary) {
          $conn = ormYo::Conn();
          $conn->startTransaction();
          $bind_type = $loan_product_ids > 0 ? 1 : 0;
          $row = $product_item->getRow(array('uid' => $itemid));
          //$chk_code = $product_item->find(array('item_code' => $item_code, 'uid' => array('neq', $itemid), 'product_key' => array('eq', $row['product_key'])));
          //if ($chk_code) {
          //    return new result(false, 'Code Exist!');
          //}
          try {
              $row->item_code = $item_code;
              $row->item_name = $item_name;
              $row->is_fixed_amount = $is_fixed_amount;
              $row->fixed_amount = $fixed_amount;
              $row->bind_type = $bind_type;
              $row->loan_product_ids = $loan_product_ids;
              $row->is_fixed_price = $is_fixed_price;
              $row->fixed_price = $fixed_price;
              $row->price_rate = $price_rate;
              $row->is_fixed_valid_days = $is_fixed_valid_days;
              $row->fixed_valid_days = $fixed_valid_days;
              $rt = $row->update();
              if (!$rt->STS) {
                  $conn->rollback();
                  return new result(false, 'Update Failure!');
              }
              //更新产品表
              $row1 = $product->getRow(array('uid' => $item_info['product_id']));
              $row1->update_time = Now();
              $rt3 = $row1->update();
              if (!$rt3->STS) {
                  $conn->rollback();
                  return new result(false, 'Update Failure11!' . $rt3->MSG);
              }
              //删除产品联系
              if($item_info['loan_product_ids']){
                $sql = "select uid from insurance_product_relationship where loan_product_id in (".$item_info['loan_product_ids'].") and insurance_product_item_id = ".$itemid;
                $ret = $product_relationship->conn->execute($sql);
                $ids = array_column($ret->RESULT,'uid');
                if($ids){
                  $sql1 = "DELETE  FROM insurance_product_relationship where uid in (".implode(',', $ids).")";
                  $ret1 = $product_relationship->conn->execute($sql1);
                  if(!$ret1->STS){
                    $conn->rollback();
                    return new result(false, $sql1 . $ret1->MSG);
                  }
                }
              }

              //添加联系表
              if($bind_type){
                // 单一语句执行，循环执行速度超鸡慢
                $field_array = array(
                    'loan_product_id',
                    'insurance_product_item_id'
                );
                $insert_sql = "insert into insurance_product_relationship(".join(',',$field_array).") values  ";
                $sql_array = array();
                $arr = explode(',', $loan_product_ids);
                foreach( $arr as $value ){
                    // 严格按照上面定义的字段插入顺序
                    $temp = array(
                        'loan_product_id' => $value,
                        'insurance_product_item_id' => $itemid
                    );
                    $str = "( '".$temp['loan_product_id']."',";
                    $str .= "'".$temp['insurance_product_item_id']."' )";

                    $sql_array[] = $str;
                }
                // 拼接sql
                $insert_sql .= trim(join(',',$sql_array),',');
                $re = $product_relationship->conn->execute($insert_sql);
                if( !$re->STS ){
                    $conn->rollback();
                    return new result(false,'Update product item fail: '.$re->MSG,null,errorCodesEnum::DB_ERROR);
                }
              }
              $conn->submitTransaction();
              return new result(true, 'Update Successful!', $row);
          } catch (Exception $ex) {
              $conn->rollback();
              return new result(false, $ex->getMessage());
          }
        }else{
          $conn = ormYo::Conn();
          $conn->startTransaction();
          $bind_type = $loan_product_ids > 0 ? 1 : 0;
          try {
              //1、copy新的product
              $product_row = $product->newRow($product_info);
              $product_row->state = 10;
              $product_row->creator_id = $creator_id;
              $product_row->creator_name = $creator_name;
              $product_row->create_time = Now();
              $product_row->update_time = Now();
              $rt_1 = $product_row->insert();
              if (!$rt_1->STS) {
                  $conn->rollback();
                  return new result(false, 'Failed!--' . $rt_1->MSG);
              }
              $pid = $rt_1->AUTO_ID;

              //2、copy当前product_item
              $item_row = $product_item->newRow($item_info);
              $item_row->product_id = $pid;
              $item_row->item_code = $item_code;
              $item_row->item_name = $item_name;
              $item_row->is_fixed_amount = $is_fixed_amount;
              $item_row->fixed_amount = $fixed_amount;
              $item_row->bind_type = $bind_type;
              $item_row->loan_product_ids = $loan_product_ids;
              $item_row->is_fixed_price = $is_fixed_price;
              $item_row->fixed_price = $fixed_price;
              $item_row->price_rate = $price_rate;
              $item_row->is_fixed_valid_days = $is_fixed_valid_days;
              $item_row->fixed_valid_days = $fixed_valid_days;
              $rt = $item_row->insert();
              if (!$rt->STS) {
                  $conn->rollback();
                  return new result(false, 'Failed!--' . $rt->MSG);
              }
              $new_item_uid = $rt->AUTO_ID;
              //3、copy当前product下的所有item
              $item_list = $product_item->select(array('product_id' => $item_info['product_id'], 'uid' => array('neq',$itemid)));

              if( count($item_list) > 0 ){
                  // 单一语句执行，循环执行速度超鸡慢
                  $field_array = array(
                      'product_id',
                      'item_code',
                      'item_name',
                      'is_fixed_amount',
                      'fixed_amount',
                      'bind_type',
                      'loan_product_ids',
                      'is_fixed_price',
                      'fixed_price',
                      'price_rate',
                      'is_fixed_valid_days',
                      'fixed_valid_days',
                      'product_key',
                  );
                  $insert_sql = "insert into insurance_product_item(".join(',',$field_array).") values  ";
                  $sql_array = array();
                  foreach( $item_list as $value ){
                      // 严格按照上面定义的字段插入顺序
                      $temp = array(
                          'product_id' => $pid,
                          'item_code' => $value['item_code'],
                          'item_name' => $value['item_name'],
                          'is_fixed_amount' => $value['is_fixed_amount'],
                          'fixed_amount' => $value['fixed_amount'],
                          'bind_type' => $value['bind_type'],
                          'loan_product_ids' => $value['loan_product_ids'],
                          'is_fixed_price' => $value['is_fixed_price'],
                          'fixed_price' => $value['fixed_price'],
                          'price_rate' => $value['price_rate'],
                          'is_fixed_valid_days' => $value['is_fixed_valid_days'],
                          'fixed_valid_days' => $value['is_fixed_valid_days'],
                          'product_key' => $value['product_key']
                      );
                      $str = "( '".$temp['product_id']."',";
                      $str .= "'".$temp['item_code']."',";
                      $str .= "'".$temp['item_name']."',";
                      $str .= "'".$temp['is_fixed_amount']."',";
                      $str .= "'".$temp['fixed_amount']."',";
                      $str .= "'".$temp['bind_type']."',";
                      $str .= "'".$temp['loan_product_ids']."',";
                      $str .= "'".$temp['is_fixed_price']."',";
                      $str .= "'".$temp['fixed_price']."',";
                      $str .= "'".$temp['price_rate']."',";
                      $str .= "'".$temp['is_fixed_valid_days']."',";
                      $str .= "'".$temp['fixed_valid_days']."',";
                      $str .= "'".$temp['product_key']."' )";

                      $sql_array[] = $str;
                  }
                  // 拼接sql
                  $insert_sql .= trim(join(',',$sql_array),',');
                  $re = $product_item->conn->execute($insert_sql);
                  if( !$re->STS ){
                      $conn->rollback();
                      return new result(false,'Insert product item fail: '.$re->MSG,null,errorCodesEnum::DB_ERROR);
                  }
              }


              //4、删除产品联系
              /*$relationship_del = $product_relationship->getRow(array('loan_product_id' => $item_info['bind_type'], 'insurance_product_item_id' => $itemid));
              if($relationship_del){
                $rt2 = $relationship_del->delete();
                if (!$rt2->STS) {
                    $conn->rollback();
                    return new result(false, 'Update Failure!' . $rt2->MSG);
                }
              }*/
              //4、添加产品联系
              if($bind_type){
                // 单一语句执行，循环执行速度超鸡慢
                $field_array = array(
                    'loan_product_id',
                    'insurance_product_item_id'
                );
                $insert_sql = "insert into insurance_product_relationship(".join(',',$field_array).") values  ";
                $sql_array = array();
                $arr = explode(',', $loan_product_ids);
                foreach( $arr as $value ){
                    // 严格按照上面定义的字段插入顺序
                    $temp = array(
                        'loan_product_id' => $value,
                        'insurance_product_item_id' => $new_item_uid
                    );
                    $str = "( '".$temp['loan_product_id']."',";
                    $str .= "'".$temp['insurance_product_item_id']."' )";

                    $sql_array[] = $str;
                }
                // 拼接sql
                $insert_sql .= trim(join(',',$sql_array),',');
                $re = $product_relationship->conn->execute($insert_sql);
                if( !$re->STS ){
                    $conn->rollback();
                    return new result(false,'Update product item fail: '.$re->MSG,null,errorCodesEnum::DB_ERROR);
                }
                  /*$relationship = $product_relationship->newRow();
                  $relationship->loan_product_id = $pid;
                  $relationship->insurance_product_item_id = $new_item_uid;
                  $rt1 = $relationship->insert();
                  if (!$rt1->STS) {
                      $conn->rollback();
                      return new result(false, 'Update Failure!' . $rt1->MSG);
                  }*/
              }
              //5、添加其余产品联系

              //6、提交所有
              $conn->submitTransaction();
              $uid = $rt_1->AUTO_ID;
              return new result(true, '', array('is_copy' => true, 'uid' => $uid, 'size_rate_map' => $size_rate_map));
          } catch (Exception $ex) {
              $conn->rollback();
              return new result(false, $ex->getMessage());
          }
        }
      }else{
        $chk_code = $product_item->find(array('item_code' => $item_code));
        if (!$itemid && $chk_code) {
          return new result(false, 'Code Exist!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $bind_type = $loan_product_ids ? 1 : 0;
        try {
          $row = $product_item->newRow();
          $row->product_id = $product_id;
          $row->item_code = $item_code;
          $row->item_name = $item_name;
          $row->is_fixed_amount = $is_fixed_amount;
          $row->fixed_amount = $fixed_amount;
          $row->bind_type = $bind_type;
          $row->loan_product_ids = $loan_product_ids;
          $row->is_fixed_price = $is_fixed_price;
          $row->fixed_price = $fixed_price;
          $row->price_rate = $price_rate;
          $row->is_fixed_valid_days = $is_fixed_valid_days;
          $row->fixed_valid_days = $fixed_valid_days;
          $row->product_key = md5(uniqid());
          $rt = $row->insert();
          if (!$rt->STS) {
              $conn->rollback();
              return new result(false, 'Save Failure!' . $rt->MSG);
          }
          if($bind_type){
            // 单一语句执行，循环执行速度超鸡慢
            $field_array = array(
                'loan_product_id',
                'insurance_product_item_id'
            );
            $insert_sql = "insert into insurance_product_relationship(".join(',',$field_array).") values  ";
            $sql_array = array();
            $arr = explode(',', $loan_product_ids);
            foreach( $arr as $value ){
                // 严格按照上面定义的字段插入顺序
                $temp = array(
                    'loan_product_id' => $value,
                    'insurance_product_item_id' => $rt->AUTO_ID
                );
                $str = "( '".$temp['loan_product_id']."',";
                $str .= "'".$temp['insurance_product_item_id']."' )";

                $sql_array[] = $str;
            }
            // 拼接sql
            $insert_sql .= trim(join(',',$sql_array),',');
            $re = $product_relationship->conn->execute($insert_sql);
            if( !$re->STS ){
                $conn->rollback();
                return new result(false,'Insert product item fail: '.$re->MSG,null,errorCodesEnum::DB_ERROR);
            }
          }
          $conn->submitTransaction();
          return new result(true, 'Save Successful!', $row);
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
      }
    }

    /**
     * 改变产品状态
     * @param $uid
     * @param $state
     * @return result
     * 一个系列同时只能有一个产品state 为20
     */
    public function changeProductState($uid, $state){
        $m_product = M('insurance_product');
        $row = $m_product->getRow(array('uid' => $uid));
        if (!$row && $row['state'] == 40) {
            return new result(false, 'Invalid Product!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            if ($state == 20) {
                $rows = $m_product->getRows(array('product_key' => $row['product_key'], 'uid' => array('neq', $uid), 'state' => array('neq', 40)));
                foreach ($rows as $product) {
                    if ($product['state'] == 10) continue;
                    $product->state = 40;
                    $product->end_time = Now();
                    $product->update_time = Now();
                    $rt = $product->update();
                    if (!$rt->STS) {
                        $conn->rollback();
                        return new result(true, 'Update Failure!');
                    }
                }
            }
            $row->state = $state;
            if ($state == 20 && !$row->start_time) {
                $row->start_time = Now();
            }
            $row->update_time = Now();
            $rt = $row->update();
            if ($rt->STS) {
                $conn->submitTransaction();
                return new result(true, 'Update Successful!');
            } else {
                $conn->rollback();
                return new result(false, 'Update Failure!');
            }
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

}
