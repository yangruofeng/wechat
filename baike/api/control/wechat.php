<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/10/31
 * Time: 11:50
 */
class wechatControl
{
    public function callbackOp()
    {
        unset($_GET['act']);
        unset($_GET['op']);

        $options = array(
            'appid' => 'wxf8d914e25d849ea0',
            'appsecret' => '688a3ebe9048b3dd193ba8a2dd8fd695',
            'debug' => true,
            'token'=>'yangruofeng', //填写你设定的key
            'encodingaeskey'=>'G48fN20GaTDTLGjIo8tMN7ns49jdwzzZrZZb1a7NtON' //填写加密用的EncodingAESKey，如接口为明文模式可忽略
        );
        $weObj = new wechatClass($options);
        $weObj->valid(); //明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $type = $weObj->getRev()->getRevType();
        switch($type) {
            case wechatClass::MSGTYPE_TEXT:
                $weObj->text("hello, I'm wechat")->reply();
                exit;
                break;
            case wechatClass::MSGTYPE_EVENT:
                break;
            case wechatClass::MSGTYPE_IMAGE:
                break;
            default:
                $weObj->text("help info")->reply();
        }
    }
}