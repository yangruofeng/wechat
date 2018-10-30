<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/loan.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap calculator-view-wrap">
  <div class="aui-content custom-content aui-margin-b-10">
    <ul class="aui-list aui-media-list">
      <?php $list = $output['list'];?>
      <?php foreach ($list as $key => $value) { ?>
        <li class="aui-list-item aui-list-item-middle">
          <div class="aui-media-list-item-inner">
            <div class="aui-list-item-inner aui-list-item-arrow">
              <div class="aui-list-item-text">
                <?php echo $value;?>
              </div>
            </div>
          </div>
        </li>
      <?php } ?>
    </ul>
  </div>
</div>
