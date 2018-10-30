<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/11
 * Time: 17:39
 */
class industryClass
{


    /** 调查内容分组
     * @param array $research
     * @param array $type
     * @return array
     * @throws Exception
     */
    public static function sortResearchArrayByTypeArray( array $research,array $type)
    {
        if( !is_array($research) || !is_array($type) ){
            throw new Exception('Params need to be array.',errorCodesEnum::INVALID_PARAM);
        }

        if( empty($research) ){
            return $research;
        }
        $unknown_type = array();
        $sort_array = array();

        foreach( $research as $key=>$name ){
            $key_type = $type[$key];
            if( $key_type ){
                $sort_array[$key_type][$key] = $name;
            }else{
                $unknown_type[$key] = $name;
            }
        }


        $result_array = array();
        foreach( $sort_array as $t=>$t_array ){
            $result_array = $result_array + $t_array;
        }

        $result_array = $result_array + $unknown_type;
        return $result_array;
    }

    /** 按类型分组数据
     * @param $research_json
     * @param $type_json
     * @return string
     */
    public static function sortResearchJsonByTypeJson($research_json,$type_json)
    {

        $research = my_json_decode($research_json);
        $type = my_json_decode($type_json);
        $result_array = self::sortResearchArrayByTypeArray($research,$type);
        return json_encode($result_array);
    }




}