<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/7
 * Time: 18:25
 */

class loanDisbursementWorkerClass
{

    public static function schemaDisburse($schema_id)
    {
        return (new schemaDisbursementToBalanceClass($schema_id))->execute();
    }

}