<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/12
 * Time: 14:09
 */

/**
 * 全局对象基类
 * Class objectBaseClass
 */
abstract class objectBaseClass
{
    public $object_id = null;
    public $object_type = null;
    public $object_info = null;

    abstract function checkValid();

    public function getCredit()
    {
        return 0;
    }
}