<?php

class errorCodesEnum extends Enum
{
    const UNKNOWN_ERROR = 0;        // 没有定义的错误
    const NO_ERROR = 200;  // 无任何错误
    const SIGN_ERROR = 1001;  // 签名错误
    const DATA_LACK = 1002; // 参数缺乏
    const INVALID_PARAM = 1003;  // 非法参数
    const INVALID_TOKEN = 1004;  // 非法token
    const USER_EXIST = 1005;  // 用户已存在
    const PHONE_USED = 1006;  // 电话已使用
    const PHONE_VERIFIED = 1007; // 电话已验证
    const DATA_EXPIRED = 1008;  // 数据过期
    const MEMBER_NOT_EXIST = 1009;  // member不存在
    const NO_LOGIN = 1010;  //没有登录
    const NO_DATA = 1011;  // 没有数据
    const INVALID_STATE = 1012; // 无效的状态
    const UNAUTHORIZED = 1013;  // 未授权
    const PART_FAILED = 1022;  // 部分失败

    const SESSION_EXPIRED = 10001;  // session 过期
    const DB_ERROR = 10002;        // 数据库操作失败
    const UNEXPECTED_DATA = 10003; // 错误数据
    const NOT_SUPPORTED = 10004;
    const UNIMPLEMENTED = 10005;
    const API_FAILED = 10006;    // api错误
    const NOT_PERMITTED = 10007;
    const LOGIN_NULLIFIED = 10008;
    const DATA_INCONSISTENCY = 10009;   // 数据不一致
    const DATA_DUPLICATED = 10010;  // 数据已经存在
    const REQUEST_FLOOD = 10011;        // 请求过于频繁
    const CONFIG_ERROR = 10012; // 配置错误
    const TOKEN_EXPIRED = 10013;  // 令牌过期

    // 业务相关的CODE
    const SMS_CODE_ERROR = 11013; // 短信验证码错误
    const NO_LOAN_PRODUCT = 11014;  // 没有贷款产品
    const LOAN_PRODUCT_UNSHELVE = 11015; // 产品已下架(历史版本)
    const LOAN_PRODUCT_NX = 11016;  // 非产品的执行版本
    const NO_LOAN_INTEREST = 11017;  // 没有设置利率信息
    const NO_INSURANCE_ITEM = 11018; // 没有保险产品投保项
    const NO_INSURANCE_PRODUCT = 11019;  // 没有保险产品
    const INSURANCE_PRODUCT_NX = 11020; // 非执行版本
    const CREATE_INSURANCE_CONTRACT_FAIL = 11021;  // 创建保险合同失败
    const WITHDRAW_AMOUNT_INVALID = 11022; // 取现金额非法
    const OUT_OF_PER_WITHDRAW = 11023;  // 单次取现超额
    const OUT_OF_DAY_WITHDRAW = 11024; // 当日取现超额
    const NO_LOAN_ACCOUNT = 11025;  // 不存在贷款账户
    const NO_BIND_ACE_ACCOUNT = 11026;  // 没有绑定ACE账号
    const OUT_OF_ACCOUNT_CREDIT = 11027;  // 超出信用额度
    const NO_CONTRACT = 11028;  // 没有合同信息
    const TWO_PWD_DIFFER = 11029; // 两次密码不一致
    const PASSWORD_ERROR = 11030;  // 密码错误
    const NO_MESSAGE = 11031;  // 消息不存在
    const REPAYMENT_UN_MATCH_LOAN_TIME = 11032; // 贷款时间周期和还款方式不匹配
    const ACE_ACCOUNT_NOT_EXIST = 11033;  // ACE账户不存在
    const NO_PASSPORT = 11034;  // 没有登陆通行令牌
    const PASSPORT_EXPIRED = 11035;  // 登陆通行令牌失效
    const NO_ACCOUNT_HANDLER = 11036;  // 没有操作账户
    const UNDER_COOL_TIME = 11037;  // 冷却时间内，稍后再试
    const ACCOUNT_NOT_VALID = 11038;  // 账号格式不符合
    const PASSWORD_NOT_STRONG = 11039; // 密码格式错误或强度不够
    const EMAIL_BEEN_REGISTERED = 11040;  // 邮箱已被注册
    const FUNCTION_CLOSED = 11041;  // 后台功能关闭了
    const OUT_OF_CREDIT_BALANCE = 11042;  // 超出信用余额
    const INVALID_PHONE_NUMBER = 11043;  // 非法电话号码
    const INVALID_EMAIL = 11044;  // 非法邮箱
    const CAN_NOT_CANCEL_CONTRACT = 11045;  // 合同进行中，不能取消
    const LOAN_CONTRACT_CAN_NOT_REPAYMENT = 11046;  // 贷款合同未执行，不能还款
    const INSUFFICIENT_REPAYMENT_CAPACITY = 11047;  // 还款能力不足
    const APPROVING_CAN_NOT_DELETE = 11048;  // 审核中，不能删除
    const AMOUNT_TOO_LITTLE = 11049;  // 金额太小
    const INVALID_PERIOD_NUM = 11050;  // 不合理的期数数量
    const NO_CURRENCY_EXCHANGE_RATE = 11051;  // 没有设置汇率
    const NOT_SUPPORT_PREPAYMENT = 11052;  // 该合同不支持提前还款
    const NOT_CERTIFICATE_ID = 11053;  // 没有认证身份证
    const ID_SN_ERROR = 11054;  // 身份证号错误
    const ID_SN_HAS_CERTIFICATED = 11055;  // 身份证号已经被认证
    const SAME_PASSWORD = 11056;  // 新、旧密码一样
    const NOT_SET_TRADING_PASSWORD = 11057;  // 没有设置交易密码
    const MEMBER_UN_GRANT_CREDIT = 11058;  // 还未授信
    const INVALID_AMOUNT = 11059;  // 不合理的数量
    const NOT_ACE_MEMBER = 11060;  // 不是ACE的member
    const SMS_CODE_SEND_FAIL = 11061;  // 验证码发送失败
    const USER_NOT_EXISTS = 11062;  // 用户不存在
    const USER_LOCKED = 11063;  // 用户被锁定
    const HAVE_HANDLED = 11064;  // 已经处理了
    const HAVE_CANCELED = 11065;  // 已经取消
    const UN_MATCH_OPERATION = 11066;  // 不匹配操作
    const BANK_ALREADY_BOUND = 11067;  // 银行卡已经绑定了
    const NO_LOGIN_ACCESS = 11068;  // 账号没有登录权限
    const HANDLING_LOCKED = 11069;  // 处理锁定中，不能执行其他操作
    const CONTRACT_BEEN_PAID_OFF = 11070; // 合同已还清
    const CONTRACT_BEEN_WRITTEN_OFF = 11071;  // 合同已核销
    const USER_BEEN_CANCELLED = 11072;  // 用户被注销
    const PASSWORD_ERROR_MORE_TIMES = 11073;  // 密码错误次数过多
    const INVALID_AUTH_CARD = 11074;  // 非法卡
    const INVALID_PARTNER = 11075;  // 非法合作商
    const OUT_CAN_TAKE_ASSETS_AMOUNT = 11076;  // 超出可以取回的资产总额
    const UPLOAD_PIC_TO_UPYUN_FAIL = 11077;  // 上传图片到UPYUN失败
    const UPYUN_HANDLE_FAIL = 11078;  // UPYUN操作失败
    const CREDIT_CAN_NOT_USE = 11079;  // 信用不可用
    const MEMBER_IS_IN_BLACK_LIST = 11080;  // 被加入了黑名单
    const MEMBER_UN_CHECKED = 11081;   // 账户未确认
    const UN_EDITABLE = 11082;  // 不可编辑
    const UN_DELETABLE = 11083;  // 不可删除
    const EXCEED_MAX_CONTRACTS_PER_CLIENT_OF_PRODUCT = 11084;  // 超出产品最大贷款数量
    const HAVE_UN_COMPLETE_REQUEST = 11085;  // 有未处理完成申请
    const RELATIVE_PERSON_ALREADY_EXISTS = 11086;  // 关系人已经存在
    const DEVICE_NOT_SUPPORT_OTHER_LOGIN_WAY = 11087;  // 异常设备登陆只支持账户密码登陆
    const HAVE_UNPROCESSED_CONTRACT = 11088;   // 有未处理完成的合同
    const NO_MORTGAGE_CAN_NOT_LOAN = 11089;   // 没有抵押物，不能贷款
    const ASSET_SN_DUPLICATION = 11090;  // 资产编号重复
    const LIMIT_MEMBER_LOAN_PRODUCT = 11091;  // 已限制使用该贷款产品

