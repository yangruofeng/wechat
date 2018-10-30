<?php
defined('PLUTOFLAG') or exit('Access Invalid!');
/**
 * 缓存操作
 */
class cache{
	
	protected $params;
	protected $enable;
	protected $handler;

	/**
	 * 实例化缓存驱动
	 *
	 * @param unknown_type $type
	 * @param unknown_type $args
	 * @return unknown
	 */
	public function connect($type,$args = array()){
		if (empty($type)) $type = C('cache.type');
		$type = strtolower($type);
		$class = 'Cache'.ucwords($type);
        require_once(BASE_CORE_PATH."/cache/cache.".$type.".php");
		return new $class($args);
	}

	/**
	 * 取得实例
	 *
	 * @return object
	 */
	public static function getInstance(){
		$args = func_get_args();
		return get_obj_instance(__CLASS__,'connect',$args);
	}
}
?>