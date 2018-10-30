<?php
//url生成函数（用在 template.class.php里面）
function _dz_function_url($params_str, &$smarty){
	$pa=split(' ',$params_str);
	$p=array();
	foreach($pa as $pal){
		list($k,$v)=split('=',$pal);
		$p[$k]=$v;
	}
	return "<?php echo url(my_json_decode(\"".str_replace("\"","\\\"",my_json_encode($p))."\"))?".">";
}
//filetime（用在 template.class.php里面）
function _dz_function_filetime($fn){
	if(file_exists($fn)){
		return filemtime($fn);
	}
}

///////////////////////////////////////////////////////////////
//Usage: $html=eval(evalTpl($fn));
function evalTpl($fn){
	return <<<EOS
ob_start();
if(!file_exists('$fn')){
	ob_end_clean();
	throw new Exception('$fn not exists');
}
include(fetchCache('$fn'));
\$_tmp_ob_get_contents = ob_get_contents();
ob_end_clean();
return \$_tmp_ob_get_contents;
EOS;
}
function fetchCache($fn){
	if(!file_exists($fn))
		//throw new Exception("file not found ".basename($fn));
	throw new Exception("file not found $fn");

	$Viewer = DzTemplate::getInstance();

	header("Content-type: text/html; charset=UTF-8", 1);
	return $Viewer->fetchCache($fn);
}

//Usage: include(TPL($fn));
function TPL($fn,$dir){	
	if($dir===null){
		if(!file_exists($fn)){
			if (!defined("_TPL_DIR_")){ throw new Exception("//_TPL_DIR_ is not defined."); }
			$fn=_TPL_DIR_.$fn;	
		}		
	}else{
		$fn=$dir.$fn;	
	}
	
	
	
	if(!file_exists($fn))
		throw new Exception("404 FILE ".basename($fn));
	if(!defined("_TMP_")){
		throw new Exception("_TMP_ is not defined");
	}
	if(defined("_CORE_PHP_")){
		$_clsFile=_CORE_PHP_ ."/DzTemplate.php";
	}else{
		$_clsFile="template.class.php";
	}
	require_once($_clsFile);
	$Viewer = DzTemplate::getInstance();
	$Viewer->cache_dir = _TMP_;

	chdir(dirname($fn));//跳去模板文件所在的目录..
	$fn=basename($fn);

	return(fetchCache($fn));
}

