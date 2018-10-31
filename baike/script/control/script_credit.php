<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/20
 * Time: 13:17
 */
class script_creditControl
{

    public function receiveCreditContractFeeByBalanceOp()
    {
        $all_row = (new member_authorized_contractModel())->getAllPendingPayContractFeeByBalance();
        $counter = array(
            'success' => 0,
            'fail' => 0
        );
        $log_path = 'credit_contract_fee';
        if( count($all_row) > 0 ){

            $fee_class = new receiveCreditContractFeeByBalanceClass();
            foreach( $all_row as $row ){
                // 允许为负了，一次执行
                $rt = $fee_class->execute($row['uid']);
                if( $rt->STS ){
                    $counter['success'] += 1;
                }else{
                    $counter['fail'] += 1;
                    logger::record('receive_credit_contract_fee',json_encode($rt),$log_path);
                }
            }
        }
        print_r($counter);
    }



    public function receiveCreditContractFeeByBalance_oldOp()
    {

        $all_row = (new member_authorized_contractModel())->getAllPendingPayContractFeeByBalance();
        $counter = array(
            'success' => 0,
            'fail' => 0
        );
        $log_path = 'credit_contract_fee';
        if( count($all_row) > 0 ){
            foreach( $all_row as $row ){

                // 需要用 try catch ，防止第一个错误阻止后面的执行
                try{

                    // 先执行start
                    $rt = receiveCreditContractFeeByBalanceClass::start($row['uid']);
                    if( !$rt->STS ){
                        print_r($rt);
                        $counter['fail'] += 1;
                        logger::record('receive_credit_contract_fee','Start:'.json_encode($rt),$log_path);
                        continue;
                    }

                    // 再confirm
                    $rt = receiveCreditContractFeeByBalanceClass::confirm($row['uid']);
                    if( !$rt->STS ){
                        print_r($rt);
                        $counter['fail'] += 1;
                        logger::record('receive_credit_contract_fee','Confirm:'.json_encode($rt),$log_path);
                        continue;
                    }

                    $counter['success'] += 1;

                }catch( Exception  $e ){
                    $counter['fail'] += 1;
                    logger::record('receive_credit_contract_fee','Unknown:'.$e->getMessage(),$log_path);
                    continue;
                }



            }
        }

        print_r($counter);

    }


}