<?php
if (!defined("SITE_CODE")) exit("Invalid Access");
function GUID()
{
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4) . $hyphen
        . substr($charid, 20, 12);
    return $uuid;
}

//---------------------------------------------------------Json{
if (!function_exists("my_json_encode")) {
    function my_json_encode($o, $wellformat = false)
    {
        $s = json_encode($o);//will have {"a":"b"} instead of {a:"b"}, but encode speed might slightly inproved

        if ($wellformat) {
            $s = preg_replace('/","/', "\",\n\"", $s);//dirty work for tmp...
        }
        return $s;
    }

    function my_json_decode($s)
    {
       // $s=str_replace("'",'"',$s);
        $o = json_decode($s, true);//true->array, false->obj,  the json_decode not support {a:"b"} but only support {"a":"b"}. it sucks
        return $o;
    }
}
//---------------------------------------------------------}Json

/**
 * 重新排序参数数组
 *
 * @param array $array
 * @return array
 */
function array_ksort($array)
{
    ksort($array);
    reset($array);
    return $array;
}

function unicode2any($str, $target_encoding = "UTF-8")
{
    $str = rawurldecode($str);
    //print $str."\n\n";
    preg_match_all("/(?:%u.{4})|.{4};|&#\d+;|.+/U", $str, $r);
    $ar = $r[0];
    foreach ($ar as $k => $v) {
        if (substr($v, 0, 2) == "&#") {
            $ar[$k] = iconv("UCS-2", $target_encoding, pack("n", substr($v, 2, -1)));
        } elseif (substr($v, 0, 2) == "%u") {
            $ar[$k] = iconv("UCS-2", $target_encoding, pack("H4", substr($v, -4)));
        } elseif (substr($v, 0, 3) == "") {
            $ar[$k] = iconv("UCS-2", $target_encoding, pack("H4", substr($v, 3, -1)));
        }
    }
    return join("", $ar);
}

function _gzip_output($buffer)
{
    $len = strlen($buffer);
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        $gzbuffer = gzencode($buffer);
        $gzlen = strlen($gzbuffer);
        if ($len > $gzlen) {
            header("Content-Length: $gzlen");
            header("Content-Encoding: gzip");
            print $gzbuffer;
            return;
        }
    }
    header("Content-Length: $len");
    print $buffer;
    return;
}

//---------------------------------------------------------  sql safe
function qstr2($s)
{
    //replace ' to ''
    return str_replace("'", "''", $s);
}

function qstr($s)
{
    $x = "'" . str_replace("'", "''", $s) . "'";
    $x = addcslashes($x, "\\");
    return $x;
}


function qstr_arr($_a)
{
    $_a = explode(",", $_a);
    foreach ($_a as $k => $v) {
        $_a[$k] = qstr($v);
    }
    return join(',', $_a);
}

function safeParams($s)
{
    //replace ' to ''
    $x = str_replace("'", "''", $s);
    return $x;
}

function unicodeToUTF8($str, $code = 'UTF-8')
{
    $str = str_replace('\u', '%u', $str);
    $str = rawurldecode($str);
    preg_match_all("/(?:%u.{4})|.{4};|&amp;#\d+;|.+/U", $str, $r);
    $ar = $r[0];
    foreach ($ar as $k => $v) {
        if (substr($v, 0, 2) == "%u") {
            $ar[$k] = iconv("UCS-2", $code, pack("H4", substr($v, -4)));
        } elseif (substr($v, 0, 3) == "") {
            $ar[$k] = iconv("UCS-2", $code, pack("H4", substr($v, 3, -1)));
        } elseif (substr($v, 0, 2) == "&amp;#") {
            echo substr($v, 2, -1) . " ";
            $ar[$k] = iconv("UCS-2", $code, pack("n", substr($v, 2, -1)));
        }
    }
    return join("", $ar);
}

//============================================================================================ logger / debug
/*
 * 增加新的简单的log的方式
 * */
function debug()
{
    $debug = getConf("debug");
    //if(!$debug) return;
    $arr = func_get_args();
    if (!count($arr)) return;
    if (count($arr) == 1) $log_content = $arr[0];
    if (count($arr) == 2) {
        $log_type = $arr[0];
        $log_content = $arr[1];
    }
    if (count($arr) == 3) {
        $log_type = $arr[0];
        $log_content = $arr[1];
        $prefix = $arr[2];
    }

    $trace = debug_backtrace(false);
    $class = $trace[1]['class'];
    $function = $trace[1]['function'];

    if (!$log_type) {
        $log_type = $class . "-" . $function;
    }
    if ($prefix != "")
        $prefix .= ":";
    if (is_array($log_content) || is_object($log_content))
        $log_content = $prefix . json_encode($log_content);
    else {
        $log_content = $prefix . $log_content;
    }
    return logger::record($log_type, $log_content);
}

function println($s, $wellformat = false)
{
    if (is_array($s)) {
        $s = my_json_encode($s, $wellformat);
    }
    print $s . "\n";//.PHP_EOL;
}

function debug_stack($s = "")
{
    $rt = "";
    $trace = debug_backtrace();
    return $trace;
    foreach ($trace as $t) {
        $rt .= "\t" . '@ ';
        if (isset($t['file'])) $rt .= basename($t['file']) . ':' . $t['line'];
        else {
            // if file was not set, I assumed the functioncall
            // was from PHP compiled source (ie XML-callbacks).
            $rt .= '<PHP inner-code>';
        }

        $rt .= ' -- ';

        if (isset($t['class'])) $rt .= $t['class'] . $t['type'];

        $rt .= $t['function'];

        if (isset($t['args']) && sizeof($t['args']) > 0) $rt .= '(...)';
        else $rt .= '()';

        //$rt.= PHP_EOL;
        $rt .= '\n';
    }
    return $rt;
}


function adjust_timezone()
{
    $SERVER_TIMEZONE = getConf('SERVER_TIMEZONE');
    if ($SERVER_TIMEZONE == '') {
        throw new Exception("FTL00002_SERVER_TIMEZONE_must_be_config");
    } else {
        $ini_get_date_timezone = ini_get("date.timezone");
        if ($SERVER_TIMEZONE != ini_get("date.timezone")) {
            ini_set("date.timezone", $SERVER_TIMEZONE);
        }
    }
}


function _getbarcode($defaultLen = 23, $seed = '0123456789ABCDEFH')
{
    list($usec, $sec) = explode(" ", microtime());
    srand($sec + $usec * 100000);
    $len = strlen($seed) - 1;
    $code = '';
    for ($i = 0; $i < $defaultLen; $i++) {
        $code .= substr($seed, rand(0, $len), 1);
    }
    return $code;
}

function getSessionVar($key)
{
    return $_SESSION[$key];
}

