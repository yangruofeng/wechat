<?php

/**
 * 语言调用类
 *
 * 语言调用类，为静态使用
 *
 *
 * @package    library
 * @copyright  Copyright (c) 2007-2013 PlutoMall Inc. (http://www.plutomall.com)
 * @license    http://www.plutomall.com
 * @link       http://www.plutomall.com
 * @author       PlutoMall Team
 * @since      File available since Release v1.1
 */
final class Language
{
    private static $language_content = array();

    /**
     * 得到数组变量的GBK编码
     *
     * @param array $key 数组
     * @return array 数组类型的返回结果
     */
    public static function getGBK($key)
    {
        /**
         * 转码
         */
        if (strtoupper(CHARSET) == 'GBK' && !empty($key)) {
            if (is_array($key)) {
                $result = var_export($key, true);//变为字符串
                $result = iconv('UTF-8', 'GBK', $result);
                eval("\$result = $result;");//转换回数组
            } else {
                $result = iconv('UTF-8', 'GBK', $key);
            }
        }
        return $result;
    }

    /**
     * 得到数组变量的UTF-8编码
     *
     * @param array $key GBK编码数组
     * @return array 数组类型的返回结果
     */
    public static function getUTF8($key)
    {
        /**
         * 转码
         */
        if (!empty($key)) {
            if (is_array($key)) {
                $result = var_export($key, true);//变为字符串
                $result = iconv('GBK', 'UTF-8', $result);
                eval("\$result = $result;");//转换回数组
            } else {
                $result = iconv('GBK', 'UTF-8', $key);
            }
        }
        return $result;
    }

    /**
     * 取指定下标的数组内容
     *
     * @param string $key 数组下标
     * @return string 字符串形式的返回结果
     */
    public static function get($key, $charset = '')
    {
        $result = self::$language_content[$key] ? self::$language_content[$key] : '';
        if (strtoupper(CHARSET) == 'UTF-8' || strtoupper($charset) == 'UTF-8') return $result;//json格式时不转换
        /**
         * 转码
         */
        if (strtoupper(CHARSET) == 'GBK' && !empty($result)) {
            $result = iconv('UTF-8', 'GBK', $result);
        }
        return $result;
    }

    /**
     * 设置指定下标的数组内容
     *
     * @param string $key 数组下标
     * @param string $value 值
     * @return bool 字符串形式的返回结果
     */
    public static function set($key, $value)
    {
        self::$language_content[$key] = $value;
        return true;
    }

    /**
     * 通过语言包文件设置语言内容
     *
     * @param string $file 语言包文件，可以按照逗号(,)分隔
     * @return bool 布尔类型的返回结果
     */
    public static function read($file)
    {
        str_replace('，', ',', $file);
        $tmp = explode(',', $file);
        $lan = self::currentCode();
        foreach ($tmp as $v) {

            // 先读项目下的语言包
            $p_file = PROJECT_ROOT . '/language/' . $lan . '/' . $v . '.php';
            if (file_exists($p_file)) {
                require($p_file);
                if (!empty($lang) && is_array($lang)) {
                    self::$language_content = array_merge(self::$language_content, $lang);
                }
                unset($lang);
            }


            $tmp_file = CURRENT_ROOT . '/language/' . $lan . DS . $v . '.php';
            if (file_exists($tmp_file)) {//file_exists() 函数检查文件或目录是否存在。
                require($tmp_file);
                if (!empty($lang) && is_array($lang)) {
                    self::$language_content = array_merge(self::$language_content, $lang);
                }
                unset($lang);
            }
        }
        return true;
    }

    /**
     * 取语言包全部内容
     *
     * @return array 数组类型的返回结果
     */
    public static function getLangContent($charset = '')
    {
        $result = self::$language_content;
        if (strtoupper(CHARSET) == 'UTF-8' || strtoupper($charset) == 'UTF-8') return $result;//json格式时不转换
        /**
         * 转码
         */
        if (strtoupper(CHARSET) == 'GBK' && !empty($result)) {
            if (is_array($result)) {
                foreach ($result as $k => $v) {
                    $result[$k] = iconv('UTF-8', 'GBK', $v);
                }
            }
        }
        return $result;
    }

    public static function appendLanguage($lang)
    {
        if (!empty($lang) && is_array($lang)) {
            self::$language_content = array_merge(self::$language_content, $lang);
        }
    }

    /**
     * 当前语言代码，如：en, zh_cn
     */
    public static function currentCode()
    {
        $cookieLang = cookie('lang');
        if (!empty($_GET['lang'])) {
            $lang = $_GET['lang'];
        } elseif (!empty($cookieLang)) {
            $lang = $cookieLang;
        } else {

            // 先取项目默认lang
            if ($GLOBALS['config']['default_lang']) {
                $lang = $GLOBALS['config']['default_lang'];
            } else {
                $acceptLanguage = self::getAcceptLanguage();
                if (!empty($acceptLanguage)) {
                    $lang = $acceptLanguage;
                } else {
                    $lang = 'en';
                }
            }

        }
        if (array_key_exists($lang, $GLOBALS['config']['lang_type_list'])) { //防止切换到不存在的语言上去
            return $lang;
        } else {
            return $GLOBALS['config']['default_lang'];
        }
    }

    /**
     * 当前语言名称，如：English、简体中文
     * @return mixed
     */
    public static function  currentName()
    {
        $lang = self::currentCode();
        return $GLOBALS['config']['lang_type_list'][$lang];
    }

    private static function getAcceptLanguage()
    {
        $langs = array();

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
            if (count($lang_parse[1])) {
                $langs = array_combine($lang_parse[1], $lang_parse[4]);
                foreach ($langs as $lang => $val) {
                    if ($val === '') $langs[$lang] = 1;
                }
                arsort($langs, SORT_NUMERIC);
            }
        }
        foreach ($langs as $lang => $val) {
            $lang = strtolower($lang);
            $lang = str_replace('-', '_', $lang);
            if (array_key_exists($lang, $GLOBALS['config']['languages'])) {
                return $lang;
            }
        }
        return '';
    }

}