<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/loan.css?v=3">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap product-list-wrap">
  <div class="aui-content custom-content aui-margin-b-10">
    <ul class="aui-list aui-media-list product-list">
      <?php $list = $output['list'];?>
      <?php foreach ($list as $k => $v){ ?>
        <li>
          <div class="product-item <?php if($k == 0){?>product-item-red<?php }else{ ?>product-item-orange<?php } ?>">
            <div class="title">
              <span class="name"><?php echo $v['product_name'];?></span>
              <span class="detail" onclick="javascript:location.href='<?php echo getUrl('loan', 'productDetail', array('id'=>$v['uid']), false, WAP_SITE_URL)?>'">Detail >></span>
            </div>
            <div class="content">
              <div class="c-item rate">
                <label for="">Min monthly rate</label>
                <span><?php echo $v['min_rate'];?>%</span>
              </div>
              <div class="c-item introduction">
                <label for="">Product Introduction</label>
                <span><?php echo strip_tags($v['product_description']);?></span>
              </div>
            </div>
          </div>
        </li>
      <?php } ?>
    </ul>
    <div class="no-record">No more data</div>
  </div>
</div>
