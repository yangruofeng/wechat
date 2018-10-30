<?php
/**
 * 消息类型的任务，只需要判断消息有没有读取
 * Created by PhpStorm.
 * User: PC
 * Date: 7/12/2018
 * Time: 2:40 PM
 */
class task_user_msgModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('task_user_msg');
    }
}

