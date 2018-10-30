<?php
/**
 * 安全防范
 * 进行sql注入过滤，xss过滤和csrf过滤
 *
 * 令牌调用方式
 * 输出：直接在模板上调用getToken
 * 验证：在验证位置调用checkToken
 *
 * @package    library
 * @copyright  Copyright (c) 2007-2013 KHBuy Inc. (http://www.KHBuy.com)
 * @license    http://www.KHBuy.com
 * @link       http://www.KHBuy.com
 * @author       KHBuy Team
 * @since      File available since Release v1.1
 */
defined('InKHBuy') or exit('Access Invalid!');

class MyString
{
    /** 判断$haystack是否以$needle开头
     * @param string $haystack 目标字符串
     * @param string $needle 搜索字符串
     * @return bool true or false
     */
    public static function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    /** 判断$haystack是否以$needle结尾
     * @param $haystack 目标字符串
     * @param $needle 搜索字符串
     * @return bool true or false
     */
    public static function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    /** 如果$haystack以$needle结尾，则从结尾处移除；否则保留不变。
     * @param $haystack 目标字符串
     * @param $needle 要移除的字符串
     * @return string true or false
     */
    public static function trimEnd($haystack, $needle)
    {
        return substr($haystack, -(strlen($needle))) == $needle ? substr($haystack, 0, strlen($haystack) - strlen($needle)) : $haystack;
    }

    /** 如果$haystack以$needle开头，则从开头处移除；否则保留不变。
     * @param $haystack 目标字符串
     * @param $needle 要移除的字符串
     * @return string true or false
     */
    public static function trimStart($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) == $needle ? substr($haystack, strlen($needle)) : $haystack;
    }
}