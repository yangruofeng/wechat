<?php
/**
 * 验证类
 *
 * @package    library
 * @copyright  Copyright (c) 2007-2013 KHBuy Inc. (http://www.KHBuy.com)
 * @license    http://www.KHBuy.com
 * @link       http://www.KHBuy.com
 * @author	   KHBuy Team
 * @since      File available since Release v1.1
 */
defined('InKHBuy') or exit('Access Invalid!');
Class Validate{
	/**
	 * 存放验证信息
	 *
	 * @var array
	 */
	public $deliverparam = array();
	public $validateparam = array();
	/**
	 * 验证规则
	 *
	 * @var array
	 */
	private $validator = array(
		"string"=>'/\s/',
		"email"=>'/^([.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\\.[a-zA-Z0-9_-])+/',
		"phone"=>'/^(([0-9]{2,3})|([0-9]{3}-))?((0[0-9]{2,3})|0[0-9]{2,3}-)?[1-9][0-9]{6,7}(-[0-9]{1,4})?$/',
		"mobile"=>'/^1[0-9]{10}$/', //http://115.28.67.116/shopnc/shop/item-100263.html
		"url"=>'/^http:(\\/){2}[A-Za-z0-9]+.[A-Za-z0-9]+[\\/=?%-&_~`@\\[\\]\':+!]*([^<>\"\"])*$/',
		"currency"=>'/^[0-9]+(\\.[0-9]+)?$/',  //http://item.jd.com/1279804.html
		"number"=>'/^[0-9]+$/',
		"zip"=>'/^[0-9][0-9]{5}$/',
		"qq"=>'/^[1-9][0-9]{4,8}$/',
		"integer"=>'/^[-+]?[0-9]+$/',
		"integerpositive"=>'/^[+]?[0-9]+$/',
		"double"=>'/^[-+]?[0-9]+(\\.[0-9]+)?$/',
		"doublepositive"=>'/^[+]?[0-9]+(\\.[0-9]+)?$/',
		"english"=>'/^[A-Za-z]+$/',
		"chinese"=>'/^[\x80-\xff]+$/',
		"username"=>'/^[\\w]{3,}$/',
		"nochinese"=>'/^[A-Za-z0-9_-]+$/',
	);

	/**
	 * 错误输出类型
	 *
	 * @var string
	 */
	public $error_show_type='html';

	/**
	 * 验证数组中的值
	 *
	 * <code>
	 * //使用示例
	 * <?php
	 *  require("commonvalidate.class.php");
	 *	$a = new CommonValidate();
	 *	$a->setValidate("344d",true,"","不可以为空");
	 *	$a->setValidate("fdsfsfd",true,"Email","请填写正确的EMAIL");
	 *	echo $a->validate();
	 *
	 *  //显示结果：
	 *  请填写正确的EMAIL
	 * ? >
	 * </code>
	 *
	 * @param
	 * @return string 字符串类型的返回结果
	 */

	 function object2array($object) {
             $object =  json_decode( json_encode( $object),true);
             return  $object;
    }
	public function validate(){
		$this->validateparam = $this->object2array(json_decode($this->deliverparam['validate']));
		if (!is_array($this->validateparam)){
			return false;
		}

		//print_r($this->validateparam);

		foreach($this->validateparam as $k => $v){
			$field = $v['field'];
			$rules = $v['rules'];
			$messages = $v['messages'];
			$input = $this->deliverparam[$field];

			foreach ($rules as $key => $value) {
				$v['validator'] = $key;
				if($key == 'minlength' || $key == 'maxlength'){
					$v['validator'] = 'length';
				}
				if($key == 'equalTo'){
					$v['operator'] = '==';
					$to_field = substr($rules[$key], 1);
					$v['to'] = $this->deliverparam[$to_field];
				}
				if($key == 'regexp' || $key == 'regexpFun' || $key == $rules['regexpFun']){
						$v['validator'] = 'custom';
						$regField =  $field.'Regexp';
						$v['regexp'] = $this->deliverparam[$regField];
				}
			}

			if ($input == "" && $rules['required']){
				$this->validateparam[$k]['result'] = false;
				$this->validateparam[$k]['validfield']='required';
			}else{
				$this->validateparam[$k]['result'] = true;
			}
      if ($this->validateparam[$k]['result'] && $input != ""){
				switch($v['validator']){
					case "custom":
					//匹配中文表达式替换
					if((bool)stripos($v['regexp'], '\x4e00') || (bool)stripos($v['regexp'], '\x9fa5')){
							$v['regexp'] = str_replace('\x4e00', '\x{4e00}', $v['regexp']);
							$v['regexp'] = str_replace('\x9fa5', '\x{9fa5}', $v['regexp']);
							$v['regexp'] = $v['regexp'].'u';
					}
						$this->validateparam[$k]['result'] = $this->check($input,$v['regexp']);
						$this->validateparam[$k]['validfield'] = 'regexp';
						break;
					case "equalTo":
						if ($v['operator'] != ""){
							eval("\$result = '" . $input . "'" . $v['operator'] . "'" . $v['to'] . "'" . ";" );
							$this->validateparam[$k]['result'] = $result;
							$this->validateparam[$k]['validfield'] = $v['validator'];
						}
						break;
					case "length":
            //判断编码取字符串长度
            $input_encode = mb_detect_encoding($input,array('UTF-8','GBK','ASCII',));
            $input_length = mb_strlen($input,$input_encode);
						if (intval($rules['minlength']) >= 0 && intval($rules['maxlength']) > intval($rules['minlength'])){
							$this->validateparam[$k]['validfield'] = $input_length < intval($rules['minlength']) ? 'minlength' : ($input_length > intval($rules['maxlength']) ? 'maxlength': '');
							$this->validateparam[$k]['result'] = ($input_length >= intval($rules['minlength']) && $input_length <= intval($rules['maxlength']));
						}else if (intval($rules['minlength']) >= 0 && intval($rules['maxlength']) <= intval($rules['minlength'])){
							$this->validateparam[$k]['result'] = ($input_length == intval($rules['minlength']));
							$this->validateparam[$k]['validfield'] = 'minlength';
						}
            break;
          case "range":
              if (intval($v['min']) >= 0 && intval($v['max']) > intval($v['min'])){
                  $this->validateparam[$k]['result'] = (intval($v['input']) >= intval($v['min']) && intval($v['input']) <= intval($v['max']));
              }
              else if (intval($v['min']) >= 0 && intval($v['max']) <= intval($v['min'])){
                  $this->validateparam[$k]['result'] = (intval($v['input']) == intval($v['min']));
              }
              break;
					default:
						$this->validateparam[$k]['result'] = $this->check($input,$this->validator[$v['validator']]);
						$this->validateparam[$k]['validfield'] = $v['validator'];
				}
			}
		}
		$error = $this->getError();
		$this->validateparam = array();
		return $error;
	}

	public function validateOld(){
		$validatearr = $this->object2array($this->validateparam['validate']);
		if (!is_array($this->validateparam)){
			return false;
		}
		foreach($this->validateparam as $k=>$v){
			$v['validator'] = strtolower($v['validator']);
			if ($v['require'] == ""){
				$v['require'] = false;
			}
			if ($v['input'] == "" && $v['require'] == "true"){
				$this->validateparam[$k]['result'] = false;
			}else{
				$this->validateparam[$k]['result'] = true;
			}
      if ($this->validateparam[$k]['result'] && $v['input'] != ""){
				switch($v['validator']){
					case "custom":
						$this->validateparam[$k]['result'] = $this->check($v['input'],$v['regexp']);
						break;
					case "compare":
						if ($v['operator'] != ""){
							eval("\$result = '" . $v['input'] . "'" . $v['operator'] . "'" . $v['to'] . "'" . ";" );
							$this->validateparam[$k]['result'] = $result;
						}
						break;
					case "length":
            //判断编码取字符串长度
            $input_encode = mb_detect_encoding($v['input'],array('UTF-8','GBK','ASCII',));
            $input_length = mb_strlen($v['input'],$input_encode);
						if (intval($v['min']) >= 0 && intval($v['max']) > intval($v['min'])){
							$this->validateparam[$k]['result'] = ($input_length >= intval($v['min']) && $input_length <= intval($v['max']));
						}
						else if (intval($v['min']) >= 0 && intval($v['max']) <= intval($v['min'])){
							$this->validateparam[$k]['result'] = ($input_length == intval($v['min']));
						}
            break;

         case "range":
            if (intval($v['min']) >= 0 && intval($v['max']) > intval($v['min'])){
                $this->validateparam[$k]['result'] = (intval($v['input']) >= intval($v['min']) && intval($v['input']) <= intval($v['max']));
            }
            else if (intval($v['min']) >= 0 && intval($v['max']) <= intval($v['min'])){
                $this->validateparam[$k]['result'] = (intval($v['input']) == intval($v['min']));
            }
            break;
					default:
						$this->validateparam[$k]['result'] = $this->check($v['input'],$this->validator[$v['validator']]);
				}
			}
		}
		$error = $this->getErrorOld();
		$this->validateparam = array();
		return $error;
	}

	/**
	 * 正则表达式运算
	 *
	 * @param string $str 验证字符串
	 * @param string $validator 验证规则
	 * @return bool 布尔类型的返回结果
	 */
	private function check($str='',$validator=''){
		if ($str != "" && $validator != ""){
			if (preg_match($validator,$str)){
				return true;
			}
			else{
				return false;
			}
		}
		return true;
	}

	/**
	 * 需要验证的内容
	 *
	 * @param array $validateparam array("input"=>"","require"=>"","validator"=>"","regexp"=>"","operator"=>"","to"=>"","min"=>"","max"=>"",message=>"")
	 * input要验证的值
	 * require是否必填，true是必填false是可选
	 * validator验证的类型:
	 * 其中Compare，Custom，Length,Range比较特殊。
	 * Compare是用来比较2个字符串或数字，operator和to用来配合使用，operator是比较的操作符(==,>,<,>=,<=,!=)，to是用来比较的字符串；
	 * Custom是定制验证的规则，regexp用来配合使用，regexp是正则表达试；
	 * Length是验证字符串或数字的长度是否在一顶的范围内，min和max用来配合使用，min是最小的长度，max是最大的长度，如果不写max则被认为是长度必须等于min;
	 * Range是数字是否在某个范围内，min和max用来配合使用。
	 * 值得注意的是，如果需要判断的规则比较复杂，建议直接写正则表达式。
	 *
	 * @return void
	 */
	public function setValidate($validateparam){
		$validateparam["result"] = true;
		$this->validateparam = array_merge($this->validateparam,array($validateparam));
	}

	/**
	 * 得到验证的错误信息
	 *
	 * @param
	 * @return string 字符串类型的返回结果
	 */
	private function getError($validfield){
		$error = '';
		foreach($this->validateparam as $k => $v){
			$message = $v['messages'];
			if ($v['result'] == false){
				$error .= $message[$v['validfield']];
				if ($this->error_show_type == 'html' and trim($error) != ''){
					$error .= "<br/>";
				}
			}
		}
		return $error;
	}

	private function getErrorOld(){
		$error = '';
		foreach($this->validateparam as $k=>$v){
			//$message = $v['messages'];
			if ($v['result'] == false){
				$error .= $v['message'];
				if ($this->error_show_type == 'html' and trim($error) != ''){
					$error .= "<br/>";
				}
			}
		}
		return $error;
	}
}
?>
