<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container">
        <div class="collection-div">
            <div class="basic-info">
                <?php include(template("widget/item.member.summary.v2"))?>
            </div>
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Fingerprint Information</h5>
                </div>
                <div class="content">
                   <table class="table table-bordered table-hover">
                       <tr>
                           <td>Image</td>
                           <td>Create Time</td>
                           <td>Operator</td>
                           <td>Remark</td>
                       </tr>
                       <?php foreach($output['history'] as $item){?>
                        <tr>
                            <td>
                                <div class="thumbnail">
                                    <?php
                                    $image_item=$item['member_image'];
                                    include(template(":widget/item.image.viewer.item"));
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php echo $item['create_time']?:'N/A'?>
                            </td>
                            <td>
                                <?php echo $item['operator_name']?:'N/A'?>
                            </td>
                            <td>
                                <?php echo $item['scene_code'].' / '.$item['scene_id']?>
                            </td>
                        </tr>
                       <?php }?>
                   </table>
                </div>
            </div>
            <div class="operation" style="margin-bottom: 40px">
                <a  class="btn btn-default"  href="<?php echo getUrl('member_index', 'index', array('member_id'=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL) ?>"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
            </div>
        </div>

    </div>
</div>