<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/4
 * Time: 11:32
 */
class phpTreeClass
{

    protected static $config = array(
        /* 主键 */
        'primary_key' 	=> 'id',
        /* 父键 */
        'parent_key'  	=> 'parent_id',
        /* 展开属性 */
        'expanded_key'  => 'expanded',
        /* 叶子节点属性 */
        'leaf_key'      => 'leaf',
        /* 孩子节点属性 */
        'children_key'  => 'children',
        /* 是否展开子节点 */
        'expanded'    	=> false
    );

    /* 结果集 */
    protected static $result = array();

    /* 层次暂存 */
    protected static $level = array();

    /**
     * @name 生成树形结构
     * @param array @二维数组
     * @return mixed 多维数组
     */
    public static function makeTree($rook_key,$data,$options=array()){
        self::$result = array();  // 销毁残留数据，防止重复调用bug
        self::$level = array();
        $dataset = self::buildData($data,$options);

        $r = self::makeTreeCore($rook_key,$dataset,'normal');
        return $r;
    }

    /* 生成线性结构, 便于HTML输出, 参数同上 */
    public static function makeTreeForHtml($root_key,$data,$options=array()){
        self::$result = array();   // 销毁残留数据，防止重复调用bug
        self::$level = array();
        $dataset = self::buildData($data,$options);
        $r = self::makeTreeCore($root_key,$dataset,'linear');
        return $r;
    }

    /* 格式化数据, 私有方法 */
    private static function buildData($data,$options){
        $config = array_merge(self::$config,$options);
        self::$config = $config;
        $primary_key = $config['primary_key'];
        $parent_key = $config['parent_key'];

        $r = array();
        foreach($data as $item){

            $id = $item[$primary_key]?:0;
            $parent_id = $item[$parent_key]?:0;
            $r[$parent_id][$id] = $item;
        }

        return $r;
    }

    /* 生成树核心, 私有方法  */
    private static function makeTreeCore($index,$data,$type='normal')
    {

        $config = self::$config;
        $expanded_key = $config['expanded_key'];
        $children_key = $config['children_key'];
        $parent_key = $config['parent_key'];
        $leaf_key = $config['leaf_key'];
        $r = array();


        if( is_array($index) ){
            $root_keys = $index;
        }else{
            $root_keys = array($index);
        }

        foreach( $root_keys as $key ){

            //$idx = $key;  // 规定父级的level
            foreach($data[$key] as $id=>$item)
            {
                if($type=='normal'){

                    $parent_id = $item[$parent_key];
                    self::$level[$id] = self::$level[$parent_id]+1;
                    //self::$level[$id] = $idx==0?0:self::$level[$parent_id]+1;
                    $item['level'] = self::$level[$id];


                    if(isset($data[$id]))
                    {
                        $item[$expanded_key]= self::$config['expanded'];
                        $item[$children_key]= self::makeTreeCore($id,$data,$type);
                    }
                    else
                    {
                        $item[$leaf_key]= true;
                    }

                    $r[] = $item;

                }else if($type=='linear'){
                    $parent_id = $item[$parent_key];

                    self::$level[$id] = self::$level[$parent_id]+1;
                    //self::$level[$id] = $idx==self::$level[$parent_id]?0:self::$level[$parent_id]+1;
                    $item['level'] = self::$level[$id];
                    self::$result[] = $item;
                    if(isset($data[$id])){
                        self::makeTreeCore($id,$data,$type);
                    }

                    $r = self::$result;
                }
            }
        }

        return $r;
    }
}