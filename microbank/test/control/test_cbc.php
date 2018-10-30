<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 9/17/2018
 * Time: 9:39 AM
 */
class test_cbcControl{
    function testConnectOp(){
        $cbc=new cbcConnection();
        // 构造xml数据
        $xmlData = "
<xml>
<AppId>wxf8b4f85f3a794e77</AppId>
<ErrorType>1001</ErrorType>
<Description>错误描述</Description>
<AlarmContent>transaction_id=33534453534</AlarmContent>
<TimeStamp>1393860740</TimeStamp>
<AppSignature>f8164781a303f4d5a944a2dfc68411a8c7e4fbea</AppSignature>
<SignMethod>sha1</SignMethod>
</xml>";

        $ret=$cbc->exec($xml);
        var_dump($ret);


    }
}
class cbcConnection{
    public $url="https://uat.creditbureaucambodia.com.kh/enquiry/inthttp.pgm";//这只是test的，后面要根据config读取
    //public $url="https://uat.creditbureaucambodia.com.kh/nimr/enquiry/inthttp.pgm";
    public $curl;
    public function __construct(){
        $this->initConnection();
    }
    protected function initConnection(){
        $this->curl=curl_init();
        curl_setopt($this->curl,CURLOPT_URL,$this->url);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, Array("Content-Type:text/xml; charset=utf-8"));
        curl_setopt($this->curl, CURLOPT_PORT, 8080);//ip:port
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl,CURLOPT_PROXYTYPE,CURLPROXY_SOCKS5);
        curl_setopt($this->curl,CURLOPT_PROXYUSERPWD,"SRTUATXML:srtuat789");
        curl_setopt($this->curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);//必须要加这行
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 3); // PHP脚本在成功连接服务器前等待多久，单位秒
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在

    }
    public function exec($xmlData){
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $xmlData);//post提交的数据包
        $result = curl_exec($this->curl);   // 抓取URL并把它传递给浏览器
        // 是否报错
        if(curl_errno($this->curl))
        {
            $err=curl_error($this->curl);
        }
        curl_close($this->curl);    // //关闭cURL资源，并且释放系统资源
        if($err){
            return new result(false,"",$err);
        }else{
            return new result(true,"",$result);
        }
    }
}