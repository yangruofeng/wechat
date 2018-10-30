<?php

class userToUserTradingClass extends outstandingTradingClass {
    private $from_user_id;
    private $to_user_id;
    private $amount;
    private $currency;

    private $from_user_info;
    private $to_user_info;

    public function __construct($fromUserId, $toUserId, $amount, $currency,$remark=null)
    {
        parent::__construct();

        $this->from_user_id = $fromUserId;
        $this->to_user_id = $toUserId;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = 'User To User';
        $this->remark = $remark;

        $m_user = new um_userModel();

        $this->from_user_info = $m_user->find(array('uid'=>$fromUserId));
        $this->to_user_info = $m_user->find(array('uid'=>$toUserId));

        $this->sys_memo = $this->from_user_info['user_name'].'('.$this->from_user_info['user_code'].')'.
         'transfer to '. $this->to_user_info['user_name'].
         '('.$this->to_user_info['user_code'].') '.$remark.':'.$amount.$currency;
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     * @throws Exception
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_from = passbookClass::getUserPassbook($this->from_user_id);
        $passbook_to = passbookClass::getUserPassbook($this->to_user_id);

        // 构建detail
        // 转入账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_to,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,'Transfer from - '.$this->from_user_info['user_name']);
        // 转出账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_from,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,'Transfer to - '.$this->to_user_info['user_name']);

        return $detail;
    }

    public static function filterFlowsForConfirmVerify($flows) {
        $ret = array();
        foreach ($flows as $flow) {
            if ($flow->obj_type == "user" && $flow->debit > 0) {
                $ret[]=$flow;
            }
        }
        return $ret;
    }
}