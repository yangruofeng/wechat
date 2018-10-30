<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap information-wrap">
  <?php $data = $output['data'];?>
  <div>
    <ul class="aui-list request-detail-ul">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Cert Sn
          </div>
          <div class="aui-list-item-input label-on">
            <?php echo $data['id_sn'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Nationality
          </div>
          <div class="aui-list-item-input label-on">
          <?php echo $data['nationality'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            Address
          </div>
          <div class="aui-list-item-input label-on">
          <?php echo $data['nationality'];?>
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
          Expire Time
          </div>
          <div class="aui-list-item-input label-on">
          <?php echo timeFormat($data['id_expire_time']);?>
          </div>
        </div>
      </li>
    </ul>
  </div>
</div>
<script type="text/javascript">

</script>