    const BALANCE_NOT_ENOUGH = 11100;  // 余额不足
    const BILL_NOT_EXIST = 11101;  // 账单不存在
    const EXCEEDED_PER_TIMES_LIMIT = 11102;  // 单次超额
    const EXCEEDED_PER_DAY_LIMIT = 11103;  // 单日超额
    const NOT_CHIEF_TELLER = 11104;  // 不是chief teller
    const CAN_NOT_TRANSFER_TO_SELF = 11105;  // 不能给自己转账

    const IC_CARD_NOT_FOUND = 11200;   // IC卡不存在
    const IC_CARD_EXPIRED = 11201;     // IC卡已经过期
    const IC_CARD_BOUND = 11202;       // IC卡已经绑定其他用户

    const SAVINGS_TERM_OUT_OF_RANGE = 12001;    // 期限超出范围

    // API相关错误代码
    const API_ERROR_ACE_BASE = 20000;   // ACE API错误CODE的基础编号
    const ACE_UNKNOWN_PARTNER = 20900;  // Unknown Partner
    const ACE_INVALID_RETURN_URL = 20901;  // 901 Invalid Return URL
    const ACE_INVALID_NOTIFY_URL = 20902;// 902 Invalid Notify URL

    const ACE_INVALID_MEMBER_PHONE_NUMBER = 20905;  //905 Invalid Member Phone Number
    const ACE_INVALID_SIGN_ID = 20907;  // 907 Invalid Application ID
    const ACE_INVALID_SIGN_ID_STATUS = 20908;  //908 Invalid Application Status
    const ACE_INVALID_BIZ_CONTENT = 20909;  // 909 Invalid Biz Content (Null)
    const ACE_INVALID_BIZ_CONTENT_TRADE_NO = 20910;  // 910 Invalid Biz Content (Trade No)
    const ACE_INVALID_BIZ_CONTENT_TRADE_PAID = 20911;  // Invalid Biz Content (Trade No, Paid)
    const ACE_INVALID_BIZ_CONTENT_TITLE = 20912;   // 912 Invalid Biz Content (Title)
    const ACE_INVALID_BIZ_CONTENT_CURRENCY = 20913; // 913 Invalid Biz Content (Currency)
    const ACE_INVALID_BIZ_CONTENT_AMOUNT = 20914;  // 914 Invalid Biz Content (Amount)
    const ACE_INVALID_TRADE_ID = 20916; // 916 Invalid Trade ID
    const ACE_TRADE_WRONG_STATUS = 20917; // 917 Trade Wrong Status
    const ACE_TRADE_LOCKED = 20918;  // Trade Locked
    const ACE_MEMBER_NOT_ENOUGH_BALANCE = 20920;  // 920 Member Not Enough Balance
    const ACE_OVER_PARTNER_BALANCE_LIMIT = 20921;  // 921 Over Partner Balance Limit
    const ACE_INACTIVE_MEMBER = 20922;  // 922 Inactive Member
    const ACE_PARTNER_NOT_ENOUGH_BALANCE = 20923;  // 923 Partner Not Enough Balance
    const ACE_OVER_MEMBER_BALANCE_LIMIT = 20924;  // 924 Over Member Balance Limit
    const ACE_OVER_MEMBER_MAX_PAY_PER_TIME = 20926; // Over Member Max Pay Per Time
    const ACE_OVER_MEMBER_MAX_PER_DAY = 20927;  // Over Member Max Pay Per Day
    const ACE_OVER_MEMBER_MAX_PAY_PER_TIME_CHANNEL = 20928; // Over Member Max Pay Per Time (Channel)
    const ACE_OVER_MEMBER_MAX_PAY_PER_DAY_CHANNEL = 20929;  // Over Member Max Pay Per Day (Channel)
    const ACE_INVALID_PHONE_NUMBER = 20930;  // 930 INot ACE Member
    const ACE_SIGNED_MEMBER = 20931;  // 931 Not Active ACE Member
    const ACE_NOT_SIGNED_MEMBER = 20932; // 932 Not Signed Member
    const ACE_OVER_MEMBER_MAX_PAY_PER_TIME_PARTNER = 20938; // Over Member Max Pay Per Time (Partner)
    const ACE_OVER_MEMBER_MAX_PAY_PER_DAY_PARTNER = 20939; // Over Member Max Pay Per Day (Partner)
    const ACE_INVALID_TRANSFER_ID = 20940;  //940 Invalid Transfer ID
    const ACE_TRANSFER_WRONG_STATUS = 20941; //941 Transfer Wrong Status
    const ACE_INVALID_CURRENCY = 20943;  // Invalid Currency
    const ACE_INVALID_AMOUNT = 20944; // Invalid Amount
    const ACE_UNSUPPORTED_CURRENCY = 20945; // Unsupported Currency
    const ACE_INVALID_VERIFY_CODE = 20950; // Invalid Verify Code
    const ACE_OVER_MAX_TRANSFER_PER_TIME = 20960; // Over Max Transfer Per Time
    const ACE_OVER_MAX_TRANSFER_PER_DAY = 20961; // Over Max Transfer Per Day
    const ACE_OVER_MAX_TRANSFER_PER_TIME_CHANNEL = 20962; // Over Max Transfer Per Time (Channel)
    const ACE_OVER_MAX_TRANSFER_PER_DAY_CHANNEL = 20963; // Over Max Transfer Per Day (Channel)
    const ACE_OVER_MAX_TRANSFER_PER_TIME_PARTNER = 20965; // Over Max Transfer Per Time (Partner)
    const ACE_OVER_MAX_TRANSFER_PER_DAY_PARTNER = 20966; // Over Max Transfer Per Day (Partner)
    const ACE_OVER_PAY_TO_MEMBER_MAX_PER_TIME = 20970;// Over Pay To Member Max Per Time
    const ACE_OVER_PAY_TO_MEMBER_MAX_PER_DAY = 20971; // Over Pay To Member Max Per Day
    const ACE_OVER_PAY_TO_MEMBER_MAX_PER_TIME_CHANNEL = 20972; // Over Pay To Member Max Per Time (Channel)
    const ACE_OVER_PAY_TO_MEMBER_MAX_PER_DAY_CHANNEL = 20973; // Over Pay To Member Max Per Day (Channel)
    const ACE_OVER_PAY_TO_MEMBER_MAX_PER_TIME_PARTNER = 20975; // Over Pay To Member Max Per Time (Partner)
    const ACE_OVER_PAY_TO_MEMBER_MAX_PER_DAY_PARTNER = 20976; // Over Pay To Member Max Per Day (Partner)
    const ACE_UNKNOWN_API_CALL = 20995; // Unknown API Call
    const ACE_NO_PRIVILEGE = 20996; // No Privilege
    const ACE_API_MAINTENANCE = 20997;  // API Maintenance
    const ACE_API_UNAUTHORIZED_IP = 20998;  // Unauthorized IP
    const ACE_API_ILLEGAL_CAL = 20999;   // Illegal API Call

    // 内部交易相关CODE
    const TRADING_UNEXPECTED_STATE = 30001;
    const TRADING_CANCELLED = 30002;
    const TRADING_FINISHED = 30003;

    const APP_CLOSED = 90000;  // app closed


}


class globalOrderStateEnum extends Enum
{
    const ORDER_STATE_CANCEL = "0";  // 取消
    const ORDER_STATE_CREATED = '1'; // 创建
    const ORDER_STATE_PENDING_PAY = "10";  // 待支付
    const ORDER_STATE_PAYING = '11';  // 正在支付
    const ORDER_STATE_PAID = "20";  // 已支付
    const ORDER_STATE_SUCCESS = "40";  // 完成
}

class dictionaryKeyEnum extends Enum
{
    const GLOBAL_SETTINGS = 'global_settings';
    const CREDIT_GRANT_RATE = 'credit_grant_rate';
    const AUTHORIZED_CONTRACT_FEE = 'authorized_contract_fee';
    const SYSTEM_CLOSE_MEMBER_APP = 'system_close_member_app';
    const SYSTEM_CLOSE_CREDIT_OFFICER_APP = 'system_close_credit_officer_app';
    const SYSTEM_CLOSE_CONSOLE = 'system_close_console';
    const SYSTEM_CLOSE_OPERATOR = 'system_close_operator';
    const SYSTEM_CLOSE_BRANCH_MANAGER = 'system_close_branch_manager';
    const SYSTEM_CLOSE_COUNTER = 'system_close_counter';
    const GL_CODE_RULE = 'gl_code_rule';
    const MODULE_BUSINESS_SETTING = 'module_business_setting';
}

class treeKeyTypeEnum extends Enum
{
    const ADDRESS = 'region';  // 地址key
}

class addressCategoryEnum extends Enum
{
    const MEMBER_RESIDENCE_PLACE = 'residence_place';
}

