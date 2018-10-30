<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap suggest-history-wrap">
  <?php $list = $output['list'];?>
  <ul class="aui-list suggest-history-ul">
    <?php if(count($list) > 0){?>
      <?php foreach ($list as $v) { ?>
        <li class="aui-margin-b-10">
          <div class="item">
            <label for="">income_rental_housing</label><span><?php echo $v['income_rental_housing'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">income_rental_land</label><span><?php echo $v['income_rental_land'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">business_industry</label><span><?php echo $v['business_industry'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">business_place</label><span><?php echo $v['business_place'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">business_employees</label><span><?php echo $v['business_employees'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">business_profit</label><span><?php echo $v['business_profit'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">business_text</label><span><?php echo $v['business_text'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">company_name</label><span><?php echo $v['company_name'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">company_industry</label><span><?php echo $v['company_industry'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">work_position</label><span><?php echo $v['work_position'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">income_salary</label><span><?php echo $v['income_salary'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">Income Others</label><span><?php echo $v['income_others'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">Research Time</label><span><?php echo timeFormat($v['research_time']);?><em></em></span>
          </div>
          <div class="item">
            <label for="">Others Remark</label><span><?php echo $v['others_remark'];?><em></em></span>
          </div>
          <div class="item">
            <label for="">Research Remark</label><span><?php echo $v['research_remark'];?><em></em></span>
          </div>
        </li>
      <?php }?>
    <?php }else{ ?>
      <div class="no-record"><?php echo $lang['label_no_data'];?></div>
    <?php } ?>
    
  </ul>
</div>
<script type="text/javascript">

</script>
