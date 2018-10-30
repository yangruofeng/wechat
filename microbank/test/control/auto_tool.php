<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/7/25
 * Time: 16:57
 */
class auto_toolControl
{

    public function getAllTradingCodeOp()
    {
        $file_path = _APP_CLASS_.'/passbook/trading';
        $file_array = array();
        $trade_code = array();
        if( is_dir($file_path)){
            $ls = scandir($file_path);
            foreach( $ls as $path ){
                if( substr($path,0,1) === '.'){
                    continue;
                }

                $file_array[] = $path;

                // 得到code
                $prefix = substr($path,0,strpos($path,'.trading.class.php'));
                $prefix = trim($prefix,'.');
                $code = strtolower(str_replace('.','_',$prefix));
                $trade_code[$code] = ucwords(str_replace('_',' ',$code));
            }
        }

       // var_export($trade_code);
       // var_export($file_array);

        $config_enum = global_settingClass::getAllTradingType();
        //print_r($config_enum);
        $new = array_merge($trade_code,$config_enum);
        var_export($new);
    }
}