class addressStateEnum extends Enum
{
    const INACTIVE = 0;
    const ACTIVE = 1;
}

class partnerEnum extends Enum
{
    // 合作伙伴code
    const ACE = 'ace';
}

class partnerBizTypeEnum extends Enum
{
    const TRANSFER = 'transfer';
}

class memberSourceEnum extends Enum
{
    const ONLINE = 0;  // member-app
    const COUNTER = 1;  // 柜台
    const CO = 2;  // CO
    const THIRD = 10;  // 第三方

}

class memberStateEnum extends Enum
{
    const CANCEL = 0;  // 注销
    const CREATE = 1;  // 创建,待检查
    const CHECKED = 10;  // 已检查,待认证
    const TEMP_LOCKING = 20;  // 暂时锁定
    const SYSTEM_LOCKING = 21;  // 系统锁定
    //const SUSPENDED = 30;  // 系统锁定
    const VERIFIED = 100;  // 已验证
}

class memberPropertyKeyEnum extends Enum
{
    // member property 扩展json的key值
    const ORIGINAL_STATE = 'original_member_state'; // member操作前状态
    const LOCK_FOR_CO = 'is_lock_for_co';
}

class memberGenderEnum extends Enum
{
    const FEMALE = 'female';  // 女性
    const MALE = 'male';  // 男性
}

class memberMaritalStatusEnum extends Enum
{
    const SINGLE = 'single';
    const MARRIED = 'married';
    const Divorce = 'divorce';
}

class clientOccupationTypeEnum extends Enum
{
    const STAFF = 'staff'; // 公司内部员工
    const GOVERNMENT = 'government';    // 政府员工
    const RIVAL_CLIENT = 'rival_client';  // 对手客户
}

class newMemberCheckStateEnum extends Enum
{
    const CREATE = 0;   // 新创建
    const LOCKED = 10;  // 锁定
    const CLOSE = 11;   //关闭
    const ALLOT = 20;   //分配给branch
    const PASS = 100;  // 通过验证
}

class operateTypeEnum extends Enum
{
    const NEW_CLIENT = 'new_client';
    const LOAN_CONSULT = 'loan_consult';
    const CERTIFICATION_FILE = 'certification_file';
}

class creditProcessEnum extends Enum
{
    // 信用激活过程
    const FINGERPRINT = 'fingerprint';  // 指纹录入
    const AUTHORIZED_CONTRACT = 'authorized_contract';  // 授权合同
}

class creditEventTypeEnum extends Enum
{
    const GRANT = 'grant';  // 授信
    const CREDIT_LOAN = 'credit_loan';  //　信用贷款
    const CUT_CREDIT = 'cut_credit';  //　信用贷款
    const MORTGAGE_ASSETS = 'mortgage_assets'; // 抵押财产
    const TAKE_OUT_ASSETS = 'take_out_assets'; // 取回资产
}

class smsTaskType extends Enum
{
    const VERIFICATION_CODE = "VerificationCode";
    const PIN_CODE = "PinCode";
    const WALLET_CHANGED = "WalletChanged";
    const LUCKY_NOTICE = 'LuckyNotice';
    const TOPUP_NOTICE = 'TopupNotice';
}

class smsTaskState extends Enum
{
    const NONE = 0;
    const CREATE = 1;
    const SENDING = 10;
    const SEND_FAILED = 11;
    const SEND_SUCCESS = 20;
    const CANCEL = 30;
}


class phoneCodeCDEnum extends Enum
{
    const CD = 60;  // 发送短信验证码的冷却时间(s)
}

class emailCoolTimeEnum extends Enum
{
    const CD = 60;
}

class memberLoginTypeEnum extends Enum
{
    const LOGIN_CODE = 1;
    const PHONE = 2;
    const EMAIL = 3;
}

class insuranceProductStateEnum extends Enum
{
    const TEMP = 10;
    const ACTIVE = 20;
    const INACTIVE = 30;
    const HISTORY = 40;
}

class objGuidTypeEnum extends Enum
{
    const SYSTEM = 0;  // 系统
    const CLIENT_MEMBER = 1;
    const UM_USER = 2;
    const SITE_BRANCH = 3;
    const PARTNER = 4;
    const BANK_ACCOUNT = 5;
    const SHORT_LOAN = 6;
    const LONG_LOAN = 7;
    const SHORT_DEPOSIT = 8;
    const LONG_DEPOSIT = 9;
    const GL_ACCOUNT = 11;
    const STAFF = 12;
}

class appTypeEnum extends Enum
{
    const MEMBER_APP = 'smarithiesak-member';
    const OPERATOR_APP = 'credit_officer';
}

class loanAccountTypeEnum extends Enum
{
    const MEMBER = 0;
    const PARTNER = 10;
    const DEALER = 20;
    const LEGAL = 30;
}

class insuranceAccountTypeEnum extends Enum
{
    const MEMBER = 0;
    const PARTNER = 10;
    const DEALER = 20;
    const LEGAL = 30;
}

class schemaStateTypeEnum extends Enum
{
    const CANCEL = -1;  // 取消
    const CREATE = 0;
    const GOING = 10;  // 开始执行
    const FAILURE = 11;
    const PENDING_MANUAL_EXECUTE = 20;  // 需要手工执行的
    const COMPLETE = 100;

}

class loanContractStateEnum extends Enum
{
    const CANCEL = -1;
    const CREATE = 0; // 新建，临时合同
    const PENDING_APPROVAL = 10;  // 已确认，待审核的合同
    const REFUSED = 11;  // 审核拒绝
    const PENDING_DISBURSE = 20;  // 待放款，进入执行状态
    const PROCESSING = 30;
    const PAUSE = 90;  // 执行异常，人工介入 异常状态用90以上
    const ONLY_PENALTY = 99;  // 计划已还清，只残留罚金
    const COMPLETE = 100;
    const WRITE_OFF = 101;  // 注销
}

class contractCreateSourceEnum extends Enum
{
    const MEMBER_APP = 'member_app';
    const COUNTER = 'counter';
}

class dueDateTypeEnum extends Enum
{
    // 还款日类型 0 固定日期 1 每周 2 每月 3 每年
    const FIXED_DATE = 0;
    const PER_WEEK = 1;
    const PER_MONTH = 2;
    const PER_YEAR = 3;
    const PER_DAY = 4;  // 每天
}

class insuranceContractStateEnum extends Enum
{
    const CANCEL = -1;
    const CREATE = 0;  // 待审核
    const PENDING_APPROVAL = 10;
    const REFUSED = 11;  // 审核拒绝
    const PENDING_RECEIPT = 20;  // 待收款
    const PROCESSING = 30;  // 进行中
    const PAUSE = 90;  // 执行异常，人工介入
    const COMPLETE = 100;
    const WRITE_OFF = 101;  // 注销
}

class contractPrefixSNEnum extends Enum
{
    const LOAN = 1;
    const INSURANCE = 2;
}

class memberAccountHandlerTypeEnum extends Enum
{
    const CASH = 0;
    const BANK = 10;
    const PARTNER_ASIAWEILUY = 21;
    const PARTNER_LOAN = 22;
    const PASSBOOK = 30;
}


class accountHandlerStateEnum extends Enum
{
    const TEMP = 0;  // 临时未确认的
    const ACTIVE = 10;
    const HISTORY = 20;
}

class certSourceTypeEnum extends Enum
{
    const MEMBER = 0;  // 会员自提交
    const CLIENT = 1;  // 柜台提交
    const OPERATOR = 2;  // co app（业务员提交）
    const BACK_OPERATOR = 3;  // 后台的operator
}

class certificationTypeEnum extends Enum
{
    const ID = 1; //身份证
    const FAIMILYBOOK = 2; //户口本
    const PASSPORT = 3; //护照
    const HOUSE = 4; //房屋资产证明
    const CAR = 5; //汽车资产证明
    const WORK_CERTIFICATION = 6; // 工作证明
    //const CIVIL_SERVANT = 7;  // 公务员证明
    const GUARANTEE_RELATIONSHIP = 8;  // 担保人信息
    const LAND = 9;  // 土地
    const RESIDENT_BOOK = 10;  // 居住证
    const MOTORBIKE = 11;  // 摩托车
    const STORE = 12;  // 店铺
    const BIRTH_CERTIFICATE = 13;  // 出生证明
    const DEGREE = 15;//毕业证

}

class certImageKeyEnum extends Enum
{
    const ID_HANDHELD = 'id_handheld';  // 手持身份证照片
    const ID_FRONT = 'id_front';   // 身份证正面
    const ID_BACK = 'id_back';  // 身份证背面

