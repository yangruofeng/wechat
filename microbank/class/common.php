<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class common
{
    private static $date_format = 1;

    function __construct()
    {
        $m_core_dictionary = M('core_dictionary');
        $data = $m_core_dictionary->getDictionary('global_settings');
        if ($data) {
            $global_settings = my_json_decode($data['dict_value']);
            $date_format = $global_settings['date_format'];
        } else {
            $date_format = 1;
        }
        self::$date_format = $date_format;
    }

    /**
     * 格式化日期
     * @param $date
     * @param $connector
     * @return bool|string
     */
    public static function dateFormat($date, $connector = '/')
    {
        if (self::$date_format == 1) {
            return $date ? date('d' . $connector . 'm' . $connector . 'Y', strtotime($date)) : '';
        } else {
            return $date ? date('Y-m-d', strtotime($date)) : '';
        }
    }

    public static function timeFormat($time)
    {
        if (self::$date_format == 1) {
            return $time ? date('d/m/Y H:i:s', strtotime($time)) : '';
        } else {
            return $time ? date('Y-m-d H:i:s', strtotime($time)) : '';
        }
    }

    /**
     * 导出excel
     * @param $title
     * @param $cellName
     * @param $data
     * @param string $filter
     */
    public static function exportDataToExcel($title, $cellName, $data, $filter = '')
    {
        //引入核心文件
        require_once BASE_CORE_PATH . '/phpExcel/PHPExcel.php';
        $objPHPExcel = new \PHPExcel();

        //定义配置
        $topNumber = 2;//表头有几行占用
        $xlsTitle = $title;
        $fileName = $title . '-' . date('Y-m-d');//文件名称
        $cellKey = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM',
            'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'
        );

        //写在处理的前面（了解表格基本知识，已测试）
//     $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);//所有单元格（行）默认高度
//     $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);//所有单元格（列）默认宽度
//     $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);//设置行高度
//     $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);//设置列宽度
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);//设置文字大小
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);//设置是否加粗
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);// 设置文字颜色
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//设置文字居左（HORIZONTAL_LEFT，默认值）中（HORIZONTAL_CENTER）右（HORIZONTAL_RIGHT）
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);//设置填充颜色
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('FF7F24');//设置填充颜色

        //处理表头标题
        $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $cellKey[count($cellName) - 1] . '1');//合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $xlsTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //设置筛选条件
        if ($filter) {
            $objPHPExcel->getActiveSheet()->mergeCells('A2:' . $cellKey[count($cellName) - 1] . '2');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $filter);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(false);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
//            $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            ++$topNumber;
        }

        //处理表头
        foreach ($cellName as $k => $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k] . $topNumber, $v[1]);//设置表头数据
//            $objPHPExcel->getActiveSheet()->freezePane($cellKey[$k] . ($topNumber + 1));//冻结窗口
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k] . $topNumber)->getFont()->setBold(true);//设置是否加粗
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k] . $topNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
            if ($v[2] > 0)//大于0表示需要设置宽度
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cellKey[$k])->setWidth($v[2]);//设置列宽度
            }
        }
        //处理数据
        foreach ($data as $k => $v) {
            $row_number = $k + 1 + $topNumber;
            if ($v['is_merge_row']) {//合并一行显示
                $objPHPExcel->getActiveSheet()->mergeCells("A$row_number:" . $cellKey[count($cellName) - 1] . $row_number);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$row_number", $v['content']);
                if ($v['is_bold']) $objPHPExcel->getActiveSheet()->getStyle("A$row_number")->getFont()->setBold(true);
                continue;
            }
            $data = $v['data'];
            foreach ($cellName as $k1 => $v1) {
                $objPHPExcel->getActiveSheet()->setCellValue($cellKey[$k1] . ($row_number), $data[$v1[0]]);
                if ($v1[3] != "" && in_array($v1[3], array("LEFT", "CENTER", "RIGHT"))) {
                    $v1[3] = eval('return PHPExcel_Style_Alignment::HORIZONTAL_' . $v1[3] . ';');
                    if ($v['is_bold']) $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1] . ($row_number))->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1] . ($row_number))->getAlignment()->setHorizontal($v1[3]);
                }
            }
        }

        //导出execl
//        header('pragma:public');
//        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
//        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
//        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//        $objWriter->save('php://output');

        ob_get_clean();
        ob_clean();
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        exit;
    }

}