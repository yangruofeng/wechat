
<?php $list = $output['list']; ?>

<style>
    body{
        font-size: 18px;
    }
    .icon{
        color: #e6c514;
    }
</style>

<div class="page">
    <div>
        <ul>
            <?php foreach( $list as $v ){ ?>
                <li>
                    <?php if( $v['is_dir'] ){ ?>
                        <i class="fa fa-folder-open icon"></i>
                        <a href="<?php echo $v['request_url']; ?>">
                            <?php echo $v['name']; ?>
                        </a>
                    <?php }else{ ?>
                        <i class="fa fa-file icon"></i>
                        <a href="<?php echo $v['file_url']; ?>">
                            <?php echo $v['name']; ?>
                        </a>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