function setSessionVar($key, $var)
{
    session_start();
    $_SESSION[$key] = $var;
    session_write_close();
    return true;
}

function arr2var_all($name_of_arr)
{
    return <<<EOS
eval(arr2var("name_of_arr",array_keys(\$$name_of_arr)));
EOS
        ;
}

//对象转换成数组,by tim
function obj2array($obj, $is_deep = true)
{
    $vars = is_object($obj) ? get_object_vars($obj) : $obj;
    foreach ($vars as $k => $v) {
        if (is_numeric(stripos($k, 'parent_obj'))) continue;
        $new_v = $is_deep && (is_array($v) || is_object($v)) ? obj2array($v) : $v;
        $arr[$k] = $new_v;
    }
    return $arr;
}

function array2obj($arr, $obj, $create = false)
{
    $vars = get_class_vars(get_class($obj));
    foreach ($vars as $pn => $pv) {
        if (is_array($pv)) continue;
        $obj->$pn = $arr[$pn];
    }
    if ($create) {
        foreach ($arr as $k => $v) {
            if (!is_numeric($k)) {
                $obj->$k = $v;
            }
        }
    }
}

function arr2var($name_arr, $arr)
{
    if (!is_array($arr)) {
        throw new Exception(getLang("KO-arr2var-notarray") . my_json_encode($arr));
    }
    $rt = "";
    foreach ($arr as $key) {
        $rt .= '$' . $key . '=$' . $name_arr . '["' . $key . '"];';
    }
    return $rt;
}

function startWith($haystack, $needle)
{
    if (strpos($haystack, $needle) !== 0) return false;
    return true;
}

function endWith($haystack, $needle)
{
    $Mcnt = strlen($haystack);
    $Ncnt = strlen($needle);
    if ($Mcnt < $Ncnt) return false;
    $i = strpos($haystack, $needle);
    if (!is_numeric($i)) return false;
    if ($i === ($Mcnt - $Ncnt)) return true;
    return false;
}


/****  未捕获的异常处理  ****/
function global_exception_handler($e)
{
    if ($e instanceof Throwable) {
        $rt = global_error_handler($e->getFile(), $e->getLine(), $e->getMessage(), $e->getTrace(), $e->getCode());
        $rt = new result(false, $e->getMessage(), $rt, $e->getCode());
    } else if ($e instanceof Exception) {
        $rt = global_error_handler($e->getFile(), $e->getLine(), $e->getMessage(), $e->getTrace(), $e->getCode());
        $rt = new result(false, $e->getMessage(), $rt, $e->getCode());
    } else {
        $rt = new result(false, 'Unknown error', 0);
    }
    debug("IT-CHECK", my_json_encode($rt));
    echo json_encode($rt);
    die;
}

set_exception_handler('global_exception_handler');

/*******************************************************************************/
function global_error_handler($file, $line, $message, $trace, $errno)
{
    //$rt = new result(false,$message,array("errmsg" => $message, "errno" => $errno, "trace" => $trace, "line" => $line, "file" => $file),$errno);
    //echo json_encode($rt);die;
    return array("errmsg" => $message, "errno" => $errno, "trace" => $trace, "line" => $line, "file" => $file);
}

function global_error_handler2(Exception $ex)
{
    return global_error_handler($ex->getFile(), $ex->getLine(), $ex->getMessage(), "", $ex->getCode()//,$ex->getTraceAsString()
    );
}

function _shutdown_function($_json = true)
{
    $error = error_get_last();

    //debug("IT-CHECK",my_json_encode($error).PHP_EOL.my_json_decode(debug_stack()));
    if ($error !== NULL) {
        if (8 != $error['type'] //ignore notice
            && 2 != $error['type'] //ignore warning
            && 128 != $error['type'] //ignore deprecated warning
            && 8192 != $error['type'] //ignore deprecated warning
        ) {
            $error['errmsg'] = $error['message'];
            ob_get_clean();//not functioning unless error_reporting(0);
            $output = global_error_handler(basename($error['file'], ".php"), $error['line'], $error['message'], null, $error['type']);
            debug("IT-CHECK", $output);
            if ($_json) {
                print my_json_encode($output);
                ob_end_flush();
            } else {
                print_r($output);
                ob_end_flush();
            }
            //debug("IT-CHECK",my_json_encode($error).PHP_EOL.debug_stack());
            return $output;
        } else {
            if (2 == $error['type']) {
            }
            //debug("IT-CHECK","[".$error['type']."]".my_json_encode($error).PHP_EOL.debug_stack());
        }

    } else {
        //debug("IT-CHECK","[Not Error Shutdown?]".PHP_EOL.debug_stack());
    }
    #ini_set("display_error", "Off");
}

function _shutdown_function_nojson()
{
    _shutdown_function(false);
}

$_register_shutdown_function = "_shutdown_function";//JSON output mode...
#$_register_shutdown_function="_shutdown_function_nojson";
if (!function_exists($_register_shutdown_function)) {
    throw new Exception("$_register_shutdown_function not exists");
}
if ($_register_shutdown_function) register_shutdown_function($_register_shutdown_function);
function _get_ip_()
{
    static $_ip = "";
    do {
        if ($_ip != "") return $_ip;

        $LOCAL127 = "127.0.0.1";

        $HTTP_X_REAL_IP = _get_env("HTTP_X_REAL_IP");
        if ($HTTP_X_REAL_IP && $HTTP_X_REAL_IP != $LOCAL127) {
            $_ip = $HTTP_X_REAL_IP;
            break;
        }

        $HTTP_CLIENT_IP = _get_env("HTTP_CLIENT_IP");
        if ($HTTP_CLIENT_IP && $HTTP_CLIENT_IP != $LOCAL127) {
            $_ip = $HTTP_CLIENT_IP;
            break;
        }

        $HTTP_X_FORWARDED_FOR = _get_env("HTTP_X_FORWARDED_FOR");
        if ($HTTP_X_FORWARDED_FOR)
            list($HTTP_X_FORWARDED_FOR) = explode(",", $HTTP_X_FORWARDED_FOR);
        if ($HTTP_X_FORWARDED_FOR && $HTTP_X_FORWARDED_FOR != $LOCAL127) {
            $_ip = $HTTP_X_FORWARDED_FOR;
            break;
        }

        $REMOTE_ADDR = _get_env("REMOTE_ADDR");
        //		if($REMOTE_ADDR && $REMOTE_ADDR!=$LOCAL127){
        //			$_ip=$REMOTE_ADDR;break;
        //		}
        $_ip = $REMOTE_ADDR;
    } while (false);
    return ($_ip);
}

/*function check_ip(){
	if(@$_SESSION['_ip']!=_get_ip_()){
		throw new Exception("IP Changed, Please login again.",4444);
	}
}*/

