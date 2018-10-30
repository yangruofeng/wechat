<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/9/17
 * Time: 13:40
 */
class testApiApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Api test";
        $this->description = "API测试";
        $this->url = C("bank_api_url") . "/test.api.php";


        $this->parameters = array();

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                '@description' => '原样返回的请求数据'
            )
        );

    }
}