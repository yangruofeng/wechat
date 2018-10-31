<?php if(count($output['client_relative'])){?>
<div class="panel panel-default" style="margin-top: 0;margin-bottom: 20px">
    <div class="panel-heading">
        <h5 class="panel-title"> <i class="fa fa-id-card-o"></i>Client Relative</h5>
    </div>
    <table class="table table-bordered">
        <?php foreach($output['client_relative'] as $rel){?>
            <tr>
                <td>
                    <a href="<?php echo getImageUrl($rel['headshot']) ?>" target="_blank" title="Head portraits">
                        <img class="img-icon" style="width: 60px;height: 60px;"
                             src="<?php echo getImageUrl($rel['headshot'], imageThumbVersion::MAX_120); ?>">
                    </a>
                </td>
                <td>
                    <ul>
                        <li>
                            <label><?php echo $rel['name']?></label>
                        </li>
                        <li>
                            <?php echo $rel['relation_type']." / ".$rel['relation_name']?>
                        </li>
                    </ul>
                </td>
                <td><?php echo $rel['contact_phone']?></td>
                <td>
                    <a class="btn btn-default"
                       href="<?php echo getUrl('web_credit', 'showMemberCbcDetail', array("member_id"=>$client_info['uid'],'client_id'=>$rel['uid'],"client_type"=>1,"is_readonly"=>1), false, BACK_OFFICE_SITE_URL);?>">
                        CBC
                    </a>
                </td>
            </tr>
        <?php }?>
    </table>
</div>
<?php }?>
