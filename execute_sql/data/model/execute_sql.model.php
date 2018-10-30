<?php
/**
 * 商品供应商模型
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2013 KHBuy Inc. (http://www.KHBuy.com)
 * @license    http://www.KHBuy.com
 * @link       http://www.KHBuy.com
 * @since      File available since Release v1.1
 */


class execute_sqlModel extends tableModelBase {
    public function  __construct($_dsn=null){
        $tmp_tn="execute_sql";
        parent::__construct($tmp_tn, $_dsn);
    }
}
