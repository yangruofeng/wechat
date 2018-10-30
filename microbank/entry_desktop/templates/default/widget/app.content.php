<div id="content">
    <!--breadcrumbs-->
    <div id="content-header">
        <div id="breadcrumb">
            <?php if ($output['is_operator']) { ?>
                <a href="#" class="tip-bottom processing_task" link="<?php echo $output['processing_task']['url']?>" style="cursor: pointer;color: red;font-weight: 600">
                    <i class="fa fa-tasks"></i>
                    <span>Processing Task: </span>
                    <span id="task_name"><?php echo $output['processing_task']['title'] ?></span>
                </a>
                <a class="tip-bottom cancel_processing" style="font-weight: 600;padding-left: 0;display: none" link=""><i class="fa fa-remove" style="margin-right: 1px"></i>Cancel</a>
            <?php } else if ($output['is_sub']) { ?>
                <a href="#" class="tip-bottom" style="cursor: default">
                    <i class="fa fa-home"></i>
                    <span>Home</span>
                    <i class="fa fa-angle-right"></i>
                    <span class="title-2"></span>
                </a>
            <?php } else { ?>
                <a href="#" class="tip-bottom" style="cursor: default">
                    <i class="fa fa-home"></i>
                    <span>Home</span>
                    <i class="fa fa-angle-right"></i>
                    <span class="title-2"></span>
                    <i class="fa fa-angle-right"></i>
                    <span class="title-3"></span>
                </a>
                <i class="fa fa-arrows-alt" id="full_screen" title="Full screen" style="cursor:pointer;float: right;margin-top: 15px;margin-right: 30px"></i>
            <?php } ?>
        </div>
    </div>
    <video id="task-hint" src="resource/video/hint.mp3" style="display: none" preload="auto"></video>
    <iframe src="" id="iframe-main" frameborder='0' style="width:100%;"></iframe>
</div>
