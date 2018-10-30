<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/16
 * Time: 13:01
 */
class api_insuranceControl extends bank_apiControl
{

    public function getContractDetailOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $contract_id = $params['contract_id'];
        $re = insurance_baseClass::getContractDetail($contract_id);
        return $re;
    }
}