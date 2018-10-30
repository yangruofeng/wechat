<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class client_memberModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('client_member');
    }

    public function getMemberList($pageNumber, $pageSize, $filters)
    {

    }

}
