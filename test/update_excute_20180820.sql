ALTER TABLE `member_credit_grant_product`   
  CHANGE `product_id` `member_credit_category_id` INT(10) DEFAULT 0  NOT NULL  COMMENT 'member_credit_category_id',
  ADD COLUMN `credit_usd` INT(10) DEFAULT 0  NULL AFTER `credit`,
  ADD COLUMN `credit_khr` INT(10) DEFAULT 0  NULL AFTER `credit_usd`,
  ADD COLUMN `exchange_rate` INT(10) DEFAULT 4000  NULL AFTER `credit_khr`;
 
ALTER TABLE `member_credit_suggest_product`   
  CHANGE `product_id` `member_credit_category_id` INT(10) DEFAULT 0  NULL  COMMENT 'member_credit_category_id',
  ADD COLUMN `credit_usd` INT(10) DEFAULT 0  NULL AFTER `credit`,
  ADD COLUMN `credit_khr` INT(10) DEFAULT 0  NULL AFTER `credit_usd`,
  ADD COLUMN `exchange_rate` INT(10) DEFAULT 4000  NULL AFTER `credit_khr`; 
  
  INSERT INTO member_credit_suggest_product(credit_suggest_id,member_credit_category_id,credit,credit_usd,credit_khr,exchange_rate)
SELECT  credit_suggest_id,member_credit_category_id,SUM(credit) credit,SUM(credit) credit_usd,0,4000 FROM member_credit_suggest_detail
WHERE CONCAT(credit_suggest_id,'@',member_credit_category_id) NOT IN (SELECT CONCAT(credit_suggest_id,'@',member_credit_category_id) FROM member_credit_suggest_product) AND member_credit_category_id>0
GROUP BY credit_suggest_id,member_credit_category_id; 

UPDATE member_credit_suggest_product a,
(SELECT uid,default_credit_category_id, default_credit FROM member_credit_suggest) b
SET a.credit=a.credit+b.default_credit,a.credit_usd=a.credit_usd+b.default_credit
WHERE a.credit_suggest_id=b.uid AND a.member_credit_category_id=b.default_credit_category_id AND b.default_credit_category_id>0;

INSERT INTO member_credit_suggest_product(credit_suggest_id,member_credit_category_id,credit,credit_usd,credit_khr,exchange_rate)
SELECT  uid,default_credit_category_id,default_credit credit,default_credit credit_usd,0,4000 FROM member_credit_suggest
WHERE CONCAT(uid,'@',default_credit_category_id) NOT IN (SELECT CONCAT(credit_suggest_id,'@',member_credit_category_id) FROM member_credit_suggest_product) AND default_credit_category_id>0;

ALTER TABLE `member_credit_category`   
  ADD COLUMN `credit_usd` INT(10) DEFAULT 0  NULL AFTER `interest_package_id`,
  ADD COLUMN `credit_usd_balance` INT(10) DEFAULT 0  NULL AFTER `credit_usd`,
  ADD COLUMN `credit_khr` INT(10) DEFAULT 0  NULL AFTER `credit_usd_balance`,
  ADD COLUMN `credit_khr_balance` INT(10) DEFAULT 0  NULL AFTER `credit_khr`;

UPDATE member_credit_category SET credit_usd=credit,credit_usd_balance=credit_balance;

ALTER TABLE `biz_one_time_credit_loan`   
  ADD COLUMN `currency` VARCHAR(50) NULL AFTER `member_credit_category_id`,
  ADD COLUMN `credit_amount` INT(10) NULL AFTER `currency`;
  
  
UPDATE biz_one_time_credit_loan,member_credit_category SET biz_one_time_credit_loan.credit_amount=member_credit_category.credit,biz_one_time_credit_loan.currency='USD' 
WHERE biz_one_time_credit_loan.`member_id`=member_credit_category.`member_id` AND biz_one_time_credit_loan.`member_credit_category_id`=member_credit_category.`uid`;


  INSERT INTO member_credit_grant_product(grant_id,member_credit_category_id,credit,credit_usd,credit_khr,exchange_rate)
SELECT  grant_id,member_credit_category_id,SUM(credit) credit,SUM(credit) credit_usd,0,4000 FROM member_credit_grant_assets
WHERE CONCAT(grant_id,'@',member_credit_category_id) NOT IN (SELECT CONCAT(grant_id,'@',member_credit_category_id) FROM member_credit_grant_product) AND member_credit_category_id>0
GROUP BY grant_id,member_credit_category_id;

UPDATE member_credit_grant_product a,
(SELECT uid,default_credit_category_id, default_credit FROM member_credit_grant) b
SET a.credit=a.credit+b.default_credit,a.credit_usd=a.credit_usd+b.default_credit
WHERE a.grant_id=b.uid AND a.member_credit_category_id=b.default_credit_category_id AND b.default_credit_category_id>0;

INSERT INTO member_credit_grant_product(grant_id,member_credit_category_id,credit,credit_usd,credit_khr,exchange_rate)
SELECT  uid,default_credit_category_id,default_credit credit,default_credit credit_usd,0,4000 FROM member_credit_grant
WHERE CONCAT(uid,'@',default_credit_category_id) NOT IN (SELECT CONCAT(grant_id,'@',member_credit_category_id) FROM member_credit_grant_product) AND default_credit_category_id>0;

CREATE TABLE `task_co_bm` (
  `uid` INT(11) NOT NULL AUTO_INCREMENT,
  `member_id` INT(11) NOT NULL,
  `co_id` INT(11) DEFAULT NULL,
  `co_name` VARCHAR(50) DEFAULT NULL,
  `submit_time` DATETIME DEFAULT NULL,
  `submit_comment` TEXT,
  `handle_time` DATETIME DEFAULT NULL,
  `handle_comment` VARCHAR(50) DEFAULT NULL,
  `handler_id` INT(11) DEFAULT NULL,
  `handler_name` VARCHAR(50) DEFAULT NULL,
  `update_time` DATETIME DEFAULT NULL,
  `state` INT(2) DEFAULT '0',
  PRIMARY KEY (`uid`)
);