    const FAMILY_BOOK_FRONT = 'family_book_front';  // 户口本正面
    const FAMILY_BOOK_BACK = 'family_book_back';  // 户口本背面
    const FAMILY_BOOK_HOUSEHOLD = 'family_book_household';  // 户主页

    const RESIDENT_BOOK_FRONT = 'resident_book_front';  // 居住证正面
    const RESIDENT_BOOK_BACK = 'resident_book_back';  // 居住证背面

    const PASSPORT_FRONT = 'passport_front';  // 正面
    const PASSPORT_IN = 'passport_in';   // 首页
    const PASSPORT_VISA = 'passport_visa';  // 签证

    const WORK_CARD = 'work_card';                      // 工作卡或证明
    const WORK_EMPLOYMENT_CERTIFICATION = 'work_employment_certification';  // 雇佣证明

    const FAMILY_RELATION_CERT_PHOTO = 'family_relation_cert_photo';  // 家庭关系人证件照

    const MOTORBIKE_PHOTO = 'motorbike_photo';  // 摩托车照片
    const MOTORBIKE_CERT_FRONT = 'motorbike_cert_front';  // 摩托车证件正面
    const MOTORBIKE_CERT_BACK = 'motorbike_cert_back';  // 摩托车证件背面

    const CAR_FRONT = 'car_front';                  // 汽车前面照片
    const CAR_BACK = 'car_back';                    // 汽车后面照片
    const CAR_CERT_FRONT = 'car_cert_front';        // 汽车证件正面
    const CAR_CERT_BACK = 'car_cert_back';          // 汽车证件背面

    const HOUSE_PROPERTY_CARD = 'house_property_card';  // 房屋产权证
    const HOUSE_RELATIONSHIPS_CERTIFY = 'house_relationships_certify';  // 房屋关系证明
    const HOUSE_FRONT = 'house_front';               // 房屋正面图
    const HOUSE_SIDE_FACE = 'house_side_face';     // 房屋侧面图
    const HOUSE_FRONT_ROAD = 'house_front_road';    // 房屋门前马路图
    const HOUSE_INSIDE = 'house_inside';           // 房内图

    const LAND_PROPERTY_CARD = 'land_property_card';   // 土地产权证
    const LAND_TRADING_RECORD = 'land_trading_record';   // 土地交易记录表

    const STORE_BUSINESS_LICENSE = 'business_license';
    const STORE_POSITION = 'position';
    const STORE_STORE_PHOTO = 'store_photo';
    const STORE_MARKET_PHOTO = 'market_photo';

    const BIRTH_CARD = 'birth_card';  // 出生证明

    const DEGREE_CARD = "degree_card";//学历证明


}

class certTypeCalculateValueEnum extends Enum
{
    // 二进制递增
    const ID = 1;
    const FAMILY_BOOK = 2;
    const GUARANTEE_RELATIONSHIP = 4;
    const WORK_CERT = 8;
    const CIVIL_SERVANT = 16;
    const CAR_CERT = 32;
    const HOUSE_CERT = 64;
    const LAND_CERT = 128;
    const PASSPORT = 256;
    const RESIDENT_BOOK = 512;
    const MOTORBIKE = 1024;
    const FAMILY_RELATION = 2048;

}

class certStateEnum extends Enum
{
    const CANCEL = -2;  // 删除
    const LOCK = -1;  // 审核中，锁定
    const CREATE = 0;
    const PASS = 10;
    const EXPIRED = 11;  // 过期
    const NOT_PASS = 100;
}

class creditLevelTypeEnum extends Enum
{
    const MEMBER = 0;
    const MERCHANT = 1;
}

class blackTypeEnum extends Enum
{
    const LOGIN = 1; //登录
    const DEPOSIT = 11; //存款
    const WITHDRAW = 12;//限制取款
    const TRANSFER = 13;//限制转账
    const LOAN = 20;    //信用贷
    const SAVINGS = 30; // 存款产品

}

class fileDirsEnum extends Enum
{
    // 文件夹名常量
    const CLIENT = 'client';
    const ID = 'id';
    const PASSPORT = 'passport';
    const FAMILY_BOOK = 'familybook';
    const FAMILY_RELATION = 'family_relation';
    const WORK_CERT = 'work_cert';
    const BIRTH_CERT = 'birth_cert';
    const MOTORBIKE = 'motorbike';
    const HOUSE = 'house';
    const CAR = 'car';
    const LAND = 'land';
    const RESIDENT_BOOK = 'resident_book';
    const MEMBER_AVATOR = 'member_avator';
    const MEMBER_SALARY = 'member_salary';
    const MEMBER_ASSETS_RENTAL = 'assets_rental';
    const MEMBER_BUSINESS = 'member_business';
    const MEMBER_ATTACHMENT = 'member_attachment';
    const MEMBER_ASSETS = 'member_assets';
    const MEMBER_RELATION = 'member_relation';
    const USER = 'user';
    const BRANCH = 'branch';
    const MARKDOWN = 'markdown';
    const STAFF_ICON = 'staff_icon';
}

class disbursementStateEnum extends Enum
{
    const GOING = 10;
    const FAILED = 11;
    const DONE = 100;
}

class repaymentStateEnum extends Enum
{
    const GOING = 10;
    const FAILED = 11;
    const DONE = 100;
}

class loanProductCategoryEnum extends Enum
{
    const CREDIT_LOAN = 'credit_loan';
}

class loanProductStateEnum extends Enum
{
    const TEMP = 10;
    const ACTIVE = 20;
    const INACTIVE = 30;
    const HISTORY = 40;
}

class loanConsultStateEnum extends Enum
{
    // 贷款申请状态

    const CREATE = 0;  // 客人提交
    const LOCKED = 1;//operator 锁定任务
    const OPERATOR_REJECT = 2;  // Operator拒绝
    const OPERATOR_APPROVED = 5;//operator同意
    const ALLOT_BRANCH = 10;  // 指派给Branch
    const BRANCH_REJECT = 11;  // BRANCH拒绝
    const ALLOT_CO = 12;  // Branch分配CO
    const CO_HANDING = 20;  // CO处理
    const CO_CANCEL = 21;  // CO cancel
    const CO_APPROVED = 22;  // CO APPROVED
}

class loanConsultSourceEnum extends Enum
{
    const MEMBER_APP = 'member_app';
    const OPERATOR_APP = 'operator_app';
    const PHONE = 'phone';
    const FACEBOOK = 'facebook';
    const COUNTER = 'counter';
}

class loanApplyStateEnum extends Enum
{
    // 贷款申请状态
    const LOCKED = -1;
    const CREATE = 0;  // 客人提交
    const OPERATOR_REJECT = 1;  // Operator拒绝
    const ALLOT_CO = 2;  // 指派给CO
    const CO_HANDING = 10;  // CO正在这处理
    const CO_CANCEL = 11;  // CO直接cancel了
    const CO_APPROVED = 20;  // CO check通过
    const BM_APPROVED = 30;  // BM 审核通过 权限内 -> ALL_APPROVED
    const BM_CANCEL = 31;  // BM 否决
    const HQ_APPROVED = 40;  // HQ 审核通过 -> ALL_APPROVED
    const HQ_CANCEL = 41;  // HQ否决
    const ALL_APPROVED = 50;  // 可转为contract的状态
    const ALL_APPROVED_CANCEL = 51;  // approve后被取消
    const DONE = 100;  // 已经转为合同
}


class loanApplySourceEnum extends Enum
{
    const MEMBER_APP = 'member_app';
    const OPERATOR_APP = 'operator_app';
    const PHONE = 'phone';
    const FACEBOOK = 'facebook';
    const CLIENT = 'counter'; // 柜台
}

class loanRepaymentStateEnum extends Enum
{
    const START = 10;
    const FAILURE = 11;
    const SUCCESS = 100;
}

class requestRepaymentTypeEnum extends Enum
{
    const SCHEME = 'schema';  // 计划还款
    const BALANCE = 'balance';  // 提前还款
}

class repaymentWayEnum extends Enum
{
    const CASH = 0;  // 现金
    const AUTO_DEDUCTION = 1;  // partner-> api (ACE)
    const BANK_TRANSFER = 2;  // bank
    const PASSBOOK = 3;  // 储蓄账户
}

class requestRepaymentStateEnum extends Enum
{
    const CREATE = 0;//新建
    const PROCESSING = 20;//查账中
    const FAILED = 21;//未到账
    const RECEIVED = 30;  // 只是钱到账，没更改合同
    const SUCCESS = 100;// 到账，合同已更改
}

