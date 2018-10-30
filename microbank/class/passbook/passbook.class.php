<?php

class passbookClass {
    private $passbook_info;
    private $accounts;

    public function __construct($passbookInfo)
    {
        $this->passbook_info = $passbookInfo;
    }

    public function getPassbookInfo()
    {
        return $this->passbook_info;
    }

    public function getBookId()
    {
        return $this->passbook_info['uid'];
    }

    public function getName() {
        if ($this->passbook_info['book_name'])
            return $this->passbook_info['book_name'];
        else
            return $this->passbook_info['uid'];
    }

    public function getBookType()
    {
        return $this->passbook_info['book_type'];
    }


    public static function getPassbookInstanceById($book_id)
    {
        $book_id = intval($book_id);
        $book_info = (new passbookModel())->getRow($book_id);
        if( !$book_info ){
            throw new Exception('No book info:'.$book_id,errorCodesEnum::NO_DATA);
        }
        return new passbookClass($book_info);
    }

    public static function createPassbookAccounts($book_id,$currency=null,$gl_codes=array())
    {
        $m = new passbook_accountModel();
        $create_time = Now();

        $sql = "insert into passbook_account(book_id,currency,create_time,operator_id,operator_name,gl_code) values ";
        if( !$currency ){

            $currency_arr = (new currencyEnum())->toArray();
            $data = array();
            foreach ($currency_arr as $currency) {
                $str = "(".join(",", array_map(
                    function($item) {return qstr($item);},
                    array($book_id, $currency, $create_time, '0', 'System', $gl_codes[$currency]))).")";
                $data[] = $str;
            }
            $sql_str = implode(',',$data);
            $sql .= trim($sql_str,',');
        }else{
            $sql .= "(".join(",", array_map(
                    function($item) {return qstr($item);},
                    array($book_id, $currency, $create_time, '0', 'System', $gl_codes[$currency]))).")";
        }

        $insert = $m->conn->execute($sql);
        if( !$insert->STS ){
            return new result(false,'Create passbook account fail - ' . $insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');

    }

    public static function getOrCreatePassbookByObjGuid($guid, $createOpts = null) {
        $passbook_model = new passbookModel();
        $passbook_info = $passbook_model->getRow(array(
            'obj_guid' => $guid
        ));

        if (!$passbook_info) {
            if (!$createOpts) throw new Exception("Cannot found passbook");
            $passbook_info = $passbook_model->newRow();
            $passbook_info->obj_guid = $guid;
            $passbook_info->state = passbookStateEnum::ACTIVE;
            $passbook_info->book_code=$createOpts['book_code'];
            $passbook_info->parent_book_code=$createOpts['parent_book_code'];
            $passbook_info->book_name = $createOpts['book_name'];
            $passbook_info->book_type = $createOpts['book_type'];
            $passbook_info->obj_type = $createOpts['obj_type'];
            $passbook_info->create_time = date("Y-m-d H:i:s");
            $passbook_info->create_org = 0;
            $passbook_info->operator_id = 0;
            $passbook_info->operator_name = 'System';
            $passbook_info->update_time = date("Y-m-d H:i:s");
            $ret = $passbook_info->insert();


            if (!$ret->STS)
                throw new Exception("Create passbook failed - " . $ret->MSG);
            $rt = self::createPassbookAccounts($passbook_info->uid,null,$createOpts['currency_codes']);

            if( !$rt->STS ){
                throw new Exception($rt->MSG);
            }

        }

        return new passbookClass($passbook_info);
    }

    /**
     * 获得member的储蓄账户的passbook
     * @param $guid
     * @return passbookClass
     */
    public static function getSavingsPassbookOfMemberGUID($guid)
    {
        $gl_acct = gl_accountClass::getDynamicAccount(objGuidTypeEnum::CLIENT_MEMBER);

        $member_acct=memberClass::getMemberInfoByGUID($guid);
        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$member_acct['login_code'];
        $book_code=$gl_acct['book_code'].'-'.$guid;
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,$guid);

        return self::getOrCreatePassbookByObjGuid($guid
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::DEBT,          // 储蓄账户是负债类
                'obj_type' => 'client_member',
                'currency_codes' => $gl_codes
            )
        );
    }

