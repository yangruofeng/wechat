<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/27
 * Time: 0:00
 */
class creditFlowClass{
    public static function submitMemberSuggestCredit($params)
    {
        /*
        传入格式
       {"repay_ability":"1076.44",
       "credit_terms":"60",
       "max_credit":"23000",
       "default_credit":"3000",
       "default_credit_category_id":"139",
       "request_type":"0",
       "member_id":"434",
       "officer_id":"144",
       "token":"ab5d930cd7dc984d9d06d23e3608a9d9",
       "is_append":"0",
       "asset_list":[{"asset_id":599,"credit":10000,"credit_category_id":"139"},
       {"asset_id":600,"credit":10000,"credit_category_id":"139"}],
       "currency_list":[{"credit_category_id":"139","credit":"23000","credit_usd":"11500","credit_khr":"46000000","interest_rate":"1.20"}],
       "loan_fee":"1.00",
       "admin_fee":"10.00"}}:
        * */
        try{
            $member_id = intval($params['member_id']);
            $officer_id = intval($params['officer_id']);
            $member = (new memberModel())->getRow($member_id);
            if (!$member) {
                return new result(false, 'Member not exists.', null, errorCodesEnum::MEMBER_NOT_EXIST);
            }
            $userObj = new objectUserClass($officer_id);

            $monthly_repayment_ability = round($params['repay_ability'], 2);
            $credit_terms = intval($params['credit_terms']);
            $default_credit = intval($params['default_credit']);
            $max_credit = intval($params['max_credit']);
            $client_request_credit = intval($params['client_request_credit']);
            $remark = $params['remark'];
            $request_type = intval($params['request_type']);

            /*
             * 这个限制已经没必要，尽量对限制做减法
            $m_dict = new core_dictionaryModel();
            $credit_grant_profile = $m_dict->getDictValue(dictionaryKeyEnum::CREDIT_GRANT_RATE);
            if ($credit_terms > $credit_grant_profile['default_max_terms']) {
                return new result(false, 'The max terms can\'t greater than ' . $credit_grant_profile['default_max_terms']);
            }
            */


            $asset_credit = $params['asset_list'];
            //double check asset_credit+default_credit=max_credit
            $total_asset_credit = 0;
            foreach ($asset_credit as $asset_key=>$item) {
                $item['credit']=intval($item['credit']);
                $asset_credit[$asset_key]=$item;
                $total_asset_credit += $item['credit'] ?: 0;
            }
            if (($total_asset_credit + $default_credit) != $max_credit) {
                return new result(false, "Require: MaxCredit<kbd>".$max_credit."</kbd> = DefaultCredit + (Increase Credit By Mortgaged)<kbd>".($total_asset_credit+$default_credit)."</kbd>");
            }

            //double check currency credit
            $member_credit_category=loan_categoryClass::getMemberCreditCategoryList($member_id);
            $credit_currency=$params['currency_list'];
            $credit_ccy_total=0;
            foreach($credit_currency as $ccy_key=>$ccy_item){
                $ccy_item['interest_rate']=round($ccy_item['interest_rate'],2);
                $ccy_item['interest_rate_khr']=round($ccy_item['interest_rate_khr'],2);
                $ccy_item['operation_fee']=round($ccy_item['operation_fee'],2);
                $ccy_item['operation_fee_khr']=round($ccy_item['operation_fee_khr'],2);
                $ccy_item['credit_khr']=round($ccy_item['credit_khr'],2);
                $ccy_item['loan_fee']=round($ccy_item['loan_fee'],2);
                $ccy_item['loan_fee_type']=intval($ccy_item['loan_fee_type']);
                $ccy_item['admin_fee']=round($ccy_item['admin_fee'],2);
                $ccy_item['admin_fee_type']=intval($ccy_item['admin_fee_type']);
                $ccy_item['annual_fee']=round($ccy_item['annual_fee'],2);
                $ccy_item['annual_fee_type']=intval($ccy_item['annual_fee_type']);
                $ccy_item['loan_fee_khr']=round($ccy_item['loan_fee_khr'],2);
                $ccy_item['admin_fee_khr']=round($ccy_item['admin_fee_khr'],2);
                $ccy_item['annual_fee_khr']=round($ccy_item['annual_fee_khr'],2);



                $credit_currency[$ccy_key]=$ccy_item;

                $credit_ccy_total+=$ccy_item['credit_usd']+$ccy_item['credit_khr']/4000;
                $def_credit_category_item=$member_credit_category[$ccy_item['credit_category_id']];
                if(!$def_credit_category_item['is_special']){
                    //不是特殊的才检查利息设置
                    if($ccy_item['credit_usd']>0){
                        if(($ccy_item['interest_rate']+$ccy_item['operation_fee'])<=0){
                            return new result(false,"Required: Interest-Rate(<kbd>".$ccy_item['interest_rate']."</kbd>) Or Operation-Fee(<kbd>".$ccy_item['operation_fee']."</kbd>) Must More Than 0");
                        }
                    }
                    if($ccy_item['credit_khr']>0){
                        if(($ccy_item['interest_rate_khr']+$ccy_item['operation_fee_khr'])<=0){
                            return new result(false,"Required: Interest-Rate(<kbd>".$ccy_item['interest_rate_khr']."</kbd>) Or Operation-Fee(<kbd>".$ccy_item['operation_fee_khr']."</kbd>) Must More Than 0");
                        }
                    }
                }
            }

            if($credit_ccy_total!=$max_credit){
                return new result(false, "Require: Max Credit<kbd>".$max_credit."</kbd> = USD_Credit + KHR_Credit<kbd>".$credit_ccy_total."</kbd>");
            }

            /*
             * 因为现在利息是一层层提交和审批，取消这个限制
            //匹配不到利息不允许提交
            $category_setting=loan_categoryClass::getMemberCreditCategoryList($member_id);
            if($userObj->position==userPositionEnum::BRANCH_MANAGER){
                foreach($credit_currency as $chk_category){
                    $chk_cate=$category_setting[$chk_category['member_credit_category_id']];
                    $chk_cate['credit_usd']=$chk_category['credit_usd'];
                    $chk_cate['credit_khr']=$chk_category['credit_khr'];
                    $chk_cate['credit_terms']=$credit_terms;
                    $chk_ret=loan_categoryClass::matchInterestForCategory($chk_cate['interest_rate_list'],$chk_cate,false);
                    foreach($chk_ret as $chk_ret_ccy=>$chk_ret_item){
                        if(!$chk_ret_item['is_matched']){
                            $msg=$chk_ret_item['msg'];
                            $str_msg=join($msg," , ");
                            $str_msg="<kbd>".$str_msg."</kbd>";
                            return new result(false,"No Matched Interest For ".$chk_cate['alias']." ,Currency:".strtoupper($chk_ret_ccy).",Amount:".$chk_category['credit_'.$chk_ret_ccy]." MSG:".$str_msg);
                        }
                    }
                }
            }
            */

            $m = new member_credit_suggestModel();
            $row = $m->newRow();
            $row->branch_id = $userObj->branch_id;
            $row->member_id = $member_id;
            $row->request_type = $request_type;
            $row->request_time = Now();
            $row->operator_id = $userObj->user_id;
            $row->operator_name = $userObj->user_name;
            $row->client_request_credit = $client_request_credit;
            $row->monthly_repayment_ability = $monthly_repayment_ability;
            $row->default_credit = $default_credit;
            $row->default_credit_category_id = intval($params['default_credit_category_id']);
            $row->max_credit = $max_credit;
            $row->credit_terms = $credit_terms;
            $row->is_append = $params['is_append'] ?: 0;
            //$row->loan_fee=$params['loan_fee'];
            //$row->admin_fee=$params['admin_fee'];
            //$row->loan_fee_type=intval($params['loan_fee_type']);
            //$row->admin_fee_type=intval($params['admin_fee_type']);


            $row->remark = $remark;
            $insert = $row->insert();
            if (!$insert->STS) {
                return new result(false, $insert->MSG, null, errorCodesEnum::DB_ERROR);
            }

            if (count($asset_credit)) {
                // 插入资产详细
                $sql = "insert into member_credit_suggest_detail(credit_suggest_id,member_asset_id,credit,member_credit_category_id) values ";
                $arr = array();
                $suggest_id = $row->uid;
                foreach ($asset_credit as $asset) {
                    $str = "('$suggest_id','" . $asset['asset_id'] . "','" . $asset['credit'] . "','" . $asset['credit_category_id'] . "')";
                    $arr[] = $str;
                }
                $sql .= implode(',', $arr);
                $insert = $m->conn->execute($sql);
                if (!$insert->STS) {
                    return new result(false, 'Submit fail.', null, errorCodesEnum::DB_ERROR);
                }
            }
            $collateral_list=$params['collateral_list'];
            if(count($collateral_list)){
                //担保的，还是插入资产表
                $sql = "insert into member_credit_suggest_detail(credit_suggest_id,member_asset_id) values ";
                $arr = array();
                $suggest_id = $row->uid;
                foreach ($collateral_list as $cert_id) {
                    $str = "('$suggest_id','" . $cert_id . "')";
                    $arr[] = $str;
                }
                $sql .= implode(',', $arr);
                $insert = $m->conn->execute($sql);
                if (!$insert->STS) {
                    return new result(false, 'Submit fail.', null, errorCodesEnum::DB_ERROR);
                }
            }
            if(count($credit_currency)){
                $m_credit_product=new member_credit_suggest_productModel();
                foreach($credit_currency as $ccy_item){
                    $ccy_row=$m_credit_product->newRow($ccy_item);
                    $ccy_row->member_credit_category_id=$ccy_item['credit_category_id'];
                    $ccy_row->credit_suggest_id=$row->uid;
                    $ccy_row->credit=$ccy_item['credit_usd']+$ccy_item['credit_khr']/4000;
                    $ccy_row->exchange_rate=4000;
                    $ccy_row->interest_rate=$ccy_item['interest_rate'];
                    $ccy_row->interest_rate_khr=$ccy_item['interest_rate_khr'];
                    $ccy_row->operation_fee=$ccy_item['operation_fee'];
                    $ccy_row->operation_fee_khr=$ccy_item['operation_fee_khr'];
                    $ccy_row->loan_fee=$ccy_item['loan_fee'];
                    $ccy_row->loan_fee_type=$ccy_item['loan_fee_type'];
                    $ccy_row->admin_fee=$ccy_item['admin_fee'];
                    $ccy_row->admin_fee_type=$ccy_item['admin_fee_type'];
                    $ccy_row->annual_fee=$ccy_item['annual_fee'];
                    $ccy_row->annual_fee_type=$ccy_item['annual_fee_type'];


                    $ccy_sts=$ccy_row->insert();
                    if(!$ccy_sts){
                        return $ccy_sts;
                    }
                }
            }

            return new result(true, 'Save Successful.');
        }catch (Exception $ex){
            return new result(false,$ex->getMessage());
        }
    }