class classLoader
{
    static $_class_map = null;

    static function decamelize($text)
    {
        return preg_replace('/([a-z\d])([A-Z])/', '$1.$2', $text);
    }

    static function load_file($className)
    {
        static $cache = array();
        $key = "class_map_" . $className;

        if (!isset($cache[$key])) {
            @include_once(BASE_CORE_PATH . DS ."cache.php");
            $cache_type = C("cache.type") ?: 'file';
            $obj_cache = cache::getInstance($cache_type);
            $filename = $obj_cache->get($key);
            if (!$filename || $filename=="notfound") {
                $obj_cache->rm($key);
                $filename = self::find_class($className);
                if (!($filename && file_exists($filename))) {
                    $filename = 'notfound';
                }
                $obj_cache->set($key, $filename, null, null);
                $cache[$key] = $filename;
            } else {
                $cache[$key] = $filename;
            }
        } else {
            $filename = $cache[$key];
        }

        if ($filename && $filename != "notfound" && file_exists($filename)) {
            include_once($filename);
        } else {
            debug('CLASS-LOADER', "$className Not Found");
        }
    }

    static function find_class($className) {
        $class = strtolower($className);
        if (strlen($class) > 7 && ucwords(substr($class, -7)) == 'Control') {
            if ($filename=self::find_file($className, CURRENT_ROOT . DS . "control", substr($class, 0, -7)))
                return $filename;
            if ($filename=self::find_file($className, CURRENT_ROOT . DS . "control", strtolower(self::decamelize(substr($className, 0, -7)))))
                return $filename;
            $class_path_a = array(CURRENT_ROOT . DS . "control");
        } elseif (strlen($class) > 5 && ucwords(substr($class, -5)) == 'Model') {
            if ($filename=self::find_file($className, _DATA_MODEL_, substr($class, 0, -5) . ".model"))
                return $filename;
            $class_path_a = array(_DATA_MODEL_);
            if ($filename=self::find_file($className, BASE_MODEL_PATH, substr($class, 0, -5) . ".model"))
                return $filename;
            $class_path_a[] = BASE_MODEL_PATH;
        } elseif (strlen($class) > 5 && ucwords(substr($class, -5)) == 'Class') {
            if ($filename=self::find_file($className, PROJECT_ROOT . DS . "class", substr($class, 0, -5) . ".class"))
                return $filename;
            $class_path_a = array(PROJECT_ROOT . DS . "class");
        } elseif (strlen($class) > 3 && ucwords(substr($class, -3)) == 'Api') {
            if ($filename=self::find_file($className, BASE_CORE_PATH . DS . "api", substr($class, 0, -3) . ".api"))
                return $filename;
            if ($filename=self::find_file($className, BASE_DATA_PATH . DS . "api", substr($class, 0, -3) . ".api"))
                return $filename;
            $class_path_a = array(
                BASE_CORE_PATH . DS . "api",
                BASE_DATA_PATH . DS . "api"
            );

            if (defined("BASE_COMMON_PATH")) {
                if ($filename=self::find_file($className, BASE_COMMON_PATH . DS . "api", substr($class, 0, -3) . ".api"))
                    return $filename;
                $class_path_a[] = BASE_COMMON_PATH . DS . "api";
            }
        } else {
            $class_path_a = getConf("class_path_a");
            if (!$class_path_a) $class_path_a = array();
            //$class_path_a[]=_ROOT_;
            $class_path_a[] = BASE_CORE_PATH;
            $class_path_a[] = PROJECT_ROOT . DS . "class";
            $class_path_a[] = CURRENT_ROOT . DS . "control";
            $class_path_a[] = _DATA_MODEL_;
        }

        foreach ($class_path_a as $path) {
            if ($filename=self::find_file($className, $path)) {
                return $filename;
            }
            if ($filename=self::find_file(strtolower($className), $path)) {
                return $filename;
            }
            if ($filename=self::find_file(strtolower(self::decamelize($className)), $path)) {
                return $filename;
            }
            if ($filename=self::find_file(self::decamelize($className), $path)) {
                return $filename;
            }
        }

        return null;
    }

    static function find_file($className, $path, $filename = null)
    {
        if (!$filename) $filename = $className;
        //if (!file_exists($path. "/$filename.php")) $filename=self::decamelize($filename);
        if (file_exists($path . "/$filename.php")) {
            $_fname = $path . "/$filename.php";
            // $_fname = substr($_fname, strlen(GLOBAL_ROOT) + 1);
            return $_fname;
        } else {
            foreach (scandir($path) as $d) {
                //if ($d == ".." || $d == ".")
                //跳过.svn等目录
                if (substr($d, 0, 1) === ".") continue;
                if (is_dir("$path/$d")) {
                    if ($file_found = self::find_file($className, "$path/$d", $filename)) {
                        return $file_found;
                    }
                }
            }
            return false;
        }
    }
}

function __autoload($className)
{
    classLoader::load_file($className);
}


/**
 * 去除代码中的空白和注释
 *
 * @param string $content 待压缩的内容
 * @return string
 */
function compress_code($content)
{
    $stripStr = '';
    //分析php源码
    $tokens = token_get_all($content);
    $last_space = false;
    for ($i = 0, $j = count($tokens); $i < $j; $i++) {
        if (is_string($tokens[$i])) {
            $last_space = false;
            $stripStr .= $tokens[$i];
        } else {
            switch ($tokens[$i][0]) {
                case T_COMMENT:    //过滤各种PHP注释
                case T_DOC_COMMENT:
                    break;
                case T_WHITESPACE:    //过滤空格
                    if (!$last_space) {
                        $stripStr .= ' ';
                        $last_space = true;
                    }
                    break;
                default:
                    $last_space = false;
                    $stripStr .= $tokens[$i][1];
            }
        }
    }
    return $stripStr;
}

/**
 * 取得对象实例
 *
 * @param object $class
 * @param string $method
 * @param array $args
 * @return object
 */
function get_obj_instance($class, $method = '', $args = array())
{
    static $_cache = array();
    $key = $class . $method . (empty($args) ? null : md5(serialize($args)));
    if (isset($_cache[$key])) {
        return $_cache[$key];
    } else {
        if (class_exists($class)) {
            $obj = new $class;
            if (method_exists($obj, $method)) {
                if (empty($args)) {
                    $_cache[$key] = $obj->$method();
                } else {
                    $_cache[$key] = call_user_func_array(array(&$obj, $method), $args);
                }
            } else {
                $_cache[$key] = $obj;
            }
            return $_cache[$key];
        } else {
            show_exception('Class ' . $class . ' isn\'t exists!');
        }
    }
}


