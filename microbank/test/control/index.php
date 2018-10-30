<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/29
 * Time: 18:01
 */
class indexControl
{

    public function __construct()
    {
        Tpl::setDir('home');
        Tpl::setLayout('empty_layout');
    }


    public function testBccompFunctionOp()
    {
        $a = 20.4333;
        $b = 20.4333001;
        $re = bccomp($a,$b,6);
        echo $a.'-'.$b.'='.$re;
    }

    public function testOneTimeLoanOp()
    {
        $user_id = 1;
        $member_id = 310;
        $class = new bizOneTimeCreditLoanClass(bizSceneEnum::COUNTER);
        $list = $class->getMemberOneTimeLoanList($member_id);
        //print_r($list);

        //$rt = $class->insertLoanTask(1,$member_id,2);
        //print_r($rt);
        $biz_id = 1;

       $m = new biz_one_time_credit_loanModel();
       $biz = $m->getRow($biz_id);

       $row_state = $biz->getRowState();
       echo $row_state;

       $biz->update_time = Now();
       echo  '-'.$biz->getRowState();
       //print_r($biz);

        //$rt = $class->insertLoanTask($member_id,$list);
        //print_r($rt);

        //$rt = $class->approveTask(array(3),0);
        //print_r($rt);

        //$rt = $class->createContract($member_id);
        //print_r($rt);
    }


    public function certStructureOp()
    {
        $data = global_settingClass::getCertImageStructure();
        print_r($data);
    }

    public function coreTreeViewOp()
    {
        $m = new core_treeModel();
        $sql = "select * from core_tree where node_level=4 limit 0,20";
        $list = $m->reader->getRows($sql);
        foreach ($list as $k=>$v)
        {
            $list[$k]['node_text_alias'] = json_decode($v['node_text_alias']);
        }
        print_r($list);
    }


    public function testExcelReaderOp()
    {
        ini_set("memory_limit", "1024M");
        set_time_limit(0);
        $path = BASE_CORE_PATH.'/phpExcel/PHPExcel/IOFactory.php';
        require $path;
        //$objReader =PHPExcel_IOFactory::createReader('Excel2007' ); //创建一个2007的读取对象
        $excel_path = GLOBAL_ROOT.'/enlish_address.xlsx';

        //$sheetname = 'English';       // 单个工作表，传入字符串
        $objReader =PHPExcel_IOFactory::createReader('Excel2007' ); //创建一个2007的读取对象
        //$objReader->setLoadSheetsOnly($sheetname);       // 加载单个工作表，传入工作表名字(例如：'Data Sheet #2')
        $objPHPExcel = $objReader->load($excel_path);



        $sheet = $objPHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        //$highestColumm = $sheet->getHighestColumn(); // 取得总列数

        //$highestRow = 5;
        $highestColumm = 'H';


        // 获取一行的数据

        $all_data = array();
        for ($row = 4; $row <= $highestRow; $row++) {
// Read a row of data into an array
            $rowData = $sheet->rangeToArray('A'. $row . ':'. $highestColumm . $row, NULL, TRUE, FALSE);
//这里得到的rowData都是一行的数据，得到数据后自行处理，我们这里只打出来看看效果
            //print_r($rowData);
            $all_data[] = $rowData[0];
            echo '<br />';
        }

        $php_file = GLOBAL_ROOT.'/english_address.php';

        $file_s = var_export($all_data, true);
        $file_s = "return $file_s; /*";
        $file_content = "<" . "?php\n$file_s";

        file_put_contents($php_file,$file_content);
        //print_r($all_data);
        die;

    }


    public function testAssetUploadOp()
    {
        $params = array_merge($_GET,$_POST);

        if( $params['form_submit'] == 'ok' ){

            $conn = ormYo::Conn();
            $conn->startTransaction();
            try{
                $re = memberClass::assetCertNew($params,certSourceTypeEnum::OPERATOR);
                print_r($re);
                if( !$re->STS ){
                    $conn->rollback();
                    return $re;
                }

                $conn->submitTransaction();
                return $re;

            }catch ( Exception $e ){
                $conn->rollback();
                return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
            }
            //die;
        }

        $asset_type = $params['asset_type'];
        $asset_class = new member_assetsClass();
        $page_data = $asset_class->getAssetPageDataByType($asset_type);
        //print_r($page_data);

        Tpl::output('asset_page_data',$page_data);
        Tpl::showpage('test.asset.upload');


    }


    public function testAssetImageUploadOp()
    {
        $url = 'http://dev.samrithisak.com/microbank/api/v2/co/member.asset.add.extend.image.php';

        $image_list = array();
        for( $i=1;$i<=20;$i++){
            $image_list[] = array(
                'image_key' => time(),
                'image_url' => 'other/20180911frwx3qszl6t6lqbkf6u42xnaswc8ueph.jpg',
                'image_source' => 0
            );
        }
        $data = array(
            'officer_id' => 131,
            'token' => 'b30688154dfe691fe00a74143fcc6f23',
            'asset_id' => 589,
        );
        $data['image_list'] = urlencode(json_encode($image_list));

        //echo $data['image_list'];
        echo '<br />';
        echo 'Start:'.microtime().'<br />';

        print_r($data);die;

        $rt = curl_post($url,$data);
        print_r($rt);
        echo '<hr />';
        echo 'End:'.microtime().'<br />';
    }






}