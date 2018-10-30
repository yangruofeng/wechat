<style>
    #auth-list .list-group-item {
        border-radius: 0px;
        font-size: 14px;
        padding: 7px 15px;
    }

    #auth-list .auth_group {
        margin-bottom: 10px;
    }
    .form-filter {
      width: 49%;
      float: left;
    }
    .client-info {
      width: 49%;
      float: right;
    }
    .client-info .ibox-content {
      padding: 0;
    }
    .client-info .verification-info .ibox-content .item {

      padding: 0 15px 15px;
      width: 50%;
      float: left;
    }
    .client-info .verification-info .item span {
      font-size: 12px;
      margin-left: 5px;
      float: right;
    }
    .client-info .verification-info .item span.checked {
      color: #32BC61;
    }
    .client-info .verification-info .item span.checking {
      color: red;
    }
    .form-group-label {
      font-weight: 600;
      margin-top: 7px;
      display: block;
    }
</style>
<?php $approval_info = $output['approval_info'];?>
<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Black</h3>
          <ul class="tab-base">
              <li><a href="<?php echo getUrl('client', 'blackList', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
              <li><a class="current"><span>Edit</span></a></li>
          </ul>
      </div>
  </div>
  <div class="container clearfix">
    <?php $info = $output['info'];?>
    <?php $black = $output['black'];?>
    <div class="form-filter">
      <div class="form-wrap">
        <form class="form-horizontal" method="post" action="<?php echo getUrl('client', 'editBlack', array(), false, BACK_OFFICE_SITE_URL) ?>">
          <input type="hidden" name="form_submit" value="ok">
          <input type="hidden" name="obj_guid" value="<?php echo $info['obj_guid'];?>">
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo 'Member GUID'?></label>
            <div class="col-sm-8">
              <span class="form-group-label"><?php echo $info['obj_guid'];?></span>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo 'Member Name'?></label>
            <div class="col-sm-8">
              <span class="form-group-label"><?php echo $info['display_name'];?></span>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo 'Login'?></label>
            <div class="col-sm-8">
              <label class="radio-inline">
                <input type="radio" name="t<?php echo blackTypeEnum::LOGIN;?>" value="0" <?php if(!$black['t'.blackTypeEnum::LOGIN] || $black['t'.blackTypeEnum::LOGIN]['state'] == 0){echo 'checked';}?> /> Available
              </label>
              <label class="radio-inline">
                <input type="radio" name="t<?php echo blackTypeEnum::LOGIN;?>" value="1" <?php if($black['t'.blackTypeEnum::LOGIN] && $black['t'.blackTypeEnum::LOGIN]['state'] == 1){echo 'checked';}?> /> Unavailable
              </label>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo 'Deposit'?></label>
            <div class="col-sm-8">
              <label class="radio-inline">
                <input type="radio" name="t<?php echo blackTypeEnum::DEPOSIT;?>" value="0" <?php if(!$black['t'.blackTypeEnum::DEPOSIT] || $black['t'.blackTypeEnum::DEPOSIT]['state'] == 0){echo 'checked';}?> /> Available
              </label>
              <label class="radio-inline">
                <input type="radio" name="t<?php echo blackTypeEnum::DEPOSIT;?>" value="1" <?php if($black['t'.blackTypeEnum::DEPOSIT] && $black['t'.blackTypeEnum::DEPOSIT]['state'] == 1){echo 'checked';}?> /> Unavailable
              </label>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo 'Insurance'?></label>
            <div class="col-sm-8">
              <label class="radio-inline">
                <input type="radio" name="t<?php echo blackTypeEnum::INSURANCE;?>" value="0" <?php if(!$black['t'.blackTypeEnum::INSURANCE] || $black['t'.blackTypeEnum::INSURANCE]['state'] == 0){echo 'checked';}?> /> Available
              </label>
              <label class="radio-inline">
                <input type="radio" name="t<?php echo blackTypeEnum::INSURANCE;?>" value="1" <?php if($black['t'.blackTypeEnum::INSURANCE] && $black['t'.blackTypeEnum::INSURANCE]['state'] == 1){echo 'checked';}?> /> Unavailable
              </label>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo 'Credit Loan'?></label>
            <div class="col-sm-8">
              <label class="radio-inline">
                <input type="radio" name="t<?php echo blackTypeEnum::CREDIT_LOAN;?>" value="0" <?php if(!$black['t'.blackTypeEnum::CREDIT_LOAN] || $black['t'.blackTypeEnum::CREDIT_LOAN]['state'] == 0){echo 'checked';}?> /> Available
              </label>
              <label class="radio-inline">
                <input type="radio" name="t<?php echo blackTypeEnum::CREDIT_LOAN;?>" value="1" <?php if($black['t'.blackTypeEnum::CREDIT_LOAN] && $black['t'.blackTypeEnum::CREDIT_LOAN]['state'] == 1){echo 'checked';}?> /> Unavailable
              </label>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo 'Mortgage Loan'?></label>
            <div class="col-sm-8">
              <label class="radio-inline">
                <input type="radio" name="t<?php echo blackTypeEnum::MORTGAGE_LOAN;?>" value="0" <?php if(!$black['t'.blackTypeEnum::MORTGAGE_LOAN] || $black['t'.blackTypeEnum::MORTGAGE_LOAN]['state'] == 0){echo 'checked';}?> /> Available
              </label>
              <label class="radio-inline">
                <input type="radio" name="t<?php echo blackTypeEnum::MORTGAGE_LOAN;?>" value="1" <?php if($black['t'.blackTypeEnum::MORTGAGE_LOAN] && $black['t'.blackTypeEnum::MORTGAGE_LOAN]['state'] == 1){echo 'checked';}?> /> Unavailable
              </label>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-col-sm-8" style="padding-left: 15px">
              <button type="button" class="btn btn-danger <?php if($approval_info['uid']){echo 'disabled';} ?>"><?php echo 'Save' ?></button>
              <!--<button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><?php echo 'Back'?></button>-->
              <?php if($approval_info['uid']){ ?><span style="color: red;">Auditing...</span><?php }?>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="client-info">
      <div class="base-info">
        <div class="ibox-title">
          <h5>Client Info</h5>
        </div>
        <div class="ibox-content">
          <div class="activity-list clearfix">
            <table class="table">
                <tbody class="table-body">
                    <tr>
                      <td><label class="control-label">GUID</label></td><td><?php echo $info['obj_guid'];?></td>
                    </tr>
                    <tr>
                      <td><label class="control-label">Name</label></td><td><?php echo $info['display_name'];?></td>
                    </tr>
                    <tr>
                      <td><label class="control-label">Credit</label></td><td><?php echo $info['credit'];?></td>
                    </tr>
                    <tr>
                      <td><label class="control-label">Account Type</label></td><td><?php echo 'Member';?></td>
                    </tr>
                    <tr>
                      <td><label class="control-label">Phone</label></td><td><?php echo $info['phone_id'];?></td>
                    </tr>
                    <tr>
                      <td><label class="control-label">Email</label></td><td><?php echo $info['email'];?></td>
                    </tr>
                  </tbody>
                </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
    $(function () {

    });

    $('.btn-danger').click(function () {
        $('.form-horizontal').submit();
    })

</script>
