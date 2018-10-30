<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap certtpye-wrap">
  <?php $list = $output['list'];
    $guarantee_list = $list['guarantee_list'];$count = count($guarantee_list);
    $apply_list = $list['apply_list'];$count1 = count($apply_list);
  ?>
  <div class="aui-tab contract-tab" id="tab">
    <div class="aui-tab-item aui-active"><?php echo 'Guarantor';?></div>
    <div class="aui-tab-item"><div></div><?php echo 'Apply Guarantor';?></div>
  </div>
  <div class="aui-refresh-content">
    <div class="limit-calculation tab-panel" id="tab-1" type="1">
      <div class="aui-content aui-margin-b-15">
        <?php if($count <= 0){?>
          <div class="certtpye-no-content">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/no-content.png" alt="">
            <p>no content</p>
          </div>
        <?php }else{ ?>
          <ul class="aui-list aui-media-list">
            <?php foreach ($guarantee_list as $key => $value) { ?>
              <li class="aui-list-item aui-list-item-middle" onclick="">
                <div class="aui-media-list-item-inner">
                  <div class="aui-list-item-media avatar"><img src="<?php echo getImageUrl($value['cert_images']['car_front']); ?>" class="aui-img-round aui-list-img-sm"></div>
                  <div class="aui-list-item-inner">
                    <p><?php echo 'No: '.($key+1); ?></p>
                    <p><?php echo 'Phone: ';echo $value['phone_id']?:'null'; ?></p>
                    <p><?php $relation_type_name_json = json_decode($value['relation_type_name_json'], true);echo 'Relationship: '.$relation_type_name_json[Language::currentCode()]; ?></p>
                    <p><?php echo 'name: ';echo $value['display_name']?:'null'; ?></p>
                  </div>
                </div>
              </li>
            <?php } ?>
          </ul>
        <?php }?>
        <div class="add-btn" onclick="javascript:location.href='<?php echo getUrl('credit', 'cerification', array('type'=>$_GET['type']), false, WAP_SITE_URL)?>'">
          <span><img src="<?php echo WAP_SITE_URL;?>/resource/image/iocn-add.png" alt=""><?php echo $lang['act_add'];?></span>
        </div>
      </div>
    </div>
    <div class="limit-calculation tab-panel" id="tab-2" type="2" style="display: none;">
      <div class="aui-content aui-margin-b-15">
        <?php if($count1 <= 0){?>
          <div class="certtpye-no-content">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/no-content.png" alt="">
            <p>no content</p>
          </div>
        <?php }else{ ?>
          <ul class="aui-list aui-media-list">
            <?php foreach ($apply_list as $key => $value) { ?>
              <li class="aui-list-item aui-list-item-middle" onclick="">
                <div class="aui-media-list-item-inner">
                  <div class="aui-list-item-media avatar"><img src="<?php echo getImageUrl($value['cert_images']['car_front']); ?>" class="aui-img-round aui-list-img-sm"></div>
                  <div class="aui-list-item-inner">
                    <p><?php echo 'No: '.($key+1); ?></p>
                    <p><?php echo 'Phone: ';echo $value['phone_id']?:'null'; ?></p>
                    <p><?php $relation_type_name_json = json_decode($value['relation_type_name_json'], true);echo 'Relationship: '.$relation_type_name_json[Language::currentCode()]; ?></p>
                    <p><?php echo 'name: ';echo $value['display_name']?:'null'; ?></p>
                  </div>

                  <?php if($value['relation_state'] == 0){ ?>
                    <div class="relation-oprt">
                      <span class="accept" onclick="relationDeal('<?php echo $value['uid'];?>',1);">Accept</span>
                      <span class="refuse" onclick="relationDeal('<?php echo $value['uid'];?>',0);">Refuse</span>
                    </div>
                  <?php } ?>
                </div>
              </li>
            <?php } ?>
          </ul>
        <?php }?>
      </div>
    </div>
  </div>


</div>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-tab.js"></script>
<script type="text/javascript">
  var back = '<?php echo $_GET['back'];?>';
  if(back){
    $('#header .back').attr('onclick', "javascript:location.href='<?php echo getUrl('credit', 'certList', array('back'=>'credit'), false, WAP_SITE_URL)?>'");
  }
  var tab = new auiTab({
    element: document.getElementById('tab'),
    index: 1,
    repeatClick: false
  },function(ret){
    var i = ret.index;
    $('.tab-panel').hide();
    $('#tab-' + i).show();
  });
  function relationDeal(uid, state){
    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=ajaxGuaranteeConfirm',
      data: {uid: uid, state: state},
      dataType: 'json',
      success: function(data){
        toast.hide();
        toast.success({
          title: data.MSG,
          duration: 2000
        });
        if(data.STS){
          setTimeout(function(){
            window.location.reload();
          }, 2000);
        }else{
          verifyFail(data.MSG);
        }

      },
      error: function(xhr, type){
        toast.hide();
        verifyFail('<?php echo $lang['tip_get_data_error'];?>');
      }
    });
  }
</script>