/**
 * 文件数据读取和保存 字符串、数组
 *
 * @param string $name 文件名称（不含扩展名）
 * @param mixed $value 待写入文件的内容
 * @param string $path 写入cache的目录
 * @param string $ext 文件扩展名
 * @return mixed
 */
function F($name, $value = null, $path = 'cache', $ext = '.php')
{
    if (strtolower(substr($path, 0, 5)) == 'cache') {
        $path = 'data/' . $path;
    }
    static $_cache = array();
    if (isset($_cache[$name . $path])) return $_cache[$name . $path];
    $filename = _DATA_PATH_ . '/' . $path . '/' . $name . $ext;
    if (!is_null($value)) {
        $dir = dirname($filename);
        if (!is_dir($dir)) mkdir($dir);
        return write_file($filename, $value);
    }

    if (is_file($filename)) {
        $_cache[$name . $path] = $value = include $filename;
    } else {
        $value = false;
    }
    return $value;
}

/**
 * 内容写入文件
 *
 * @param string $filepath 待写入内容的文件路径
 * @param string /array $data 待写入的内容
 * @param  string $mode 写入模式，如果是追加，可传入“append”
 * @return bool
 */
function write_file($filepath, $data, $mode = null)
{
    if (is_array($data)) {

    } elseif (!is_scalar($data)) {
        return false;
    }
    $data = var_export($data, true);
    if ($data === '') $data = '\'\'';
    $data = "<?php defined('PLUTOFLAG') or exit('Access Invalid!'); return " . $data . "\n?>";
    $mode = $mode == 'append' ? FILE_APPEND : null;
    if (false === file_put_contents($filepath, compress_code($data), $mode)) {
        return false;
    } else {
        return true;
    }
}

function getReferer()
{
    return empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
}

function request_uri()
{
    return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
    /*
    if (isset($_SERVER['REQUEST_URI']))
    {
        $uri = $_SERVER['REQUEST_URI'];
    }
    else
    {
        if (isset($_SERVER['argv']))
        {
            $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
        }
        else
        {
            $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
        }
    }
    return $uri;
    */
}

function template($tplpath)
{
    if (strpos($tplpath, ':') !== false) {
        $tpltmp = explode(':', $tplpath);
        $pth = BASE_CORE_PATH . '/templates/' . $tpltmp[1] . '.php';
        return $pth;
    } else {
        if (!defined('_TPL_FOLDER_')) define('_TPL_FOLDER_', "templates");
        if (!defined('TPL_NAME')) define('TPL_NAME', 'default');
        return CURRENT_ROOT . DS . _TPL_FOLDER_ . DS . TPL_NAME . DS . $tplpath . '.php';
    }
}

/**
 * 输出信息
 *
 * @param string $msg 输出信息
 * @param string /array $url 跳转地址 当$url为数组时，结构为 array('msg'=>'跳转连接文字','url'=>'跳转连接');
 * @param string $show_type 输出格式 默认为html
 * @param int $msg_type 0表示information,100表示成功，10表示警告，20表示错误
 * @param string $is_show 是否显示跳转链接，默认是为1，显示
 * @param int $time 跳转时间，默认为2秒
 * @return string 字符串类型的返回结果
 */
function showMessage($msg, $url = '',$msg_type=100,$time = 2000)
{

    /**
     * 如果默认为空，则跳转至上一步链接
     */
    logger::record("message", $_SERVER['REQUEST_URI'] . "\n" . $msg . "\nredirect to:" . $url);
    $url = ($url != '' ? $url : getReferer());
    Tpl::output('msg', $msg);
    Tpl::output('url', $url);
    Tpl::output("msg_type",$msg_type);
    Tpl::showpage('msg', 'msg_layout', "default", $time);
    exit;
}

function show_exception($msg)
{
    showMessage($msg,'',20);
}

function showInformation($msg, $title = "Information")
{
    Tpl::output("msg", $msg);
    Tpl::output("title", $title);
    Tpl::showPage("msg", "information_layout");
}

function C($key, $value = null)
{
    if ($value) {
        $setting_config[$key] = $value;
        return $value;
    }
    $conf = $GLOBALS['setting_config'];
    if ($key) {
        if (array_key_exists($key, $conf)) {
            return $conf[$key];
        } else {
            throw new Exception("ConfigError:$key not found");
        }
    }
    throw new Exception("ConfigError:$key not found");
}

function getConf($key)
{
    return $GLOBALS['config'][$key];
}

function getLangTypeList()
{
    $a = $GLOBALS['config']["lang_type_list"];
    return array_keys($a);
}

/**
 * 读/写 缓存方法
 *
 * H('key') 取得缓存
 * H('setting',true) 生成缓存并返回缓存结果
 * H('key',null) 清空缓存
 * H('setting',true,'file') 生成商城配置信息的文件缓存
 * H('setting',true,'memcache') 生成商城配置信息到memcache
 * @param string $key 缓存名称
 * @param string $value 缓存内容
 * @param string $type 缓存类型，允许值为 file,memcache,xcache,apc,eaccelerator，可以为空，默认为file缓存
 * @param int /null $expire 缓存周期
 * @param mixed $args 扩展参数
 * @return mixed
 */
function H($key, $value = '', $cache_type = '', $expire = null, $args = null)
{
    static $cache = array();
//    if (!in_array($key, $GLOBALS['config']['content_cache_key']) && getConf('debug')) {
//        $key = $key . '_' . Language::currentCode();
//        $list = Model('cache')->call($key);
//        return $list; //绕过缓存，可能在开发阶段不使用缓存
//    }
    $key = $key . '_' . Language::currentCode();
    $cache_type = $cache_type ? $cache_type : 'file';
    $obj_cache = cache::getInstance($cache_type, $args);
    if (is_null($value)) {//删除缓存
        $value = $obj_cache->rm($key);
        return $value;
    }
    if (!$value) {
        //有就不管,没有就重新生成缓存
        if (!isset($cache[$cache_type . '_' . $key])) {
            $value = $obj_cache->get($key);
            if (!$value) {
                $obj_cache->rm($key);
                $list = Model('cache')->call($key);
                $obj_cache->set($key, $list, null, $expire);
                $cache[$cache_type . '_' . $key] = $list;
                return $list;
            } else {
                return $value;
            }
        } else {
            $value = $cache[$cache_type . '_' . $key];
            return $value;
        }
    } else {
        // 强制重新缓存数据
        if ($value === true) {
            $obj_cache->rm($key);
            $list = Model('cache')->call($key);
            $obj_cache->set($key, $list, null, $expire);
            $cache[$cache_type . '_' . $key] = $list;
            return $value === true ? $list : true;
        } else {//重置缓存内容
            $obj_cache->set($key, $value, null, $expire);
            $cache[$cache_type . '_' . $key] = $value;
            return $value;
        }
    }

}