    /**
     * 提交讨论结果投票
     * @param $params
     * @return result
     * @throws Exception
     */
    public static function ApproveBMSuggestCreditToVote($param){
        $suggest_id = intval($param['suggest_id']);
        $sg_credit=credit_researchClass::getLastSuggestCreditBySuggestId($suggest_id);
        if(!$sg_credit){
            return new result(false,"Invalid Parameter:No Suggest Credit Found");
        }
        if($sg_credit['state']!=memberCreditSuggestEnum::PENDING_APPROVE){
            return new result(false,"Invalid Parameter:Invalid Suggestion State");
        }
        $max_credit=$sg_credit['max_credit'];
        $member_id = $sg_credit['member_id'];
        $operator_id = $param['operator_id'];
        $operator_name = $param['operator_name'];

        $remark = trim($param['remark']);
        $committee_member = $param['committee_member'];
        $is_auto_Authorize = intval($param['is_auto_Authorize']);


        //判断该客人是否还存在待签订合同
        if (memberClass::isSignAuthorizedContract($member_id) < 0) {
            return new result(false, 'Not allowed to grant Credit: The client has some credit-agreement that are not signed');
        }

        $is_grant_by_bm = ($param['operator_type'] == 1) ? 1 : 0;
        $is_auto_vote = 0;//是否自动完成投票
        if (!$is_grant_by_bm) {
            //如果不是bm提交的，判断voter人数
            //先计算max-credit，判断voter人数
            $limit_voter = global_settingClass::getVoterOfGrantingCreditByAmount($max_credit);
            if ($limit_voter > 1) {//必须超过1人投票
                if (count($committee_member) < $limit_voter) {
                    return new result(false, "Asking for at least " . $limit_voter . " voters");
                }
            }
            if (count($committee_member) == 1) {
                if ($committee_member[0] == $operator_id) {
                    $is_auto_vote = 1;//说明允许只有一个投票者，并且是自己
                }
            }
        } else {
            $is_auto_Authorize = 0;//bm提交的，必须授权
            $is_auto_vote = 1;
        }


        /*
        if (empty($committee_member)) {
            return new result(false, 'Please select committee member!');
        }
        */

        //$max_credit = $default_credit;
        $conn = ormYo::Conn();
        if (!$param['is_start_transaction_outside']) {
            $conn->startTransaction();
        }
        $m_member_credit_suggest = new member_credit_suggestModel();
        if ($suggest_id) {
            $member_credit_suggest = $m_member_credit_suggest->getRow($suggest_id);
            if ($member_credit_suggest['state'] != memberCreditSuggestEnum::PENDING_APPROVE) {
                $conn->rollback();
                return new result(false, 'Invalid State.');
            }
            $member_credit_suggest->state = memberCreditSuggestEnum::APPROVING;
            $member_credit_suggest->update_time = Now();
            $rt_4 = $member_credit_suggest->update();
            if (!$rt_4->STS) {
                $conn->rollback();
                return new result(false, 'Commit Failure!');
            }
        }
        //处理member_credit_request
        $m_credit_request = new member_credit_requestModel();
        $last_request = $m_credit_request->getRow(array("member_id" => $member_id, "state" => creditRequestStateEnum::CREATE));
        if ($last_request) {
            $request_id = $last_request->uid;
            $last_request->update_time = Now();
            $last_request->update_operator_id = $operator_id;
            $last_request->update_operator_name = $operator_name;
            $ret_request = $last_request->update();
            if (!$ret_request->STS) {
                $conn->rollback();
                return new result(false, 'Commit Failure:Failed to Update Credit Request State');
            }
        } else {
            $request_id = 0;
        }


        $m = new member_credit_grantModel();
        $row = $m->newRow();
        $row->grant_time = Now();
        $row->member_id = $member_id;
        $row->operator_id = $operator_id;
        $row->operator_name = $operator_name;
        //$row->client_request_credit = $client_request_credit;
        $row->monthly_repayment_ability = $sg_credit['monthly_repayment_ability'];
        $row->default_credit = $sg_credit['default_credit'];
        $row->default_credit_category_id = $sg_credit['default_credit_category_id'];
        $row->max_credit = $max_credit;
        $row->credit = $max_credit;
        $row->credit_terms = $sg_credit['credit_terms'];
        $row->remark = $remark;
        $row->credit_suggest_id = $suggest_id;
        $row->credit_request_id = $request_id;
        $row->is_append = intval($sg_credit['is_append']) ?: 0;
        $row->state = commonApproveStateEnum::APPROVING;
        $row->update_time = Now();
        $expire_time = date('Y-m-d H:i:s', strtotime('+2 day'));
        $row->vote_expire_time = $expire_time;
        //$row->loan_fee=$sg_credit['loan_fee'];
        //$row->admin_fee=$sg_credit['admin_fee'];
        //$row->loan_fee_type=$sg_credit['loan_fee_type'];
        //$row->admin_fee_type=$sg_credit['admin_fee_type'];

        $row->is_auto_Authorize = $is_auto_Authorize;
        $rt_1 = $row->insert();
        if (!$rt_1->STS) {
            $conn->rollback();
            return new result(false, 'Commit Failure!');
        } else {
            $grant_id = $rt_1->AUTO_ID;
        }

        $m_member_credit_grant_assets = new member_credit_grant_assetsModel();
        $arr_credit_increase = $sg_credit['suggest_detail_list'];

        foreach ($arr_credit_increase as $id => $item) {
            /* 担保的不需要category了
            if (!$item['member_credit_category_id']) {
                $conn->rollback();
                return new result(false, "Require Credit Category For Mortgage Asset");
            }
            */
            //判断资产有没有处于已经抵押或者待签合同状态
            $chk_asset_valid=member_assetsClass::checkAssetIdValidOfGrantCredit($id);
            if(!$chk_asset_valid->STS){
                $conn->rollback();
                return $chk_asset_valid;
            }

            $row_1 = $m_member_credit_grant_assets->newRow();
            $row_1->grant_id = $row->uid;
            $row_1->member_asset_id = $item['member_asset_id'];
            $row_1->credit = $item['credit'];
            $row_1->member_credit_category_id = $item['member_credit_category_id'];
            $rt_2 = $row_1->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new $rt_2;
            }
        }

        //处理currency
        if($suggest_id){
            $credit_currency=$sg_credit['suggest_product'];//$m_member_credit_suggest->getSuggestProductBySuggestId($suggest_id);
            foreach($credit_currency as $ccy_credit){
                $m_member_credit_grant_product=new member_credit_grant_productModel();
                $prod_row=$m_member_credit_grant_product->newRow();
                $prod_row->grant_id=$row->uid;
                $prod_row->member_credit_category_id=$ccy_credit['member_credit_category_id'];
                $prod_row->credit=$ccy_credit['credit'];
                $prod_row->credit_usd=$ccy_credit['credit_usd'];
                $prod_row->credit_khr=$ccy_credit['credit_khr'];
                $prod_row->exchange_rate=$ccy_credit['exchange_rate'];
                $prod_row->interest_rate=$ccy_credit['interest_rate'];
                $prod_row->interest_rate_khr=$ccy_credit['interest_rate_khr'];
                $prod_row->operation_fee=$ccy_credit['operation_fee'];
                $prod_row->operation_fee_khr=$ccy_credit['operation_fee_khr'];

                $prod_row->loan_fee=$ccy_credit['loan_fee'];
                $prod_row->loan_fee_khr=$ccy_credit['loan_fee_khr'];
                $prod_row->loan_fee_type=$ccy_credit['loan_fee_type'];
                $prod_row->admin_fee=$ccy_credit['admin_fee'];
                $prod_row->admin_fee_khr=$ccy_credit['admin_fee_khr'];
                $prod_row->admin_fee_type=$ccy_credit['admin_fee_type'];
                $prod_row->annual_fee=$ccy_credit['annual_fee'];
                $prod_row->annual_fee_type=$ccy_credit['annual_fee_type'];
                $prod_row->annual_fee_khr=$ccy_credit['annual_fee_khr'];

                $rt_prod=$prod_row->insert();
                if(!$rt_prod->STS){
                    $conn->rollback();
                    return $rt_prod;
                }
            }
        }