class prepaymentApplyStateEnum extends Enum
{
    // 提前还款申请的状态
    const CREATE = 0;       // 申请
    const AUDITING = 10;    //提前还款审核中
    const DISAPPROVE = 11;  //审核不通过
    const APPROVED = 20;  // 审核通过
    const PAID = 30;       // 已付款
    const PROCESSING = 31; // 查账中
    const RECEIVED = 40;  // 钱已到账，未处理合同
    const SUCCESS = 100;  // 合同处理完成
    const FAIL = 101;     // 合同处理失败
}

class prepaymentRequestTypeEnum extends Enum
{
    const PARTLY = 0;    // 部分偿还
    const FULL_AMOUNT = 1;  // 全部偿还
    const LEFT_PERIOD = 2;  // 偿还期数方式
}

class penaltyOnEnum extends Enum
{
    const OVERDUE_PRINCIPAL = 'overdue_principal';
    const PRINCIPAL_INTEREST = 'principal_interest';
    const TOTAL = 'total';
}

class singleRepaymentEnum extends Enum
{
    const DAYS_7 = '7_days';
    const DAYS_15 = '15_days';
    const MONTH_1 = '1_month';
    const MONTHS_3 = '3_months';
    const MONTHS_6 = '6_months';
    const YEAR_1 = '1_year';
}

class interestRatePeriodEnum extends Enum
{
    const YEARLY = 'yearly';
    const SEMI_YEARLY = 'semi_yearly';
    const QUARTER = 'quarter';
    const MONTHLY = 'monthly';
    const WEEKLY = 'weekly';
    const DAILY = 'daily';
}

class loanPeriodUnitEnum extends Enum
{
    const YEAR = 'year';
    const MONTH = 'month';
    const DAY = 'day';
}


class interestPaymentEnum extends Enum
{
    const SINGLE_REPAYMENT = 'single_repayment';
    const ADVANCE_SINGLE_REPAYMENT = 'advance_single_repayment';
    const ANYTIME_SINGLE_REPAYMENT = 'anytime_single_repayment';  // 随借随还
    const FIXED_PRINCIPAL = 'fixed_principal';
    const ANNUITY_SCHEME = 'annuity_scheme';
    const ANYTIME_ANNUITY = 'anytime_annuity';
    const FLAT_INTEREST = 'flat_interest';
    const BALLOON_INTEREST = 'balloon_interest';
    const SEMI_BALLOON_INTEREST = 'semi_balloon_interest';
    const ADVANCE_FIX_REPAYMENT_DATE = 'advance_fix_repayment_date';
}


class memberFamilyStateEnum extends Enum
{
    const CREATE = 0;
    const INVALID = 10;  // 无效
    const REMOVE = 11;  // 解除
    const APPROVAL = 100; // 核准
}

class memberGuaranteeStateEnum extends Enum
{
    const CANCEL = -1;  // 取消
    const CREATE = 0;
    const REJECT = 11;
    const ACCEPT = 100;
}

class workStateStateEnum extends Enum
{
    const CREATE = 0;
    const APPROVING = 10;
    const INVALID = 11;  // 审核未通过
    const VALID = 20;  // 核实
    const HISTORY = 30;    // 历史
}

class assetStateEnum extends Enum
{
    const CANCEL = -1;  // 删除
    const CREATE = 0;   // 新加
    const INVALID = 11;  //无效
    //const ABANDON = 20;  // 作废
    const CERTIFIED = 100;  // 已认证
    const GRANTED = 110;   // 已授信
}

class workTypeEnum extends Enum
{
    //const FREE = "free";//自由职业
    const STAFF = 'internal_staff'; // 公司内部员工
    const GOVERNMENT = 'government';    // 政府员工
    const EXTERNAL_STAFF = 'external_staff';  // 外部客户
    const BUSINESS = 'business';  // 做生意
    const HOUSE_WIFE="house_wife";//家庭主妇
}

class contractWriteOffTypeEnum extends Enum
{
    const SYSTEM = 10;
    const ABNORMAL = 20;
}

class writeOffStateEnum extends Enum
{
    const CREATE = 0;
    const APPROVING = 10;
    const REJECT = 11;  // 审核未通过
    const COMPLETE = 100;  // 审核通过，已核销
}

class loanDeductingPenaltiesState extends Enum
{
    const CREATE = 0;
    const PROCESSING = 10;
    const DISAPPROVE = 20;
    const APPROVE = 30;
    const USED = 40;
}

class helpCategoryEnum extends Enum
{
    const CREDIT_LOAN = 'credit_loan';
    const MORTGAGE_LOAN = 'mortgage_loan';
    const SAVINGS = 'savings';
    const INSURANCE = 'insurance';
}

class helpStateEnum extends Enum
{
    const CREATE = 0;
    const NOT_SHOW = 10;
    const SHOW = 100;
}

class pointEventEnum extends Enum
{
    const ADD = 'add';
    const AUDIT = 'audit';
    const VERIFY = 'verify';
}

class currencyEnum extends Enum
{
    const USD = "USD";
    const KHR = "KHR";
    //const CNY = "CNY";
    //const VND = 'VND';
    //const THB = 'THB';
}

/**
 * c财务上要用这个，否则后面增加一个货币，会乱套
 * Class accountingCurrencyEnum
 */
class accountingCurrencyEnum extends Enum
{
    const USD = "USD";
    const KHR = "KHR";
    const THB = 'THB';
}

class currencyMinValueEnum extends Enum
{
    const USD = 0.01;
    const KHR = 100;
    //const CNY = "CNY";
    //const VND = 'VND';
    //const THB = 'THB';
}

//用户定义enum start
class userDefineEnum extends Enum
{
    const MORTGAGE_TYPE = 'mortgage_type';
    const GUARANTEE_TYPE = 'guarantee_type';
    const LOAN_USE = 'loan_use';
    //const GENDER = 'gender';
    const OCCUPATION = 'occupation';
    const FAMILY_RELATIONSHIP = 'family_relationship';
    const GUARANTEE_RELATIONSHIP = 'guarantee_relationship';  // Guarantee Relationship
    const MARITAL_STATUS = 'marital_status';
//    const BANK_CODE = 'bank_code';
    const INDUSTRY_CATEGORY = 'industry_category';
}

//用户定义enum end

class trxTypeEnum extends Enum
{
    const DEC = -1;  // 减
    const INVALID = 0;  // 无效
    const INC = 1;  // 加
}

class apiStateEnum extends Enum
{
    const CANCELLED = 0;
    const CREATED = 10;
    const STARTED = 20;
    const PENDING_CHECK = 30;
    const FINISHED = 40;
}

class nationalityEnum extends Enum
{
    const CAMBODIA = 'cambodia';
    const CHINA = 'china';
}

class refBizTypeEnum extends Enum
{
    // API 外部业务类型
    const LOAN = 'loan';
    const INSURANCE = 'insurance';
    const SAVINGS = 'savings';
}

class limitKeyEnum extends Enum
{
    const LIMIT_LOAN = 'limit_loan';
    const LIMIT_DEPOSIT = 'limit_deposit';
    const LIMIT_EXCHANGE = 'limit_exchange';
    const LIMIT_WITHDRAW = 'limit_withdraw';
    const LIMIT_TRANSFER = 'limit_transfer';
}

class passbookTypeEnum extends Enum
{
    const ASSET = "asset";      // 资产类
    const DEBT = "debt";        // 负债类
    const EQUITY = "equity";    // 所有着权益类
    const PROFIT = "profit";    // 损益类 - 收入
    const COST = "cost";        // 成本类
    const COMMON = "common";    // 共同类,
    const PROFIT_INCOME = "profit_income";
    const PROFIT_EXPENSE = "profit_expense";
}

class passbookObjTypeEnum extends Enum
{
    // 储蓄账户对象类型
    const CLIENT_MEMBER = 'client_member';
    const UM_USER = 'user';
    const BRANCH = 'branch';
    const GL_ACCOUNT = 'gl_account';
    const BANK = 'bank';
    const PARTNER = 'partner';
}

class passbookStateEnum extends Enum
{
    const CANCEL = -1;
    const ACTIVE = 100;
    const FREEZE = 10;
}

class passbookTradingStateEnum extends Enum
{
    const CANCELLED = -1;
    const CREATE = 0;
    const DONE = 100;
}

class passbookTradingTypeEnum extends Enum
{