/**
 * 模型实例化入口
 *
 * @param string $model_name 模型名称
 * @return obj 对象形式的返回结果
 */
function Model($model = null)
{
    static $_data_model_sub_dir;
    if (!is_array($_data_model_sub_dir)) {
        $_data_model_sub_dir = array();
        $lst_dir = glob(_DATA_MODEL_ . "/*");
        foreach ($lst_dir as $k) {
            if (is_dir($k)) {
                $_data_model_sub_dir[] = $k;
            }
        }

        $_data_model_sub_dir[] = BASE_MODEL_PATH; // 加公用model目录

    }
    static $_cache_model = array();
    if (!is_null($model) && isset($_cache_model[$model])) return $_cache_model[$model];
    $file_name = _DATA_MODEL_ . '/' . $model . '.model.php';
    $class_name = $model . 'Model';
    $tmp_file_name = null;
    if (!file_exists($file_name)) {
        //遍历目录
        foreach ($_data_model_sub_dir as $tmp_dir) {
            $tmp_file_name = $tmp_dir . '/' . $model . '.model.php';
            if (file_exists($tmp_file_name)) {
                break;
            } else {
                $tmp_file_name = null;
            }
        }
        if (!$tmp_file_name) {
            return $_cache_model[$model] = new tableModelBase($model);  //new Model($model);
        } else {
            $file_name = $tmp_file_name;
        }
    }
    require_once($file_name);
    if (!class_exists($class_name)) {
        $error = 'Model Error:  Class ' . $class_name . ' is not exists!';
        throw new Exception($error);
    } else {
        return $_cache_model[$model] = new $class_name();
    }
}

/*
 * @return tableModelBase
 * */
function M($model = null)
{
    return Model($model);
}

/*
 * 视图入口
 * */
function View($model = null)
{
    static $_cache_view = array();
    if (!is_null($model) && isset($_cache_view[$model])) return $_cache_view[$model];
    $file_name = _DATA_MODEL_ . '/' . $model . '.view.php';
    $class_name = $model . 'View';
    if (!file_exists($file_name)) {
        return $_cache_view[$model] = new ormDataView($model);  //new Model($model);
    } else {
        require_once($file_name);
        if (!class_exists($class_name)) {
            $error = 'Model Error:  Class ' . $class_name . ' is not exists!';
            throw new Exception($error);
        } else {
            return $_cache_view[$model] = new $class_name();
        }
    }
}

function V($model = null)
{
    return View($model);
}

function makeURL($api, $method, $args)
{
    if (!$args || !$args['site']) {
        $site = _SITE_URL_ . "/?_c=" . $api . "&_m=" . $method;
    } else {
        $site = $args['site'];
    }
    return $site;
}

function goURL($api, $method, $args)
{
    $url = makeURL($api, $method, $args);
    //有可能会根据args打开另外一个窗口

    @header('Location: ' . $url);
    exit();
}

/**
 * 这个跳转方式支持页面的额外输出，可以在跳转新页面前做处理，不足的地方是需要增加对应的layout和page
 * @param $url
 */
function goURL2($url){
    Tpl::output("new_url",$url);
    Tpl::setDir("");
    Tpl::setLayout("refresh_layout");
    Tpl::showPage("refresh");
}
function L($key)
{
    return Language::get($key);
}

function redirect2($url, $msg = "", $target = "window")
{
    ?>
    <script type="text/javascript">
        <!--
        <?php if($msg!="") { ?>
        window.alert("<?php echo $msg?>");
        <?php } ?>
        __target = <?php echo $target?>;
        while (__target.parent != __target) {
            __target = __target.parent
        }
        __target.location = "<?php echo $url?>";
        -->
    </script>
    <?php
    exit();
}

function redirect($url)
{
    ?>
    <script type="text/javascript">
        window.location = "<?php echo $url?>";
    </script>
    <?php
    exit();
}

function trimEnd($haystack, $needle)
{
    return substr($haystack, -(strlen($needle))) == $needle ? substr($haystack, 0, strlen($haystack) - strlen($needle)) : $haystack;
}

/** 如果$haystack以$needle开头，则从开头处移除；否则保留不变。
 * @param $haystack 目标字符串
 * @param $needle 要移除的字符串
 * @return string true or false
 */
function trimStart($haystack, $needle)
{
    return substr($haystack, strlen($needle)) == $needle ? substr($haystack, strlen($needle)) : $haystack;
}

/*
 * 获取当前时间，以后可能需要和数据库同步
 * */
function Now()
{
    return date('Y-m-d H:i:s');
}

