<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/6
 * Time: 10:28
 */
class testNetworkApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Network test";
        $this->description = "网络超时测试";
        $this->url = C("bank_api_url") . "/test.network.php";


        $this->parameters = array();

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
            )
        );

    }
}