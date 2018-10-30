<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>

    <div class="container" style="min-height: 600px;background-color: #ffffff">
        <?php
        $data=$output['node_list'];
        if(!$data){
            include_once(template(":widget/no_record"));
        }else{
            include(template("gl/node.display"));
        }

        ?>
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