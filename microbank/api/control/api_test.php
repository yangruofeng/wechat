<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/3
 * Time: 14:11
 */
class api_testControl extends bank_apiControl
{

    public function indexOp()
    {
        $re = loan_contractClass::getRepaymentSchemaByAmount(2,250,'USD');
        print_r($re);
    }

    public function testOp()
    {

        $r = new ormReader();
        $conn = ormYo::Conn();

        $conn->startTransaction();

        $sql1 = "select count(uid) cnt from test where state=0";

        $before_cnt = $r->getOne($sql1);
        print_r($before_cnt);

        $sql2 = "update test set state=1";
        $up = $conn->execute($sql2);

        $after_cnt = $r->getOne($sql1);
        print_r($after_cnt);

        $conn->submitTransaction();



    }

    public function transferCertOp()
    {
        die('close');
        set_time_limit(0);
        $m_image = new member_verify_cert_imageModel();
        $m_cert  = new member_verify_certModel();
        $list = $m_cert->getAll();
        foreach( $list as $cert ){
            switch( $cert['cert_type'] ){
                case certificationTypeEnum::ID :
                    $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha) values ";
                    $sql .= "('".$cert['uid']."','".certImageKeyEnum::ID_HANDHELD."','".$cert['cert_photo']."','".$cert['cert_photo_sha']."')";
                    $sql .= ",('".$cert['uid']."','".certImageKeyEnum::ID_FRONT."','".$cert['cert_photo1']."','".$cert['cert_photo1_sha']."')";
                    $sql .= ",('".$cert['uid']."','".certImageKeyEnum::ID_BACK."','".$cert['cert_photo2']."','".$cert['cert_photo2_sha']."')";
                    $m_image->conn->execute($sql);
                    break;
                case certificationTypeEnum::FAIMILYBOOK :
                    $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha) values ";
                    $sql .= "('".$cert['uid']."','".certImageKeyEnum::FAMILY_BOOK_FRONT."','".$cert['cert_photo1']."','".$cert['cert_photo1_sha']."')";
                    $sql .= ",('".$cert['uid']."','".certImageKeyEnum::FAMILY_BOOK_BACK."','".$cert['cert_photo2']."','".$cert['cert_photo2_sha']."')";
                    $sql .= ",('".$cert['uid']."','".certImageKeyEnum::FAMILY_BOOK_HOUSEHOLD."','".$cert['cert_photo3']."','".$cert['cert_photo3_sha']."')";
                    $m_image->conn->execute($sql);
                    break;
                case certificationTypeEnum::FAMILY_RELATIONSHIP :
                    $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha) values ";
                    $sql .= "('".$cert['uid']."','".certImageKeyEnum::FAMILY_RELATION_CERT_PHOTO."','".$cert['cert_photo']."','".$cert['cert_photo_sha']."')";
                    $m_image->conn->execute($sql);
                    break;
                case certificationTypeEnum::WORK_CERTIFICATION :
                    $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha) values ";
                    $sql .= "('".$cert['uid']."','".certImageKeyEnum::WORK_CARD."','".$cert['cert_photo1']."','".$cert['cert_photo1_sha']."')";
                    $sql .= ",('".$cert['uid']."','".certImageKeyEnum::WORK_EMPLOYMENT_CERTIFICATION."','".$cert['cert_photo2']."','".$cert['cert_photo2_sha']."')";
                    $m_image->conn->execute($sql);
                    break;
                case certificationTypeEnum::CAR :
                    $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha) values ";
                    $sql .= "('".$cert['uid']."','".certImageKeyEnum::CAR_CERT_BACK."','".$cert['cert_photo2']."','".$cert['cert_photo2_sha']."')";
                    $sql .= ",('".$cert['uid']."','".certImageKeyEnum::CAR_CERT_FRONT."','".$cert['cert_photo1']."','".$cert['cert_photo1_sha']."')";
                    $m_image->conn->execute($sql);
                    break;
                case certificationTypeEnum::HOUSE :
                    $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha) values ";
                    $sql .= "('".$cert['uid']."','".certImageKeyEnum::HOUSE_RELATIONSHIPS_CERTIFY."','".$cert['cert_photo2']."','".$cert['cert_photo2_sha']."')";
                    $sql .= ",('".$cert['uid']."','".certImageKeyEnum::HOUSE_PROPERTY_CARD."','".$cert['cert_photo1']."','".$cert['cert_photo1_sha']."')";
                    $m_image->conn->execute($sql);
                    break;
                case certificationTypeEnum::LAND :
                    $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha) values ";
                    $sql .= "('".$cert['uid']."','".certImageKeyEnum::LAND_PROPERTY_CARD."','".$cert['cert_photo1']."','".$cert['cert_photo1_sha']."')";
                    $sql .= ",('".$cert['uid']."','".certImageKeyEnum::LAND_TRADING_RECORD."','".$cert['cert_photo2']."','".$cert['cert_photo2_sha']."')";
                    $m_image->conn->execute($sql);
                    break;
                default:

            }
        }

    }


    public function copyLoanHandlerOp()
    {
        die('close');
        $r = new ormReader();
        $sql = "select m.uid member_id,h.* from loan_account_handler h left join loan_account a on a.uid=h.account_id left join 
        client_member m on m.obj_guid=a.obj_guid where h.is_verified='1' and h.state='".accountHandlerStateEnum::ACTIVE."'  group by h.account_id order by uid desc";
        $rows = $r->getRows($sql);
        $m_handler = new member_account_handlerModel();
        $suc = array();
        $fail = array();
        foreach( $rows as $row ){
            $new_handler = $m_handler->newRow($row);
            $insert = $new_handler->insert();
            if( $insert->STS ){
                $suc[] = $new_handler->toArray();
            }else{
                $fail[] = $new_handler->toArray();
            }
        }
        print_r(array(
            'success' => $suc,
            'fail' => $fail
        ));
    }


    public function updateContractInfoOp()
    {
        set_time_limit(0);
        $m_contract = new loan_contractModel();
        $lists = $m_contract->getRows('operation_fee is null');
        $suc = array();
        $fail = array();
        $m_size_rate = new loan_product_size_rateModel();
        $m_special_rate = new loan_product_special_rateModel();
        foreach( $lists as $contract ){
            $interest_info = array();
            if( $contract->product_size_rate_id ){
                $size_rate = $m_size_rate->find(array(
                    'uid' => $contract->product_size_rate_id
                ));
                if( $size_rate ){
                    $interest_info = $size_rate;
                }
            }
            if( $contract->product_special_rate_id ){
                $special_rate = $m_special_rate->find(array(
                    'uid' => $contract->product_special_rate_id
                ));
                if( $special_rate ){
                    $interest_info = array_merge($interest_info,$special_rate);
                }
            }
            $repayment_type = $interest_info['interest_payment'];
            $repayment_period = $interest_info['interest_rate_period'];

            $due_data_arr = loan_baseClass::getLoanDueDate($contract->loan_term_day,$repayment_type,$repayment_period,$contract->start_date);
            $contract->due_date = $due_data_arr['due_date'];
            $contract->due_date_type = $due_data_arr['due_date_type'];
            $contract->repayment_type = $repayment_type;
            $contract->repayment_period = $repayment_period;
            $contract->interest_rate = $interest_info['interest_rate'];
            $contract->interest_rate_type = $interest_info['interest_rate_type'];
            $contract->interest_rate_unit = $interest_info['interest_rate_unit'];
            $contract->interest_min_value = $interest_info['interest_min_value'];
            $contract->operation_fee = $interest_info['operation_fee'];
            $contract->operation_fee_unit = $interest_info['operation_fee_unit'];
            $contract->operation_fee_type = $interest_info['operation_fee_type'];
            $contract->operation_min_value = $interest_info['operation_min_value'];
            $contract->admin_fee = $interest_info['admin_fee'];
            $contract->admin_fee_type = $interest_info['admin_fee_type'];
            $contract->loan_fee = $interest_info['loan_fee'];
            $contract->loan_fee_type = $interest_info['loan_fee_type'];
            $contract->is_full_interest = $interest_info['is_full_interest'];
            $contract->prepayment_interest = $interest_info['prepayment_interest'];
            $contract->prepayment_interest_type = $interest_info['prepayment_interest_type'];
            $up = $contract->update();
            if( !$up->STS ){
                $fail[] = $contract->uid;
            }else{
                $suc[] = $contract->uid;
            }
        }

        print_r(array(
            'success' => $suc,
            'fail' => $fail
        ));
    }
    function  testConfirmContractOp(){
        $conn=ormYo::Conn();
        //$conn->startTransaction();
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->confirmContract(0);
        var_dump($re);
        //$conn->rollback();
        var_dump("done");


    }


    function networkTestOp()
    {
        sleep(2*60);  // 2分钟
        return new result(true,'success');
    }


    public function testApiOp()
    {
        // 原样返回数据
        $data = array_merge($_GET,$_POST);
        return new result(true,'success',$data);
    }


    public function testFileUploadOp()
    {
        $data = $_FILES['upload_file'];
        // 计算的图片大小
        $file_size =  filesize($data['tmp_name']);
        $data['size_byte'] = $file_size;
        return new result(true,'success',$data);
    }



}