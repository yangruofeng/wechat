<style>

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>GL-Account</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('gl_tree', 'index', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Tree Style</span></a></li>
                <li><a class="current"><span>Table Style</span></a></li>
                <li><a href="<?php echo getUrl('gl_tree', 'showUserDefined', array(), false, BACK_OFFICE_SITE_URL)?>"><span>User Defined</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <?php
        $list=$output['node_list'];
        ?>
        <table class="table table-bordered">
            <tr class="table-header">
                <td style="width: 200px">GL CODE</td>
                <td>GL Name</td>
            </tr>
            <?php foreach($list as $item){?>
                <tr>
                    <td>
                        <?php echo $item['gl_code']?>
                    </td>
                    <td>
                        <?php echo $item['gl_name']?>
                    </td>
                </tr>
            <?php }?>
        </table>
    </div>
</div>
<script>
   function expendGlNode(_e){
       var _node=$(_e).closest(".list-group-item");
       var _node_sts=_node.data("sts");
       if(_node_sts=='1'){
           _gl_code=_node.data("gl-code");
           yo.dynamicTpl({
               tpl: "gl/node.display",
               dynamic: {
                   api: "gl_tree",
                   method: "getChildrenNode",
                   param: {parent_gl_code:_gl_code}
               },
               callback: function (_tpl) {
                   _node.find(".node-children").html(_tpl);
                   _node.find(".node-children").show();
                   _node.children(".input-group").find(".btn-i-style").removeClass("fa-chevron-circle-right").addClass("fa-chevron-circle-down");
                   _node.data("sts","3");
               }
           });

       }else if(_node_sts=="2"){
           _node.find(".node-children").show();
           _node.children(".input-group").find(".btn-i-style").removeClass("fa-chevron-circle-right").addClass("fa-chevron-circle-down");
           _node.data("sts","3");
       }else if(_node_sts=="3"){
           _node.find(".node-children").hide();
           _node.data("sts","2");
           _node.children(".input-group").find(".btn-i-style").removeClass("fa-chevron-circle-down").addClass("fa-chevron-circle-right");
       }
   }


</script>