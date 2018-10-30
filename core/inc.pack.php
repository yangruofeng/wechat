<?php
/* vim: set tabstop=2 shiftwidth=2 softtabstop=2: */
function loadExcel($xls){
	require_once _CORE_PHP_."/sampleExcel/src/SimpleExcel/SimpleExcel.php";
	$excel = new SimpleExcel\SimpleExcel('xml');
	$excel->parser->loadFile("$xls");   
	$csv_a=$excel->parser->getField();
	return $csv_a;
}
function getLangPack($lang){
	if(!$lang)$lang=$_SESSION['lang'];
	if(!$lang)$lang=getConf("default_lang");//default
	static $lang_a=null;

	$lang_pack_conf=getConf("lang_pack");//注意是相对目录
	if(!$lang_pack_conf) throw new Exception("404 lang_pack_conf");

	$lang_pack_xls=_ROOT_.DIRECTORY_SEPARATOR.$lang_pack_conf;
	$filemtime=filemtime($lang_pack_xls);
	if(!$filemtime){ throw new Exception("404 lang_pack"); }

	$target_cache_file=_TMP_."/lang_pack_".$filemtime."_cache.php";//TODO md5性能好像不是特别好，所以...先不用...
	$cache_file_mtime=filemtime($target_cache_file);
	if(!$cache_file_mtime){
		//gen cache
		$csv_a=loadExcel($lang_pack_xls);
		$csv_a_1st=array_shift($csv_a);
		array_shift($csv_a);//第二行不要
		array_shift($csv_a_1st);//不要第一行第一个...
		$_lang_full_a=array();
		foreach($csv_a as $k=>$v){
			foreach($csv_a_1st as $kk=>$vv){
				if(!$_lang_full_a[$vv])$_lang_full_a[$vv]=array();
				$_lang_full_a[$vv][$v[0]]=$v[$kk+1];
			}
		}
		$lang_full_s=var_export($_lang_full_a,true);
		file_put_contents($target_cache_file,"<"."?php\n\$lang_full_a=$lang_full_s;");
		$cache_file_mtime=filemtime($target_cache_file);
		if(!$cache_file_mtime) throw new Exception("KO FOR COMPILE lang_pack");
	}
	//include($target_cache_file);
	require($target_cache_file);
	if($lang_full_a){
		$lang_a=$lang_full_a;
	}
	return $lang_a[$lang];
}
function getLang($k,$lang=null){
	if(!$k) throw new Exception("KO: getLang(null) is not supported");
	if(!$lang)$lang=$_SESSION['lang'];
	if(!$lang)$lang="en";
	$lang_a=getLangPack($lang);	
	return $lang_a[$k]?$lang_a[$k]:$k;
}
function getOrmPack($orm){
	if(!$lang)$lang=$_SESSION['lang'];
	if(!$lang)$lang=getConf("default_lang");//default
	static $lang_a=null;

	$lang_pack_conf=getConf("lang_pack");//注意是相对目录
	if(!$lang_pack_conf) throw new Exception("404 lang_pack_conf");

	$lang_pack_xls=_APP_DIR_.DIRECTORY_SEPARATOR.$lang_pack_conf;
	$filemtime=filemtime($lang_pack_xls);
	if(!$filemtime){ throw new Exception("404 lang_pack"); }

	$target_cache_file=_TMP_."/lang_pack_".$filemtime."_cache.php";//TODO md5性能好像不是特别好，所以...先不用...
	$cache_file_mtime=filemtime($target_cache_file);
	if(!$cache_file_mtime){
		//gen cache
		$csv_a=loadExcel($lang_pack_xls);
		$csv_a_1st=array_shift($csv_a);
		array_shift($csv_a);//第二行不要
		array_shift($csv_a_1st);//不要第一行第一个...
		$_lang_full_a=array();
		foreach($csv_a as $k=>$v){
			foreach($csv_a_1st as $kk=>$vv){
				if(!$_lang_full_a[$vv])$_lang_full_a[$vv]=array();
				$_lang_full_a[$vv][$v[0]]=$v[$kk+1];
			}
		}
		$lang_full_s=var_export($_lang_full_a,true);
		file_put_contents($target_cache_file,"<"."?php\n\$lang_full_a=$lang_full_s;");
		$cache_file_mtime=filemtime($target_cache_file);
		if(!$cache_file_mtime) throw new Exception("KO FOR COMPILE lang_pack");
	}
	//include($target_cache_file);
	require($target_cache_file);
	if($lang_full_a){
		$lang_a=$lang_full_a;
	}
	return $lang_a[$lang];
}
