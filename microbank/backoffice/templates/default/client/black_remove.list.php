<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/loan.css?v=1" rel="stylesheet" type="text/css"/>
<style>
  .black-client-wrap {
    padding: 20px 20px 0 20px;
  }
  .black-client-wrap .black-item {
    float: left;
    margin: 0 20px 20px 0;
  }
  .black-client-wrap .black-item dt img {
    width: 80px;
    border-radius: 50%;
  }
  .black-client-wrap .black-item dd {
    margin-left: 15px;
    width: 160px;
  }
  .black-client-wrap .black-item dd .label i {
    margin-right: 5px;
  }
  .black-client-wrap .black-item dd .btn:hover {
    cursor: pointer;
    color: #fff;
  }
  .black-client-wrap .black-item dd .btn {
    font-weight: 100;
    padding: 4px 12px;
  }
</style>
<div class="black-client-wrap clearfix">
  <?php $list = $data['data']; $count = count($list);?>
  <?php if($count > 0){?>
    <?php foreach ($list as $key => $value) { ?>
      <dl class="black-item clearfix">
        <dt class="pull-left"><img src="<?php echo getImageUrl($value['member_icon'], imageThumbVersion::AVATAR) ?: ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg' ?>" class="avatar-lg" alt=""></dt>
        <dd class="pull-left">
          <p class="text-small"><span class="show pull-left base-name marginright10">Name</span>:<span class="marginleft10"><a href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$value['uid'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $value['display_name'];?></a></span></p>
          <p class="text-small">
  					<span class="show pull-left base-name marginright10">CID</span>:<span class="marginleft10"><?php echo $value['obj_guid'];?></span>
            <input type="hidden" name="obj_guid" id="obj_guid" value="<?php echo $value['obj_guid'];?>">
            <input type="hidden" name="uid" id="uid" value="<?php echo $value['uid'];?>">
  				</p>
          <p class="text-small"><span class="label label-warning btn" onclick="removeBlack(<?php echo $value['uid'];?>);"><i class="fa fa-minus"></i>Remove</span></p>
        </dd>
      </dl>
    <?php } ?>
  <?php }else{ ?>
    <div class="no-record">
      No Record
    </div>
  <?php } ?>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
<script type="text/javascript">

</script>