        //更新member_attachment,member_income_salary,member_income_business的状态
        $sql="update member_attachment set state=100,request_id='".$request_id."',grant_id='".$grant_id."' where member_id='".$member_id."' and state=0 ";
        $ret_upt=$conn->execute($sql);
        if(!$ret_upt->STS){
            $conn->rollback();
            return $ret_upt;
        }

        $sql="update member_income_business set state=100,request_id='".$request_id."',grant_id='".$grant_id."' where member_id='".$member_id."' and state=0 ";
        $ret_upt=$conn->execute($sql);
        if(!$ret_upt->STS){
            $conn->rollback();
            return $ret_upt;
        }

        $sql="update member_income_salary set state=100,request_id='".$request_id."',grant_id='".$grant_id."' where member_id='".$member_id."' and state=0 ";
        $ret_upt=$conn->execute($sql);
        if(!$ret_upt->STS){
            $conn->rollback();
            return $ret_upt;
        }







        $m_member_credit_grant_attender = M('member_credit_grant_attender');
        if ($is_auto_vote) {//自动投票的处理,插入投票人，调用自动完成投票的逻辑
            if (!count($committee_member)) {
                $committee_member[] = $operator_id;
            }
        }

        $m_user = new um_userModel();
        foreach ($committee_member as $val) {
            // 有字段都没有插入投票人名字
            $user_info = $m_user->find(array(
                'uid' => $val
            ));
            $row_2 = $m_member_credit_grant_attender->newRow();
            $row_2->attender_id = $val;
            $row_2->attender_name = $user_info['user_name'];
            $row_2->grant_id = $row->uid;
            $row_2->vote_result = commonApproveStateEnum::CREATE;
            if ($is_auto_vote) {
                $row_2->vote_result = commonApproveStateEnum::PASS;
                $row_2->update_time = Now();
            }
            $rt_3 = $row_2->insert();
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, 'Commit Failure!');
            }
        }
        if ($is_auto_vote) {
            $ret_vote = self::completeVoteGrantCredit($row->uid, true);
            if (!$ret_vote->STS) {
                $conn->rollback();
                return $ret_vote;
            }
        }

        if (!$param['is_start_transaction_outside']) {
            $conn->submitTransaction();
        }

        return new result(true, 'Commit Successful!', array('uid' => $rt_1->AUTO_ID));
    }
    /**
     * 确认投票
     * @param $uid
     * @return ormResult|result
     */
    public static function completeVoteGrantCredit($uid, $is_start_transaction_outside = false)
    {
        $m_member_credit_grant = M('member_credit_grant');
        $m_member_credit_suggest = M('member_credit_suggest');
        $row = $m_member_credit_grant->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid id.');
        } else {
            $suggest_id = $row->credit_suggest_id;
        }
        $member_id=$row->member_id;


        $r = new ormReader();
        $sql = "select count(*) vote_count from member_credit_grant_attender WHERE grant_id = " . $uid;
        $vote_count = $r->getOne($sql);

        $sql = "select count(*) approval_count from member_credit_grant_attender WHERE vote_result = 100 AND grant_id = " . $uid;
        $approval_count = $r->getOne($sql);

        $conn = ormYo::Conn();
        if (!$is_start_transaction_outside) {
            $conn->startTransaction();
        }

        if ($approval_count >= $vote_count) {
            $row->state = commonApproveStateEnum::PASS;
            $row->vote_result = commonApproveStateEnum::PASS;
            $row->vote_time = Now();
            $row->update_time = Now();
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return $rt_1;
            }

            $suggest_state = memberCreditSuggestEnum::PASS;
            if ($row->credit_request_id > 0) {
                $m_credit_request = new member_credit_requestModel();
                $request_row = $m_credit_request->getRow($row->credit_request_id);
                $request_row->state = creditRequestStateEnum::GRANTED;
                $request_row->update_time = Now();
                $rt_2 = $request_row->update();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return $rt_2;
                }
            }

            /*
             暂时不支持自动签约
            if ($row->is_auto_Authorize == 1) {
                $rt_4 = member_credit_grantClass::signFastAuthorizedContract($row->uid, $row->member_id, $row->operator_id);
                if (!$rt_4->STS) {
                    $conn->rollback();
                    return $rt_4;
                }
            }
            */

            // 更新资产的信用
            $rt = self::updateAssetsCreditAndStateByGrant($uid);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }

            $data = array(
                'is_fast_grant' => $suggest_id == 0 ? true : false,
            );
            $msg = 'Pass!';
            $task_msg = "Approve Request For Credit【No.：" . $suggest_id . "】 At " . Now();

        } else {
            $row->state = commonApproveStateEnum::REJECT;
            $row->vote_result = commonApproveStateEnum::REJECT;
            $row->vote_time = Now();
            $row->update_time = Now();
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return $rt_1;
            }

            //更新member_attachment,member_income_salary,member_income_business的状态
            $sql="update member_attachment set state=0 where member_id='".$member_id."' and state=100 and grant_id='".$uid."'";
            $ret_upt=$conn->execute($sql);
            if(!$ret_upt->STS){
                $conn->rollback();
                return $ret_upt;
            }
            $sql="update member_income_business set state=0 where member_id='".$member_id."' and state=100 and grant_id='".$uid."'";

            $ret_upt=$conn->execute($sql);
            if(!$ret_upt->STS){
                $conn->rollback();
                return $ret_upt;
            }
            $sql="update member_income_salary set state=0 where member_id='".$member_id."' and state=100 and grant_id='".$uid."'";
            $ret_upt=$conn->execute($sql);
            if(!$ret_upt->STS){
                $conn->rollback();
                return $ret_upt;
            }


            $suggest_state = memberCreditSuggestEnum::NO_PASS;
            $data = array(
                'is_fast_grant' => $suggest_id == 0 ? true : false,
            );
            $msg = 'Not Pass!';
            $task_msg = "Rejected Request For Credit【No.：" . $suggest_id . "】 At " . Now();
        }

        if ($suggest_id) {
            $suggest_row = $m_member_credit_suggest->getRow($suggest_id);
            $branch_id = $suggest_row->branch_id;
            $suggest_row->state = $suggest_state;
            $suggest_row->update_time = Now();
            $rt_2 = $suggest_row->update();
            if (!$rt_2->STS) {
                $conn->rollback();
                return $rt_2;
            }
        }

        if (!$is_start_transaction_outside) {
            $conn->submitTransaction();
        }

        taskControllerClass::handleNewTask($suggest_id, userTaskTypeEnum::BM_REQUEST_FOR_CREDIT, intval($branch_id), objGuidTypeEnum::SITE_BRANCH, 0, objGuidTypeEnum::UM_USER, $task_msg);

        return new result(true, $msg, $data);
    }
    public static function updateAssetsCreditAndStateByGrant($grant_id)
    {
        $r = new ormReader();
        $sql = "select * from member_credit_grant_assets where grant_id='$grant_id' ";
        $rows = $r->getRows($sql);
        if (count($rows) < 1) {
            return new result(true);
        }
        $conn = ormYo::Conn();
        foreach ($rows as $v) {
            $sql = "update member_assets set credit='" . $v['credit'] . "',asset_state=" . qstr(assetStateEnum::GRANTED) . " where uid='" . $v['member_asset_id'] . "' ";
            $up = $conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update asset credit fail.');
            }
        }
        return new result(true);
    }
    public static function rejectBMSuggestCreditApplication($param)
    {
        $suggest_id = intval($param['suggest_id']);
        $remark = trim($param['remark']);
        $m_member_credit_suggest = M('member_credit_suggest');
        $member_credit_suggest = $m_member_credit_suggest->getRow($suggest_id);
        if ($member_credit_suggest['state'] != memberCreditSuggestEnum::PENDING_APPROVE) {
            return new result(false, 'Invalid State.');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $member_credit_suggest->state = memberCreditSuggestEnum::HQ_REJECT;
        $member_credit_suggest->remark = $remark;
        $member_credit_suggest->update_time = Now();
        $rt = $member_credit_suggest->update();
        if (!$rt->STS) {
            $conn->rollback();
            return new result(true, 'Handle Failed.');
        }

        $m_credit_request = new member_credit_requestModel();
        $credit_request = $m_credit_request->getRow(array("member_id" => $member_credit_suggest['member_id'], "state" => creditRequestStateEnum::GRANTED));
        if ($credit_request) {
            $credit_request->state = creditRequestStateEnum::CREATE;
            $credit_request->update_time = Now();
            $credit_request->update_operator_id = $param['operator_id'];
            $credit_request->update_operator_name = $param['operator_name'];
            $ret_request = $credit_request->update();
            if (!$ret_request->STS) {
                $conn->rollback();
                return new result(true, 'Handle Failed.');
            }
        }

        $task_msg = "Reject Request For Credit【No.：" . $suggest_id . "】 For Remark：【" . $remark . "】 At " . Now();
        taskControllerClass::handleNewTask($suggest_id, userTaskTypeEnum::BM_REQUEST_FOR_CREDIT, $member_credit_suggest->branch_id, objGuidTypeEnum::SITE_BRANCH, $param['operator_id'], objGuidTypeEnum::UM_USER, $task_msg);

        $conn->submitTransaction();
        return new result(true, 'Handle Successful.');
    }

    /**
     * 在柜台签订信用合同
     * @param $args
     * @return result
     * @throws Exception
     */
    public static function signCreditAgreementAtCounter($args){
        /*
         * 传入数据格式
          {"grant_id":"242",
        "member_id":"315",
        "member_image":"adc775cd_2e8d_4283_b6a5_1402c9b63f52.jpg",
        "mortgage_list":[{"member_asset_id":398,"is_received":1,"asset_images":[]},{"member_asset_id":425,"is_received":1,"asset_images":[]}],
        "contract_images":"94c09cf4_92fe_49b0_9f55_bed5c6ec6004.jpg,607487ac_bb4f_49bf_aa7f_21f0734f13b4.jpg",
        "fee_from":"0","usd_amount":"",
        "khr_amount":"",
        "cashier_trading_password":"",
        "member_trading_password":""}
         * */
        // 约定一个grant只签合同一次
        $user_id = $args['operator_id'];
        $trading_password = $args['cashier_trading_password'];
        $grant_id = $args['grant_id'];
        $member_image = $args['member_image'];
        $member_trading_password = $args['member_trading_password'];
        $mortgage_list = $args['mortgage_list'] ?: array();
        $contract_images = $args['contract_images'] ?: array();
        $payment_way = $args['payment_way'];  // 支付方式
        $currency_amount = $args['currency_amount'];  // 实收金额币种
        //$is_auto_disburse_one_time = $args['is_auto_disburse_one_time'];

        $objUser = new objectUserClass($user_id);
        $chk = $objUser->checkTradingPassword($trading_password);
        if (!$chk->STS) {
            return $chk;
        }
        //获取授信信息
        $grant_detail = (new member_credit_grantModel())->getRow($grant_id);
        if (!$grant_detail) {
            return new result(false, 'No grant info.', null, errorCodesEnum::NO_DATA);
        }
        $member_id=$grant_detail->member_id;
        $member_info = (new memberModel())->getRow($member_id);
        if (!$member_info) {
            return new result(false, 'No member.', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        //检查member的密码  取消了
        /*
        if ($payment_way == repaymentWayEnum::PASSBOOK) {
            if ($member_trading_password != md5($member_info['trading_password'])) {
                return new result(false, 'Member trading password error.', null, errorCodesEnum::PASSWORD_ERROR);
            }
        }
        */
        //获取授信的产品信息
        $grant_category=(new member_credit_grant_productModel())->select(array("grant_id"=>$grant_id));
        //计算total-fee


        // usd fee
        $loan_fee=0;
        $admin_fee=0;
        $annual_fee=0;

        // khr fee
        $loan_fee_khr=0;
        $admin_fee_khr=0;
        $annual_fee_khr=0;

        $is_only_khr=true;
        foreach($grant_category as $gc_item){
            if($gc_item['credit_usd']>0){
                $is_only_khr=false;
                if($gc_item['loan_fee_type']){
                    $loan_fee+=round($gc_item['loan_fee'],2);
                }else{
                    $loan_fee+=round($gc_item['credit_usd']*$gc_item['loan_fee']/100,2);
                }
                if($gc_item['admin_fee_type']){
                    $admin_fee+=round($gc_item['admin_fee'],2);
                }else{
                    $admin_fee+=round($gc_item['credit_usd']*$gc_item['admin_fee']/100,2);
                }
                if($gc_item['annual_fee_type']){
                    $annual_fee+=round($gc_item['annual_fee'],2);
                }else{
                    $annual_fee+=round($gc_item['credit_usd']*$gc_item['annual_fee']/100,2);
                }
            }
            if($gc_item['credit_khr']>0){
                if($gc_item['loan_fee_type']){
                    $loan_fee_khr+=round($gc_item['loan_fee_khr'],2);
                }else{
                    $loan_fee_khr+=round($gc_item['credit_khr']*$gc_item['loan_fee_khr']/100,2);
                }
                if($gc_item['admin_fee_type']){
                    $admin_fee_khr+=round($gc_item['admin_fee_khr'],2);
                }else{
                    $admin_fee_khr+=round($gc_item['credit_khr']*$gc_item['admin_fee_khr']/100,2);
                }
                if($gc_item['annual_fee_type']){
                    $annual_fee_khr+=round($gc_item['annual_fee_khr'],2);
                }else{
                    $annual_fee_khr+=round($gc_item['credit_khr']*$gc_item['annual_fee_khr']/100,2);
                }

            }

        }

        $total_fee=$loan_fee+$admin_fee+$annual_fee;
        $total_fee_khr = $loan_fee_khr+$admin_fee_khr+$annual_fee_khr;

        /*if($is_only_khr){
            $total_fee_khr=$total_fee*4000;
            $loan_fee_khr=$loan_fee*4000;
            $admin_fee_khr=$admin_fee*4000;
            $annual_fee_khr=$annual_fee*4000;
        }*/

        // todo check 暂时是这样处理
        // 这里处理多币种的特殊利率问题，存在KHR下，换算汇率使用4000,而且操作完成后需要马上销毁
        if($total_fee_khr>0 ){
            global_settingClass::resetCurrencyExchangeRate(currencyEnum::USD,currencyEnum::KHR,4000);
            global_settingClass::resetCurrencyExchangeRate(currencyEnum::KHR,currencyEnum::USD,1/4000);
        }


        if ($payment_way == repaymentWayEnum::CASH) {
            // 计算是否金额足够
            /*if($total_fee_khr>0){
                $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($total_fee_khr, currencyEnum::KHR, $currency_amount);
                if (!$rt->STS) {
                    return new result(false, 'Receive cash not enough for: <kbd>' . $total_fee_khr . currencyEnum::KHR.'</kbd>', null, errorCodesEnum::INVALID_AMOUNT);
                }
            }else{
                $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($total_fee, currencyEnum::USD, $currency_amount);
                if (!$rt->STS) {
                    return new result(false, 'Receive cash not enough for: <kbd>' . $total_fee . currencyEnum::USD.'</kbd>', null, errorCodesEnum::INVALID_AMOUNT);
                }
            }*/
            $destination_amount = array(
                currencyEnum::USD => $total_fee,
                currencyEnum::KHR => $total_fee_khr
            );

            $rt = system_toolClass::allotMultiCurrencyForMultiCurrencyAmount($destination_amount,$currency_amount);
            if( !$rt->STS ){
                return $rt;
            }
            if( !$rt->DATA['is_enough'] ){
                return new result(false,'Receive cash not enough for:<kbd>'.$total_fee.' USD </kbd> <kbd>'.$total_fee_khr.' KHR </kbd>',null,errorCodesEnum::INVALID_AMOUNT);
            }

        }else{
            $currency_amount = array(
                currencyEnum::USD => $total_fee,
                currencyEnum::KHR => $total_fee_khr
            );
        }



        //插入场景
        if($member_image){
            $ret_scene=(new biz_scene_imageModel())->insertSceneImage($member_id,$member_image,'sign_credit_agreement',bizSceneEnum::COUNTER);
        }
        // 插入合同表
        $m_auth_contract = new member_authorized_contractModel();
        $chk_first = $m_auth_contract->find(array("grant_credit_id" => $grant_id));
        if($chk_first){
            //已经签订过就不允许再签
            return new result(false,"This Application Has Been Signed,Not Allowed to Sign Again");
        }

        $conn = $m_auth_contract->conn;

        $contract_no = $m_auth_contract->generateAuthorizedContractSn();
        $auth_contract = $m_auth_contract->newRow();
        $auth_contract->contract_no = $contract_no;
        $auth_contract->contract_type = 1;  // 抵押 1
        $auth_contract->grant_credit_id = $grant_id;
        $auth_contract->member_id = $member_id;
        $auth_contract->member_img = $member_image;
        $auth_contract->total_credit = $grant_detail['max_credit'];
        $auth_contract->fee = $total_fee;
        $auth_contract->loan_fee_amount = $loan_fee;
        $auth_contract->admin_fee_amount = $admin_fee;
        $auth_contract->annual_fee_amount=$annual_fee;
        $auth_contract->fee_khr=$total_fee_khr;
        $auth_contract->loan_fee_khr_amount = $loan_fee_khr;
        $auth_contract->admin_fee_khr_amount = $admin_fee_khr;
        $auth_contract->annual_fee_khr_amount = $annual_fee_khr;

        if($payment_way==repaymentWayEnum::CASH){
            $auth_contract->cash_usd=$currency_amount[currencyEnum::USD];
            $auth_contract->cash_khr=$currency_amount[currencyEnum::KHR];
        }else{
            $auth_contract->cash_usd=0;
            $auth_contract->cash_khr=0;
        }


        $auth_contract->officer_id = $user_id;
        $auth_contract->officer_name = $objUser->user_name;
        $auth_contract->create_time = Now();
        $auth_contract->update_time=Now();
        $auth_contract->is_paid = 0;
        $auth_contract->payment_way = $payment_way;
        $auth_contract->branch_id = $objUser->branch_id;

        $is_pending_receive = false;
        if (count($mortgage_list)) {
            foreach ($mortgage_list as $item) {
                if (!$item['is_received']) {
                    $is_pending_receive = true;
                    break;
                }
            }
        }
        if ($is_pending_receive) {
            $auth_contract->state = authorizedContractStateEnum::UN_RECEIVED;
        } else {
            $auth_contract->state = authorizedContractStateEnum::COMPLETE;
        }

        $insert = $auth_contract->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert auth contract fail:'.$insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        //处理request
        if ($grant_detail['credit_request_id']) {
            $m_credit_request = new member_credit_requestModel();
            $row_credit_request = $m_credit_request->getRow($grant_detail['credit_request_id']);
            if ($row_credit_request) {
                $row_credit_request->state = creditRequestStateEnum::DONE;
                $row_credit_request->update_time = Now();
                $row_credit_request->update();
            }
        }

        // 插入合同的纸质文件图片
        if (!empty($contract_images)) {
            $image_arr = array();
            $sql = "insert into member_authorized_contract_image(authorized_contract_id,image_path) values ";
            foreach ($contract_images as $image_path) {
                $temp = "('" . $auth_contract->uid . "','$image_path')";
                $image_arr[] = $temp;
            }
            $sql .= implode(',', $image_arr);
            $insert = $m_auth_contract->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Insert auth contract image fail.', null, errorCodesEnum::DB_ERROR);
            }
        }

        // 处理抵押的物品
        if (!empty($mortgage_list)) {
            $m_asset_mortgage = new member_asset_mortgageModel();
            $m_grant_asset = new member_credit_grant_assetsModel();
            $m_member_assets = new member_assetsModel();

            foreach ($mortgage_list as $asset_detail) {

                $member_asset_id = $asset_detail['member_asset_id'];
                //$mortgage_file_type = $asset_detail['mortgage_file_type'];
                $member_asset_info = $m_member_assets->getRow($member_asset_id);
                if (!$member_asset_info) {
                    return new result(false, 'Invalid asset id.', null, errorCodesEnum::INVALID_PARAM);
                }
                $mortgage_file_type = $member_asset_info['asset_cert_type'];

                $grant_asset = $m_grant_asset->getRow(array(
                    'grant_id' => $grant_id,
                    'member_asset_id' => $member_asset_id
                ));
                if (!$grant_asset) {
                    return new result(false, 'Invalid asset id.', null, errorCodesEnum::INVALID_PARAM);
                }
                // 插入抵押记录
                $row = $m_asset_mortgage->newRow();
                $row->grant_id = $grant_id;
                $row->contract_type = assetMortgageContractTypeEnum::CREDIT_LOAN;
                $row->contract_no = $auth_contract->contract_no;
                $row->member_asset_id = $member_asset_id;
                $row->mortgage_type = 1;
                $row->credit = $grant_asset->credit;
                $row->mortgage_file_type = $mortgage_file_type;
                $row->is_received = $asset_detail['is_received'] ? 1 : 0;
                $row->operator_id = $objUser->user_id;
                $row->operator_name = $objUser->user_name;
                $row->operator_time = Now();
                $row->branch_id = $objUser->branch_id;
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Insert asset mortgage record fail.', null, errorCodesEnum::DB_ERROR);
                }
                $mortgage_id = $row->uid;
                if ($row->is_received) {
                    $ret_storage = member_assetsClass::insertStorageFlow(array(
                        "member_asset_id" => $row->member_asset_id,
                        "mortgage_id" => $mortgage_id,
                        "contract_no" => $row->contract_no,
                        "to_branch_id" => $args['branch_id'],
                        "to_branch_name" => $args['branch_name'],
                        "to_operator_id" => $args['operator_id'],
                        "to_operator_name" => $objUser->user_name,
                        "remark" => "Received From Client",
                        "creator_id" => $args['operator_id'],
                        "creator_name" => $objUser->user_name,
                        "flow_type" => assetStorageFlowType::RECEIVED_FROM_CLIENT
                    ));
                    if (!$ret_storage->STS) {
                        return $ret_storage;
                    }
                }

                // 插入图片
                if (!empty($asset_detail['asset_images'])) {
                    $asset_images = array();
                    $sql = "insert into member_asset_mortgage_image(asset_mortgage_id,image_path) values ";
                    foreach ($asset_detail['asset_images'] as $image_path) {
                        $temp = "('$mortgage_id','$image_path')";
                        $asset_images[] = $temp;
                    }
                    $sql .= implode(',', $asset_images);
                    $insert = $conn->execute($sql);
                    if (!$insert->STS) {
                        return new result(false, 'Insert asset mortgage image fail.', null, errorCodesEnum::DB_ERROR);
                    }
                }
                // 更新授信资产记录
                $row_grant_asset = (new member_credit_grant_assetsModel())->getRow(array(
                    "grant_id" => $grant_id,
                    "member_asset_id" => $member_asset_id
                ));
                if ($row_grant_asset) {
                    $row_grant_asset->is_mortgage = 1;
                    $row_grant_asset->asset_mortgage_id = $mortgage_id;
                    $ret = $row_grant_asset->update();
                    if (!$ret->STS) {
                        return $ret;
                    }
                } else {
                    return new result(false, "not found grant record for this asset");
                }

                // 更新资产记录
                $member_asset_info->mortgage_state = 1;
                $member_asset_info->hold_state = $asset_detail['is_received'] ? 1 : 0;
                $member_asset_info->mortgage_time = Now();
                $member_asset_info->update_time = Now();
                $up = $member_asset_info->update();
                if (!$up->STS) {
                    return new result(false, 'Update member assets info fail.', null, errorCodesEnum::DB_ERROR);
                }
            }
        }

        // 处理收费的问题
        if ( ($auth_contract->fee+$auth_contract->fee_khr) > 0) {

            switch ($payment_way) {
                case repaymentWayEnum::CASH :
                    $remark = 'Pay credit contract fee by cash:' . $auth_contract->contract_no;
                    $rt = passbookWorkerClass::memberDepositByCash(
                        $member_id,
                        $user_id,
                        null,
                        null,
                        $remark,
                        $currency_amount,
                        array(
                            currencyEnum::USD => $auth_contract->fee,
                            currencyEnum::KHR => $auth_contract->fee_khr
                        ));
                    if (!$rt->STS) return $rt;


                    // 收费,不做部分失败的更新了，失败了统一回滚
                    if( $total_fee > 0 ){
                        $rt = passbookWorkerClass::receiveCreditAuthContractFeeByBalance(
                            $member_id,
                            $loan_fee,
                            $admin_fee,
                            $annual_fee,
                            currencyEnum::USD,
                            $remark);
                        if( !$rt->STS ){
                            return $rt;
                        }
                    }

                    if( $total_fee_khr > 0 ){
                        $rt = passbookWorkerClass::receiveCreditAuthContractFeeByBalance(
                            $member_id,
                            $loan_fee_khr,
                            $admin_fee_khr,
                            $annual_fee_khr,
                            currencyEnum::KHR,
                            $remark);
                        if( !$rt->STS ){
                            return $rt;
                        }
                    }


                    // 处理完收费后，马上清除特殊利率的设置
                    global_settingClass::unsetCurrencyExchangeRate(currencyEnum::USD,currencyEnum::KHR);
                    global_settingClass::unsetCurrencyExchangeRate(currencyEnum::KHR,currencyEnum::USD);


                    $m_payment_detail = new member_authorized_contract_payment_detailModel();
                    $rt = $m_payment_detail->insertPaymentDetail($auth_contract['uid'],$currency_amount);
                    if( !$rt->STS ){
                        return $rt;
                    }

                    $auth_contract->is_paid = 1;
                    $auth_contract->pay_time = Now();
                    $auth_contract->update_time = Now();
                    $up = $auth_contract->update();
                    if (!$up->STS) {
                        return new result(false, 'Receive cash fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
                    }
                    break;
                case repaymentWayEnum::PASSBOOK :
                    // 脚本执行
                    break;
                default:
                    return new result(false, 'Not support payment way.', null, errorCodesEnum::NOT_SUPPORTED);
            }

        }

        //处理信用问题
        //先判断是否replace,如果是replace，则需要使原来的无效

        $ret_credit=member_creditClass::DisburseCreditToClientByGrant($grant_id,$auth_contract->uid);
        if(!$ret_credit->STS){
            return $ret_credit;
        }
        //todo 产生onetime的任务

        return new result(true, 'success',$auth_contract);
    }
    /*
    * 按照SRS以前的计算规则，获取一个客户的信用分析，operator_id可以是operator/co/bm/customer_service
    */
    public static function getSystemAnalysisCreditOfMember($member_id, $operator_id, $operator_position = userPositionEnum::CREDIT_OFFICER,$grant_id=0)
    {
        /*
         Total income (1)				 $500.00
Water supply				 $10.00
Electric supply				 $20.00
Family expense				 $250.00
Total expense(2)				 $280.00
Total net income (A) = (1-2) 				220
Payment ability
Total profit (A)				 $220.00
Monthly payment (SRS Loan+Other MFI Loan+New SRS Loan) (B)				 $100.00
Monthly ability payment ≥1.3 (C) = (A) / (B)				2.2

Collateral Assessment
Land Square				 500
Market value 100%				 10,000
Market value 70%				 7,000
Total collateral value/Loan amount request ≥ 150%				 3.5


         * */
        $obj_guid=generateGuid($member_id,objGuidTypeEnum::CLIENT_MEMBER);//构造member的obj_guid
        $member_request = credit_researchClass::getClientRequestCredit($member_id);
        $relative_ids = array('0');//要判断资产是否属于ids
        if (!$member_request || $member_request['state'] != creditRequestStateEnum::CREATE) {
            $member_request = array();
        } else {
            $relative_list = $member_request['relative_list'];
            if (is_array($relative_list)) {
                $relative_ids = array_merge($relative_ids, array_keys($relative_list));
            }
        }

        //计算在SRS的还款计划（当前月和下月应还额的非0最大值）
        $reader=new ormReader();


        //获取credit-category的设置
        $sql="select a.*,b.interest_type,c.`is_special`,c.`special_key` from member_credit_category a INNER JOIN loan_sub_product b on a.sub_product_id=b.uid INNER JOIN loan_category c ON a.`category_id`=c.uid where a.is_close=0 and a.member_id=".qstr($member_id);
        $arr_category=$reader->getRows($sql);
        //获取loan-account里semi-balloon的设置
        $sql="select * from loan_account where obj_guid=".qstr($obj_guid);
        $arr_account=$reader->getRow($sql);


        //获取pay_to_cbc
        $m_cbc = new client_cbcModel();
        $last_cbc = $m_cbc->orderBy("uid desc")->find(array("client_id" => $member_id, "client_type" => 0));



        //获取资产
        $asset_by_type = credit_officerClass::getMemberAssetsListAndEvaluateOfOfficerGroupByType($member_id, $operator_id, $operator_position);
        $analysis_asset=array();
        $cert_list = (new certificationTypeEnum())->Dictionary();

        if(!count($asset_by_type)){
            $analysis_asset[]="Never verified any asset, <kbd>no asset can mortgage</kbd>";
        }

        $asset = array();
        foreach ($asset_by_type as $arr) {
            if (is_array($arr)) {
                foreach ($arr as $item) {
                    if (!$item['mortgage_state']) {//没有抵押的才能继续
                        $owner_id_list = $item['owner_id_list'];//这个只是id的一维数组
                        $is_valid_owner = true;
                        foreach ($owner_id_list as $owner_id) {
                            if (!in_array($owner_id, $relative_ids)) {//属于关联人列表的才能继续
                                $is_valid_owner = false;
                                break;
                            }
                        }
                        if ($is_valid_owner) {
                            $asset[] = $item;
                        }else{
                            $analysis_asset[]=$item['uid'].".Not Allowed to Mortgage <kbd>".$item['asset_name'] . "(" . $cert_list[$item['asset_type']] . ")"."</kbd>,Invalid Owner:<kbd>".$relative_list[$owner_id]['name']."</kbd>";
                        }
                    }//end if
                }// end for each
            }
        }


        if($grant_id>0){
            $special_where=" and mis.grant_id=".qstr($grant_id);
        }else{
            $special_where=" and mis.state<100";
        }

        //获取salary
        $sql="SELECT mis.*,IFNULL(mcrr.`relation_name`,'') relation_name FROM member_income_salary mis ";
        $sql.=" LEFT JOIN member_credit_request_relative mcrr";
        $sql.=" ON mis.`relative_id`=mcrr.`uid`";
        $sql.=" WHERE (mcrr.`request_id`='".$member_request['uid']."' OR mis.relative_id=0)  AND mis.`member_id`='".$member_id."' ".$special_where;
        $arr_salary=$reader->getRows($sql);

        //获取attachement
        $sql="SELECT mis.*,IFNULL(mcrr.`relation_name`,'') relation_name FROM member_attachment mis ";
        $sql.=" LEFT JOIN member_credit_request_relative mcrr";
        $sql.=" ON mis.`relative_id`=mcrr.`uid`";
        $sql.=" WHERE (mcrr.`request_id`='".$member_request['uid']."' OR mis.relative_id=0)  AND mis.`member_id`='".$member_id."' ".$special_where;
        $arr_attachment=$reader->getRows($sql);
        $attachment_income = array();
        $attachment_expense = array();
        foreach ($arr_attachment as $val) {
            if ($val['ext_type'] == 1) {
                $attachment_income[] = $val;
            }
            if ($val['ext_type'] == 2) {
                $attachment_expense[] = $val;
            }
        }

        //获取business
        $business = credit_researchClass::getMemberIncomeBusinessAnalysis($member_id, $operator_id, $operator_position,$grant_id);

        //suggest_profile
        $m_dict = new core_dictionaryModel();
        $setting = $m_dict->getDictValue(dictionaryKeyEnum::CREDIT_GRANT_RATE);
        //$ret['rate_discount'] = $setting;


        $total_income=0;
        $total_expense=0;
        $arr_income=array();
        $arr_expense=array();
        if (count($arr_salary)) {
            foreach ($arr_salary as $item) {
                $arr_income[]=array(
                  "desc"=>$item['relative_name']." Salary".($item['relation_name']?'('.$item['relation_name'].')':''),
                    "income"=>$item['salary'],
                    "expense"=>0,
                    "profit"=>$item['salary'],
                    "is_own"=>($item['relative_id']==0?1:0)
                );
                $total_income+=$item['salary'];
            }
        }
        $list_salary=$arr_income;//为了super单独展示salary
        if (count($attachment_income)) {
            foreach ($attachment_income as $item) {
                $arr_income[]=array(
                    "desc"=>$item['title'],
                    "income"=>$item['ext_amount'],
                    "expense"=>0,
                    "profit"=>$item['ext_amount']
                );
                $total_income+=$item['ext_amount'];
            }
        }
        if (count($business)) {
            foreach ($business as $item) {
                $arr_income[]=array(
                    "desc"=>$item['industry_name'],
                    "income"=>$item['income'],
                    "expense"=>$item['expense'],
                    "profit"=>$item['profit']
                );
                $total_income+=$item['profit'];
            }
        }

        if (count($asset)) {
            foreach ($asset as $item) {
                if ($item['officer_rent'] > 0) {
                    $arr_income[]=array(
                        "desc"=> $item['asset_name'] . "(" . $cert_list[$item['asset_type']] . ") Rental",
                        "income"=>$item['officer_rent'],
                        "expense"=>0,
                        "profit"=>$item['officer_rent']
                    );
                    $total_income+=$item['officer_rent'];
                }
            }
        }

        if (count($attachment_expense)) {
            foreach ($attachment_expense as $item) {
                $arr_expense[]=array(
                    "desc"=>$item['title'],
                    "expense"=>$item['ext_amount'],
                );
                $total_expense+=$item['ext_amount'];
            }
        }

        $arr_repay=array();
        $arr_repay['srs_old']=$last_cbc['pay_to_srs']?:0;
        $arr_repay['cbc']=$last_cbc['pay_to_cbc']?:0;
        $arr_srs_new=array();
        $is_only_super_loan=true;
        //计算srs-new
        if(count($arr_category)){
            foreach($arr_category as $cate){
                if($cate['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){
                   $is_only_super_loan=false;
                }
                $srs_new_item=0;
                $srs_new_formula="";
                $srs_new_type="Unknown";
                if(!$member_request['terms']){
                    $srs_new_formula="No Set Term";
                }else{
                    switch($cate['interest_type']){
                        case interestPaymentEnum::ANNUITY_SCHEME:
                        case interestPaymentEnum::ANYTIME_ANNUITY:
                            $srs_new_type="Annuity";
                            $srs_new_item=self::PMT($member_request['credit'],$member_request['interest_rate']/100,$member_request['terms']);
                            $srs_new_formula="PMT(InterestRate<code>".$member_request['interest_rate']."%</code>,Terms<code>".$member_request['terms']."</code>,RequestCredit<code>".$member_request['credit']."</code>)";
                            break;
                        case interestPaymentEnum::FIXED_PRINCIPAL:
                            $srs_new_type="Decline";
                            $srs_new_item=intval($member_request['credit']/$member_request['terms']);
                            $srs_new_formula="LoanAmount(".$member_request['credit'].") / Term(".$member_request['terms'].")";
                            break;
                        case interestPaymentEnum::SEMI_BALLOON_INTEREST:
                            $srs_new_type="Semi-Balloon";
                            if(!$arr_account['principal_periods']){
                                $srs_new_formula="No Set Principal Period";
                            }else{
                                $srs_new_item=intval($member_request['credit']/$member_request['terms']*$arr_account['principal_periods']);
                                $srs_new_formula="LoanAmount(".$member_request['credit'].") / Term(".$member_request['terms'].")*PrincipalPeriod(".$arr_account['principal_periods'].")";
                            }
                            break;

                        default:
                            $srs_new_type="Loan-AnyTime,Repay-OneTime";
                            $srs_new_item=intval($member_request['credit']);
                            $srs_new_formula="LoanAmount(".$member_request['credit'].")";
                    }
                }
                $arr_srs_new[]=array(
                    "category"=>$cate['alias'],
                    "repay_type"=>$srs_new_type,
                    "value"=>$srs_new_item,
                    "formula"=>$srs_new_formula
                );
            }
        }else{
            $error[]="No Set <kbd>Credit Category</kbd>";
        }


        if($member_request['terms']>0){
            $arr_repay['srs_new']=round($member_request['credit']/$member_request['terms']);
        }else{
            $error[]="No Set <kbd>Loan Term</kbd>";
        }
        if(count($arr_srs_new)){
            $arr_repay['srs_new']=current($arr_srs_new)['value'];
        }else{
            $arr_repay['srs_new']=0;
        }

        $state_ability=false;
        $total_profit=$total_income-$total_expense;
        $total_repay=$arr_repay['srs_old']+$arr_repay['srs_new']+$arr_repay['cbc'];
        if($total_repay>0){
            $ability=round($total_profit/$total_repay,2);
            if($ability>=1.3){
                $state_ability=true;
            }
        }else{
            $ability="N/A";
            $error[]="No Pending Repay";
        }

        foreach($arr_srs_new as $k=>$item){
            $tmp_repay=$arr_repay['srs_old']+$item['value']+$arr_repay['cbc'];
            $item['total_repay']=$tmp_repay;
            $item['state_ability']=false;
            if($tmp_repay){
                $item['ability_coefficient']=round($total_profit/$tmp_repay,2);
                if($item['ability_coefficient']>=1.3){
                    $item['state_ability']=true;
                }
            }else{
                $item['ability_coefficient']=0;

            }

            $arr_srs_new[$k]=$item;
        }


        $rate_asset = global_settingClass::getAssetsCreditGrantRateAndDefaultInterest();//默认抵押物估值折率
        $allowed_mortgage_type=member_assetsClass::getMortgageType();
        $allowed_collateral_type=member_assetsClass::getCollateralType();


        $arr_asset=array();

        $ret['rate_increase'] = $rate_asset;
        $total_assessment = 0;
        if (count($asset)) {
            foreach ($asset as $item) {
                $is_mortgage=0;
                if(in_array($item['asset_type'],$allowed_collateral_type)){
                    //担保类型不抵押
                }else{
                    if(!in_array($item['asset_type'],$allowed_mortgage_type)){
                        continue;
                    }
                    $is_mortgage=1;
                    if($item['officer_evaluation']<=0){
                       $error[]="Not allowed to mortgage:No Set Evaluation for<kbd>".$item['asset_name'] . "(" . $cert_list[$item['asset_type']] . ")"."</kbd>";
                    }
                }
                $credit_item = array(
                    "credit_key" => $item['asset_name'] . "(" . $cert_list[$item['asset_type']] . ")",
                    "credit_val" => $item['officer_evaluation'],
                    "credit_rate" => (($rate_asset[$item['asset_type']] ?: 0) * 100) . '%',  // 0就是0
                    "credit" => intval($item['officer_evaluation'] * ($rate_asset[$item['asset_type']] ?: 0)),  // 0就是0
                    "is_mortgage"=>$is_mortgage
                );
                $arr_asset[]=$credit_item;
                $total_assessment += $credit_item['credit'];
            }
        }
        $state_credit=0;
        if($member_request['credit']>0){
            $credit_coefficient=round($total_assessment/$member_request['credit'],2);
            if($credit_coefficient>=1.5){
                $state_credit=1;
            }
        }else{
            $credit_coefficient="N/A";
            $error[]="No Set Loan Credit";
        }
        $ret=array(
            "request"=>$member_request,
            "list_income"=>$arr_income,
            "list_salary"=>$list_salary,
            "list_expense"=>$arr_expense,
            "total_income"=>$total_income,
            "total_expense"=>$total_expense,
            "total_profit"=>$total_profit,
            "list_repay"=>$arr_repay,
            "list_srs_new"=>$arr_srs_new,
            "ability_coefficient"=>$ability,
            "total_repay"=>$total_repay,
            "state_ability"=>$state_ability,
            "credit_coefficient"=>$credit_coefficient,
            "list_assessment"=>$arr_asset,
            "state_credit"=>$state_credit,
            "total_credit"=>$total_assessment,
            "list_error"=>$error?:array(),
            "is_only_super_loan"=>$is_only_super_loan
        );
        return $ret;
    }

    /**
     * 等额本息计算函数
     * @param $principal
     * @param $rate //Monthly
     * @param $terms //Months
     * @return float
     */
    static function PMT($principal,$rate,$terms){
        //月还款额=本金*月利率*(1+月利率)^n/[(1+月利率)^n-1]
        if($rate<=0){
            return intval($principal/$terms);
        }
        return intval($principal*$rate*pow((1+$rate),$terms)/((pow((1+$rate),$terms))-1));
    }

    /**
     * 判断一次授信审核是否可以删除
     * @param $grant_id
     */
    static function checkGrantDeletePermission($grant_id){
        $grant_id=intval($grant_id);
        if(!$grant_id) return false;

        //如果已经签了授信合同，则不允许删除
        $sql="select * from member_authorized_contract where grant_credit_id=".qstr($grant_id);
        $r=new ormReader();
        $row=$r->getRow($sql);
        if($row){
            return false;
        }else{
            return true;
        }
    }
    static function getTempContractByCreditGrantProductID($grant_credit_product_id,$currency=currencyEnum::USD){
        $gp_uid=intval($grant_credit_product_id);//intval($_GET['grant_product_uid']);
        $gp_item=(new member_credit_grant_productModel())->find(array("uid"=>$gp_uid));
        $grant_item=(new member_credit_grantModel())->find(array("uid"=>$gp_item['grant_id']));
        $mcc_id=$gp_item['member_credit_category_id'];
        $mcc_item=(new member_credit_categoryModel())->find(array("uid"=>$mcc_id));
        $sub_product_item=(new loan_sub_productModel())->find(array("uid"=>$mcc_item['sub_product_id']));
        $product_info = (new loan_productModel())->find(array('uid'=>$sub_product_item['product_id']));
        $member_info=(new client_memberModel())->getRow($grant_item['member_id']);
        $loan_category = (new loan_categoryModel())->find(array(
            'uid' => $mcc_item['category_id']
        ));


        $args_currency = $currency;
        if( $args_currency == currencyEnum::USD ){
            $loan_amount = $gp_item['credit_usd'];
        }else{
            $loan_amount = $gp_item['credit_khr'];
        }
        if( $loan_amount <= 0 ){
            return new result(false,'Product credit is less than 0 of '.$args_currency);
        }
        $loan_period=$grant_item['credit_terms'];
        $payment_type=$sub_product_item['interest_type'];
        $payment_period=$sub_product_item['repayment_type'];
        $loan_period_unit=loanPeriodUnitEnum::MONTH;


        $rt = loan_baseClass::getLoanInterestDetail($grant_item['member_id'],
            $sub_product_item['uid'],$loan_amount,$args_currency,$loan_period*30,array(
                'member_credit_category_id' => $mcc_id
            ));

        if( !$rt->STS ){
            return $rt;
        }

        $base_interest_info = $rt->DATA['interest_info'];

        if($args_currency==currencyEnum::USD){
            $interest_info=array(
                //"currency"=>$args_currency,
                "interest_rate"=>$gp_item['interest_rate'],
                //"interest_rate_type"=>0,
                //"interest_rate_unit"=>interestRatePeriodEnum::MONTHLY,
                "operation_fee"=>$gp_item['operation_fee'],
                //"operation_fee_type"=>0,
                //"operation_fee_unit"=>interestRatePeriodEnum::MONTHLY
            );
        }else{
            $interest_info=array(
                //"currency"=>$args_currency,
                "interest_rate"=>$gp_item['interest_rate_khr'],
                //"interest_rate_type"=>0,
                //"interest_rate_unit"=>interestRatePeriodEnum::MONTHLY,
                "operation_fee"=>$gp_item['operation_fee_khr'],
                //"operation_fee_type"=>0,
                //"operation_fee_unit"=>interestRatePeriodEnum::MONTHLY
            );
        }

        $interest_info = array_merge((array)$base_interest_info,$interest_info);


        // 贷款账户
        $m_account = new loan_accountModel();
        $loan_account_info = $m_account->getRow(array(
            'obj_guid' => $member_info->obj_guid,
            'account_type' => loanAccountTypeEnum::MEMBER
        ));
        $client_due_date = date('d');
        if( $client_due_date > 28 ){
            $client_due_date = '01';
        }
        if( !$loan_account_info ){
            $loan_account_info = $m_account->newRow();
            $loan_account_info->obj_guid = $member_info->obj_guid;
            $loan_account_info->due_date = $client_due_date;
            $loan_account_info->account_type = loanAccountTypeEnum::MEMBER;
            $insert = $loan_account_info->insert();
            if( !$insert->STS ){
                return new result(false,'Loan account error: '.$insert->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }else{
            if( !$loan_account_info->due_date){
                $loan_account_info->due_date = $client_due_date;
                $loan_account_info->update_time = Now();
                $up = $loan_account_info->update();
                if( !$up->STS ){
                    return new result(false,'Update loan account due date fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
                }
            }
        }


        $today = date('Y-m-d');
        $is_single_repayment = interestTypeClass::isOnetimeRepayment($payment_type);
        $disburse_date = $today;
        $disburse_date_timestamp = strtotime($disburse_date);
        $interest_start_day = $disburse_date;   // 利息计算的起始时间
        $interest_start_timestamp = $disburse_date_timestamp;

        // 合同起止时间
        $contract_s_time = $interest_start_timestamp;
        $rt = loan_baseClass::getLoanEndDateTimestamp($loan_period,$loan_period_unit,$contract_s_time);
        if( !$rt->STS ){
            return $rt;
        }
        $contract_e_time = $rt->DATA;
        // 是否要重置还款日期
        $is_fix_loan_repayment_date = global_settingClass::loanIsFixClientRepaymentDate();

        if( $loan_category['special_key'] == specialLoanCateKeyEnum::QUICK_LOAN ){
            $is_fix_loan_repayment_date = false;
        }
        // 计算第一次还款日
        $first_repayment_day = null;
        //$calFirstRepaymentDate = null;
        // 计算第一次还款日期
        if( !$is_single_repayment ){
            $adjust_start_date = null;
            // 需要调整还款日
            if( $is_fix_loan_repayment_date ){
                $day = $loan_account_info->due_date?:$client_due_date;
                $adjust_start_date = date("Y-m-$day",$interest_start_timestamp);
                $first_repayment_day = interestTypeClass::getPeriodicFirstRepaymentDate($payment_period,$interest_start_day,$adjust_start_date);
                //$calFirstRepaymentDate = $first_repayment_day;
            }else{
                //$calFirstRepaymentDate = null;
                $adjust_start_date = $today;
                $first_repayment_day = interestTypeClass::getPeriodicFirstRepaymentDate($payment_period,$interest_start_day,$adjust_start_date);

            }
            $first_repayment_date_timestamp = strtotime($first_repayment_day);

        }else{
            //$calFirstRepaymentDate = null;
            $first_repayment_date_timestamp = $contract_e_time;
        }

        // 计算目标天数
        $rt = loan_baseClass::calLoanDays($loan_period,loanPeriodUnitEnum::MONTH);
        if( !$rt->STS ){
            return $rt;
        }
        $loan_days = $rt->DATA;
        if( $loan_days <= 0 ){
            return new result(false,'Invalid loan days',null,errorCodesEnum::INVALID_AMOUNT);
        }


        $first_repayment_day = date('Y-m-d',$first_repayment_date_timestamp);
        $loan_base=new loan_baseClass();
        $payment_re = $loan_base->getPaymentDetail($loan_amount,$loan_days,$loan_period,$loan_period_unit,$interest_info,$payment_type,$payment_period,$interest_start_day,$first_repayment_day,$loan_account_info);
        if( !$payment_re->STS ){
            return new result(false,'Create installment schema fail:'.$payment_re->MSG,null,errorCodesEnum::UNEXPECTED_DATA);
        }

        $payment_data=$payment_re->DATA;
        $installment_schema=$payment_data['payment_schema'];
        $payment_total = $payment_data['payment_total'];
        $contract_total_repayment = 0;
        $interest_date = $interest_start_day;
        foreach( $installment_schema as $k=>$v ){
            $installment_schema[$k]['receivable_date'] = $v['receive_date'];
            $installment_schema[$k]['interest_date'] = $interest_date;
            $contract_total_repayment += $v['amount'];
            $interest_date = $v['receive_date'];
        }

        $left_payable_info = $contract_total_repayment;

        $client_receive_amount = $loan_amount-$payment_total['deduct_interest']-$payment_total['deduct_operation_fee']-$payment_total['deduct_service_fee'];

        $disbursement_scheme = array(
            array(
                'uid' => 0,
                'contract_id' => 0,
                'scheme_idx' => 1,
                'disbursable_date' => $interest_start_day,
                'principal' => $loan_amount,
                'deduct_annual_fee' => 0,
                'deduct_interest' => $payment_total['deduct_interest'],
                'deduct_admin_fee' => 0,
                'deduct_loan_fee' => 0,
                'deduct_operation_fee' => $payment_total['deduct_operation_fee'],
                'deduct_insurance_fee' => 0,
                'deduct_service_fee' => $payment_total['deduct_service_fee'],
                'amount' => $client_receive_amount
            )
        );

        $contract=array(
            "uid"=>0,
            'account_id' => $loan_account_info['uid'],
            "contract_sn"=>"/",
            "virtual_contract_sn"=>"/",
            "apply_amount"=>$loan_amount,
            "currency"=>$args_currency,
            "loan_period_value"=>$loan_period,
            "loan_period_unit"=>$loan_period_unit,
            "repayment_type"=>$payment_type,
            "repayment_period"=>$payment_period,
            "due_date"=>"",
            "due_date_type"=>"",
            "receivable_admin_fee"=>0,
            "receivable_loan_fee"=>0,
            "receivable_insurance_fee"=>0,
            "receivable_operation_fee"=>$payment_data['payment_total']['total_operator_fee'],
            "total_service_fee"=>$payment_data['payment_total']['deduct_service_fee'],
            "receivable_interest"=>$payment_data['payment_total']['total_interest'],
            "create_time"=>Now(),
            'interest_rate' => $interest_info['interest_rate'],
            'interest_rate_type' => $interest_info['interest_rate_type'],
            'interest_rate_unit' => $interest_info['interest_rate_unit'],
            'operation_fee' => $interest_info['operation_fee'],
            'operation_fee_unit' => $interest_info['operation_fee_unit'],
            'operation_fee_type' => $interest_info['operation_fee_type'],
            'operation_min_value' => $interest_info['operation_min_value'],
            'penalty_rate' => $sub_product_item['penalty_rate'],
            'penalty_divisor_days' => $sub_product_item['penalty_divisor_days'],
            'penalty_is_compound_interest' => $sub_product_item['penalty_is_compound_interest']

        );
        //构造数据集合与         loan_contractClass::getLoanContractDetailInfo() 一样
        $return = array(
            'contract_id' => $contract['uid'],
            'contract_sn' => $contract['contract_sn'],
            'virtual_contract_sn' => $contract['virtual_contract_sn'],
            'loan_amount' => $contract['apply_amount'],
            'currency' => $contract['currency'],
            'loan_period_value' => $contract['loan_period_value'],
            'loan_period_unit' => $contract['loan_period_unit'],
            'repayment_type' => $contract['repayment_type'],
            'repayment_period' => $contract['repayment_period'],
            'due_date' => $contract['due_date'],
            'due_date_type' => $contract['due_date_type'],
            'due_date_type_val' => $contract['due_date_type'],
            'interest_rate' => $interest_info['interest_rate'],
            'interest_rate_type' => $interest_info['interest_rate_type'],
            'interest_rate_unit' => $interest_info['interest_rate_unit'],
            'total_admin_fee' => $contract['receivable_admin_fee'],
            'total_loan_fee' => $contract['receivable_loan_fee'],
            'total_insurance_fee' => $contract['receivable_insurance_fee'],
            'total_operation_fee' => $contract['receivable_operation_fee'],
            'total_service_fee' => $contract['receivable_service_fee'],
            'total_interest' => $contract['receivable_interest'],
            'total_deduct_interest' => $payment_total['deduct_interest'],
            'total_deduct_operation_fee' => $payment_total['deduct_operation_fee'],
            'total_deduct_service_fee' => $payment_total['deduct_service_fee'],
            'actual_receive_amount' => $client_receive_amount,
            'total_repayment' => $contract_total_repayment,
            'lending_time' => $contract['create_time'],
            'loan_product_info' => $product_info,
            'loan_sub_product_info' => $sub_product_item,
            'interest_info' => $interest_info, // 实际计算利率
            'contract_info' => $contract,
            'member_info' => $member_info,
            'loan_installment_scheme' => $installment_schema,
            'loan_disbursement_scheme' => $disbursement_scheme,
            'remain_payable_amount' => $left_payable_info,
            'product_category_name' => $mcc_item['alias']
        );
        return new result(true,"",$return);

    }
}