    const ADJUST = 'adjust';
    const BANK_ADJUST = 'bank_adjust';
    const BANK_TO_BRANCH = 'bank_to_branch';
    const BANK_TO_HEADQUARTER = 'bank_to_headquarter';
    const BRANCH_ADJUST = 'branch_adjust';
    const BRANCH_TO_BANK = 'branch_to_bank';
    const BRANCH_TO_CASHIER = 'branch_to_cashier';
    const BRANCH_TO_HEADQUARTER = 'branch_to_headquarter';
    const CAPITAL_RECEIVE = 'capital_receive';
    const CASHIER_TO_BRANCH = 'cashier_to_branch';
    const CLIENT_ADJUST = 'client_adjust';
    const CLIENT_DEPOSIT_BY_BANK = 'client_deposit_by_bank';
    const CLIENT_DEPOSIT_BY_CASH = 'client_deposit_by_cash';
    const CLIENT_DEPOSIT_BY_PARTNER = 'client_deposit_by_partner';
    const CLIENT_PAYMENT_TO_CLIENT = 'client_payment_to_client';
    const CLIENT_TO_CLIENT = 'client_to_client';
    const CLIENT_WITHDRAW_BY_BANK = 'client_withdraw_by_bank';
    const CLIENT_WITHDRAW_BY_CASH = 'client_withdraw_by_cash';
    const CLIENT_WITHDRAW_BY_PARTNER = 'client_withdraw_by_partner';
    const HEADQUARTER_TO_BANK = 'headquarter_to_bank';
    const HEADQUARTER_TO_BRANCH = 'headquarter_to_branch';
    const LOAN_DEDUCT = 'loan_deduct';
    const LOAN_DISBURSE = 'loan_disburse';
    const LOAN_PREPAYMENT = 'loan_prepayment';
    const LOAN_REPAYMENT = 'loan_repayment';
    const MEMBER_ADJUST = 'member_adjust';
    const MEMBER_DEPOSIT_BY_BANK = 'member_deposit_by_bank';
    const MEMBER_DEPOSIT_BY_CASH = 'member_deposit_by_cash';
    const MEMBER_DEPOSIT_BY_PARTNER = 'member_withdraw_by_partner';
    const MEMBER_PAYMENT_TO_MEMBER = 'member_payment_to_member';
    const MEMBER_TO_MEMBER = 'member_to_member';
    const MEMBER_WITHDRAW_BY_BANK = 'member_withdraw_by_bank';
    const MEMBER_WITHDRAW_BY_CASH = 'member_withdraw_by_cash';
    const MEMBER_WITHDRAW_BY_PARTNER = 'member_withdraw_by_partner';
    const USER_TO_USER = 'user_to_user';
}

class passbookAccountFlowStateEnum extends Enum
{
    const CANCELLED = -1;
    const CREATE = 0;
    const OUTSTANDING = 90;
    const DONE = 100;
}

/**
 * 内部用户的职位，实际是以入口为划分原则
 * Class userPositionEnum
 */
class userPositionEnum extends Enum
{
    const BRANCH_MANAGER = 'branch_manager';
    const CHIEF_TELLER = 'chief_teller';
    const TELLER = 'teller';
    const CREDIT_OFFICER = 'credit_officer';
    const CUSTOMER_SERVICE = 'customer_service';
    const OPERATOR = 'operator';

    //上面的账户可以hr开，下面的账户只能是root开
    const MONITOR = 'monitor';
    const DEVELOPER = 'developer';
    const ROOT = "root";
    //const COMMITTEE_MEMBER = 'committee_member'; 弃用了，用user_group才能表达意义
    const BACK_OFFICER = 'back_officer';
    const CHIEF_CREDIT_OFFICER="chief_credit_officer";
    const CREDIT_CONTROLLER="credit_controller";
    const RISK_CONTROLLER="risk_controller";
}

class userGroupKeyEnum extends Enum
{
    const GRANT_CREDIT_COMMITTEE = "grant_credit_committee";
    const FAST_CREDIT_COMMITTEE = "fast_credit_committee";
    const WRITTEN_OFF_LOAN_COMMITTEE = "written_off_loan_committee";
}

class authTypeEnum extends Enum
{
    const BACK_OFFICE = 'back_office';
    const COUNTER = 'counter';
}

/**
 * Class incomingTypeEnum
 * 收入类型
 */
class incomingTypeEnum extends Enum
{
    /**
     * 年费
     */
    const ANNUAL_FEE = "annual_fee_incoming";

    /**
     * 利息
     */
    const INTEREST = "interest_incoming";

    /**
     * 管理费
     */
    const ADMIN_FEE = "admin_fee_incoming";

    /**
     * 手续费
     */
    const LOAN_FEE = "loan_fee_incoming";

    /**
     * 运营费
     */
    const OPERATION_FEE = "operation_fee_incoming";

    /**
     * 保险费
     */
    const INSURANCE_FEE = "insurance_fee_incoming";

    /**
     * 服务费
     */
    const SERVICE_FEE = "service_fee_incoming";

    /**
     * 逾期罚金
     */
    const OVERDUE_PENALTY = "overdue_penalty_incoming";

    /**
     * 提前还款违约金
     */
    const PREPAYMENT_PENALTY = "prepayment_penalty_incoming";

    /**
     * 贷款利息科目
     */
    const INTEREST_STDL_SHORT = "interest_stdl_short";//小于1年贷款的利息收入
    const INTEREST_STDL_LONG = "interest_stdl_long";//大于1年贷款的利息收入
    const INTEREST_SUB_STDL_SHORT = "interest_sub_stdl_short";//小于1年贷款逾期(30-60)利息收入
    const INTEREST_SUB_STDL_LONG = "interest_sub_stdl_long";//大于1年贷款逾期(30-60)利息收入
    const INTEREST_DFL_SHORT = "interest_dfl_short";//小于1年贷款逾期(60-90)利息收入
    const INTEREST_DFL_LONG = "interest_dfl_long";//大于1年贷款逾期(60-90)利息收入
    const INTEREST_LL_SHORT = "interest_ll_short";//小于1年贷款逾期(>90)利息收入
    const INTEREST_LL_LONG = "interest_ll_long";//大于1年贷款逾期(>90)利息收入

    /**
     * 银行调整收入
     */
    const BANK_ADJUST_INTEREST = "bank_adjust_interest";
    /**
     * 信用授权合同费
     */
    //const CREDIT_CONTRACT_FEE = "credit_auth_contract_fee_incoming";

    const OTHER_INCOMING = 'other_incoming';

    const TOP_UP_INCOMING = 'top_up_incoming';

}

/**
 * Class outgoingTypeEnum
 * 支出类型
 */
class outgoingTypeEnum extends Enum
{
    /**
     * 银行费用
     */
    const BANK_FEE = "bank_adjust_fee";
    const DEPOSIT_BY_PARTNER = "deposit_by_partner";
    const OTHER_EXPENSE = 'other_expense';
}

/**
 * Class businessTypeEnum
 * 业务类型
 */
class businessTypeEnum extends Enum
{
    /**
     * 信用贷
     */
    const CREDIT_LOAN = "credit_loan";

    /**
     * 其他
     */
    const OTHER = "other";

    /**
     * Savings
     */
    const SAVINGS = "savings";
}

/**
 * Class systemAccountCodeEnum
 * 系统账户代码枚举
 */
class systemAccountCodeEnum extends Enum
{
    const HQ_CIV = "hq_civ";
    const HQ_COD = "hq_cod";
    const HQ_CAPITAL = "hq_capital";
    const HQ_INIT = "hq_init";
    const HQ_DEBT_INIT = "hq_debt_init";
    const EXCHANGE_SETTLEMENT = "exchange_settlement";  // 换汇结算户
    const ROUND_ADJUST = "round_adjustment";    // 抹零调整户
    const BIZ_REVENUE = "biz_revenue";
    const BIZ_EXPENSE = "biz_expense";

    const FEE_WITHDRAW_TO_PARTNER = "fee_withdraw_to_partner";

    const LOSS_LOAN_PRINCIPAL = "loan_principal_loss";   // 贷款核销本金损失
    const RECOVERY_ON_LOAN = "recovery_on_loan";          //坏账收回
    const OUT_SYSTEM_INCOME_AND_EXPENSES = "out_system_income_and_expenses";   // 系统外收支
    const BRANCH_CIV_EXT_IN = "branch_civ_ext_in";
    const BRANCH_CIV_EXT_OUT = "branch_civ_ext_out";

    const FINANCIAL_EXPENSES = "financial_expenses";  // 弃用，财务费用
    const RECEIVABLE_LOAN_INTEREST = "receivable_loan_interest";//弃用
    const RECEIVABLE_LOAN_OPERATION_FEE = "receivable_loan_operation_fee";//弃用
}

/**
 * Class accountingDirectionEnum
 * 会计记账方向
 */
class accountingDirectionEnum extends Enum
{
    /**
     * 借方
     * 对于资产类、费用类账户，借加
     * 对于负债、所有者权益、收入类账户，借减
     */
    const DEBIT = 0;

    /**
     * 贷方
     * 对于负债、所有者权益、收入类账户，贷加
     * 对于资产类、费用类账户，贷减
     */
    const CREDIT = 1;
}


