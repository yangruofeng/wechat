<ul class="list-group">
    <?php foreach($data as $node){?>
        <li class="list-group-item node-<?php echo $node['gl_code']?>" style="border: none;padding-top: 2px;padding-bottom: 2px;padding-left: 20px" data-sts="1" data-gl-code="<?php echo $node['gl_code']?>">
            <div class="input-group">
                <span class="input-group-addon" style="width: 150px;text-align: left">
                    <?php echo $node['gl_code']?>
                </span>
                <span class="input-group-addon" style="width: 300px;text-align: left">
                    <?php echo $node['gl_name']?>
                </span>
                <?php if(!$node['is_leaf']){?>
                    <div class="input-group-btn" style="width: auto">
                        <button class="btn btn-default" onclick="expendGlNode(this)"><i class="fa btn-i-style fa-chevron-circle-right"></i></button>
                    </div>
                <?php }?>
            </div>
            <?php if(!$node['is_leaf']){?>
                <div class="node-children" style="display: none">
                </div>
            <?php }?>
        </li>
    <?php }?>
</ul>
