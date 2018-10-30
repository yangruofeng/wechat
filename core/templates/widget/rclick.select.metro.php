
<!--引用前，先初始化变量 zTree  -->
<!--引用此公用部分，只要重写几个操作方法就可以了  _addNewNode(treeNode),_delNode(treeNode) ,_renameNode(treeId,treeNode,newName)   -->

<!--菜单区域-->
<style>
    div#zTree_rMenu {
        display: block;
        position:absolute;
        visibility: hidden;
        top:0;
        background-color: #555;
        text-align: left;
        z-index: 99;
    }

    div#zTree_rMenu ul li{
        font-size: 12px;
        padding: 5px;
        cursor: pointer;
        text-align: left;
        list-style: none;
        vertical-align: baseline;
        background-color: #F5F6FA;
        color: #000;
        border: solid 1px #ddd;
    }

    div#zTree_rMenu ul li:hover{
        background-color: #ccc;
    }
</style>
<div id="zTree_rMenu">
    <ul>
        <li id="zTr_m_add" >Add</li>
        <li id="zTr_m_del" >Delete</li>
        <li id="zTr_m_rename">Edit</li>
    </ul>
</div>

<script>

    /**
     * 参数过滤器
     * @param treeId
     * @param parentNode
     * @param childNodes
     * @returns {*}
     */
    function filter(treeId, parentNode, childNodes) {
        //console.log(treeId);
        //console.log(parentNode);
        //console.log(childNodes);
        /*if (!childNodes) return null;
         for (var i=0, l=childNodes.length; i<l; i++) {
         childNodes[i].name = childNodes[i].name.replace(/\.n/g, '.');
         }*/
        return childNodes;
    }

    /**
     *  鼠标右键单击显示菜单
     */
    function zTreeOnRightClick(event, treeId, treeNode) {

        var zTree = zTreeGetObj(treeId);

        if (!treeNode && event.target.tagName.toLowerCase() != "button" && $(event.target).parents("a").length == 0) {
            zTree.cancelSelectedNode();
            showRMenu("root", event.clientX, event.clientY);

        } else if (treeNode && !treeNode.noR) {
            zTree.selectNode(treeNode);
            showRMenu("node", event.clientX, event.clientY);
        }

        $('#zTr_m_add').off().on('click',function(){
            hideRMenu();
            _addNewNode(treeId,treeNode);
        });

        $('#zTr_m_del').off().on('click',function(){
            hideRMenu();
            zTree.removeNode(treeNode,true);  // 触发回调
        });
        $('#zTr_m_rename').off().on('click',function(){
            hideRMenu();
            zTree.editName(treeNode);
        });

    }

    function showRMenu(type, x, y) {
        $("#zTree_rMenu ul").show();

        if (type=="root") {
            $('#zTr_m_del').hide();
            $('#zTr_m_rename').hide();
        } else {
            $('#zTr_m_del').show();
            $('#zTr_m_rename').show();
        }

        y += document.body.scrollTop;
        x += document.body.scrollLeft;
        $('#zTree_rMenu').css({"top":y+"px", "left":x+"px", "visibility":"visible"});

        $("body").on("mousedown", onBodyMouseDown);
    }

    function hideRMenu() {
        var rMenu =  $('#zTree_rMenu');
        if (rMenu) {
            rMenu.css({"visibility": "hidden"});
        }
        $("body").off("mousedown", onBodyMouseDown);
    }

    function onBodyMouseDown(event){
        var rMenu =  $('#zTree_rMenu');
        if (!(event.target.id == "zTree_rMenu" || $(event.target).parents("#zTree_rMenu").length>0)) {
            rMenu.css({"visibility" : "hidden"});
        }
    }

    function zTreeGetObj(id){
        return $.fn.zTree.getZTreeObj(id);
    }

    /**
     * 鼠标左键单击选中节点
     */
    function zTreeOnClick(event,treeId,treeNode){
        var zTree = zTreeGetObj(treeId);
        zTree.checkNode(treeNode, !treeNode.checked, true, true);  // 第三个参数是父子的连选
        return false;
    }

    /*function addTreeNode() {
        hideRMenu();
        var treeNode = null;
        if (zTree.getSelectedNodes()[0]) {
            treeNode = zTree.getSelectedNodes()[0];
        }
        _addNewNode(treeNode);

    }

    function renameTreeNode(){
        hideRMenu();
        var treeNode = null;
        if (zTree.getSelectedNodes()[0]) {
            treeNode = zTree.getSelectedNodes()[0];
            zTree.editName(treeNode);
        }
    }

    function removeTreeNode(){
        hideRMenu();
        var treeNode = null;
        if (zTree.getSelectedNodes()[0]) {
            treeNode = zTree.getSelectedNodes()[0];
            zTree.removeNode(treeNode,true);  // 触发回调
        }

    }*/


    /**
     * 修改节点前的操作方法
     */
    function zTreeBeforeRename(treeId, treeNode, newName, isCancel) {
        if( isCancel ){
            return true;
        }
        return _renameNode(treeId,treeNode,newName);

    }

    /**
     * 删除节点前的操作方法
     *  返回 true 删除节点， 返回false 不删除
     * @param treeId
     * @param treeNode  被删除节点
     * @returns true/false
     */
    function zTreeBeforeRemove(treeId, treeNode) {
        return _delNode(treeId,treeNode);
    }

    /**
     * 显示悬浮操作菜单方法
     * @param treeId
     * @param treeNode
     */
    function zTreeAddHoverDom(treeId, treeNode) {
        //console.log(treeId);  //  treeDemo
        //console.log(treeNode);  // 父 node
        var sObj = $("#" + treeNode.tId + "_span");
        if (treeNode.editNameFlag || $("#addBtn_"+treeNode.tId).length>0) return;
        var addStr = "<span class='button add' id='addBtn_" + treeNode.tId
            + "' title='add node' onfocus='this.blur();'></span>";
        sObj.after(addStr);
        var btn = $("#addBtn_"+treeNode.tId);

        if (btn.length > 0 ) {

            btn.off().on("click", function(){
                _addNewNode(treeId,treeNode);
                return false;
            });
        }

    }

    /**
     *  悬浮移开隐藏菜单
     * @param treeId
     * @param treeNode
     */
    function zTreeRemoveHoverDom(treeId, treeNode) {
        $("#addBtn_"+treeNode.tId).off().remove();
    }


</script>