class bizStateEnum extends Enum
{
    const CANCEL = -1;
    const CREATE = 0;
    const REJECT = 10;
    const PENDING_APPROVE = 20;  // 待审核
    const APPROVED = 21;
    const PENDING_CONFIRM = 30; // 待确认
    const PENDING_CHECK = 80;   // 待核查，异常需要手工处理的
    const DONE = 100;
    const FAIL = 101;
}

class bizSceneEnum extends Enum
{
    const APP_MEMBER = "app_member";
    const APP_CO = "app_co";
    const COUNTER = "counter";
    const BACK_OFFICE = "back_office";
    const SCRIPT = "script";
}


class bizCheckTypeEnum extends Enum
{
    const TRADING_PASSWORD = 'trading_password';
}

class bizCodeEnum extends Enum
{
    const MEMBER_WITHDRAW_TO_PARTNER = 'member_withdraw_to_partner';
    const MEMBER_WITHDRAW_TO_BANK = 'member_withdraw_to_bank';
    const MEMBER_WITHDRAW_TO_CASH = 'member_withdraw_to_cash';
    const MEMBER_TRANSFER_TO_MEMBER = 'member_transfer_to_member';
    const MEMBER_TRANSFER_TO_BANK = 'member_transfer_to_bank';
    const MEMBER_DEPOSIT_BY_PARTNER = 'member_deposit_by_partner';
    const MEMBER_DEPOSIT_BY_BANK = 'member_deposit_by_bank';
    const MEMBER_DEPOSIT_BY_CASH = 'member_deposit_by_cash';
    const TELLER_TO_BRANCH = 'teller_to_branch';
    const BRANCH_TO_TELLER = 'branch_to_teller';
    const BRANCH_TO_BANK = 'branch_to_bank';
    const BANK_TO_BRANCH = 'bank_to_branch';
    const BANK_ADJUST_FEE_INTEREST = 'bank_adjust_fee_interest';
    const MEMBER_SCAN_PAY_TO_MEMBER = 'member_scan_pay_to_member';
    const HEADQUARTER_TO_BANK = "headquarter_to_bank";
    const BANK_TO_HEADQUARTER = "bank_to_headquarter";
    const MEMBER_LOAN_REPAYMENT_BY_CASH = 'member_loan_repayment_by_cash';
    const MEMBER_LOAN_REPAYMENT_BY_MEMBER_APP = 'member_loan_repayment_by_member_app';
    const MEMBER_CHANGE_TRADING_PASSWORD_BY_COUNTER = 'member_change_trading_password_by_counter';
    const MEMBER_CHANGE_PHONE_BY_COUNTER = 'member_change_phone_by_counter';
    const CO_RECEIVE_LOAN_FROM_MEMBER = 'co_receive_loan_from_member';
    const CO_TRANSFER_TO_TELLER = 'co_transfer_to_teller';
    const CAPITAL_TO_CIV = 'capital_to_civ';
    const CIV_TO_COD = 'civ_to_cod';
    const MEMBER_CREATE_LOAN_CONTRACT = 'member_create_loan_contract'; //  counter
    const MEMBER_LOAN_BY_MEMBER_APP = 'member_loan_by_member_app';
    const MEMBER_UNLOCK_BY_COUNTER = 'member_unlock_by_counter';
    const RECEIVE_LOAN_PENALTY_BY_COUNTER = 'receive_loan_penalty_by_counter';
    const MEMBER_PREPAYMENT = 'member_prepayment';
    const MEMBER_PREPAYMENT_BY_MEMBER_APP = 'member_prepayment_by_member_app';
    const OUT_SYSTEM_CASH_FLOW = 'out_system_cash_flow';
    const MANUAL_VOUCHER = "manual_voucher";
    const HEADQUARTER_TO_BRANCH = "headquarter_to_branch";
    const BRANCH_TO_HEADQUARTER = "branch_to_headquarter";
    const HEADQUARTER_TO_PARTNER = "headquarter_to_partner";
    const PARTNER_TO_HEADQUARTER = "partner_to_headquarter";
    const CIV_EXT_IN = "civ_ext_in";
    const CIV_EXT_OUT = "civ_ext_out";
    const ONE_TIME_CREDIT_LOAN = 'one_time_credit_loan';
    const GL_BATCH = 'gl_batch';
    const BRANCH_EXCHANGE = 'branch_exchange';
    const CANCEL_CREDIT_CONTRACT = 'cancel_credit_contract';
    const CHECK_LOAN_BILL_PAY_BY_CONSOLE = 'check_loan_bill_pay_by_console';
    const MEMBER_PURCHASE_SAVINGS_PRODUCT = "member_purchase_savings_product";
    const MEMBER_REDEEM_SAVINGS_PRODUCT = "member_redeem_savings_product";
}

class memberTransferToTypeEnum extends Enum
{
    const MEMBER = 'member';
    const BANK = 'bank';
}

class complaintAdviceEnum extends Enum
{
    const CREATE = 1;
    const HANDLE = 2;
    const CHECKED = 3;
}

class memberCreditSuggestEnum extends Enum
{
    const CANCEL = -1;
    const CREATE = 0;
    const PENDING_APPROVE = 1;
    const HQ_REJECT = 2;
    const HANDLE_REJECT = 3;
    const APPROVING = 9;
    const NO_PASS = 10;
    const PASS = 100;
}

class authorizedContractStateEnum extends Enum
{
    const LOCK = -10;
    const CANCEL = -1;
    const CREATE = 0;
    const UN_RECEIVED = 10;
    const COMPLETE = 100;
}

class assetMortgageContractTypeEnum extends Enum
{
    const CREDIT_LOAN = 0;
    const OFFLINE_LOAN = 1;
}

class mortgageFileTypeEnum extends Enum
{
    // 同下面资产的一致
    const SOFT = 'soft';
    const HARD = 'hard';
}

class assetsCertTypeEnum extends Enum
{
    const SOFT = 'soft';
    const HARD = 'hard';
}

class degreeTypeEnum extends Enum
{
    const HIGH_SCHOOL = "high_school_degree";
    const TECHNICAL_TRAINING = "technical_training";
    const BACHELOR = "bachelor_degree";
    const MASTER = "master_degree";
    const DOCTOR = "doctor_degree";
}

class memberIndustryStateEnum extends Enum
{
    const HISTORY = 0;
    const ACTIVE = 1;
}

class businessPhotoTypeEnum extends Enum
{
    const PLACE_SCENE = 1;
    const CONTRACT = 2;
}

class passwordTypeEnum extends Enum
{
    const TRADING_PASSWORD = 'trading_password';
}

class imageThumbVersion extends Enum
{
    const AVATAR = "f120x120";
    const SMALL_ICON = "60";
    const SMALL_IMG = "90";
    const VERIFY_IMG = "w300";
    const W150 = "w300";
    const MAX_120 = "120";
    const MAX_240 = "240";
}

class surveyType extends Enum
{
    const DESCRIPTION = "description";
    const INCOME = "income";
    const EXPENSE = "expense";
    const CHECKBOX = "checkbox";
}

class assetSurveyType extends Enum
{
//    const TEXT = "text";
    const DESCRIPTION = "description";
    const CHECKBOX = "checkbox";
}

class operatorTypeEnum extends Enum
{
    const CO = 0;
    const BM = 1;
}

class memberAttachmentTypeEnum extends Enum
{
    const FILE = 0;
    const INCOME = 1;
    const EXPENSE = 2;
}

class loanPenaltyHandlerStateEnum extends Enum
{
    const CREATE = 0;
    const APPLY_REDUCE = 10;
    const DONE = 100;
}

class loanPenaltyReceiptStateEnum extends Enum
{
    const CREATE = 0;
    const AUDITING = 10; // 审核中
    const REJECTED = 11;  // 审核未通过
    const APPROVED = 20;  // 审核通过
    const COMPLETE = 100;  // 处理完成
}

/*
 * 商业调查或者资产评估的参与人员类型,本来应该用userPosition的，可惜那里之前设计成了value为字符串
 */

class researchPositionTypeEnum extends Enum
{
    const CREDIT_OFFICER = 0;
    const BRANCH_MANAGER = 1;
    const OPERATOR = 2;
}

class userTaskStateTypeEnum extends Enum
{
    const CANCEL = 0;
    const RUNNING = 10;
    const DONE = 100;
}


class userTaskTypeEnum extends Enum
{
    const OPERATOR_NEW_CLIENT = "operator_new_client";
    const OPERATOR_NEW_CONSULT = "operator_new_consult";
    const OPERATOR_NEW_CERT = "operator_new_cert";
    const OPERATOR_RELATIVE_NEW_CERT = 'operator_relative_new_cert';
    const OPERATOR_MY_CONSULT = "operator_my_consult";
    const CHANGE_CLIENT_ICON = "change_client_icon";
    const CHANGE_CLIENT_DEVICE = "change_client_device";
    const CLIENT_CHANGE_TRADING_PASSWORD = 'client_change_trading_password';

