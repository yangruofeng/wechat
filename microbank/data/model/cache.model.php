<?php
/**
 * 缓存操作
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2013 KHBuy Inc. (http://www.KHBuy.com)
 * @license    http://www.KHBuy.com
 * @link       http://www.KHBuy.com
 * @since      File available since Release v1.1
 */
defined('InKHBuy') or exit('Access Invalid!');

class cacheModel
{
    public function __construct()
    {
    }

    public function call($method)
    {
        $method = '_' . strtolower($method);
        global $config;
        foreach ($config['languages'] as $key => $value) {
            $needle = '_' . $key;
            if (MyString::endsWith($method, $needle)) {
                $method = MyString::trimEnd($method, $needle);
                break;
            }
        }
        if (preg_match('/^_class_map_(\w+)$/', $method, $m)) {
            return $this->findClass($m[1]);
        }
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            return false;
        }
    }

    static function decamelize($text)
    {
        return preg_replace('/([a-z\d])([A-Z])/', '$1.$2', $text);
    }

    private function findClass($className) {
        $class = strtolower($className);
        if (strlen($class) > 7 && ucwords(substr($class, -7)) == 'Control') {
            if ($filename=$this->find_file($className, CURRENT_ROOT . DS . "control", substr($class, 0, -7)))
                return $filename;
            if ($filename=$this->find_file($className, CURRENT_ROOT . DS . "control", strtolower(self::decamelize(substr($className, 0, -7)))))
                return $filename;
            $class_path_a = array(CURRENT_ROOT . DS . "control");
        } elseif (strlen($class) > 5 && ucwords(substr($class, -5)) == 'Model') {
            if ($filename=$this->find_file($className, _DATA_MODEL_, substr($class, 0, -5) . ".model"))
                return $filename;
            $class_path_a = array(_DATA_MODEL_);
            if ($filename=$this->find_file($className, BASE_MODEL_PATH, substr($class, 0, -5) . ".model"))
                return $filename;
            $class_path_a[] = BASE_MODEL_PATH;
        } elseif (strlen($class) > 5 && ucwords(substr($class, -5)) == 'Class') {
            if ($filename=$this->find_file($className, PROJECT_ROOT . DS . "class", substr($class, 0, -5) . ".class"))
                return $filename;
            $class_path_a = array(PROJECT_ROOT . DS . "class");
        } elseif (strlen($class) > 3 && ucwords(substr($class, -3)) == 'Api') {
            if ($filename=$this->find_file($className, BASE_CORE_PATH . DS . "api", substr($class, 0, -3) . ".api"))
                return $filename;
            if ($filename=$this->find_file($className, BASE_DATA_PATH . DS . "api", substr($class, 0, -3) . ".api"))
                return $filename;
            $class_path_a = array(
                BASE_CORE_PATH . DS . "api",
                BASE_DATA_PATH . DS . "api"
            );

            if (defined("BASE_COMMON_PATH")) {
                if ($filename=$this->find_file($className, BASE_COMMON_PATH . DS . "api", substr($class, 0, -3) . ".api"))
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
            if ($filename=$this->find_file($className, $path)) {
                return $filename;
            }
            if ($filename=$this->find_file(strtolower($className), $path)) {
                return $filename;
            }
            if ($filename=$this->find_file(strtolower(self::decamelize($className)), $path)) {
                return $filename;
            }
            if ($filename=$this->find_file(self::decamelize($className), $path)) {
                return $filename;
            }
        }

        return null;
    }

    private function find_file($className, $path, $filename = null)
    {
        if (!$filename) $filename = $className;
        //if (!file_exists($path. "/$filename.php")) $filename=self::decamelize($filename);
        if (file_exists($path . "/$filename.php")) {
            include_once($path . "/$filename.php");
            $_fname = $path . "/$filename.php";
            $_fname = substr($_fname, strlen(GLOBAL_ROOT) + 1);
            return $_fname;
        } else {
            foreach (scandir($path) as $d) {
                //if ($d == ".." || $d == ".")
                //跳过.svn等目录
                if (substr($d, 0, 1) === ".") continue;
                if (is_dir("$path/$d")) {
                    if ($file_found = $this->find_file($className, "$path/$d", $filename)) {
                        return $file_found;
                    }
                }
            }
            return false;
        }
    }

    /**
     * 基本设置
     *
     * @return array
     */
    private function _setting()
    {
        $list = $this->table('setting')->where(true)->select();
        $array = array();
        foreach ((array)$list as $v) {
            $array[$v['name']] = $v['value'];
        }
        unset($list);
        return $array;
    }
}
