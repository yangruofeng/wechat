<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/3/1
 * Time: 12:01
 */

abstract class tradingClass {
    protected static function className2TradingType($className) {
        return strtolower(preg_replace('/([a-z\d])([A-Z])/', '$1_$2', preg_replace('/(?:Trading)?Class$/', '', $className)));
    }

    protected static function tradingType2ClassName($tradingType) {
        return preg_replace_callback('/([a-z\d])_([a-z])/', function($matches) {
            return $matches[1] . strtoupper($matches[2]);
        }, $tradingType) . "TradingClass";
    }


    private $trading_type;
    public $category;
    public $remark;
    public $subject;
    public $is_outstanding;
    public $is_lock;
    public $sys_memo;
    public $ref_trade_id;

    protected function __construct() {
        $this->trading_type = self::className2TradingType(get_class($this));
    }

    /**
     * 是否允许余额为负
     * ***注意***，需要特别小心使用，如非必要，不要重写此方法
     * @return bool
     */
    protected function allowNegativeBalance() {
        return false;
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     * @throws Exception
     */
    protected abstract function getTradingDetail();

    /**
     * 获得交易明细记录项
     * @param $passbook passbookClass
     * @param $amount
     * @param $currency
     * @param $direction
     * @param $exchange_rate
     * @param $subject
     * @return array
     */
    protected function createTradingDetailItem($passbook, $amount, $currency, $direction, $subject = null, $exchange_rate = 1.0, $base_currency = null) {
        return array(
            'passbook' => $passbook,
            'currency' => $currency,
            'credit' => $direction == accountingDirectionEnum::CREDIT ? $amount : 0,
            'debit' => $direction == accountingDirectionEnum::DEBIT ? $amount : 0,
            'subject' => $subject,
            'exchange_rate' => $exchange_rate,
            'base_currency' => $base_currency
        );
    }

    /**
     * 检查交易明细记录合法性，要求各种货币借方金额与贷方金额合计相等
     * @param $tradingDetail
     * @return result
     */
    private function checkTradingDetail($tradingDetail) {
        // 统计各种货币借方与贷方的合计金额
        $sum_credit = array();    // 贷方金额合计
        $sum_debit = array();     // 借方金额合计
        $currencies = array();    // 交易涉及到的货币列表
        foreach ($tradingDetail as $item) {
            $currency = $item['currency'];
            if (!in_array($currency, $currencies)) {
                $currencies[]=$currency;
                $sum_credit[$currency] = 0;
                $sum_debit[$currency] = 0;
            }
            $sum_credit[$currency] += $item['credit'];
            $sum_debit[$currency] += $item['debit'];
        }
        // 检查各种货币借贷双方金额是否相等
        foreach ($currencies as $currency) {
            if (round($sum_debit[$currency],2) != round($sum_credit[$currency],2)) {
                return new result(false, 'Invalid trading detail', null, errorCodesEnum::UNEXPECTED_DATA);
            }
        }

        return new result(true);
    }

    /**
     * 保存交易主要信息
     * @return result
     */
    private function insertTradingInfo() {
        $trading_model = new passbook_tradingModel();
        $trading_row = $trading_model->newRow();
        $trading_row->category = $this->category;
        $trading_row->trading_type = $this->trading_type;
        $trading_row->subject = $this->subject;
        $trading_row->remark = $this->remark;
        $trading_row->is_outstanding = $this->is_outstanding;
        $trading_row->create_time = date("Y-m-d H:i:s");
        $trading_row->update_time = date("Y-m-d H:i:s");
        $trading_row->state = passbookTradingStateEnum::CREATE;
        if( $this->sys_memo ){
            $trading_row->sys_memo = $this->sys_memo;
        }else{
            $trading_row->sys_memo = $this->subject.': '.$this->remark;
        }
        if( $this->ref_trade_id ){
            $trading_row->ref_trade_id = intval($this->ref_trade_id);
        }
        $ret = $trading_row->insert();
        if (!$ret->STS) {
            return new result(false, 'Insert trading failed - ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        } else {
            return new result(true, null, $trading_row);
        }
    }

    /**
     * 保存交易明细信息
     * @param $tradingDetail
     * @param $tradingRow
     * @return result
     */
    private function insertTradingDetail($tradingDetail, $tradingRow) {
        $flow_model = new passbook_account_flowModel();
        $flows = array();
        $accounts = array();
        foreach ($tradingDetail as $item) {
            if ($item['credit'] == 0 && $item['debit'] == 0) continue;

            $passbook = $item['passbook'];
            $account = $passbook->getAccount($item['currency']);
            $account_balance = $account->balance - $account->outstanding;
            $row = $flow_model->newRow();
            $row->account_id = $account->uid;
            $row->credit = $item['credit'];
            $row->debit = $item['debit'];
            $row->subject = $item['subject'] ?: $this->subject;
            $row->exchange_rate = $item['exchange_rate'];
            $row->base_currency = $item['base_currency'];
            $row->remark=$this->remark?:'';

            $delta = $passbook->getBalanceDelta($item['credit'], $item['debit']);
            if (!$tradingRow->is_outstanding) {
                $row->begin_balance = $account->balance;
                $row->end_balance = $account->balance + $delta;
                $account->balance = $row->end_balance;
            } else {
                if ($delta < 0) {
                    $account->outstanding -= $delta;
                }
            }

            $row->trade_id = $tradingRow->uid;
            $row->create_time = date("Y-m-d H:i:s");
            $row->update_time = date("Y-m-d H:i:s");
            $row->state = passbookAccountFlowStateEnum::CREATE;
            $ret = $row->insert();
            if (!$ret->STS) {
                return new result(false, 'Insert flow failed - '. $ret->MSG, null, errorCodesEnum::DB_ERROR);
            }


            if ($delta < 0 && !$this->allowNegativeBalance() &&
                !in_array($passbook->getPassbookInfo()->obj_type, array('gl_account', 'partner'))) {
                // 直接使用数据库数据比较存在小数精度位数问题
                if (!$this->is_lock && bccomp($account->balance,$account->outstanding,2) < 0) {
                    return new result(false, $passbook->getPassbookInfo()->obj_type.'-Insufficient Balance - ' . $passbook->getName() . "/" .$account_balance. $item['currency'] .' for '.($item['credit']+$item['debit']).$item['currency'], null, errorCodesEnum::BALANCE_NOT_ENOUGH);
                }
            }

            $flows[]=$row;
            $accounts[$account->uid] = $account;
        }

        return new result(true, null, array(
            'flows' => $flows,
            'accounts' => $accounts));
    }

    /**
     * 完成交易，一齐更改记录状态、更新相关账户余额
     * @param $tradingRow
     * @param $tradingFlows
     * @param $accounts
     * @return result
     */
    private function completeTrading($tradingRow, $tradingFlows, $accounts)
    {
        $tables = array();
        $sets = array();
        $filters = array();

        // trading 表
        $tables[] = "passbook_trading t";
        $sets[] = "t.state=" . passbookTradingStateEnum::DONE;
        $sets[] = "t.update_time=" . qstr(date("Y-m-d H:i:s"));
        $filters[] = "t.uid=" . $tradingRow->uid . " AND t.state=" . passbookTradingStateEnum::CREATE;

        // flow 表
        foreach ($tradingFlows as $i => $flow) {
            $tables[] = "passbook_account_flow f$i";
            if ($tradingRow->is_outstanding) {
                $sets[] = "f$i.state=" . passbookAccountFlowStateEnum::OUTSTANDING;
            } else {
                $sets[] = "f$i.state=" . passbookAccountFlowStateEnum::DONE;
            }
            $sets[] = "f$i.update_time=" . qstr(date("Y-m-d H:i:s"));
            $filters[] = "f$i.uid=" . $flow->uid . " AND f$i.state=" . passbookAccountFlowStateEnum::CREATE;
        }

        // account 表
        foreach ($accounts as $i => $account) {
            $tables[] = "passbook_account a$i";
            $sets[] = "a$i.update_time=" . qstr(date("Y-m-d H:i:s"));
            $filters[] = "a$i.uid=" . $account->uid;
            if ($tradingRow->is_outstanding) {
                $sets[] = "a$i.outstanding=" . $account->outstanding;
                $filters[]= "a$i.outstanding=" . $account->getOldRow()->outstanding;
            } else {
                $sets[] = "a$i.balance=" . $account->balance;
                $filters[]= "a$i.balance=" . $account->getOldRow()->balance;
            }
        }

        $sql = "update " . join(",", $tables) . " set " . join(",", $sets) . " where " . join(" AND ", $filters);
        $ret = ormYo::Conn()->execute($sql);
        if (!$ret->STS) {
            return new result(false, 'Complete trading failed - ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        } else if ($ret->AFFECTED_ROWS == 0) {
            return new result(false, 'Complete trading failed - update conflicted', null, errorCodesEnum::UNKNOWN_ERROR);
        } else {
            return new result(true);
        }
    }

    /**
     * 确认交易，一齐更改记录状态，账户outstanding金额
     * @param $tradingRow
     * @param $tradingFlows
     * @param $accounts
     * @return result
     */
    private static function confirmTrading($tradingRow, $tradingFlows, $accounts)
    {
        $tables = array();
        $sets = array();
        $filters = array();

        // trading 表
        $tables[] = "passbook_trading t";
        $sets[] = "t.is_outstanding=0";
        $sets[] = "t.update_time=". qstr(date("Y-m-d H:i:s"));
        $filters[] = "t.uid=" . $tradingRow->uid . " AND t.is_outstanding = 1 AND t.state=" . passbookTradingStateEnum::DONE;

        // flow 表
        foreach ($tradingFlows as $i => $flow) {
            $tables[] = "passbook_account_flow f$i";
            $sets[] = "f$i.begin_balance = " . floatval($flow->begin_balance);
            $sets[] = "f$i.end_balance = " . floatval($flow->end_balance);
            $sets[] = "f$i.state=" . passbookAccountFlowStateEnum::DONE;
            $sets[] = "f$i.update_time=" . qstr(date("Y-m-d H:i:s"));
            $filters[] = "f$i.uid=" . $flow->uid . " AND f$i.state=" . passbookAccountFlowStateEnum::OUTSTANDING;
        }

        // account 表
        foreach ($accounts as $i => $account) {
            $tables[] = "passbook_account a$i";
            $sets[] = "a$i.balance=" . $account->balance;
            $sets[] = "a$i.update_time=" . qstr(date("Y-m-d H:i:s"));
            $filters[]= "a$i.balance=" . $account->getOldRow()->balance;
            $filters[] = "a$i.uid=" . $account->uid;

            if ($account->getOldRow()->outstanding != $account->outstanding) {
                $sets[] = "a$i.outstanding=" . $account->outstanding;
                $filters[]= "a$i.outstanding=" . $account->getOldRow()->outstanding;
            }
        }

        $sql = "update " . join(",", $tables) . " set " . join(",", $sets) . " where " . join(" AND ", $filters);
        $ret = ormYo::Conn()->execute($sql);
        if (!$ret->STS) {
            return new result(false, 'Confirm trading failed - ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        } else if ($ret->AFFECTED_ROWS == 0) {
            return new result(false, 'Confirm trading failed - update conflicted', null, errorCodesEnum::UNKNOWN_ERROR);
        } else {
            return new result(true);
        }
    }

    /**
     * 取消交易，一齐更改记录状态，账户outstanding金额
     * @param $tradingRow
     * @param $tradingFlows
     * @param $accounts
     * @return result
     */
    private static function cancelTrading($tradingRow, $tradingFlows, $accounts)
    {
        $tables = array();
        $sets = array();
        $filters = array();

        // trading 表
        $tables[] = "passbook_trading t";
        $sets[] = "t.state=" . passbookTradingStateEnum::CANCELLED;
        $sets[] = "t.update_time=". qstr(date("Y-m-d H:i:s"));
        $filters[] = "t.uid=" . $tradingRow->uid . " AND t.is_outstanding = 1 AND t.state=" . passbookTradingStateEnum::DONE;

        // flow 表
        foreach ($tradingFlows as $i => $flow) {
            $tables[] = "passbook_account_flow f$i";
            $sets[] = "f$i.state=" . passbookAccountFlowStateEnum::CANCELLED;
            $sets[] = "f$i.update_time=" . qstr(date("Y-m-d H:i:s"));
            $filters[] = "f$i.uid=" . $flow->uid . " AND f$i.state=" . passbookAccountFlowStateEnum::OUTSTANDING;
        }

        // account 表
        foreach ($accounts as $i => $account) {
            if ($account->getOldRow()->outstanding != $account->outstanding) {
                $tables[] = "passbook_account a$i";
                $sets[] = "a$i.outstanding=" . $account->outstanding;
                $sets[] = "a$i.update_time=" . qstr(date("Y-m-d H:i:s"));
                $filters[] = "a$i.uid=" . $account->uid;
                $filters[]= "a$i.outstanding=" . $account->getOldRow()->outstanding;
            }
        }

        $sql = "update " . join(",", $tables) . " set " . join(",", $sets) . " where " . join(" AND ", $filters);
        $ret = ormYo::Conn()->execute($sql);
        if (!$ret->STS) {
            return new result(false, 'Confirm trading failed - ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        } else if ($ret->AFFECTED_ROWS == 0) {
            return new result(false, 'Confirm trading failed - update conflicted', null, errorCodesEnum::UNKNOWN_ERROR);
        } else {
            return new result(true);
        }
    }

    /**
     * 执行交易
     * 交易的内容在$this之中，由具体的交易类构建成trading标准的数据
     * @return result
     */
    public function execute() {
        try {
            $trading_detail = $this->getTradingDetail();
            // 检查trading detail
            $ret = $this->checkTradingDetail($trading_detail);
            if (!$ret->STS) return $ret;

            // 建立trading数据
            $ret = $this->insertTradingInfo();
            if (!$ret->STS) return $ret;
            $trading_row = $ret->DATA;

            // 建立flow数据，并构建完成交易更新所需参数
            $ret = $this->insertTradingDetail($trading_detail, $trading_row);
            if (!$ret->STS) return $ret;
            $flows = $ret->DATA['flows'];
            $accounts = $ret->DATA['accounts'];

            // 准备都没有问题，一起更新状态、余额完成交易
            $ret = $this->completeTrading($trading_row, $flows, $accounts);
            if (!$ret->STS) return $ret;

            return new result(true, null, $trading_row->uid);
        } catch(Exception $ex) {
            return new result(false, $ex->getMessage(), null, $ex->getCode());
        }
    }

    /**
     * 验证签名
     * @param $tradingInfo
     * @param $tradingFlows
     * @param $signs
     * @return result
     */
    private static function verifySigns($tradingInfo, $tradingFlows, $signs) {
        try {
            // 获得trading的类，寻找filterFlowsForConfirmVerify方法，如果有并符号格式，调用该方法获得需要验证签名的flow
            $trading_class = new ReflectionClass(self::tradingType2ClassName($tradingInfo->trading_type));
            if ($trading_class->hasMethod("filterFlowsForConfirmVerify")) {
                $filter_method = $trading_class->getMethod("filterFlowsForConfirmVerify");
                if ($filter_method->getNumberOfRequiredParameters() == 1) {
                    $verifying_flows = $filter_method->invoke(null, $tradingFlows);
                } else {
                    $verifying_flows = array();
                }
            } else {
                $verifying_flows = array();
            }

            // 构建需要验证的flow签名数据
            $verifying_data = array();
            foreach ($verifying_flows as $flow) {
                if (!$verifying_data[$flow->obj_guid]) {
                    $verifying_data[$flow->obj_guid] = array(
                        'sign' => $signs[$flow->obj_guid],
                        'data' => $flow->currency . round($flow->credit + $flow->debit, 2),
                        'obj_guid' => $flow->obj_guid,
                        'obj_type' => $flow->obj_type
                    );
                } else {
                    $verifying_data[$flow->obj_guid]['data'] .= $flow->currency . round($flow->credit + $flow->debit, 2);
                }
            }

            // 验证
            foreach ($verifying_data as $item) {
                $trading_password = self::getTradingPassword($item['obj_guid'], $item['obj_type']);
                if ($trading_password) {
                    if (!$item['sign']) {
                        return new result(false, $item['obj_guid'] . ' confirmation required', null, errorCodesEnum::INVALID_PARAM);
                    }

                    $sign = md5($tradingInfo->uid . $item['data'] . $trading_password);
                    if ($sign != $item['sign']) {
                        return new result(false, $item['obj_guid'] . ' sign mismatched', null, errorCodesEnum::DATA_INCONSISTENCY);
                    }
                }
            }

            return new result(true);
        } catch (Exception $ex) {
            return new result(false, $ex->getMessage(), null, $ex->getCode());
        }
    }

    /**
     * 确认交易
     * @param int $tradingId 交易ID
     * @param array $signs 多组签名，格式为： (
     *  $guid => md5($tradingId . $currency . $amount . md5($password))
     * )
     * @return result
     */
    public static function confirm($tradingId, $signs = array())
    {
        try {
            $trading_model = new passbook_tradingModel();
            $account_model = new passbook_accountModel();
            $passbook_model = new passbookModel();

            $trading_info = $trading_model->getRow($tradingId);
            if (!$trading_info) {
                return new result(false, 'Trading not found', null, errorCodesEnum::UNEXPECTED_DATA);
            }
            if ($trading_info->state == passbookTradingStateEnum::CANCELLED) {
                return new result(false, 'Trading is cancelled', null, errorCodesEnum::TRADING_CANCELLED);
            } else if ($trading_info->state != passbookTradingStateEnum::DONE) {
                return new result(false, 'Trading state [' . $trading_info->state . '] is not supported to confirm', null, errorCodesEnum::TRADING_UNEXPECTED_STATE);
            }
            if (!$trading_info->is_outstanding) {
                return new result(false, 'Trading is finished', null, errorCodesEnum::TRADING_FINISHED);
            }

            // 获得本次交易下的所有flow
            $trading_flows = $trading_model->getTradingFlows($tradingId);

            // 验证签名
            $verify_ret = self::verifySigns($trading_info, $trading_flows, $signs);
            if (!$verify_ret->STS) return new result(false, "Verify Failed - " . $verify_ret->MSG, null, errorCodesEnum::NOT_PERMITTED, $verify_ret);

            // 获得本次交易涉及到的account
            $accounts = $account_model->loadAccountsByFlows($trading_flows);
            // 获得本次交易涉及到的passbook
            $passbooks = $passbook_model->loadPassbooksByFlows($accounts);

            // 更新account
            foreach ($trading_flows as $flow) {
                $account = $accounts[$flow->account_id];
                $passbook_info = $passbooks[$account->book_id];
                $delta = passbookClass::getDelta($flow->book_type, $flow->credit, $flow->debit);
                $flow->begin_balance = $account->balance;
                $account->balance += $delta;
                $flow->end_balance = $account->balance;
                if ($delta < 0) {
                    $account->outstanding += $delta;
                    if (!in_array($passbook_info->obj_type, array('gl_account', 'partner'))) {
                        if ($account->balance < 0) {
                            return new result(false, 'Insufficient Balance - AccountID:' . $account->uid . "/" . $flow->begin_balance . $account->currency . ' for ' . (-$delta), null, errorCodesEnum::BALANCE_NOT_ENOUGH);
                        }
                    }
                }
            }

            return self::confirmTrading($trading_info, $trading_flows, $accounts);
        } catch (Exception $ex) {
            return new result(false, $ex->getMessage(), null, $ex->getCode());
        }
    }

    /**
     * 拒绝交易
     * @param $tradingId
     * @param array $signs
     * @return result
     */
    public static function reject($tradingId, $signs = array()) {
        try {
            $trading_model = new passbook_tradingModel();
            $account_model = new passbook_accountModel();

            $trading_info = $trading_model->getRow($tradingId);
            if (!$trading_info) {
                return new result(false, 'Trading not found', null, errorCodesEnum::UNEXPECTED_DATA);
            }
            if ($trading_info->state == passbookTradingStateEnum::CANCELLED) {
                return new result(false, 'Trading is cancelled', null, errorCodesEnum::TRADING_CANCELLED);
            } else if ($trading_info->state != passbookTradingStateEnum::DONE) {
                return new result(false, 'Trading state [' . $trading_info->state . '] is not supported to reject', null, errorCodesEnum::TRADING_UNEXPECTED_STATE);
            }
            if (!$trading_info->is_outstanding) {
                return new result(false, 'Trading is finished', null, errorCodesEnum::TRADING_FINISHED);
            }

            // 获得本次交易下的所有flow
            $trading_flows = $trading_model->getTradingFlows($tradingId);

            // 验证签名
            $verify_ret = self::verifySigns($trading_info, $trading_flows, $signs);
            if (!$verify_ret->STS) return new result(false, "Verify Failed - " . $verify_ret->MSG, null, errorCodesEnum::NOT_PERMITTED, $verify_ret);

            // 获得本次交易涉及到的account
            $accounts = $account_model->loadAccountsByFlows($trading_flows);

            // 更新account
            foreach ($trading_flows as $flow) {
                $account = $accounts[$flow->account_id];
                $delta = passbookClass::getDelta($flow->book_type, $flow->credit, $flow->debit);
                if ($delta < 0) {
                    $account->outstanding += $delta;
                }
            }

            return self::cancelTrading($trading_info, $trading_flows, $accounts);
        } catch (Exception $ex) {
            return new result(false, $ex->getMessage(), null, $ex->getCode());
        }
    }

    /**
     * @param string $guid  obj_guid
     * @param string $type  obj_type
     * @return string
     * @throws Exception
     */
    private static function getTradingPassword($guid, $type) {
        switch ($type) {
            case "client_member":
                $member_info = (new memberModel())->getRow(array('obj_guid' => $guid));
                if (!$member_info) throw new Exception("Member not found", errorCodesEnum::UNEXPECTED_DATA);
                return $member_info->trading_password;
            case "user":
                $user_info = (new um_userModel())->getRow(array('obj_guid' => $guid));
                if (!$user_info) throw new Exception("User not found", errorCodesEnum::UNEXPECTED_DATA);
                return $user_info->trading_password;
            default:
                throw new Exception("Trading password is not supported now for - " . $type, errorCodesEnum::NOT_SUPPORTED);
        }
    }
}
