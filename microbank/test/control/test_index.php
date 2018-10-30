<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/6
 * Time: 13:55
 */
class test_indexControl
{
    public function __construct()
    {
        Tpl::setDir('home');
        Tpl::setLayout('empty_layout');
    }




    public function indexOp()
    {
        $formatter = new NumberFormatter('en-US',NumberFormatter::SPELLOUT);
        echo($formatter->format(1234567));
        echo 'hello1';
    }

    public function phpinfoOp() {
        echo(phpinfo());
    }

    public function uploadFileOp()
    {
        if( $_POST['form_submit'] == 'ok' ){
            $rt = error_logClass::coAppUploadErrorLog('log_file');
            if( $rt->STS ){
                showMessage('Upload success');
            }else{
                showMessage('Upload fail');
            }
        }
        Tpl::showPage('upload.file');
    }


    public function viewLogOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $path = $params['path']?urldecode($params['path']):_LOG_;
        $list = array();
        if( is_dir($path) ){

            $ls = scandir($path);
            foreach( $ls as $d ){
                if( substr($d,0,1) === '.'){
                    continue;
                }
                $new_path = $path.'/'.$d;
                if( is_dir($new_path) ){
                    $list[] = array(
                        'is_dir' => true,
                        'dir_path' => $new_path,
                        'name' => $d,
                        'request_url' => getConf('project_site_url').'/test'.'/index.php?act=test_index&op=viewLog&path='.urlencode($new_path)

                    );
                }else{
                    $list[] = array(
                        'is_dir' => false,
                        'dir_path' => $new_path,
                        'file_url' => str_replace(_LOG_,getConf('project_site_url').'/data/log',$new_path),
                        'name' => $d,
                    );
                }
            }

        }
        Tpl::output('list',$list);
        Tpl::showPage('file.scan');
    }


    public function scanAppErrorLogOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $path = $params['path']?urldecode( $params['path']):_UPLOAD_.'/app_log';
        $list = error_logClass::ls($path);
        // 处理地址
        foreach( $list as $k=>$v ){
            if( $v['is_dir'] ){
                $v['request_url'] = getConf('project_site_url').'/test'.'/index.php?act=test_index&op=scanAppErrorLog&path='.urlencode($v['dir_path']);
            }
            $list[$k] = $v;
        }
        Tpl::output('list',$list);
        Tpl::showPage('file.scan');
    }
}