function get_current_url()
{
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    return $http_type . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function getBackOfficeUrl($cls, $method, $parameter = array(), $pseudo_static_style = false)
{
    return getUrl($cls, $method, $parameter, $pseudo_static_style,BACK_OFFICE_SITE_URL);
}
function getWapOperatorUrl($cls, $method, $parameter = array(), $pseudo_static_style = false)
{
    return getUrl($cls, $method, $parameter, $pseudo_static_style,WAP_OPERATOR_SITE_URL);
}
function getCounterUrl($cls, $method, $parameter = array(), $pseudo_static_style = false)
{
    return getUrl($cls, $method, $parameter, $pseudo_static_style,ENTRY_COUNTER_SITE_URL);
}
/**
 * 根据api/method配置url
 *
 */
function getUrl($cls, $method, $parameter = array(), $pseudo_static_style = false, $site_url = '')
{
    if (empty($cls) && empty($method) && empty($parameter)) {
        return $site_url;
    }

    $cls = !empty($cls) ? $cls : 'index';
    $method = !empty($method) ? $method : 'index';

    if ($pseudo_static_style && ENABLE_PSEUDO_STATIC_URL) {
        //伪静态模式
        $url_perfix = "{$cls}-{$method}";
        if (!empty($args)) {
            $url_perfix .= '-';
        }
        $url_string = $url_perfix . http_build_query($parameter, '', '-') . ".html";
        $url_string = str_replace('=', '-', $url_string);
    } else {
        //默认路由模式
        $url_perfix = "act={$cls}&op={$method}";
        if (!empty($parameter)) {
            $url_perfix .= '&';
        }
        $url_string = 'index.php?' . $url_perfix . http_build_query($parameter);
    }

    return rtrim($site_url, '/') . '/' . $url_string;
}

function getIp()
{
    if (@$_SERVER['HTTP_CLIENT_IP'] && $_SERVER['HTTP_CLIENT_IP'] != 'unknown') {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (@$_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['HTTP_X_FORWARDED_FOR'] != 'unknown') {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match('/^\d[\d.]+\d$/', $ip) ? $ip : '';
}

/**
 * 价格格式化
 *
 * @param int $price
 * @return string    $price_format
 */
function ncPriceFormat($price,$decimals=2)
{
    $price_format = number_format($price, $decimals, '.', ',');
    return $price_format;
}

/**
 * 金额格式化,带千分位
 * todo:处理货币符号问题
 * @param int $price
 * @return string    $price_format
 */
function ncAmountFormat($price, $ignore_zero = false, $currency = currency::USD)
{
    if ($ignore_zero && $price == 0) return "";
    $ccy = $_REQUEST['display_currency'] ?: $currency;
//    $rate1 = currency::getRateOf($ccy);
//    $price = $price * $rate1;
    switch ($ccy) {
        case currency::USD:
            $s = "$";
            break;
        case currency::KHR:
            $s = "R";
            break;
        case currency::CNY:
            $s = "￥";
            break;
        default:
            $s = $ccy;
    }

    $price_format = number_format($price, 2, '.', ',');
    return $s . $price_format;
}

/**
 * 数量格式化去零
 *
 */
function formatQuantity($number, $ignore_zero = false)
{
    if ($ignore_zero && $number <= 0) return "";
    $number = number_format(trim($number), 3, '.', ',');
    if (strpos($number, '.') !== false) {
        return rtrim(rtrim($number, '0'), '.');
    } else {
        return $number;
    }
}


function dateAdd($date, $diff)
{
    $i = intval($diff);
    $date = date("Y-m-d 00:00:00", strtotime($date));
    if ($i > 0) {
        $date_end = date("Y-m-d 23:59:59", strtotime($date) + 60 * 60 * 24 * ($i - 1));//modify by seven
    } else {
        $date_end = date("Y-m-d 00:00:00", strtotime($date) + 60 * 60 * 24 * $i);
    }

    return $date_end;
}

/*
 * 把二维数组组合成一维数组
 * */
function combineArray($arr, $separator)
{
    if (count($arr) <= 1) {
        return $arr;
    }
    $new_arr = array_shift($arr);
    foreach ($arr as $v) {
        $temp = $new_arr;
        $step = count($new_arr);
        for ($i = 1; $i < count($v); $i++) {
            $new_arr = array_merge($new_arr, $temp);
        }
        for ($i = 0; $i < count($new_arr); $i++) {
            $new_idx = $i / $step;
            $new_arr[$i] .= $separator . $v[$new_idx];
        }
    }
    return $new_arr;
}

/**
 * @param $maxpage  总页数
 * @param $page    当前页
 * @param string $para 翻页参数(不需要写$page),如http://www.example.com/article.php?page=3&id=1，$para参数就应该设为'&id=1'
 * @return string  返回的输出分页html内容
 */
function multipage($maxpage, $page, $para = '')
{
    $multipage = '';  //输出的分页内容
    $listnum = 5;     //同时显示的最多可点击页面

    if ($maxpage < 2) {
        return '';
    } else {
        $offset = 2;
        if ($maxpage <= $listnum) {
            $from = 1;
            $to = $maxpage;
        } else {
            $from = $page - $offset; //起始页
            $to = $from + $listnum - 1;  //终止页
            if ($from < 1) {
                $to = $page + 1 - $from;
                $from = 1;
                if ($to - $from < $listnum) {
                    $to = $listnum;
                }
            } elseif ($to > $maxpage) {
                $from = $maxpage - $listnum + 1;
                $to = $maxpage;
            }
        }

        $multipage .= ($page - $offset > 1 && $maxpage >= $page ? '<li><a data-page="1" href="?page=1' . $para . '" >1...</a></li>' : '') .
            ($page > 1 ? '<li><a data-page="' . ($page - 1) . '" href="?page=' . ($page - 1) . $para . '" >&laquo;</a></li>' : '');

        for ($i = $from; $i <= $to; $i++) {
            $multipage .= $i == $page ? '<li class="active"><a data-page="' . $i . '" href="?page=' . $i . $para . '" >' . $i . '</a></li>' : '<li><a data-page="' . $i . '" href="?page=' . $i . $para . '" >' . $i . '</a></li>';
        }

        $multipage .= ($page < $maxpage ? '<li><a data-page="' . ($page + 1) . '" href="?page=' . ($page + 1) . $para . '" >&raquo;</a></li>' : '') .
            ($to < $maxpage ? '<li><a data-page="' . $maxpage . '" href="?page=' . $maxpage . $para . '" class="last" >...' . $maxpage . '</a></li>' : '');
        /*		$multipage .=  ' <li><a href="#" ><input type="text" size="3"  onkeydown="if(event.keyCode==13) {self.window.location=\'?page=\'+this.value+\''.$para.'\'; return false;}" ></a></li>';*/


        $multipage = $multipage ? '<ul class="pagination">' . $multipage . '</ul>' : '';
    }

    return $multipage;
}

class result
{
    public $STS;
    public $MSG;
    public $DATA;
    public $CODE;

    function __construct($_sts, $msg = "", $data = null, $_code = 0, $inner_result = null)
    {
        $this->STS = $_sts;
        if (is_null($_code)) {
            $this->CODE = $this->STS ? 200 : 0;
        } else {
            $this->CODE = $_code;
        }
        $this->MSG = $msg;
        $this->DATA = $data;
    }

    public function toArray()
    {
        return array("STS" => $this->STS, "MSG" => $this->MSG, "DATA" => $this->DATA, "CODE" => $this->CODE);
    }
}

function checkSubmit()
{
    $submit = isset($_POST['form_submit']) ? $_POST['form_submit'] : $_GET['form_submit'];
    if ($submit != 'ok') return false;
    return true;
    //以后还可以加入其他逻辑
}

function isMobileClient()
{
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent)
        || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))
    ) {
        $is_mobile = true;
    } else {
        $is_mobile = false;
    }
    return $is_mobile;
}

function getUniqueNumber()
{
    $tmp_name = sprintf('%010d', time() - 946656000)
        . sprintf('%03d', microtime() * 1000)
        . sprintf('%04d', mt_rand(0, 9999));
    return $tmp_name;
}

function addUrlArg($url, $key, $value)
{
    $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
    $url = substr($url, 0, -1);
    if (strpos($url, '?') === false) {
        return ($url . '?' . $key . '=' . $value);
    } else {
        return ($url . '&' . $key . '=' . $value);
    }
}

function removeUrlArg($url, $key)
{
    $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
    $url = substr($url, 0, -1);
    if (strpos($url, '?') === false) {
        return ($url . '?');
    } else {
        return $url;
    }
}

