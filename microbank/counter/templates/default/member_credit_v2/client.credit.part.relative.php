<div class="panel panel-default"  style="min-height: 350px">
    <div class="panel-heading">
        <h5 class="panel-title"><i class="fa fa-user-circle-o"></i> Relative</h5>
    </div>
    <?php if ($detail['relative_list']) { ?>
        <ul class="list-group">
            <?php foreach($detail['relative_list'] as $rel){?>
                <li class="list-group-item">
                    <table class="table table-no-background">
                        <tr>
                            <td rowspan="3">
                                <a href="<?php echo getImageUrl($rel['headshot']) ?>" target="_blank" title="Head portraits">
                                    <img class="img-icon"
                                         src="<?php echo getImageUrl($rel['headshot'], imageThumbVersion::SMALL_ICON) ?>">
                                </a>
                            </td>
                            <td> <label><?php echo $rel['name']?></label></td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $rel['relation_type']." / ".$rel['relation_name']?>
                            </td>
                            <td>
                                <?php echo $rel['contact_phone']?>
                            </td>
                        </tr>
                    </table>
                </li>
            <?php }?>
        </ul>
    <?php }else{?>
        <div>
           <?php include(template(":widget/no_record"))?>
        </div>
    <?php }?>
</div>