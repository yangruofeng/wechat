<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2016/12/12
 * Time: 11:27
 */
/*
 *  数据导出到excel
 */

class toolExcel
{

    public function __construct()
    {
    }

    /*
     *  通过远程图片创建图片资源  只有gif,jpg,png图片，TODO:需要添加更多类型
     * @param $imgUrl 图片地址
     */
    public function createImg($imgUrl)
    {
        $img_info = getimagesize($imgUrl);
        $img_type = $img_info[2];
        switch ($img_type) {
            case 1:
                $img = imagecreatefromgif($imgUrl);
                break;
            case 2:
                $img = imagecreatefromjpeg($imgUrl);
                break;
            case 3:
                $img = imagecreatefrompng($imgUrl);
                break;
            default:
                $img = false;
                break;
        }
        return $img;
    }

    /*
     * 无图片数据excel导出
     * @param $data 导出数据
     */
    public static function exportTxtExcel($data)
    {
        require_once _CORE_PHP_ . '/phpExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->fromArray($data, NULL, 'A1'); //将数组从A1开始填充

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('data');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        ob_get_clean();  // 框架原因，必须添加
        ob_clean();
        ob_end_clean();//清除缓冲区,Bom头，避免乱码和不能识别的文件类型
        $filename = date('Y-m-d') . '_' . mt_rand(1, 100) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /*
     *  带远程图片的excel导出
     * @param $data 导出的数据
     * @param $img_key 是图片的字段key值
     * @param $startCell 表格开始的单元格
     */
    public static function exportImgExcel($data, $img_key = array(), $startCell = 'A1')
    {
        require_once _CORE_PHP_ . '/phpExcel/PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // 开始单元格
        list ($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($startCell);
        // 循环数据
        foreach ($data as $rowData) {
            $currentColumn = $startColumn;
            foreach ($rowData as $key => $cellValue) {
                $cell = $objPHPExcel->getActiveSheet()->getCell($currentColumn . $startRow);
                if (in_array($key, $img_key)) {  // 图片写入
                    $img = self::createImg($cellValue);
                    if ($img == false) {
                        $cell->setValue($cellValue);
                    } else {
                        $width = imagesx($img);
                        $height = imagesy($img);
                        $objPHPExcel->getActiveSheet()->getColumnDimension($currentColumn)->setWidth($width / 5);
                        $objPHPExcel->getActiveSheet()->getRowDimension($startRow)->setRowHeight($height);
                        $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
                        $objDrawing->setName('image');
                        $objDrawing->setDescription('image');
                        $objDrawing->setCoordinates($currentColumn . $startRow);  // 传单元格位置，不能传单元格对象
                        $objDrawing->setImageResource($img);
                        $objDrawing->setOffsetX(10);
                        $objDrawing->setOffsetY(10);
                        $objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT);//渲染方法
                        $objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                        $objDrawing->setHeight($height);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                    }
                } else {
                    $cell->setValue($cellValue);
                }
                ++$currentColumn;
            }
            ++$startRow;
        }
        $objPHPExcel->getActiveSheet()->setTitle('data');
        $objPHPExcel->setActiveSheetIndex(0);
        ob_get_clean();  // 框架原因，必须添加
        ob_clean();
        ob_end_clean();//清除缓冲区,Bom头，避免乱码和不能识别的文件类型
        $filename = date('Y-m-d') . '_' . mt_rand(1, 100) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /*
     * @param $file 文件的全路径
     * */
    public static function readExcelToArray($file)
    {
        if (!$file) return new result(false, "Invalid Parameter:Empty File Info");
        if (!file_exists($file)) return new result(false, "Invalid File");
        $fi = pathinfo($file);
        $ext = strtoupper($fi["extension"]);
        require_once BASE_CORE_PATH . '/phpExcel/PHPExcel.php';
        //require_once _CORE_PHP_. '/phpExcel/Classes/PHPExcel/IOFactory.php';
        require_once BASE_CORE_PATH . '/phpExcel/PHPExcel/Reader/Excel5.php';
//        require_once _CORE_PHP_. 'phpexcel/Classes/PHPExcel/Reader/Excel2003XML.php';
        require_once BASE_CORE_PATH . '/phpExcel/PHPExcel/Reader/Excel2007.php';

        switch ($ext) {
            case "CSV":
                $reader = new PHPExcel_Reader_CSV();
                break;
            case "XLS":
                $reader = new PHPExcel_Reader_Excel5();
                break;
            case "XLSX":
                $reader = new PHPExcel_Reader_Excel2007();
                break;
            case "XML":
                $reader = new PHPExcel_Reader_Excel2003XML();
                break;
            default:
                return new result(false, "Invalid Extension Of File");
        }
        $PHPExcel = $reader->load($file);
        $currentSheet = $PHPExcel->getSheet(0);
        $allColumn = $currentSheet->getHighestColumn();
        $allRow = $currentSheet->getHighestRow();
        $all = array();
        for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
            $flag = 0;
            $col = array();
            for ($currentColumn = 'A'; self::getAscii($currentColumn) <= self::getAscii($allColumn); $currentColumn++) {
                $address = $currentColumn . $currentRow;
                $string = $currentSheet->getCell($address)->getValue();
                $col[$flag] = $string;
                $flag++;
            }
            $all[] = $col;
        }
        return new result(true, "", $all);
    }

    static function getAscii($ch)
    {  //读取字符串的ASCII码
        if (strlen($ch) == 1)
            return ord($ch) - 65;
        return ord($ch[1]) - 38;
    }
}