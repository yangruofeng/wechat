<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $output['html_title']; ?></title>

   <!-- <link rel="stylesheet" href="<?php /*echo GLOBAL_RESOURCE_SITE_URL; */?>/ztree_master_v3/css/metroStyle/metroStyle.css" type="text/css">-->

    <link rel="stylesheet" href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/ztree_master_v3/css/zTreeStyle/zTreeStyle.css" type="text/css">

    <script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery214.js"></script>
    <script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/ztree_master_v3/js/jquery.ztree.all.min.js"></script>

    <style>

        #div_add_node{
            display: none;
            margin: 10px 0;
        }

        .content_wrap{
            background-color: #f3dbdb;
        }

        .btn-confirm{
            display: inline-block;
            text-decoration: none;
            padding: 2px 12px;
            background-color: red;
            color: #fff;
        }

        .btn-cancel{
            display: inline-block;
            text-decoration: none;
            padding: 2px 12px;
            background-color: #ccc;
            color: #000;
        }



    </style>
</head>
<body>


<div class="content_wrap">

    <div class="zTreeDemoBackground left">
        <ul id="treeDemo" class="ztree"></ul>
    </div>

</div>


<?php
//$file =  BASE_CORE_PATH.'/templates/widget/rclick.select.metro.php';
 $file = template(':widget/rclick.select.metro');
require_once($file);
?>


<script>

    var API_SITE_URL = '<?php echo API_SITE_URL; ?>';
    var nodeNewCount = 1;

    var setting = {

       /* async: {
            enable: true,
            type: 'post',
            url: API_SITE_URL+"/index.php?act=tree&op=getNodes",
            autoParam:["id", "name", "level"], // 设置 level=lv 表示服务器只接受lv参数
            otherParam:{"otherParam":"zTreeAsyncTest"},
            dataFilter: filter
        },*/

        check: {
            enable: false
        },
        edit:{
            enable: true,
            drag: {   // 禁止拖曳
                isCopy: false,
                isMove: false
            }
        },
        view: {
            dblClickExpand: true,
            selectedMulti: false,
            addHoverDom: zTreeAddHoverDom,  // 新增按钮，高级应用，需要自定义,同removeHoverDom: removeHoverDom 一起使用
            removeHoverDom: zTreeRemoveHoverDom
        },
        data: {
            simpleData: {
                enable: true
            }
        },
        callback: {
            beforeRename: zTreeBeforeRename,
            beforeRemove: zTreeBeforeRemove
        }
    };

    var zNodes =[
        {id:1, pId:0, name:"中国",open:true},
        {id:2, pId:1, name:"天津"},
        {id:3, pId:1, name:"上海"},
        {id:6, pId:1, name:"重庆"},
        {id:4, pId:1, name:"河北省", open:true},
        {id:41, pId:4, name:"石家庄"},
        {id:42, pId:4, name:"保定"},
        {id:43, pId:4, name:"邯郸"},
        {id:44, pId:4, name:"承德"},
        {id:5, pId:1, name:"广东省", open:true},
        {id:51, pId:5, name:"广州"},
        {id:52, pId:5, name:"深圳"},
        {id:53, pId:5, name:"东莞"},
        {id:54, pId:5, name:"佛山"},
        {id:6, pId:1, name:"福建省", open:true},
        {id:61, pId:6, name:"福州"},
        {id:62, pId:6, name:"厦门"},
        {id:63, pId:6, name:"泉州"},
        {id:64, pId:6, name:"三明"}
    ];





    function _getTreeObj(id)
    {
        return $.fn.zTree.getZTreeObj(id);
    }


    function _addNewNode(treeId,treeNode){

        var zTree =  _getTreeObj(treeId);
        var param = {};
        param.name = 'new node ' + nodeNewCount;
        if( treeNode ){
            param.pid = treeNode.id;
        }else{
            param.pid = null;
        }

        var treePid = param.pid;


        $.post(API_SITE_URL+'/index.php?act=tree&op=addNewNode',param,function(data){
            if( data.STS ){
                nodeNewCount++;
                var new_node = data.DATA;
                zTree.addNodes(treeNode, {id:new_node.id,pid:treePid, name:new_node.name});
                alert('API新增成功');

            }else{
                alert('API新增失败');
            }

        },'json');

    }



    function _delNode(treeId,treeNode){

        var zTree =  _getTreeObj(treeId);
        if( confirm('子节点一并删除，确定要删除吗？') ){
            var param = {};

            console.log(treeNode);

            param.id = treeNode.id;
            param.pid = treeNode.pId;

            $.post(API_SITE_URL+'/index.php?act=tree&op=delNode',param,function(data){
                if( data.STS ){

                    alert('API删除成功');
                    return true;

                }else{
                    alert('API删除失败');
                    return false;
                }

            },'json');

        }else{
            return false;
        }


    }

    function _renameNode(treeId,treeNode,newName){

        console.log(treeNode);
        console.log(treeNode.name);
        console.log(newName);
        var zTree =  _getTreeObj(treeId);

        var param = {};
        var pre_name = treeNode.name;
        param.id = treeNode.id;
        param.name = treeNode.name;
        param.new_name = newName;
        $.post(API_SITE_URL+'/index.php?act=tree&op=nodeRename',param,function(data){

            if( data.STS ){
                alert( "api修改成功，new name:  " + newName);
            }else{
                alert( "api修改失败, pre name: " + pre_name);
                treeNode.name = pre_name;
                zTree.updateNode(treeNode);
                console.log(pre_name);

            }

            return true;
        },'json');

    }




    $(document).ready(function(){

        $.fn.zTree.init($("#treeDemo"), setting,zNodes);


    });
</script>
</body>
</html>