/**
 * 设置cookie
 *
 * @param string $name cookie 的名称
 * @param string $value cookie 的值
 * @param int $expire cookie 有效周期
 * @param string $path cookie 的服务器路径 默认为 /
 * @param string $domain cookie 的域名
 * @param string $secure 是否通过安全的 HTTPS 连接来传输 cookie,默认为false
 */
function setNcCookie($name, $value, $expire = '36000', $path = '', $domain = '', $secure = false)
{
    if (empty($path)) $path = '/';
    if (empty($domain)) $domain = SUBDOMAIN_SUFFIX ? SUBDOMAIN_SUFFIX : '';

    // RFC 2109, cookie domains must contain at least one dot other than the first.
    // http://www.w3.org/Protocols/rfc2109/rfc2109
    // For hosts such as 'localhost', we don't set a cookie domain.
    if (count(explode('.', MyString::trimStart($domain, '.'))) < 2) $domain = '';

    $name = defined('COOKIE_PRE') ? COOKIE_PRE . $name : strtoupper(substr(md5(MD5_KEY), 0, 4)) . '_' . $name;
    $expire = intval($expire) ? intval($expire) : (intval(SESSION_EXPIRE) ? intval(SESSION_EXPIRE) : 3600);
    $result = setcookie($name, $value, time() + $expire, $path, $domain, $secure);
    $_COOKIE[$name] = $value;
}


// 取cookie值
function cookie($name = '')
{
    $name = defined('COOKIE_PRE') ? COOKIE_PRE . $name : strtoupper(substr(md5(MD5_KEY), 0, 4)) . '_' . $name;
    return $_COOKIE[$name];
}

/**
 * 加密函数
 *
 * @param string $txt 需要加密的字符串
 * @param string $key 密钥
 * @return string 返回加密结果
 */
function encrypt($txt, $key = '')
{
    if (empty($txt)) return $txt;
    if (empty($key)) $key = md5(MD5_KEY);
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $nh1 = rand(0, 64);
    $nh2 = rand(0, 64);
    $nh3 = rand(0, 64);
    $ch1 = $chars{$nh1};
    $ch2 = $chars{$nh2};
    $ch3 = $chars{$nh3};
    $nhnum = $nh1 + $nh2 + $nh3;
    $knum = 0;
    $i = 0;
    while (isset($key{$i})) $knum += ord($key{$i++});
    $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
    $txt = base64_encode(time() . '_' . $txt);
    $txt = str_replace(array('+', '/', '='), array('-', '_', '.'), $txt);
    $tmp = '';
    $j = 0;
    $k = 0;
    $tlen = strlen($txt);
    $klen = strlen($mdKey);
    for ($i = 0; $i < $tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = ($nhnum + strpos($chars, $txt{$i}) + ord($mdKey{$k++})) % 64;
        $tmp .= $chars{$j};
    }
    $tmplen = strlen($tmp);
    $tmp = substr_replace($tmp, $ch3, $nh2 % ++$tmplen, 0);
    $tmp = substr_replace($tmp, $ch2, $nh1 % ++$tmplen, 0);
    $tmp = substr_replace($tmp, $ch1, $knum % ++$tmplen, 0);
    return $tmp;
}

/**
 * 解密函数
 *
 * @param string $txt 需要解密的字符串
 * @param string $key 密匙
 * @return string 字符串类型的返回结果
 */
function decrypt($txt, $key = '', $ttl = 0)
{
    if (empty($txt)) return $txt;
    if (empty($key)) $key = md5(MD5_KEY);

    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $knum = 0;
    $i = 0;
    $tlen = @strlen($txt);
    while (isset($key{$i})) $knum += ord($key{$i++});
    $ch1 = @$txt{$knum % $tlen};
    $nh1 = strpos($chars, $ch1);
    $txt = @substr_replace($txt, '', $knum % $tlen--, 1);
    $ch2 = @$txt{$nh1 % $tlen};
    $nh2 = @strpos($chars, $ch2);
    $txt = @substr_replace($txt, '', $nh1 % $tlen--, 1);
    $ch3 = @$txt{$nh2 % $tlen};
    $nh3 = @strpos($chars, $ch3);
    $txt = @substr_replace($txt, '', $nh2 % $tlen--, 1);
    $nhnum = $nh1 + $nh2 + $nh3;
    $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
    $tmp = '';
    $j = 0;
    $k = 0;
    $tlen = @strlen($txt);
    $klen = @strlen($mdKey);
    for ($i = 0; $i < $tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = strpos($chars, $txt{$i}) - $nhnum - ord($mdKey{$k++});
        while ($j < 0) $j += 64;
        $tmp .= $chars{$j};
    }
    $tmp = str_replace(array('-', '_', '.'), array('+', '/', '='), $tmp);
    $tmp = trim(base64_decode($tmp));

    if (preg_match("/\d{10}_/s", substr($tmp, 0, 11))) {
        if ($ttl > 0 && (time() - substr($tmp, 0, 11) > $ttl)) {
            $tmp = null;
        } else {
            $tmp = substr($tmp, 11);
        }
    }
    return $tmp;
}


/*
 * 重新构建arr的key值，根据arr的某一列
 * */
function resetArrayKey($arr, $needle)
{
    if (!is_array($arr)) return array();
    if (!count($arr)) return array();
    $ret = array();
    foreach ($arr as $k => $v) {
        $ret[$v[$needle]] = $v;
    }
    return $ret;
}
/*
 * 计算数组某列的合计值
 */
function sumArrayByKey($arr,$needle){
    $total=0;
    if(is_array($arr)){
        foreach($arr as $item){
            $total+=$item[$needle]?floatval($item[$needle]):0;
        }
    }
    return $total;
}
/*
 * 计算数组某咧平均值
 */
