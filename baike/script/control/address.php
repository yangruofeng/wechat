<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/16
 * Time: 20:38
 */
class addressControl
{

    public function testExcelReaderOp()
    {
        ini_set("memory_limit", "1024M");
        set_time_limit(0);
        $path = BASE_CORE_PATH.'/phpExcel/PHPExcel/IOFactory.php';
        require $path;
        //$objReader =PHPExcel_IOFactory::createReader('Excel2007' ); //创建一个2007的读取对象
        $excel_path = GLOBAL_ROOT.'/enlish_address.xlsx';
        $excel_path = GLOBAL_ROOT.'/kh_address.xlsx';
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
        for ($row = 2; $row <= $highestRow; $row++) {
        // Read a row of data into an array
            $rowData = $sheet->rangeToArray('A'. $row . ':'. $highestColumm . $row, NULL, TRUE, FALSE);
        //这里得到的rowData都是一行的数据，得到数据后自行处理，我们这里只打出来看看效果
            //print_r($rowData);
            $all_data[] = $rowData[0];
        }

        $php_file = GLOBAL_ROOT.'/kh_address.php';

        $file_s = var_export($all_data, true);
        $file_s = "return $file_s; /*";
        $file_content = "<" . "?php\n$file_s";

        file_put_contents($php_file,$file_content);
        //print_r($all_data);
        die;

    }


    public function mergeAddressOp()
    {
        $en_path = GLOBAL_ROOT.'/english_address.php';
        $kh_path = GLOBAL_ROOT.'/kh_address.php';

        //echo $en_path.'<br />'.$kh_path;die;

        $en_address = require $en_path;
        $kh_address = require $kh_path;


        // 得到四级的翻译
        $data = array();
        foreach( $en_address as $key=>$v ){
            $kh_data = $kh_address[$key];
            $temp = array();
            $temp['en_code'] = $v[6];
            $temp['en_text'] = $v[7];
            $temp['kh_code'] = $kh_data[6];
            $temp['kh_text'] = $kh_data[7];
            $temp['text_alias'] = json_encode(array(
                'en' => $temp['en_text'],
                'kh' => $temp['kh_text'],
                'zh_cn' => '',
            ));
            $data[] = $temp;
        }

        $php_file = GLOBAL_ROOT.'/merge_address.php';

        $file_s = var_export($data, true);
        $file_s = "return $file_s; /*";
        $file_content = "<" . "?php\n$file_s";

        file_put_contents($php_file,$file_content);
        //print_r($all_data);

    }

    public function generateSqlOp()
    {
        $path = GLOBAL_ROOT.'/merge_address.php';
        $data = require $path;

        $str = '';
        foreach( $data as $v ){
            $str .= "insert into village_address_text(en_text,kh_text,text_alias) value
            (".qstr($v['en_text']).",".qstr($v['kh_text']).",".qstr($v['text_alias']).") ;"."\n";
        }

        $sql_file = GLOBAL_ROOT.'/village_sql.sql';
        file_put_contents($sql_file,$str);
    }
}