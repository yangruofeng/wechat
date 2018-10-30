<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=4">
<?php include_once(template('widget/inc_header'));?>
<?php $data = $output['data']; $list = $data['list'];?>
<div class="wrap assets-evalute-wrap">
  <div class="evalute-amount">
    <p class="amount"><?php echo '3,000';?> <em>USD</em></p>
    <p class="title">Total Income</p>
  </div>
  <div class="assets-list">
    <ul class="aui-list assets-ul aui-margin-b-10">
      <li class="aui-list-item assets-item business-title">
        Rental
      </li>
      <li class="aui-list-item assets-item">
        <div><?php echo 'Housing';?></div>
        <div><input type="text" value="" class="mui_input" /></div>
        <div>【Contract】</div>
      </li>
      <li class="aui-list-item assets-item">
        <div><?php echo 'Land';?></div>
        <div>1,000</div>
        <div>【Contract】</div>
      </li>
    </ul>
    <ul class="aui-list assets-ul aui-margin-b-10">
      <li class="aui-list-item assets-item business-title">
        Business
      </li>
      <li class="aui-list-item assets-item">
        <div><?php echo 'Industry';?></div>
        <div>&lt;Supermarket&gt;</div>
      </li>
      <li class="aui-list-item assets-item">
        <div><?php echo 'Place';?></div>
        <div>&lt;ABC&gt;</div>
      </li>
      <li class="aui-list-item assets-item">
        <div><?php echo 'Employees';?></div>
        <div>10</div>
      </li>
      <li class="aui-list-item assets-item">
        <div><?php echo 'Profiles';?></div>
        <div>1,500</div>
      </li>
    </ul>
    <ul class="aui-list assets-ul aui-margin-b-10">
      <li class="aui-list-item assets-item business-title">
        Salary
      </li>
      <li class="aui-list-item assets-item">
        <div><?php echo 'Position';?></div>
        <div>&lt;None&gt;</div>
      </li>
      <li class="aui-list-item assets-item">
        <div><?php echo 'Salary';?></div>
        <div>0</div>
      </li>
    </ul>
  </div>
</div>
<script type="text/javascript">

</script>
