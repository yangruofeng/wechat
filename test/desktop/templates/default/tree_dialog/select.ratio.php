<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $output['html_title']; ?></title>

    <link rel="stylesheet" href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/ztree_master_v3/css/zTreeStyle/zTreeStyle.css" type="text/css">
    <script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery214.js"></script>
    <script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/ztree_master_v3/js/jquery.ztree.all.min.js"></script>
</head>
<body>


<div>
    <div>
        选中：<span id="checked_html_span"></span>
    </div>
    Test: <input type="text" readonly title="select name" name="select_name" id="input_select_name" onclick="_showSelectMenu();" /> <a href="#" onclick="_showSelectMenu();">选择</a>

</div>

<div>
    <div id="menuContent" class="menuContent" style="display:none; position: absolute;background-color: #fdf9f9;">
        <ul id="treeDemo" class="ztree" style="margin-top:0; width:180px; height: 300px;">

        </ul>
    </div>
</div>


<script>

    function onBodyKeyDown(){
        if (!(event.target.id == "menuBtn" || event.target.id == "citySel" || event.target.id == "menuContent" || $(event.target).parents("#menuContent").length>0)) {
            _hideSelectMenu();
        }
    }

    function treeOnClick(event,treeId,treeNode){
        var zTree = $.fn.zTree.getZTreeObj("treeDemo");
        zTree.checkNode(treeNode, !treeNode.checked, null, true);
        return false;
    }


    function treeOnCheck(event,treeId,treeNode){

        //console.log(treeId);
        //console.log(treeNode);

        var text = 'id: '+treeNode.id+' ; pid: '+treeNode.pId+' ; name: '+treeNode.name+' ; level: '+treeNode.level;
        $('#checked_html_span').text(text);
        $('#input_select_name').val(treeNode.name);
    }

    function _showSelectMenu()
    {
        var cityObj = $("#input_select_name");
        var cityOffset = $("#input_select_name").offset();
        $("#menuContent").css({left:cityOffset.left + "px", top:cityOffset.top + cityObj.outerHeight() + "px"}).fadeIn("fast");

        $("body").bind("mousedown", onBodyKeyDown);
    }

    function _hideSelectMenu()
    {
        $("#menuContent").fadeOut("fast");
        $("body").unbind("mousedown", onBodyKeyDown);
    }



    var setting = {
        check: {
            enable: true,
            chkStyle: "radio",
            radioType: "all"
        },
        view: {
            dblClickExpand: false
        },
        data: {
            simpleData: {
                enable: true
            }
        },
        callback: {
            onClick: treeOnClick,
            onCheck: treeOnCheck
        }
    };
    var zNodes =[
        {id:4, pId:0, name:"河北省", open:true, nocheck:false},  // nocheck true
        {id:41, pId:4, name:"石家庄"},
        {id:42, pId:4, name:"保定"},
        {id:43, pId:4, name:"邯郸"},
        {id:44, pId:4, name:"承德"},
        {id:5, pId:0, name:"广东省", open:true, nocheck:false},
        {id:51, pId:5, name:"广州"},
        {id:52, pId:5, name:"深圳"},
        {id:53, pId:5, name:"东莞"},
        {id:54, pId:5, name:"佛山"},
        {id:6, pId:0, name:"福建省", open:true, nocheck:false},
        {id:61, pId:6, name:"福州"},
        {id:62, pId:6, name:"厦门"},
        {id:63, pId:6, name:"泉州"},
        {id:64, pId:6, name:"三明"}
    ];

    $(document).ready(function(){

        $.fn.zTree.init($("#treeDemo"), setting, zNodes);

    });
</script>
</body>
</html>