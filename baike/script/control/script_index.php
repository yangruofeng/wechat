<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/14
 * Time: 11:24
 */
class script_indexControl
{
    public function indexOp()
    {
        echo 'Index.';
        die;
    }


    public function reinsertIndustryInfoOp()
    {

        set_time_limit(0);
        $m = new common_industryModel();
        $rows = $m->getRows(array(
            'uid' => array('>',0)
        ));
        $suc_arr = $err_arr = array();
        foreach( $rows as $row ){
           $r_json = my_json_decode($row->industry_json)?:array();
           $r_json_kh = my_json_decode($row->industry_json_kh)?:array();
           $r_type = my_json_decode($row->industry_json_type)?:array();

           $r_json_sort = industryClass::sortResearchArrayByTypeArray($r_json,$r_type);
           $r_json_kh_sort = industryClass::sortResearchArrayByTypeArray($r_json_kh,$r_type);

           $row->industry_json = json_encode($r_json_sort);
           $row->industry_json_kh = json_encode($r_json_kh_sort);
           $row->update_time = Now();
           $up = $row->update();
           if( $up->STS ){
               $suc_arr[] = $row->uid;
           }else{
               $err_arr[] = $row->uid;
           }
        }

        print_r(array(
            'suc' => $suc_arr,
            'err' => $err_arr
        ));
    }


}