<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/10/17
 * Time: 13:41
 */

class treeControl extends control
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getTestDataOp()
    {
        $data = array();

        $p_data = array();

        for( $i=1;$i<=5;$i++){
            $tem = array(
                'id' => $i,
                'pId' => null,
                'name' => 'Node '.$i,
            );
            $data[] = $tem;
            $p_data[$i] = $tem;
        }

        foreach( $p_data as $k=>$v ){
            $mt_len = mt_rand(1,4);
            for( $x=1;$x<=$mt_len;$x++){
                $tem = array(
                    'id' => $k.$x,
                    'pId' => $k,
                    'name' => 'Node '.$k.$x,
                );

                $data[] = $tem;
            }
        }

        echo json_encode($data);
    }

    public function getSmallDataOp()
    {

        $data_str = '';
        $data_str .= '[';

        $pId = "0";
        $pName = "";
        $pLevel = "";
        $pCheck = "";
        if(array_key_exists( 'id',$_REQUEST)) {
            $pId=$_REQUEST['id'];
        }
        if(array_key_exists( 'lv',$_REQUEST)) {
            $pLevel=$_REQUEST['lv'];
        }
        if(array_key_exists('n',$_REQUEST)) {
            $pName=$_REQUEST['n'];
        }
        if(array_key_exists('chk',$_REQUEST)) {
            $pCheck=$_REQUEST['chk'];
        }
        if ($pId==null || $pId=="") $pId = "0";
        if ($pLevel==null || $pLevel=="") $pLevel = "0";
        if ($pName==null) $pName = "";
       // else $pName = $pName.".";

        $pId = htmlspecialchars($pId);

        $pName = htmlspecialchars($pName);

        for ($i=1; $i<5; $i++) {
            $nId = $pId.$i;
            $nName = $pName."n".$i;
            $data_str .= "{ id:'".$nId."',	name:'".$nName."',	isParent:".(( $pLevel < "2" && ($i%2)!=0)?"true":"false").($pCheck==""?"":((($pLevel < "2" && ($i%2)!=0)?", halfCheck:true":"").($i==3?", checked:true":"")))."}";
            if ($i<4) {
               $data_str .= ",";
            }
        }

        $data_str .= ']';
        echo $data_str;

    }

    public function getBigDataOp()
    {
        $data_str = '';
        $data_str .= '[';

        $pId = "-1";
        if(array_key_exists( 'id',$_REQUEST)) {
            $pId=$_REQUEST['id'];
        }
        $pCount = "1000";
        if(array_key_exists( 'count',$_REQUEST)) {
            $pCount=$_REQUEST['count'];
        }
        if ($pId==null || $pId=="") $pId = "0";
        if ($pCount==null || $pCount=="") $pCount = "10";

        $pId = htmlspecialchars($pId);

        $max = (int)$pCount;
        for ($i=1; $i<=$max; $i++) {
            $nId = $pId."_".$i;
            $nName = "tree".$nId;
            $data_str .= "{ id:'".$nId."',	name:'".$nName."'}";
            if ($i<$max) {
                $data_str .= ",";
            }

        }

        $data_str .= ']';
        echo $data_str;
    }

    public function getNodesOp()
    {

$nodes = <<<EOT
{ id:1, pId:0, name:"父节点1", open:true},
{ id:11, pId:1, name:"父节点11"},
{ id:111, pId:11, name:"叶子节点111"},
{ id:112, pId:11, name:"叶子节点112"},
{ id:113, pId:11, name:"叶子节点113"},
{ id:114, pId:11, name:"叶子节点114"},
{ id:12, pId:1, name:"父节点12"},
{ id:121, pId:12, name:"叶子节点121"},
{ id:122, pId:12, name:"叶子节点122"},
{ id:123, pId:12, name:"叶子节点123"},
{ id:124, pId:12, name:"叶子节点124"},
{ id:13, pId:1, name:"父节点13", isParent:true},
{ id:2, pId:0, name:"父节点2"},
{ id:21, pId:2, name:"父节点21", open:true},
{ id:211, pId:21, name:"叶子节点211"},
{ id:212, pId:21, name:"叶子节点212"},
{ id:213, pId:21, name:"叶子节点213"},
{ id:214, pId:21, name:"叶子节点214"},
{ id:22, pId:2, name:"父节点22"},
{ id:221, pId:22, name:"叶子节点221"},
{ id:222, pId:22, name:"叶子节点222"},
{ id:223, pId:22, name:"叶子节点223"},
{ id:224, pId:22, name:"叶子节点224"},
{ id:23, pId:2, name:"父节点23"},
{ id:231, pId:23, name:"叶子节点231"},
{ id:232, pId:23, name:"叶子节点232"},
{ id:233, pId:23, name:"叶子节点233"},
{ id:234, pId:23, name:"叶子节点234"},
{ id:3, pId:0, name:"父节点3", isParent:true}
EOT;

        echo '['.$nodes.']';  // 输出js数组格式


    }

    public function nodeRenameOp()
    {
        $param = $_POST;
        $id = $param['id'];
        $new_name = $param['new_name'];
        return new result(true,'',$param);
    }

    public function addNewNodeOp()
    {
        $param = $_POST;
        $pid = $param['pid'];
        $name = $param['name'];

        return new result(true,'',array(
            'id' => 1000+mt_rand(1000,10000),
            'pid' => $pid,
            'name' => $name
        ));
    }

    public function delNodeOp()
    {
        $param = $_POST;
        $id = $param['id'];
        $pid = $param['pid'];
        // todo 父元素节点id，需要将子节点也删除掉
        return new result(true,'',$param);
    }


}