    /**
     * 根据member-id获取member的储蓄账户
     * @param $member_id
     * @return passbookClass
     */
    public static function getSavingsPassbookOfMemberId($member_id){
        $gl_acct = gl_accountClass::getDynamicAccount(objGuidTypeEnum::CLIENT_MEMBER);
        $member_instance=memberClass::getInstanceByID($member_id);
        $guid=$member_instance->getSavingsGUID();
        $member_acct=$member_instance->getInstanceProperty();

        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$member_acct['login_code'];
        $book_code=$gl_acct['book_code'].'-'.$guid;
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,$guid);

        return self::getOrCreatePassbookByObjGuid($guid
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::DEBT,          // 储蓄账户是负债类
                'obj_type' => 'client_member',
                'currency_codes' => $gl_codes
            )
        );
    }

    /**
     * 获得loanAccount储蓄账户的passbook todo:如果增加了obj-type，这里逻辑要改
     * @param $loanAccount loan_accountClass
     * @return passbookClass
     */
    public static function getSavingsPassbookOfLoanAccount($loanAccount) {
        $loan_acct_info=$loanAccount->getAccountInfo();
        $guid=$loan_acct_info['obj_guid'];
        $member_acct=memberClass::getMemberInfoByGUID($guid);

        $gl_acct = gl_accountClass::getDynamicAccount(objGuidTypeEnum::CLIENT_MEMBER);
        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$member_acct['login_code'];
        $book_code=$gl_acct['book_code'].'-'.$guid;



        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,$guid);

        return self::getOrCreatePassbookByObjGuid($loanAccount->getSavingsGUID()
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::DEBT,          // 储蓄账户是负债类
                'obj_type' => 'client_member',
                'currency_codes' => $gl_codes
            )
        );
    }

    /**
     * 获得loanAccount短期应收贷款账户的passbook
     * @param $loanAccount loan_accountClass
     * @return passbookClass
     */
    public static function getShortLoanPassbookOfLoanAccount($loanAccount) {
        $loan_acct_info=$loanAccount->getAccountInfo();
        $guid=$loan_acct_info['obj_guid'];
        $member_acct=memberClass::getMemberInfoByGUID($guid);

        $gl_acct = gl_accountClass::getDynamicAccount(objGuidTypeEnum::SHORT_LOAN);
        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$member_acct['login_code'];
        $book_code=$gl_acct['book_code'].'-'.$guid;
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,$guid);

        return self::getOrCreatePassbookByObjGuid($loanAccount->getShortLoanGUID()
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::ASSET,        // 应收贷款账户是资产类
                'obj_type' => 'client_short_loan',
                'currency_codes' => $gl_codes
            )
        );
    }

    /**
     * 获得loanAccount长期应收贷款账户的passbook
     * @param $loanAccount loan_accountClass
     * @return passbookClass
     */
    public static function getLongLoanPassbookOfLoanAccount($loanAccount) {
        $loan_acct_info=$loanAccount->getAccountInfo();
        $guid=$loan_acct_info['obj_guid'];
        $member_acct=memberClass::getMemberInfoByGUID($guid);

        $gl_acct = gl_accountClass::getDynamicAccount(objGuidTypeEnum::LONG_LOAN);
        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$member_acct['login_code'];
        $book_code=$gl_acct['book_code'].'-'.$guid;
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,$guid);

        return self::getOrCreatePassbookByObjGuid($loanAccount->getLongLoanGUID()
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::ASSET,        // 应收贷款账户是资产类
                'obj_type' => 'client_long_loan',
                'currency_codes' => $gl_codes
            )
        );
    }

    /**
     * 获得loanAccount短期存款账户的passbook
     * @param $loanAccount loan_accountClass
     * @return passbookClass
     */
    public static function getShortDepositPassbookOfLoanAccount($loanAccount) {
        $loan_acct_info=$loanAccount->getAccountInfo();
        $guid=$loan_acct_info['obj_guid'];
        $member_acct=memberClass::getMemberInfoByGUID($guid);

        $gl_acct = gl_accountClass::getDynamicAccount(objGuidTypeEnum::SHORT_DEPOSIT);
        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$member_acct['login_code'];
        $book_code=$gl_acct['book_code'].'-'.$guid;
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,$guid);

        return self::getOrCreatePassbookByObjGuid($loanAccount->getShortDepositGUID()
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::DEBT,        // 储蓄账户是负债类
                'obj_type' => 'client_short_deposit',
                'currency_codes' => $gl_codes
            )
        );
    }

    /**
     * 获得loanAccount长期存款账户的passbook
     * @param $loanAccount loan_accountClass
     * @return passbookClass
     */
    public static function getLongDepositPassbookOfLoanAccount($loanAccount) {
        $loan_acct_info=$loanAccount->getAccountInfo();
        $guid=$loan_acct_info['obj_guid'];
        $member_acct=memberClass::getMemberInfoByGUID($guid);

        $gl_acct = gl_accountClass::getDynamicAccount(objGuidTypeEnum::LONG_DEPOSIT);
        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$member_acct['login_code'];
        $book_code=$gl_acct['book_code'].'-'.$guid;
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,$guid);

        return self::getOrCreatePassbookByObjGuid($loanAccount->getLongDepositGUID()
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::DEBT,        // 储蓄账户是负债类
                'obj_type' => 'client_long_deposit',
                'currency_codes' => $gl_codes
            )
        );
    }

    private static function getCurrencyCodesOfGlAccount($account_info) {
        $currency_codes = array();
        foreach ((new currencyEnum())->Dictionary() as $k=>$v) {
            $currency_codes[$k] = $account_info['gl_code_' . strtolower($k)];
        }
        return $currency_codes;
    }
    private static function getCurrencyCodesOfParentGLAccount($account_info,$new_id) {
        $currency_codes = array();
        $ccy_list=(new currencyEnum())->Dictionary();
        foreach ($ccy_list as $k=>$v) {
            $currency_codes[$k] = $account_info['gl_code_' . strtolower($k)]."-".$new_id;
        }
        return $currency_codes;
    }
    private static function getCurrencyCodesByRule($id, $rule) {
        $currency_codes = array();
        if ($rule) {
            foreach ((new currencyEnum())->Dictionary() as $k=>$v) {
                if ($rule['prefix'][$k]) {
                    $currency_codes[$k] = $rule['prefix'][$k] . "-" . str_pad($id, $rule['length'], '0');
                }
            }
        }
        return $currency_codes;
    }

    /**
     * 获取收入账户的passbook
     * @param $incomingType
     * @param $businessType
     * @return passbookClass
     */
    public static function getIncomingPassbook($incomingType, $businessType) {
        $account_info = gl_accountClass::getIncomingAccount($incomingType, $businessType);

        // 获取或创建收入类型下业务类型账户的passbook，并返回
        return self::getOrCreatePassbookByObjGuid($account_info->obj_guid
            , array(
                'book_code'=>$account_info['book_code'],
                "book_name"=>$account_info['book_name'],
                "parent_book_code"=>$account_info['parent_book_code'],
                'book_type' => passbookTypeEnum::PROFIT,        // 收入是损益类
                'obj_type' => 'gl_account',
                'currency_codes' => self::getCurrencyCodesOfGlAccount($account_info)
            )
        );
    }

    /**
     * 获取支出账户的passbook
     * @param $outgoingType
     * @param $businessType
     * @return passbookClass
     */
    public static function getOutgoingPassbook($outgoingType, $businessType) {
        $account_info = gl_accountClass::getOutgoingAccount($outgoingType, $businessType);

        // 获取或创建收入类型下业务类型账户的passbook，并返回
        return self::getOrCreatePassbookByObjGuid($account_info->obj_guid
            , array(
                'book_code'=>$account_info['book_code'],
                "book_name"=>$account_info['book_name'],
                "parent_book_code"=>$account_info['parent_book_code'],
                'book_type' => passbookTypeEnum::COST,        // 支出是成本类
                'obj_type' => 'gl_account',
                'currency_codes' => self::getCurrencyCodesOfGlAccount($account_info)
            )
        );
    }

    /**
     * 获取系统账户的passbook
     * @param $systemAccountCode
     * @return passbookClass
     */
    public static function getSystemPassbook($systemAccountCode) {
        $account_info = gl_accountClass::getSystemAccount($systemAccountCode);

        // 获取或创建账户的passbook，并返回
        return self::getOrCreatePassbookByObjGuid($account_info->obj_guid
            , array(
                'book_code'=>$account_info['book_code'],
                "book_name"=>$account_info['book_name'],
                "parent_book_code"=>$account_info['parent_book_code'],
                'book_type' => $account_info->category,        // 类型根据account的category
                'obj_type' => 'gl_account',
                'currency_codes' => self::getCurrencyCodesOfGlAccount($account_info)
            )
        );
    }

    /**
     * 获得分行passbook
     * @param $branchId
     * @return passbookClass
     */
    public static function getBranchPassbook($branchId) {
        $branch_acct=branchClass::getGUID($branchId,true);
        //获取银行的gl_account
        $gl_acct=gl_accountClass::getDynamicAccount(objGuidTypeEnum::SITE_BRANCH);
        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$branch_acct['branch_code'];
        $book_code=$gl_acct['book_code'].'-'.$branch_acct['obj_guid'];
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,str_pad($branch_acct['uid'], 3, '0', STR_PAD_LEFT));

        return self::getOrCreatePassbookByObjGuid(branchClass::getGUID($branchId)
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::ASSET,          // 分行账户是资产类
                'obj_type' => 'branch',
                'currency_codes' => $gl_codes
            )
        );
    }

    /**
     * 获得用户passbook
     * @param $userId
     * @return passbookClass
     */
    public static function getUserPassbook($userId) {

        $user_acct=userClass::getGUID($userId,true);
        //获取银行的gl_account
        $gl_acct=gl_accountClass::getDynamicAccount(objGuidTypeEnum::UM_USER);
        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$user_acct['user_code'];
        $book_code=$gl_acct['book_code'].'-'.$user_acct['obj_guid'];
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,$user_acct['obj_guid']);

        return self::getOrCreatePassbookByObjGuid($user_acct['obj_guid']
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::ASSET,          // 员工账户是资产类
                'obj_type' => 'user',
                'currency_codes' =>$gl_codes
            )
        );
    }

    /**
     * 获得银行账户的passbook
     * @param $bankAccountId
     * @return passbookClass
     */
    public static function getBankAccountPassbook($bankAccountId) {
        //获取银行account
        $bank_acct=bank_accountClass::getGUID($bankAccountId,true);
        //获取银行的gl_account
        $gl_acct=gl_accountClass::getDynamicAccount(objGuidTypeEnum::BANK_ACCOUNT);
        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$bank_acct['bank_code']."-".$bank_acct['bank_account_no'];
        $book_code=$gl_acct['book_code'].'-'.$bank_acct['obj_guid'];
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,str_pad($bank_acct['uid'], 3, '0', STR_PAD_LEFT));

        return self::getOrCreatePassbookByObjGuid($bank_acct['obj_guid']
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::ASSET,          // 银行账户是资产类
                'obj_type' => 'bank',
                'currency_codes' => $gl_codes
            )
        );
    }

    public static function getSavingsProductPassbook($savingsProductId) {
        $product_info=savingsProductClass::getGUID($savingsProductId,true);
        //获取gl_account，根据最小期限确定所属科目
        $gl_acct=gl_accountClass::getDynamicAccount(savingsProductClass::getObjTypeByMinTerms($product_info['min_terms']));

        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$product_info['product_name'];
        $book_code=$gl_acct['book_code'].'-'.$product_info['obj_guid'];
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,$product_info['obj_guid']);

        return self::getOrCreatePassbookByObjGuid($product_info['obj_guid']
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::DEBT,          // 存款产品账户是负债
                'obj_type' => 'savings_product',
                'currency_codes' =>$gl_codes
            )
        );
    }

    public static function getPartnerPassbook($partnerId) {
        //获取partner-account
        $partner_acct=partnerClass::getGUID($partnerId,true);
        //获取银行的gl_account
        $gl_acct=gl_accountClass::getDynamicAccount(objGuidTypeEnum::PARTNER);
        $parent_book_code=$gl_acct['book_code'];
        $book_name=$gl_acct['book_name'].'-'.$partner_acct['partner_code'];
        $book_code=$gl_acct['book_code'].'-'.$partner_acct['obj_guid'];
        //处理动态gl_code
        $gl_codes=self::getCurrencyCodesOfParentGLAccount($gl_acct,str_pad($partner_acct['uid'], 3, '0', STR_PAD_LEFT));

        return self::getOrCreatePassbookByObjGuid(partnerClass::getGUID($partnerId)
            , array(
                'book_code'=>$book_code,
                "book_name"=>$book_name,
                "parent_book_code"=>$parent_book_code,
                'book_type' => passbookTypeEnum::DEBT,          // partner结算户是负债类
                'obj_type' => 'partner',
                'currency_codes' => $gl_codes
            )
        );
    }


    /**
     * 获取passbook下各币种余额
     * @return array
     */
    public function getAccountBalance()
    {
        $ccy_balance = array();
        // 统一返回，避免没有账户的错误
        foreach( (new currencyEnum())->toArray() as $currency ){
            $ccy_balance[$currency] = 0.00;
        }
        $m_passbook_account = new passbook_accountModel();
        $accounts = $m_passbook_account->getRows(array(
            'book_id' => $this->passbook_info['uid']
        ));
        foreach( $accounts as $item ){
            $this->accounts[$item['currency']] = $item;
            $ccy_balance[$item['currency']] = round($item['balance']-$item['outstanding'],2);
        }
        return $ccy_balance;
    }

    /**
     * 获取截止日的balance,todo:怎么处理outstanding？
     */
    public function getAccountBalanceOfEndDay($date_end){

        // 格式化时间
        $date_end = date('Y-m-d 23:59:59',strtotime($date_end));

        $ccy_balance = array();
        // 统一返回，避免没有账户的错误
        foreach( (new currencyEnum())->toArray() as $currency ){
            $ccy_balance[$currency] = 0.00;
        }
        $m_passbook_account = new passbook_accountModel();
        $accounts = $m_passbook_account->select(array(
            'book_id' => $this->passbook_info['uid']
        ));
        if(count($accounts)){
            $accounts=resetArrayKey($accounts,"uid");
            $ids=array_keys($accounts);
            $str_ids=implode("','",$ids);
            $sql="SELECT account_id,MAX(update_time) last_update_time FROM passbook_account_flow "
                ." WHERE update_time <= ".qstr($date_end)." AND state = ".qstr(passbookAccountFlowStateEnum::DONE)." and account_id in ('".$str_ids."')"
                ." GROUP BY account_id";
            $flow_times=$m_passbook_account->reader->getRows($sql);
            foreach($flow_times as $flow){
                $sql="select * from passbook_account_flow where account_id='".$flow['account_id']."' and update_time='".$flow['last_update_time']."'";
                $end_row=$m_passbook_account->reader->getRow($sql);
                if($end_row){
                    $ccy_balance[$accounts[$flow['account_id']]['currency']]= round($end_row['end_balance'],2);
                }
            }
        }
        return $ccy_balance;
    }

    /** 获取passbook的各个货币账户详情
     * @return array
     */
    public function getAccountAllCurrencyDetail()
    {
        $ccy_balance = array();
        // 统一返回，避免没有账户的错误
        foreach( (new currencyEnum())->toArray() as $currency ){
            $ccy_balance[$currency] = array();
        }
        $m_passbook_account = new passbook_accountModel();
        $accounts = $m_passbook_account->select(array(
            'book_id' => $this->passbook_info['uid']
        ));
        foreach( $accounts as $item ){
            $this->accounts[$item['currency']] = $item;
            $ccy_balance[$item['currency']] = $item;
        }
        return $ccy_balance;
    }

    public function getAccount($currency) {
        if (!$this->accounts[$currency]) {
            $account_model = new passbook_accountModel();
            $this->accounts[$currency] = $account_model->getRow(array(
                'book_id' => $this->passbook_info['uid'],
                'currency' => $currency
            ));
        }

        return $this->accounts[$currency];
    }

    public function getBalanceDelta($credit, $debit)
    {
        return round(self::getDelta($this->passbook_info['book_type'], $credit, $debit),2);
    }

    public function getBalanceIncreaseDirection() {
        return self::getIncreaseDirection($this->passbook_info['book_type']);
    }

    public static function getDelta($bookType, $credit, $debit) {
        switch ($bookType) {
            case passbookTypeEnum::ASSET:   // 资产
            case passbookTypeEnum::COST:    // 成本
            case passbookTypeEnum::PROFIT_EXPENSE://费用
                return $debit - $credit;
            case passbookTypeEnum::DEBT:    // 债务
            case passbookTypeEnum::EQUITY:  // 权益
            case passbookTypeEnum::PROFIT:  // 收益
            case passbookTypeEnum::PROFIT_INCOME:
                return $credit - $debit;
            case passbookTypeEnum::COMMON:
                return $credit - $debit;
            default:
                throw new Exception('Unknown passbook type - ' . $bookType);
        }
    }

    /**
     * 获得余额增加的方向
     * @param $bookType
     * @return int
     * @throws Exception
     */
    public static function getIncreaseDirection($bookType) {
        switch ($bookType) {
            case passbookTypeEnum::ASSET:   // 资产
            case passbookTypeEnum::COST:    // 成本
            case passbookTypeEnum::PROFIT_EXPENSE://费用
                return accountingDirectionEnum::DEBIT;
            case passbookTypeEnum::DEBT:    // 债务
            case passbookTypeEnum::EQUITY:  // 权益
            case passbookTypeEnum::PROFIT:  // 收益
            case passbookTypeEnum::PROFIT_INCOME://收入
                return accountingDirectionEnum::CREDIT;
            case passbookTypeEnum::COMMON:
                return accountingDirectionEnum::CREDIT;
            default:
                throw new Exception('Unknown passbook type - ' . $bookType);
        }
    }
    /**
     * 获取手工凭证的gl-code得到的passbook
     */
    public static function getPassbookOfManualGLCode($gl_code,$currency){
        //先判断有没有在passbook_account,在里面就返回对应的book
        $pb_acct=new passbook_accountModel();
        $row=$pb_acct->find(array("gl_code"=>$gl_code,"currency"=>$currency));
        if($row){
            $pb=new passbookModel();
            $book=$pb->getRow(array("uid"=>$row['book_id']));
            return new passbookClass($book);
        }
        //需要创建passbook

        $account_info = gl_accountClass::getManualAccount($gl_code,$currency);
        // 获取或创建账户的passbook，并返回
        return self::getOrCreatePassbookByObjGuid($account_info->obj_guid
            , array(
                'book_code'=>$account_info['book_code'],
                "book_name"=>$account_info['book_name'],
                "parent_book_code"=>$account_info['parent_book_code'],
                'book_type' => $account_info->category,        // 类型根据account的category
                'obj_type' => 'gl_account',
                'currency_codes' => self::getCurrencyCodesOfGlAccount($account_info)
            )
        );



    }

    public static function getPassbookAccount($pageNumber, $pageSize, $filters = array()){
        $r = new ormReader();
        $where = "1 = 1";
        if($filters['book_code']){
            $where .= " and book_code like '%".$filters['book_code']."%' ";
        }
        if($filters['book_name']){
            $where .= " and book_name like '%".$filters['book_name']."%' ";
        }
        $sql = "select * from passbook where $where";
        $r->getPage($sql,$pageNumber,$pageSize);
        $data = $r->getPage($sql, $pageNumber, $pageSize);

        $start = ($pageNumber - 1) * $pageSize;
        $limit = "$start,$pageSize";

        $sql1 = "select p.uid,p.book_code,p.book_name,p.book_type,p.state,a.currency,a.balance,a.uid account_id
from (select * from passbook where $where limit $limit) p inner join passbook_account a on a.book_id = p.uid ";
        $ret = $r->getRows($sql1);
        $list = array();
        $num = ($pageNumber - 1) * $pageSize;
        $count = 1;
        foreach($ret as $k => $v){
            if(!$list[$v['uid']]){
                ++$num;
            }
            if($list[$v['uid']]){
                $count++;
            }else{
                $count = 1;
            }
            $list[$v['uid']]['no'] = $num;
            $list[$v['uid']]['count'] = $count;
            $list[$v['uid']]['uid'] = $v['uid'];
            $list[$v['uid']]['book_code'] = $v['book_code'];
            $list[$v['uid']]['book_name'] = $v['book_name'];
            $list[$v['uid']]['book_type'] = $v['book_type'];
            $list[$v['uid']]['state'] = $v['state'];
            $temp = array();
            $temp['account_id'] = $v['account_id'];
            $temp['currency'] = $v['currency'];
            $temp['balance'] = $v['balance'];
            $list[$v['uid']]['child'][] = $temp;

        }
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "data" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }
}