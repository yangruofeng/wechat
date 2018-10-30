<?php

class clientToClientTradingClass extends tradingClass {
    private $from_client_savings_passbook;
    private $to_client_savings_passbook;
    private $amount;
    private $currency;

    private $from_member_info;
    private $to_member_info;

    public function __construct($fromClientSavingsPassbook, $toClientSavingsPassbook, $amount, $currency)
    {
        parent::__construct();

        $this->from_client_savings_passbook = $fromClientSavingsPassbook;
        $this->to_client_savings_passbook = $toClientSavingsPassbook;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = "Transfer";

        $m_member = new memberModel();
        $from_book = $fromClientSavingsPassbook->getPassbookInfo();
        $to_book = $toClientSavingsPassbook->getPassbookInfo();
        $this->from_member_info = $m_member->find(array(
            'obj_guid' => $from_book['obj_guid'],
        ));
        $this->to_member_info = $m_member->find(array(
            'obj_guid' => $to_book['obj_guid'],
        ));

        $this->sys_memo = 'client '.($this->from_member_info['display_name']?:$this->from_member_info['login_code']).
            '(cid:'.$this->from_member_info['obj_guid'].')'.' transfer to '.
            ($this->to_member_info['display_name']?:$this->to_member_info['login_code']).
            '(cid:'.$this->to_member_info['obj_guid'].') :'.$amount.$currency;


    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     * @throws Exception
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 构建detail
        // 转出客人储蓄账户 - 借
        $detail[]=$this->createTradingDetailItem(
            $this->from_client_savings_passbook,
            $this->amount,
            $this->currency,
            accountingDirectionEnum::DEBIT,
            'Transfer to - '.$this->to_member_info['login_code']);
        // 转入客人储蓄账户 - 贷
        $detail[]=$this->createTradingDetailItem(
            $this->to_client_savings_passbook,
            $this->amount,
            $this->currency,
            accountingDirectionEnum::CREDIT,
            'Transfer from - '.$this->from_member_info['login_code']);

        return $detail;
    }
}