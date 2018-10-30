<?php
$static_schema=array (
  0 => 
  array (
    'Field' => 'uid',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
    'Comment' => 'ID',
  ),
  1 => 
  array (
    'Field' => 'client_obj_type',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '客户对象类型',
  ),
  2 => 
  array (
    'Field' => 'client_obj_guid',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '客户对象GUID',
  ),
  3 => 
  array (
    'Field' => 'account_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_account.uid',
  ),
  4 => 
  array (
    'Field' => 'credit_grant_id',
    'Type' => 'int(11) unsigned',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '授信ID',
  ),
  5 => 
  array (
    'Field' => 'account_handler_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '绑定的操作账户',
  ),
  6 => 
  array (
    'Field' => 'contract_sn',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '合同规则编号',
  ),
  7 => 
  array (
    'Field' => 'virtual_contract_sn',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '实际上的理论合同号',
  ),
  8 => 
  array (
    'Field' => 'inner_contract_sn',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '内部合同编号',
  ),
  9 => 
  array (
    'Field' => 'member_credit_category_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  10 => 
  array (
    'Field' => 'credit_amount',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '消耗的信用值',
  ),
  11 => 
  array (
    'Field' => 'product_category',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '贷款产品类型',
  ),
  12 => 
  array (
    'Field' => 'product_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_product.uid',
  ),
  13 => 
  array (
    'Field' => 'product_code',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_product.product_code',
  ),
  14 => 
  array (
    'Field' => 'product_name',
    'Type' => 'varchar(100)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_product.product_name',
  ),
  15 => 
  array (
    'Field' => 'sub_product_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '二级产品ID',
  ),
  16 => 
  array (
    'Field' => 'sub_product_code',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_sub_product.product_code',
  ),
  17 => 
  array (
    'Field' => 'sub_product_name',
    'Type' => 'varchar(100)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_sub_product.product_name',
  ),
  18 => 
  array (
    'Field' => 'product_size_rate_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_product_size_rate.uid',
  ),
  19 => 
  array (
    'Field' => 'product_special_rate_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '特殊利率id，loan_product_special_rate.uid',
  ),
  20 => 
  array (
    'Field' => 'currency',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '货币',
  ),
  21 => 
  array (
    'Field' => 'apply_amount',
    'Type' => 'decimal(14,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '申请金额',
  ),
  22 => 
  array (
    'Field' => 'application_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '申请单号:可空',
  ),
  23 => 
  array (
    'Field' => 'propose',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '贷款目的',
  ),
  24 => 
  array (
    'Field' => 'due_date',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '还款日',
  ),
  25 => 
  array (
    'Field' => 'due_date_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '还款日类型,0 固定日期 1 每周 2 每月 3 每年',
  ),
  26 => 
  array (
    'Field' => 'repayment_period',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '还款周期',
  ),
  27 => 
  array (
    'Field' => 'repayment_type',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '还款方式',
  ),
  28 => 
  array (
    'Field' => 'loan_cycle',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '贷款次数',
  ),
  29 => 
  array (
    'Field' => 'loan_actual_cycle',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '真实第几次贷款',
  ),
  30 => 
  array (
    'Field' => 'loan_term_day',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '还款目标天数',
  ),
  31 => 
  array (
    'Field' => 'loan_period_value',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '贷款周期',
  ),
  32 => 
  array (
    'Field' => 'loan_period_unit',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => 'month',
    'Extra' => '',
    'Comment' => '贷款周期单位，year 年 month 月 day 日',
  ),
  33 => 
  array (
    'Field' => 'mortgage_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '抵押方式',
  ),
  34 => 
  array (
    'Field' => 'guarantee_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '担保方式',
  ),
  35 => 
  array (
    'Field' => 'installment_frequencies',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '还款周期',
  ),
  36 => 
  array (
    'Field' => 'interest_rate',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  37 => 
  array (
    'Field' => 'interest_rate_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '利息类型 0 百分比 1 固定金额',
  ),
  38 => 
  array (
    'Field' => 'interest_rate_unit',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '利率基础单位',
  ),
  39 => 
  array (
    'Field' => 'interest_min_value',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '最低利息值',
  ),
  40 => 
  array (
    'Field' => 'operation_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '运营费利率',
  ),
  41 => 
  array (
    'Field' => 'operation_fee_unit',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '利率基准单位 yearly 年 monthly 月 daily 日',
  ),
  42 => 
  array (
    'Field' => 'operation_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '0 百分比，1数字',
  ),
  43 => 
  array (
    'Field' => 'operation_min_value',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '最低运营费',
  ),
  44 => 
  array (
    'Field' => 'service_fee',
    'Type' => 'decimal(20,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  45 => 
  array (
    'Field' => 'service_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  46 => 
  array (
    'Field' => 'admin_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '管理费',
  ),
  47 => 
  array (
    'Field' => 'admin_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '0 百分比，1数字',
  ),
  48 => 
  array (
    'Field' => 'loan_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '贷款手续费',
  ),
  49 => 
  array (
    'Field' => 'loan_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '0 百分比，1数字',
  ),
  50 => 
  array (
    'Field' => 'is_full_interest',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '提前还款是否全额利息,0 否 1是',
  ),
  51 => 
  array (
    'Field' => 'prepayment_interest',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '提前还款手续费',
  ),
  52 => 
  array (
    'Field' => 'prepayment_interest_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '提前还款手续费类型,0百分比 1 金额',
  ),
  53 => 
  array (
    'Field' => 'penalty_rate',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  54 => 
  array (
    'Field' => 'penalty_is_compound_interest',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '罚金是否复利计算',
  ),
  55 => 
  array (
    'Field' => 'penalty_divisor_days',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '罚款利率除以多少天',
  ),
  56 => 
  array (
    'Field' => 'grace_days',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '宽限日期',
  ),
  57 => 
  array (
    'Field' => 'is_balloon_payment',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '分期付利息最后本金',
  ),
  58 => 
  array (
    'Field' => 'is_advance_interest',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '预扣利息',
  ),
  59 => 
  array (
    'Field' => 'is_advance_annual_fee',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否扣年费',
  ),
  60 => 
  array (
    'Field' => 'is_first_repayment_annual_fee',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '年费预扣还是第一期还款',
  ),
  61 => 
  array (
    'Field' => 'is_insured',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否投保',
  ),
  62 => 
  array (
    'Field' => 'ref_interest',
    'Type' => 'decimal(5,3)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.000',
    'Extra' => '',
    'Comment' => '理论利息(审批有修改参考)',
  ),
  63 => 
  array (
    'Field' => 'ref_admin_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '理论管理费',
  ),
  64 => 
  array (
    'Field' => 'ref_loan_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '理论手续费',
  ),
  65 => 
  array (
    'Field' => 'ref_operation_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '理论营运费',
  ),
  66 => 
  array (
    'Field' => 'receivable_principal',
    'Type' => 'decimal(14,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '应收本金',
  ),
  67 => 
  array (
    'Field' => 'receivable_interest',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '利息',
  ),
  68 => 
  array (
    'Field' => 'receivable_admin_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '管理费',
  ),
  69 => 
  array (
    'Field' => 'receivable_loan_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '贷款手续费',
  ),
  70 => 
  array (
    'Field' => 'receivable_operation_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '营运费',
  ),
  71 => 
  array (
    'Field' => 'receivable_insurance_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '保险费',
  ),
  72 => 
  array (
    'Field' => 'receivable_annual_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '年费',
  ),
  73 => 
  array (
    'Field' => 'receivable_penalty',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '罚息',
  ),
  74 => 
  array (
    'Field' => 'receivable_service_fee',
    'Type' => 'decimal(20,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  75 => 
  array (
    'Field' => 'loss_principal',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '核销本金',
  ),
  76 => 
  array (
    'Field' => 'loss_interest',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '核销利息',
  ),
  77 => 
  array (
    'Field' => 'loss_admin_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '核销管理费',
  ),
  78 => 
  array (
    'Field' => 'loss_loan_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '损失贷款手续费',
  ),
  79 => 
  array (
    'Field' => 'loss_operation_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '核销运营费',
  ),
  80 => 
  array (
    'Field' => 'loss_annual_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '核销年费',
  ),
  81 => 
  array (
    'Field' => 'loss_penalty',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '核销罚金',
  ),
  82 => 
  array (
    'Field' => 'invoice_date',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '记账日期',
  ),
  83 => 
  array (
    'Field' => 'start_date',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '合同终止日期',
  ),
  84 => 
  array (
    'Field' => 'end_date',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '合同终止日期',
  ),
  85 => 
  array (
    'Field' => 'creator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '创建者ID',
  ),
  86 => 
  array (
    'Field' => 'creator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建者Name',
  ),
  87 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  88 => 
  array (
    'Field' => 'process_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '进程id',
  ),
  89 => 
  array (
    'Field' => 'state',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '状态',
  ),
  90 => 
  array (
    'Field' => 'finish_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '结束时间，完成或核销',
  ),
  91 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  92 => 
  array (
    'Field' => 'create_source',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建来源 member_app counter 等',
  ),
  93 => 
  array (
    'Field' => 'branch_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '分行ID',
  ),
);