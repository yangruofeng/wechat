<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/insurance.css?v=4" rel="stylesheet" type="text/css"/>
<style>
.important-info .item {
    color: #fff;
    padding: 20px 0 28px;
    font-size: 14px;
}
.important-info .item .time {
    font-size: 40px;
}
.important-info .repayment-blue {
  background-color: #00aced;
  border-color: #00aced;
}
.important-info .repayment-red {
  background-color: #e7505a;
  border-color: #e7505a;
}
.important-info .repayment-blue1 {
  background-color: #9358ac;
  border-color: #9358ac;
}
.important-info .repayment-fb1 {
  background-color: #355290;
  border-color: #355290;
}
.oprt-function {
  background: none!important;
}
.oprt-function button {
  width: 100%;
}
.oprt-function button:first-child {
  margin-bottom: 7px;
}
</style>
<div class="page">
  <?php $detail = $output['detail'];?>
  <?php $member = $output['member'];?>
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Contract</h3>
          <ul class="tab-base">
              <li><a href="<?php echo getUrl('insurance', 'contract', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
              <li><a class="current"><span>Detail</span></a></li>
          </ul>
      </div>
  </div>
  <div class="container insurance-wrap clearfix">
    <div class="insurance-left">
      <div class="important-info clearfix">
        <div class="item repayment-red">
          <div class="time"><?php echo $detail['price'];?></div>
          <div class="name">Price</div>
        </div>
        <div class="item repayment-blue">
          <div class="time"><?php echo $detail['start_insured_amount'];?></div>
          <div class="name">Start Insured Amount</div>
        </div>
        <div class="item repayment-blue1">
          <div class="time"><?php echo $member['display_name'];?></div>
          <div class="name">Name</div>
        </div>
      </div>
      <div class="contract-info">
        <div class="ibox-title">
          <h5>Contract Info</h5>
        </div>
        <div class="content">
          <table class="table">
            <tr><td>Contract Sn：</td><td><?php echo $detail['contract_sn'];?></td><td>Price：</td><td><?php echo $detail['price'];?></td></tr>
            <tr><td>Start Insured Amount：</td><td><?php echo $detail['start_insured_amount'];?></td><td>Floating Amount：</td><td><?php echo $detail['floating_amount'];?></td></tr>
            <tr><td>Start Date：</td><td><?php echo $detail['start_date'];?></td><td>End Date：</td><td><?php echo $detail['end_date'];?></td></tr>
            <tr><td>Tax Fee：</td><td><?php echo $detail['tax_fee'];?></td><td>Create Time：</td><td><?php echo timeFormat($detail['create_time']);?></td></tr>
            <tr><td>Creator Name：</td><td><?php echo $detail['creator_name'];?></td><td>Officer Name：</td><td><?php echo $detail['officer_name'];?></td></tr>
            <tr><td>Payer Type：</td><td><?php echo $detail['payer_type'];?></td><td>Payer Name：</td><td><?php echo $detail['payer_name'];?></td></tr>
            <tr><td>Payer Phone：</td><td><?php echo $detail['payer_phone'];?></td><td>Payer Account：</td><td><?php echo $detail['payer_account'];?></td></tr>
            <tr><td>Payer Image：</td><td><?php echo $detail['payer_image'];?></td><td>Teller Name：</td><td><?php echo $detail['teller_name'];?></td></tr>
            <tr><td>Serial Number：</td><td><?php echo $detail['payer_phone'];?></td><td>State：</td><td><?php echo $detail['state'];?></td></tr>
          </table>
        </div>
      </div>
      <div class="member-info">
        <div class="ibox-title">
          <h5>Member Info</h5>
          <a class="pull-right" href="<?php echo getUrl('client', 'clientDetail', array('uid'=>$member['member_id'], 'show_menu'=>'client-client'), false, BACK_OFFICE_SITE_URL)?>">More Detail</a>
        </div>
        <div class="content">
          <table class="table">
            <tr><td>Obj Guid：</td><td><?php echo $member['obj_guid'];?></td><td>Member Name：</td><td><?php echo $member['display_name'];?></td></tr>
            <tr><td>Credit：</td><td><?php echo $member['credit'];?></td><td>Type：</td><td><?php if($datail['account_type']==0){echo 'Member';}?></td></tr>
            <tr><td>Phone：</td><td><?php echo $member['phone_id'];?></td><td>Email：</td><td><?php echo $member['email'];?></td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="insurance-right">
      <div class="beneficiary-info">
        <div class="ibox-title">
          <h5>Benefit Info</h5>
        </div>
        <div class="content">
          <table class="table">
            <?php $beneficiary = $output['beneficiary'];?>
            <?php foreach ($beneficiary as $key => $value) { ?>
              <tr><td>Benefit Index：</td><td><?php echo $value['benefit_index'];?></td></tr>
              <tr><td>Benefit Name：</td><td><?php echo $value['benefit_name'];?></td></tr>
              <tr><td>Benefit Phone：</td><td><?php echo $value['benefit_phone'];?></td></tr>
              <tr><td>Benefit Address：</td><td><?php echo $value['benefit_addr']?:'None';?></td></tr>
            <?php } ?>
          </table>
        </div>
      </div>
      <div class="product-info">
        <div class="ibox-title">
          <h5>Product Info</h5>
        </div>
        <div class="content">
          <table class="table">
            <?php $product = $output['product'];?>
            <tr><td>Code：</td><td><?php echo $product['product_code'];?></td></tr>
            <tr><td>Name：</td><td><?php echo $product['product_name'];?></td></tr>
            <tr><td>Description：</td><td><?php echo $product['product_description']?:'None';?></td></tr>
            <tr><td>Feature：</td><td><?php echo $product['product_feature']?:'None';?></td></tr>
            <tr><td>Notice：</td><td><?php echo $product['product_notice']?:'None';?></td></tr>
            <tr><td>State：</td><td><?php if($product['state'] == 10){echo 'Temp';}elseif($product['state'] == 20){echo 'Active';}elseif($product['state'] == 30){echo 'Inactive';}elseif($product['state'] == 40){echo 'History';};?></td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
    $(function () {

    });

</script>