    const BM_NEW_CLIENT = "bm_new_client";
    const BM_NEW_CONSULT = "bm_new_consult";
    const BM_REQUEST_FOR_CREDIT = "bm_request_for_credit";
    const CO_SUBMIT_BM = "co_submit_bm";
    const BM_REJECT_CO = "bm_reject_co_submit";

    const MONITOR_OVERDUE_LOAN = "monitor_overdue_loan";
}

class handleLockTypeEnum extends Enum
{
    const LOCK = 0;  // 锁定
    const UNLOCK = 1;  // 解锁
}

class loanRelativeTypeEnum extends Enum
{
    const COBORROWER = 'coborrower';
    const GUARANTEE = 'guarantee';
}

class creditRequestStateEnum extends Enum
{
    const CANCEL = -1;  // 取消
    const CREATE = 0;  // 新建
    const GRANTED = 20;  // 已授信
    const DONE = 100;  // 已取钱
}

class flagTypeEnum extends Enum
{
    const INCOME = 1;  // 进
    const PAYOUT = -1;  // 出
    const OUTSTANDING = 0;  // 待确认
}

class loanBillPayCheckState extends Enum
{
    const CREATE = 0;
    const API_FAILURE = 20;
    const SUCCESS = 100;
}

class memberIdType extends Enum
{
    const DOMESTIC = 0; //国内
    const ABROAD = 1; //国外
}

class assetStorageFlowType extends Enum
{
    const RECEIVED_FROM_CLIENT = 0;
    const TRANSFER = 10;
    const WITHDRAW_BY_CLIENT = 20;
    const WITHDRAW_BY_AUCTION = 30;
}

class assetRequestWithdrawStateEnum extends Enum
{
    const REJECT = -1;
    const PENDING_APPROVE = 0;
    const PENDING_WITHDRAW = 10;
    const DONE = 100;
}

class jpushNoticeTypeEnum extends Enum
{
    const SCAN_AUTH_LOGIN_DEVICE_OK = 'scan_auth_device_success';
    const SCAN_PAY_PAYMENT_OK = 'scan_pay_payment_success';
    const SCAN_PAY_RECEIVE_OK = 'scan_pay_receive_success';
}

class numberEnum extends Enum
{
    const FIRST = 1;
    const SECOND = 2;
    const THIRD = 3;
    const FOURTH = 4;
    const FIFTH = 5;

}

class newDeviceApplyStateEnum extends Enum
{
    const CREATE = 0;
    const REFUSED = 10;
    const PASS = 100;
}

class balanceSheetColumnRedirectTypeEnum extends Enum
{
    const CASH_ON_HAND_CO = 'cash_on_hand_co';
    const CASH_ON_HAND_TELLER = 'cash_on_hand_teller';
    const CASH_ON_HAND_OTHER = 'cash_on_hand_other';
    const CASH_IN_VAULT_HEADQUARTERS = 'cash_in_vault_headquarters';
    const CASH_IN_VAULT_BRANCHES = 'cash_in_vault_branches';
    const RECEIVABLE_SHORT_TERM_PRINCIPAL = 'receivable_short_term_principal';
    const RECEIVABLE_LONG_TERM_PRINCIPAL = 'receivable_long_term_principal';
    const LIABILITY_SAVINGS = 'liability_savings';
}

class messageTypeEnum extends Enum
{
    const BROADCAST = 1;
    const NORMAL = 0;  // 要兼容旧数据，原来的默认是0
}

/**
 * Class commonApproveStateEnum
 * 现在的状态枚举太多太乱了，应该开始定义一个公用的状态枚举，特别的才独立写
 * 通用任务处理状态
 */
class commonApproveStateEnum extends Enum
{
    const CANCEL = -1;
    const CREATE = 0;
    const APPROVING = 10;
    const REJECT = 20;
    const PASS = 100;
    const EXPIRY = 110;

}

class imageSourceEnum extends Enum
{
    // 图片来源
    const CAMERA = 0;
    const ALBUM = 1;
}

class moduleBusinessEnum extends Enum
{
    const MODULE_DEPOSIT = 'module_deposit';
    const MODULE_WITHDRAW = 'module_withdraw';
    const MODULE_EXCHANGE = 'module_exchange';
    const MODULE_CREDIT = 'module_credit';
    const MODULE_CERTIFICATION = 'module_certification';
    const MODULE_BRANCH = 'module_branch';
    const MODULE_SERVICE = "module_service";
    const MODULE_SAVINGS = "module_savings";
    const MODULE_HOME = "module_home";
    const MODULE_LOAN = "module_loan";//app的一级菜单：loan
    const MODULE_LOAN_ONE_TIME = "module_loan_one_time";//app的一级菜单：loan
    const MODULE_LOAN_CONTRACT = "module_loan_contract";//app的二级菜单loan=>loan功能
    const MODULE_LOAN_REPAY = "module_loan_repay";//app的二级菜单loan=>repay功能
    const MODULE_APPROVE_CREDIT = "module_approve_credit";//限制backoffce通过新的credit
    const MODULE_TOP_UP = "module_top_up";//充值功能
}

class loanCategoryCodeEnum extends Enum
{
    const CONSUMPTION = "consumption";
    const CAR = "car";
    const MOTOR = "motor";
    const HOUSING = "housing";
    const BUSINESS = "business";
    const AGRICULTURE = "agriculture";
    const EMERGENCY = "emergency";
    const SINGLE = "single";
    const LAND = "land";
    const PERSONAL = 'personal';
}

class memberVerifyTypeEnum extends Enum
{
    const ID_CARD = 0;
    const FAMILY_BOOK = 1;
    const PASSPORT = 2;
    const BIRTH_CERTIFICATE = 3;
    const RESIDENT_BOOK = 4;
    const OTHER_CERTIFICATION = 11;
    const SPECIAL_SETTING = 12;
}

class specialLoanCateKeyEnum extends Enum
{
    const FIX_REPAYMENT_DATE = 'fix_repayment_date';  // 固定某一日还款的
    const QUICK_LOAN = 'quick_loan';
}

class topUpScene extends Enum
{
    const MEMBER_APP = 'member_app';
    const COUNTER = 'counter';
    const PARTNER = 'partner';
}

class topUpRechargeType extends Enum
{
    const DIRECT = 'direct';
    const PIN_CODE = 'pin_code';
}

class topUpTrxState extends Enum
{
    const CANCEL = -1;
    const CREATE = 0;
    const PAYMENT_FAIL = 11;
    const DIRECT_RECHARGE_FAIL = 21;
    const SUCCESS = 100;
}

class topUpDirectRechargeState extends Enum
{
    const CANCEL = -1;
    const CREATE = 0;
    const SUCCESS = 100;
}

class savingsCategoryType extends Enum
{
    const CURRENT_ASSETS = 'current';
    const TERMINAL_ASSETS = 'terminal';
}

class savingsCategoryState extends Enum
{
    const INACTIVE = 0;
    const ACTIVE = 100;
}

class  savingsPeriodUnitEnum extends Enum
{
    const YEARLY = 'yearly';
    const MONTHLY = 'monthly';
    const DAILY = 'daily';
}

class savingsProductStateEnum extends Enum
{
    const CANCEL = 0;
    const TEMP = 10;
    const ACTIVE = 20;
    const INACTIVE = 30;
}

class clientObjTypeEnum extends Enum
{
    const MEMBER = "member";
}

class savingsContractStateEnum extends Enum
{
    const CANCELLED = 0;
    const TEMP = 10;
    const CONFIRMING = 15;
    const PROCESSING = 20;
    const SETTLING = 30;
    const FINISHED = 40;
}

class savingsInterestStartTypeEnum extends Enum
{
    const NEXT_DAY = "nextday";
    const IMMEDIATELY = "immediately";
}

class staffStatusEnum extends Enum
{
    const ON_TRIAL = 0;
    const REGULAR_EMPLOYEE = 100;
    const NORMAL_DIMISSION = 101;
    const ABNORMAL_DIMISSION = 102;
}

class savingsCategoryTermStyleEnum extends Enum {
    const FIXED = 1;
    const RANGE = 2;
    const FREE = 3;
}

class savingsTransactionStateEnum extends Enum {
    const CANCELLED = 0;
    const TEMP = 10;
    const PROCESSING = 20;
    const FINISHED = 40;
}

class savingsTransactionTypeEnum extends Enum {
    const PURCHASE = 1;
    const REDEEM = 2;
}
