<?php
/**
 * 模板驱动
 *
 * 模板驱动，商城模板引擎
 *
 *
 * @package    tpl
 * @copyright  Copyright (c) 2007-2013 PlutoMall Inc. (http://www.plutomall.com)
 * @license    http://www.plutomall.com
 * @link       http://www.plutomall.com
 * @author       PlutoMall Team
 * @since      File available since Release v1.1
 */
defined('PLUTOFLAG') or exit('Access Invalid!');

class Tpl
{
    /**
     * 单件对象
     */
    private static $instance = null;
    /**
     * 输出模板内容的数组，其他的变量不允许从程序中直接输出到模板
     */
    private static $output_value = array();
    /**
     * 模板路径设置
     */
    public static $tpl_dir = 'default';
    /**
     * 默认layout
     */
    private static $layout_file = '';

    private function __construct()
    {
    }

    /**
     * 实例化
     *
     * @return obj
     */
    public static function getInstance()
    {
        if (self::$instance === null || !(self::$instance instanceof Tpl)) {
            self::$instance = new Tpl();
        }
        return self::$instance;
    }

    /**
     * 设置模板目录
     *
     * @param string $dir
     * @return bool
     */
    public static function setDir($dir)
    {
        self::$tpl_dir = $dir;
        return true;
    }

    /**
     * 设置布局
     *
     * @param string $layout
     * @return bool
     */
    public static function setLayout($layout)
    {
        self::$layout_file = $layout;
        return true;
    }

    /**
     * 抛出变量
     *
     * @param mixed $output
     * @param  void
     */
    public static function output($output, $input = '')
    {
        self::getInstance();

        self::$output_value[$output] = $input;
    }

    /*
     * 获取Tpl缓存的变量
     * */
    public static function getOutput($_key)
    {
        self::getInstance();
        if ($_key) return self::$output_value[$_key];
        return self::$output_value;
    }

    /**
     * 调用显示模板
     *
     * @param string $page_name
     * @param string $layout
     * @param string $dir
     * @param int $time
     */
    public static function showPage($page_name = '', $layout = '', $dir = "", $time = 2000)
    {
        if (!defined('TPL_NAME')) define('TPL_NAME', 'default');
        self::getInstance();

        if ($dir) {
            if ($dir != "default") {
                $tpl_dir = $dir . DS;
            } else {
                $tpl_dir = "";
            }
        } else if (!empty(self::$tpl_dir)) {
            $tpl_dir = self::$tpl_dir . DS;
        }

        //默认是带有布局文件
        if (!$layout && self::$layout_file) {
            $layout = 'layout' . DS . self::$layout_file . '.php';
        } else if ($layout) {
            $layout = 'layout' . DS . $layout . '.php';
        }

        if (!defined('_TPL_FOLDER_')) define('_TPL_FOLDER_', 'templates');
        $layout_file = CURRENT_ROOT . DS . _TPL_FOLDER_ . DS . TPL_NAME . DS . $layout;
        $tpl_file = CURRENT_ROOT . DS . _TPL_FOLDER_ . DS . TPL_NAME . DS . $tpl_dir . $page_name . '.php';

        if (file_exists($tpl_file)) {
            //对模板变量进行赋值
            $output = self::$output_value;
            //页头
            $output['html_title'] = $output['html_title'] != '' ? $output['html_title'] : $GLOBALS['setting_config']['site_name'];
            $output['seo_keywords'] = $output['seo_keywords'] != '' ? $output['seo_keywords'] : $GLOBALS['setting_config']['site_name'];
            $output['seo_description'] = $output['seo_description'] != '' ? $output['seo_description'] : $GLOBALS['setting_config']['site_name'];
            $output['ref_url'] = getReferer();

            Language::read('common');
            $lang = Language::getLangContent();

            @header("Content-type: text/html; charset=utf-8");
            @header("Cache-Control: private");
            //判断是否使用布局方式输出模板，如果是，那么包含布局文件，并且在布局文件中包含模板文件
            if ($layout) {
                if (file_exists($layout_file)) {
                    include_once($layout_file);
                } else {
                    $error = 'Tpl ERROR:' . 'templates' . DS . $layout . ' is not exists';
                    show_exception($error);
                }
            } else {
                include_once($tpl_file);
            }
        } else {
            $error = 'Tpl ERROR:' . 'templates' . DS . $tpl_dir . $page_name . '.php' . ' is not exists';
            show_exception($error);
        }
        die;
    }

