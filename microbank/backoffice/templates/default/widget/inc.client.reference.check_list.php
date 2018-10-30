<div role="tabpanel" class="tab-pane" id="identity_info">
    <div class="clearfix" style="background-color: #FFF;padding: 10px 15px">
        <?php
        $at_list = memberIdentityClass::getIdentityType();
        foreach ($at_list as $a_key => $a_value) { ?>
            <div style="padding-top: 15px"><label><?php echo $a_value ?></label></div>
            <div class="col-sm-12 content" style="padding: 10px 0px 20px 0px">
                <?php if ($check_list[$a_key]) { ?>
                    <div style="background-color: #f3f4f6;padding: 10px">
                        <div>
                            <span style="font-weight: 600;padding-right: 30px">Cert-Id：<?php echo $check_list[$a_key]['cert_sn']?:'--'?></span>
                            <span style="font-weight: 600;padding-right: 30px">Audit Time：<?php echo timeFormat($check_list[$a_key]['auditor_time'])?></span>
                            <a style="font-weight: 600;" href="javascript:void(0)" onclick="showCheckDetail('<?php echo $member_id?>','<?php echo $a_key?>')">Detail</a>
                        </div>
                        <?php
                        $image_list=array();
                        foreach($check_list[$a_key]['images'] as $img_item){
                            $image_list[] = array(
                                'url' => $img_item['image_url'],
                                'image_source' => $img_item['image_source'],
                            );
                        }
                        include(template(":widget/item.image.viewer.list"));
                        ?>
                    </div>
                <?php } else { ?>
                    <div style="background-color: #f3f4f6;padding: 10px">
                        <div style="width: 300px">
                            <?php include(template(":widget/no_record")); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>
<script>
    var _source_mark = '<?php echo $source_mark?>';
    function showCheckDetail(member_id, cert_type) {
        if(!member_id || !cert_type){
            return;
        }

        yo.loadData({
            _c: 'client',
            _m: 'getCheckDetailUrl',
            param: {member_id: member_id, cert_type: cert_type, source_mark: _source_mark},
            callback: function (_o) {
                if (_o.STS) {
                    var url = _o.DATA;
                    window.location.href = url;
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>