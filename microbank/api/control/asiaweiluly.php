<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/31
 * Time: 10:05
 */
class asiaweilulyControl extends bank_apiControl
{
    function testOp()
    {
        $ace_api = new asiaweiluyApi();
        return  $ace_api->test();
    }


    function verifyClientMemberOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $aceAccount = trim($params['account']);
        if( !$aceAccount ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $re = asiaweiluyClass::verifyAceAccount($aceAccount);
        return $re;
    }

    function bindAceAccountStartOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return member_handlerClass::bindAceStart($params);
    }


    public function bindAceAccountFinishOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{
            $re = member_handlerClass::bindAceFinish($params);
            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }
            $conn->submitTransaction();
            return $re;
        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public function editAceAccountInfoOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{
            $re = member_handlerClass::editBindAceInfo($params);
            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }
            $conn->submitTransaction();
            return $re;
        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    public function unbindAceAccountStartOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $handler_id = $params['handler_id'];
        $sign = trim($params['sign']);
        return member_handlerClass::unbindAceStart($member_id,$handler_id,$sign);
    }


    public function unbindAceAccountFinishOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return member_handlerClass::unbindAceFinish($params);
    }





}