    /*
     * add by tim
     * 增加不需要layout的输出,可以做模版共享
     * */
    public static function getTpl($page_name, $_dir = "", $_data = array())
    {

        self::getInstance();
        $is_share_tpl = false;
        if (!empty(self::$tpl_dir)) {
            if (self::$tpl_dir == "_data_share") {
                $is_share_tpl = true;
            }
            $tpl_dir = self::$tpl_dir . DS;
        }
        if ($_dir) {
            if ($_dir == "_data_share") {
                $is_share_tpl = true;
            }
            if (startWith($_dir, ":")) {
                $share_dir = $_dir . DS;
            }
            $tpl_dir = $_dir . DS;
        }

        $data = $_data ?: array();
        if ($data['STS'] === false && $data['MSG']) {
            $tpl_file =template(":widget/msg"); //_DATA_SHARE_ . '/templates/widget/msg.php';
        } else {
            //$tpl_file=template($page_name);

            if ($is_share_tpl) {
                if (!$share_dir) {
                    $tpl_file = _DATA_SHARE_ . '/templates/' . $page_name . '.php';
                } else {
                    $tpl_file = _DATA_SHARE_ . '/templates/' . $share_dir . $page_name . '.php';
                }

            } else {
                $tpl_file = CURRENT_ROOT . '/templates/default/' . $tpl_dir . $page_name . '.php';
            }

        }
        if (file_exists($tpl_file)) {
            //对模板变量进行赋值
            $output = self::$output_value;
            //页头
            Language::read('common');
            $lang = Language::getLangContent();

            //@header("Content-type: text/html; charset=".CHARSET);
            include($tpl_file);
        } else {
            //$error = 'Tpl ERROR:'.'templates'.DS.$tpl_dir.$page_name.'.php'.' is not exists';
            //show_exception($error);
            $tpl_file = _DATA_SHARE_ . '/templates/widget/coming_soon.php';
            include($tpl_file);
        }
    }

    public static function getTplValue($page_name, $_dir = "", $_data = array())
    {
        ob_start();
        self::getTpl($page_name, $_dir, $_data);
        return ob_get_clean();
    }

    /**
     * 显示页面Trace信息
     *
     * @return array
     */
    public static function showTrace()
    {
        $trace = array();
        //当前页面
        $trace['debug_current_page'] = $_SERVER['REQUEST_URI'] . '<br>';
        //请求时间
        $trace['debug_request_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . '<br>';
        //系统运行时间
        $query_time = number_format((microtime(true) - StartTime), 3) . 's';
        $trace['debug_execution_time'] = $query_time . '<br>';
        //内存
        $trace['debug_memory_consumption'] = number_format(memory_get_usage() / 1024 / 1024, 2) . 'MB' . '<br>';
        //请求方法
        $trace['debug_request_method'] = $_SERVER['REQUEST_METHOD'] . '<br>';
        //通信协议
        $trace['debug_communication_protocol'] = $_SERVER['SERVER_PROTOCOL'] . '<br>';
        //用户代理
        $trace['debug_user_agent'] = $_SERVER['HTTP_USER_AGENT'] . '<br>';
        //会话ID
        $trace['debug_session_id'] = session_id() . '<br>';
        //执行日志
        $log = logger::History();
        $trace['debug_logging'] = $log ? implode('<br/>', $log) : '';
        $trace['debug_logging'] = "<div style=' background-color: #222222; padding: 10px; color: yellow'>" . $trace['debug_logging'] . '</div><br>';
        //文件加载
        $files = get_included_files();
        $trace['debug_load_files'] = count($files) . str_replace("\n", '<br/>', substr(substr(print_r($files, true), 7), 0, -2)) . '<br>';
        return $trace;
    }
}
