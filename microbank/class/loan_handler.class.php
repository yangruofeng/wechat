<?php

abstract class loan_handlerClass {
    protected $handler_info;

    protected function __construct($handler_info)
    {
        $this->handler_info = $handler_info;
    }

    private static $_cached_handlers = array();

    /**
     * @param $id
     * @return loan_handlerClass
     */
    public static function getHandler($id) {
        if (!array_key_exists($id, self::$_cached_handlers)) {
            $m = new member_account_handlerModel();
            $handler_info = $m->getRow($id);

            if( !$handler_info ){
                self::$_cached_handlers[$id] = null;
            }else{

                switch ($handler_info->handler_type) {
                    case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY:
                        self::$_cached_handlers[$id] = new loan_asiaweiluy_handlerClass($handler_info);
                        break;
                    default:
                        self::$_cached_handlers[$id] = null;
                        break;
                }
            }

        }
        return self::$_cached_handlers[$id];
    }

    public function disburse($schema) {

       return loanDisbursementWorkerClass::schemaDisburse($schema['uid']);

    }


    /** 自动扣款
     * @param $handler_id
     * @param $amount
     * @param string $currency
     * @return result
     */
    public function automaticDeduction($refBiz,$amount,$currency,$description)
    {
        return $this->apiInstallmentExecute($refBiz,$amount, $currency, $description,false);

    }


    /**
     * @param $refBiz
     *  $refBiz['type'],
     *  $refBiz['sub_type'],
     *  $refBiz['account_id'],
     *  $refBiz['biz_id'],
     * @param $amount
     * @param $currency
     * @param $description
     * @return result
     */
    public function deposit($refBiz,$amount,$currency,$description)
    {
        return $this->apiDisbursementExecute($refBiz,$amount,$currency,$description);
    }

    public abstract function getHandlerMultiCurrencyBalance();



    /**
     * @return array
     */
    protected abstract function getDisbursementSpecialInfo();

    /**
     * @param $refBiz array 外部业务信息
     * @param $amount
     * @param $currency
     * @param $description
     * @return result
     */
    public abstract function apiDisbursementExecute($refBiz, $amount, $currency, $description);

    /**
     * @return array
     */
    protected abstract function getInstallmentSpecialInfo();

    /**
     * @param $refBiz array 外部业务信息
     * @param $amount
     * @param $currency
     * @param $description
     * @param $maximizationDeduction
     * @return result
     */
    public abstract function apiInstallmentExecute($refBiz, $amount, $currency, $description, $maximizationDeduction=true);
}