function avgArrayByKey($arr,$needle){
    $cnt=count($arr);
    if(!$cnt) return 0;
    $sum=sumArrayByKey($arr,$needle);
    return $sum/$cnt;
}
if (!function_exists('array_column')) {
    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }

        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string)$params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int)$params[2];
            } else {
                $paramsIndexKey = (string)$params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string)$row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }

        }

        return $resultArray;
    }

}
//新加
function curl_post($url, $post_data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function curl_https_post($url, $post_data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function curl_post_json($url, $post_data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, my_json_encode($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function compareFloat($a, $b, $esp = 0.000001)
{
    if (abs($a - $b) < $esp) {
        return true;
    }
    return false;
}

/**
 *    短时间显示, 几分钟前,几秒前...
 **/
function _put_time($time = 0, $default = '')
{
    if (empty($time)) {
        return $default;
    }
    $time = substr($time, 0, 10);
    $ttime = time() - $time;
    if ($ttime <= 0 || $ttime < 60) {
        return 'seconds before';
    }
    if ($ttime > 60 && $ttime < 120) {
        return '1 minute before';
    }

    $i = floor($ttime / 60);                            //分
    $h = floor($ttime / 60 / 60);                        //时
    $d = floor($ttime / 86400);                            //天
    $m = floor($ttime / 2592000);                        //月
    $y = floor($ttime / 60 / 60 / 24 / 365);            //年
    if ($i < 30) {
        return $i . ' minutes before';
    }
    if ($i > 30 && $i < 60) {
        return 'in 1 hour ';
    }
    if ($h >= 1 && $h < 24) {
        return $h . ' hours before';
    }
    if ($d >= 1 && $d < 30) {
        return $d . ' days before';
    }
    if ($m >= 1 && $m < 12) {
        return $m . ' months before';
    }
    if ($y) {
        return $y . ' years before ';
    }
    return "";
}


function output_data($data)
{

    try {
        $st = json_encode($data);
        echo $st;
        die;
    } catch (Exception $e) {

    }
}

/**
 * 字符串切割函数，一个字母算一个位置,一个字算2个位置
 *
 * @param string $string 待切割的字符串
 * @param int $length 切割长度
 * @param string $dot 尾缀
 */
function str_cut($string, $length, $dot = '')
{
    $string = str_replace(array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array(' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $strlen = strlen($string);
    if ($strlen <= $length) return $string;
    $maxi = $length - strlen($dot);
    $strcut = '';
    if (strtolower(CHARSET) == 'utf-8') {
        $n = $tn = $noc = 0;
        while ($n < $strlen) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t < 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $maxi) break;
        }
        if ($noc > $maxi) $n -= $tn;
        $strcut = substr($string, 0, $n);
    } else {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen;
        for ($i = 0; $i < $maxi; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }
    $strcut = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;'), $strcut);
    return $strcut . $dot;
}

/*
 *  格式化用户名称
 */
function formatUserNameByName($name)
{
    if (!$name) {
        return '';
    }
    $len = mb_strlen($name);
    if ($len <= 3) {
        return $name . '***';
    } elseif ($len <= 6) {
        return mb_substr($name, 0, 3) . '***' . mb_substr($name, -1);
    } else {
        return mb_substr($name, 0, 3) . '***' . mb_substr($name, -3);
    }
}

/*
 * 获取分页html
 */
function showPageHtml($each_num = 10, $total_num, $style = 2)
{

    $page = new page();
    $page->setEachNum($each_num);
    $page->setTotalNum($total_num);
    $page->setStyle($style);
    return $page->show();

}


function setCurrentLang($lan = 'en', $expire = 86400)
{
    // 针对shr的特殊格式 zh-cn
    $lan = str_replace('-', '_', $lan);
    $lang_type = getLangTypeList();

    if (!in_array($lan, $lang_type)) {

        $lan = getConf('default_lang');
        if (!in_array($lan, $lang_type)) {
            $lan = 'en';
        }
    }
    if ($expire <= 0) {
        $expire = 86400;
    }
    setNcCookie('lang', $lan, $expire);
}

function isPhoneNumber($phone)
{
    return preg_match('/^\+?[0-9]{5,25}$/', $phone);  // 手机不考虑028-3345的格式
}


function isEmail($email)
{
    return preg_match('/^[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)+$/i', $email);
}

function dateFormat($date, $connector = '/')
{
    return common::dateFormat($date, $connector);
}

function timeFormat($time)
{
    return common::timeFormat($time);
}

function getUserIcon($icon = '', $dir = 'avatar')
{
    if (empty($icon)) {
        return ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg';
    }
    return getImageUrl($icon);
}

function getCompanyIconUrl($icon)
{
    if (!$icon) {
        return '';
    }
    return getImageUrl($icon, null, 'company');
}

function generateGuid($uid = 0, $guid_type)
{
    $uid = intval($uid);
    $guid_type = strval($guid_type);
    $guid = $guid_type . str_pad($uid, 6, '0', STR_PAD_LEFT);
    return intval($guid);
}

function maskInfo($info, $str = '*')
{
    if (!$info) {
        return $info;
    }
    $len = strlen($info);
    $re = '';
    if ($len < 3) {
        $left = 6 - $len;
        $re = $info . str_pad('', $left, $str, STR_PAD_LEFT);
    } elseif ($len <= 6) {
        $re = substr($info, 0, 2) . str_pad('', 3, $str, STR_PAD_LEFT) . substr($info, -1);
    } else {
        $left = $len - 6;
        $re = substr($info, 0, 2) . str_pad('', $left, $str, STR_PAD_LEFT) . substr($info, -4);
    }
    return $re;
}

function getUpyunImgUrl($img)
{
    if (strpos($img, 'http') === 0) {
       return $img;
    } else {
        return UPYUN_URL . '/' .$img;
    }
}

function getImageUrl($img, $ver = null, $dir = null)
{
    if (!$img) return null;

    if ($dir && strpos($dir, '/')!==0) {
        $dir = '/' . $dir;
    }

    if (strpos($img, 'http') === 0) {
        $url = $img;
    } else {
        if (C('oss_target') == 'upyun') {
            $thumb_host = UPYUN_URL . $dir;
        } else {
            $thumb_host = UPLOAD_SITE_URL . $dir;
        }
        $url = $thumb_host . '/' . $img;
    }

    if ($ver) {
        return $url . '!' . $ver;
    }
    return $url;
}

function my_array_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
    if(is_array($arrays)){
        foreach ($arrays as $array){
            if(is_array($array)){
                $key_arrays[] = $array[$sort_key];
            }else{
                return $arrays;
            }
        }
    }else{
        return $arrays;
    }
    array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
    return $arrays;
}
function formatNumberAsSpell($number){
    if(class_exists('NumberFormatter')){
        $formatter = new NumberFormatter('en-US',NumberFormatter::SPELLOUT);
        $words = $formatter->format($number);
        return $words;
    }else{
        return $number;
    }
}


/** 浮点数的比较
 * @param $left_number
 * @param $right_number
 * @param string $mode
 * @param int $scale
 * @return bool
 * @throws Exception
 */
function floatNumberCompare($left_number,$right_number,$mode='=',$scale=2)
{
    $value = bccomp($left_number,$right_number,$scale);
    switch ($mode){
        case '>=':
            return $value>=0;
            break;
        case '>':
            return $value>0;
        case '=':
            return $value == 0;
            break;
        case '<=':
            return $value<=0;
            break;
        case '<':
            return $value <0;
            break;
        default:
            throw new Exception('Unknown compare type.');
    }
}


function getCBCFileUrl($file_path)
{
    if( !$file_path ){
        return '';
    }
    return UPLOAD_SITE_URL.'/cbc/'.$file_path;
}


function formatDateToYmd($date)
{
    return date('Y-m-d',strtotime($date));
}





